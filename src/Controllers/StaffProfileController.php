<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\StaffProfileRepository;
use App\Repositories\UserRepository;

class StaffProfileController extends Controller
{
  private StaffProfileRepository $staffRepo;
  private UserRepository $userRepo;

  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $this->staffRepo = new StaffProfileRepository();
    $this->userRepo = new UserRepository();
  }

  private function json($data, int $statusCode = 200)
  {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }

  private function handleFileUpload($file, $uploadSubDir)
  {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
      return null;
    }

    // LISTAHAN NG POSSIBLE PATHS (I-adjust mo base sa folder names niyo)
    // Subukan nating hanapin ang 'backend' folder
    $possiblePaths = [
      realpath(__DIR__ . '/../../../../backend/public/'), // Path A
      realpath($_SERVER['DOCUMENT_ROOT'] . '/backend/public/'), // Path B
      'C:/Users/adria/Desktop/backend/public/' // Path C (Hardcoded fallback para sa PC mo)
    ];

    $laravelPublicPath = null;
    foreach ($possiblePaths as $path) {
      if ($path && file_exists($path)) {
        $laravelPublicPath = $path;
        break;
      }
    }

    if (!$laravelPublicPath) {
      // Ito ang magsasabi sa atin kung bakit fail
      error_log("Upload Error: All possible paths failed for " . __DIR__);
      return null;
    }

    $uploadSubDir = trim($uploadSubDir, '/\\');
    $fullTargetDir = $laravelPublicPath . DIRECTORY_SEPARATOR . $uploadSubDir;

    if (!file_exists($fullTargetDir)) {
      mkdir($fullTargetDir, 0777, true);
    }

    $prefix = (strpos($uploadSubDir, 'profile') !== false) ? 'profile_' : 'reg_';
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = $prefix . ($_SESSION['user_id'] ?? 'user') . '_' . time() . '.' . $extension;

    $targetFile = $fullTargetDir . DIRECTORY_SEPARATOR . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
      return 'uploads/' . basename($uploadSubDir) . '/' . $fileName;
    }

    return null;
  }

  private function validateImageUpload($file)
  {
    $maxSize = 1 * 1024 * 1024; // 1MB
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if ($file['error'] !== UPLOAD_ERR_OK) return "Upload error.";
    if ($file['size'] > $maxSize) return "Image must be less than 1MB.";

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) return "Invalid image type. Only JPG, PNG, GIF allowed.";
    return true;
  }

  public function getProfile()
  {
    $currentUserId = $_SESSION['user_id'] ?? null;
    if (!$currentUserId) return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);

    $profile = $this->staffRepo->getProfileByUserId($currentUserId);
    if (!$profile) return $this->json(['success' => false, 'message' => 'Profile not found.'], 404);

    $profile['allow_edit'] = 1; // Staff can always edit

    $this->json(['success' => true, 'profile' => $profile]);
  }

  public function updateProfile()
  {
    $currentUserId = $_SESSION['user_id'] ?? null;
    if (!$currentUserId) {
      return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $data = $_POST;
    $profile = $this->staffRepo->getProfileByUserId($currentUserId);
    if (!$profile) {
      return $this->json(['success' => false, 'message' => 'Profile not found.'], 404);
    }

    // --- Ensure profile picture exists ---
    $hasUploadedImage = isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0;
    $hasExistingImage = !empty($profile['profile_picture']) && trim($profile['profile_picture']) !== '';

    if (!$hasUploadedImage && !$hasExistingImage) {
      return $this->json([
        'success' => false,
        'message' => 'Profile picture is required. Please upload one before saving.'
      ], 400);
    }

    // --- Validate required fields ---
    $requiredFields = ['first_name', 'last_name', 'email', 'position', 'contact'];
    $missingFields = [];
    foreach ($requiredFields as $field) {
      if (!isset($data[$field]) || trim($data[$field]) === '') $missingFields[] = $field;
    }
    if (!empty($missingFields)) {
      return $this->json([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missingFields)
      ], 400);
    }

    // --- Validate contact and email ---
    if (!preg_match('/^\d{11}$/', $data['contact'])) {
      return $this->json(['success' => false, 'message' => 'Contact number must be 11 digits.'], 400);
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      return $this->json(['success' => false, 'message' => 'Invalid email address.'], 400);
    }

    // --- Handle profile image upload ---
    if ($hasUploadedImage) {
      $validation = $this->validateImageUpload($_FILES['profile_image']);
      if ($validation !== true) {
        return $this->json(['success' => false, 'message' => $validation], 400);
      }
      $imagePath = $this->handleFileUpload($_FILES['profile_image'], "uploads/profile_images");
    }

    // --- Prepare user data for update ---
    $fullName = trim(implode(' ', array_filter([
      $data['first_name'],
      $data['middle_name'] ?? null,
      $data['last_name'],
      $data['suffix'] ?? null
    ])));

    $userData = [
      'first_name' => $data['first_name'],
      'middle_name' => $data['middle_name'] ?? null,
      'last_name' => $data['last_name'],
      'suffix' => $data['suffix'] ?? null,
      'full_name' => $fullName,
      'email' => $data['email']
    ];

    if (isset($imagePath)) {
      $userData['profile_picture'] = $imagePath;
    }

    $this->userRepo->updateUser($currentUserId, $userData);

    // --- Update staff-specific data ---
    $staffData = [
      'position' => $data['position'],
      'contact' => $data['contact'],
      'profile_updated' => 1
    ];
    $this->staffRepo->updateStaffProfile($currentUserId, $staffData);

    if (isset($_SESSION['user_data'])) {
      $_SESSION['user_data']['fullname'] = $fullName;
      if ($imagePath) {
        $_SESSION['user_data']['profile_picture'] = $imagePath;
      }
    }

    return $this->json(['success' => true, 'message' => 'Profile updated successfully!']);
  }
}
