<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\RestoreBookRepository;

class RestoreBookController extends Controller
{
  protected RestoreBookRepository $restoreBookRepo;
  protected \App\Repositories\AuditLogRepository $auditRepo;

  public function __construct()
  {
    $this->restoreBookRepo = new RestoreBookRepository();
    $this->auditRepo = new \App\Repositories\AuditLogRepository();
  }


  public function getDeletedBooksJson()
  {
    if (session_status() === PHP_SESSION_NONE) session_start();
    header('Content-Type: application/json');
    try {
      $books = $this->restoreBookRepo->getDeletedBooks();
      echo json_encode(['success' => true, 'books' => $books]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()]);
    }
  }

  public function restore()
  {
    if (session_status() === PHP_SESSION_NONE) session_start();
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
      return;
    }

    if (!$this->validateCsrf()) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'CSRF Token Validation Failed.']);
      return;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $bookId = $input['book_id'] ?? null;

    if (!$bookId) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Book ID is required.']);
      return;
    }

    $librarianId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    if (!$librarianId) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'Unauthorized action. Admin ID not found in session for restore.']);
      return;
    }

    try {
      $book = $this->restoreBookRepo->getBookById((int)$bookId);
      $restored = $this->restoreBookRepo->restoreBook((int)$bookId);
      if ($restored) {
        $bookIdentifier = $book ? "{$book['title']} (Acc: {$book['accession_number']})" : "ID: $bookId";
        $this->auditRepo->log($librarianId, 'RESTORE', 'BOOKS', $bookId, "Restored book: $bookIdentifier");
        echo json_encode(['success' => true, 'message' => 'Book restored successfully.']);
      } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to restore book. Book might not exist or already restored/archived.']);
      }
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'An internal error occurred during restoration.']);
    }
  }

  public function archive($id)
  {
    if (session_status() === PHP_SESSION_NONE) session_start();
    header('Content-Type: application/json');

    if (!$this->validateCsrf()) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'CSRF Token Validation Failed.']);
      return;
    }

    $bookId = filter_var($id, FILTER_VALIDATE_INT);
    $librarianId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    if (!$bookId) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Invalid Book ID provided in URL.']);
      return;
    }

    if (!$librarianId) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'Admin User ID not found in session for archiving.']);
      return;
    }

    if (in_array($_SESSION['role'], ['admin', 'librarian'])) {
      echo json_encode([
        'success' => false,
        'message' => 'You are not allowed to permanently delete books.'
      ]);
      exit;
    }

    try {
      $book = $this->restoreBookRepo->getBookById((int)$bookId);
      $result = $this->restoreBookRepo->archiveBook($bookId, $librarianId);

      if ($result['success']) {
        $bookIdentifier = $book ? "{$book['title']} (Acc: {$book['accession_number']})" : "ID: $bookId";
        $this->auditRepo->log($librarianId, 'ARCHIVE', 'BOOKS', $bookId, "Permanently archived book: $bookIdentifier");
        echo json_encode(['success' => true, 'message' => 'Book successfully archived.']);
      } else {
        http_response_code(400);
        echo json_encode([
          'success' => false,
          'message' => 'Failed to archive book. Check debug info.',
          'debug_reason' => $result['debug_reason'] ?? 'Unknown reason.',
          'debug_data' => $result['debug_data'] ?? null
        ]);
      }
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'An internal error occurred during archiving.']);
    }
  }
}
