<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;
use PDOException;

class RestoreBookRepository
{
  protected PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance()->getConnection();
  }

  public function getDeletedBooks(): array
  {
    $sql = "SELECT 
                    b.book_id as id, 
                    b.accession_number, b.call_number, b.title, b.author, b.book_isbn,
                    b.book_place, b.book_publisher, b.year, b.book_edition, b.description, b.book_supplementary, b.subject, b.created_at, b.availability, b.quantity, b.cover, 
                    b.deleted_at, b.is_archived, b.deleted_by as deleted_by_id, 
                    CONCAT(admin.first_name, ' ', admin.last_name) as deleted_by_name 
                FROM books b
                LEFT JOIN users admin ON b.deleted_by = admin.user_id 
                WHERE b.deleted_at IS NOT NULL
                ORDER BY b.deleted_at DESC";
    try {
      $stmt = $this->db->query($sql);
      $rawBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $filteredBooks = array_filter($rawBooks, function ($book) {
        return (isset($book['is_archived']) && $book['is_archived'] == 0);
      });

      return array_values($filteredBooks);
    } catch (PDOException $e) {
      return [];
    }
  }

  public function getBookById(int $id)
  {
    $stmt = $this->db->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function restoreBook(int $bookId): bool
  {
    $this->db->beginTransaction();
    try {
      $sqlBook = "UPDATE books 
                SET deleted_at = NULL, deleted_by = NULL, is_archived = 0
                WHERE book_id = :id AND deleted_at IS NOT NULL";
      $stmtBook = $this->db->prepare($sqlBook);
      $stmtBook->execute(['id' => $bookId]);

      if ($stmtBook->rowCount() === 0) {
        $this->db->rollBack();
        return false;
      }
      $this->db->commit();
      return true;
    } catch (PDOException $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function archiveBook(int $bookId, int $librarianId): array
  {
    $this->db->beginTransaction();
    try {

      $stmtGetBook = $this->db->prepare("SELECT * FROM books WHERE book_id = :id AND deleted_at IS NOT NULL AND is_archived = 0");
      $stmtGetBook->execute(['id' => $bookId]);
      $bookData = $stmtGetBook->fetch(PDO::FETCH_ASSOC);

      if (!$bookData) {
        $this->db->rollBack();
        return [
          'success' => false,
          'debug_reason' => 'Book not found or already archived/restored.',
          'debug_data' => ['book_id' => $bookId]
        ];
      }

      $stmtInsertDeletedBook = $this->db->prepare(
        "INSERT INTO deleted_books (book_id, accession_number, call_number, title, author, book_place, book_publisher, year, book_edition, description, book_isbn, book_supplementary, subject, created_at, availability, quantity, cover, deleted_at, deleted_by)
                 VALUES (:book_id, :accession_number, :call_number, :title, :author, :book_place, :book_publisher, :year, :book_edition, :description, :book_isbn, :book_supplementary, :subject, :created_at, :availability, :quantity, :cover, :deleted_at, :deleted_by)"
      );

      $stmtInsertDeletedBook->execute([
        ':book_id' => $bookData['book_id'],
        ':accession_number' => $bookData['accession_number'],
        ':call_number' => $bookData['call_number'],
        ':title' => $bookData['title'],
        ':author' => $bookData['author'],
        ':book_place' => $bookData['book_place'],
        ':book_publisher' => $bookData['book_publisher'],
        ':year' => $bookData['year'],
        ':book_edition' => $bookData['book_edition'],
        ':description' => $bookData['description'],
        ':book_isbn' => $bookData['book_isbn'],
        ':book_supplementary' => $bookData['book_supplementary'],
        ':subject' => $bookData['subject'],
        ':created_at' => $bookData['created_at'],
        ':availability' => $bookData['availability'],
        ':quantity' => $bookData['quantity'],
        ':cover' => $bookData['cover'],
        ':deleted_at' => $bookData['deleted_at'],
        ':deleted_by' => $librarianId
      ]);

      $stmtUpdateArchive = $this->db->prepare("UPDATE books SET is_archived = 1 WHERE book_id = :id");
      $stmtUpdateArchive->execute(['id' => $bookId]);

      $this->db->commit();
      return ['success' => true];
    } catch (PDOException $e) {
      $this->db->rollBack();
      return [
        'success' => false,
        'debug_reason' => 'Database error during archive.',
        'debug_data' => $e->getMessage()
      ];
    }
  }
}
