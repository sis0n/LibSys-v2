<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\StudentProfileRepository;
use App\Repositories\UserRepository;

class StudentProfileController extends Controller
{
  private $studentRepo;
  private $userRepo;

  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    $this->studentRepo = new StudentProfileRepository();
    $this->userRepo = new UserRepository();
  }

  private function json($data, $statusCode = 200)
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
      // Mahalaga: Forward slash (/) ang i-save sa DB para sa URL compatibility
      return 'uploads/' . basename($uploadSubDir) . '/' . $fileName;
    }

    return null;
  }

  private function validateImageUpload($file)
  {
    $maxSize = 1 * 1024 * 1024;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if ($file['error'] !== UPLOAD_ERR_OK) return "Upload error.";
    if ($file['size'] > $maxSize) return "Image must be less than 1MB.";

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) return "Invalid image type. Only JPG, PNG, GIF allowed.";
    return true;
  }

  private function validatePDFUpload($file)
  {
    $maxSize = 5 * 1024 * 1024; // 5MB
    $allowedTypes = ['application/pdf'];

    if ($file['error'] !== UPLOAD_ERR_OK) return "Upload error.";
    if ($file['size'] > $maxSize) return "PDF must be less than 5MB.";

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) return "Invalid file type. Only PDF allowed.";
    return true;
  }

  public function getProfile()
  {
    try {
      $currentUserId = $_SESSION['user_id'] ?? null;
      if (!$currentUserId) {
        return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
      }

      $profile = $this->studentRepo->getProfileByUserId($currentUserId);
      if (!$profile) {
        return $this->json(['success' => false, 'message' => 'Profile not found.'], 404);
      }

      $profile['allow_edit'] = $profile['can_edit_profile'] ?? 0;
      $this->json(['success' => true, 'profile' => $profile]);
    } catch (\Exception $e) {
      error_log("StudentProfileController::getProfile Error: " . $e->getMessage());
      $this->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

  public function updateProfile()
  {
    try {
      $currentUserId = $_SESSION['user_id'] ?? null;
      if (!$currentUserId) {
        return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
      }

      $data = $_POST;
      $profile = $this->studentRepo->getProfileByUserId($currentUserId);

      $requiredFields = ['first_name', 'last_name', 'course_id', 'year_level', 'section', 'email', 'contact'];
      $missingFields = [];
      foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '' || ($field === 'course_id' && (!is_numeric($data[$field]) || (int)$data[$field] === 0))) {
          $missingFields[] = $field;
        }
      }
      if (!empty($missingFields)) {
        return $this->json([
          'success' => false,
          'message' => 'Please fill in all required fields. (Missing: ' . implode(', ', $missingFields) . ')'
        ], 400);
      }

      $courseId = filter_var($data['course_id'], FILTER_VALIDATE_INT);
      if (!$courseId) {
        return $this->json(['success' => false, 'message' => 'Invalid course selection.'], 400);
      }

      if (!preg_match('/^\d{11}$/', $data['contact'])) {
        return $this->json(['success' => false, 'message' => 'Contact number must be numeric and 11 digits.'], 400);
      }

      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return $this->json(['success' => false, 'message' => 'Please enter a valid email address.'], 400);
      }

      if ($profile && $profile['profile_updated'] == 1 && $profile['can_edit_profile'] == 0) {
        return $this->json([
          'success' => false,
          'message' => 'Profile is locked. Please contact admin to unlock it.'
        ], 403);
      }

      // --- FILE UPLOAD CHECKS ---
      $hasExistingProfilePic = !empty($profile['profile_picture']);
      $isNewProfilePicUploaded = (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0);

      if (!$hasExistingProfilePic && !$isNewProfilePicUploaded) {
        return $this->json(['success' => false, 'message' => 'Profile picture is required.'], 400);
      }

      $hasExistingRegForm = !empty($profile['registration_form']);
      $isNewRegFormUploaded = (isset($_FILES['reg_form']) && $_FILES['reg_form']['error'] === 0);

      if (!$hasExistingRegForm && !$isNewRegFormUploaded) {
        return $this->json(['success' => false, 'message' => 'Registration form is required.'], 400);
      }

      // --- Data Construction ---
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

      // --- PROFILE IMAGE UPLOAD ---
      $imagePath = null;
      if ($isNewProfilePicUploaded) {
        $validation = $this->validateImageUpload($_FILES['profile_image']);
        if ($validation !== true) return $this->json(['success' => false, 'message' => $validation], 400);

        $imagePath = $this->handleFileUpload($_FILES['profile_image'], "uploads/profile_images");

        if ($imagePath === null) {
          return $this->json([
            'success' => false,
            'message' => 'Failed to move file. Check if the folder exists and is writable.'
          ], 500);
        }

        $userData['profile_picture'] = $imagePath;
      }

      $this->userRepo->updateUser($currentUserId, $userData);

      $studentData = [
        'course_id' => $courseId,
        'year_level' => $data['year_level'],
        'section' => $data['section'],
        'contact' => $data['contact'],
        'profile_updated' => 1,
      ];

      if (!empty($profile['can_edit_profile']) && $profile['can_edit_profile'] == 1) {
        $studentData['can_edit_profile'] = 0;
      }

      // --- REG FORM UPLOAD (FIXED: Mag-u-upload lang kapag may bagong file) ---
      $pdfPath = null;
      if ($isNewRegFormUploaded) {
        $validation = $this->validatePDFUpload($_FILES['reg_form']);
        if ($validation !== true) return $this->json(['success' => false, 'message' => $validation], 400);
        $pdfPath = $this->handleFileUpload($_FILES['reg_form'], "uploads/reg_forms"); // Fixed path
        $studentData['registration_form'] = $pdfPath;
      }

      $this->studentRepo->updateStudentProfile($currentUserId, $studentData);

      if (isset($_SESSION['user_data'])) {
        $_SESSION['user_data']['fullname'] = $fullName;
        if ($imagePath) {
          $_SESSION['user_data']['profile_picture'] = $imagePath;
        }
      }

      $this->json(['success' => true, 'message' => 'Profile updated successfully!']);
    } catch (\Exception $e) {
      error_log("StudentProfileController::updateProfile Error: " . $e->getMessage());
      $this->json(['success' => false, 'message' => 'Error updating profile: ' . $e->getMessage()], 500);
    }
  }
}
