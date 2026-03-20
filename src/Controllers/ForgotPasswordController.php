<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Repositories\PasswordResetRepository;
use App\Services\MailService;

class ForgotPasswordController extends Controller
{
  protected UserRepository $userRepo;
  protected PasswordResetRepository $tokenRepo;
  protected MailService $mailService;

  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    $this->userRepo = new UserRepository();
    $this->tokenRepo = new PasswordResetRepository();
    $this->mailService = new MailService();
  }

  public function index()
  {
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $viewData = [
      'csrf_token' => $_SESSION['csrf_token']
    ];
    $this->view('auth/forgotPassword', $viewData, false);
  }

  public function resetPasswordPage()
  {
    if (empty($_SESSION['reset_email']) || empty($_SESSION['otp_verified'])) {
      header("Location: " . BASE_URL . "/forgot-password");
      exit;
    }

    $viewData = [
      'csrf_token' => $_SESSION['csrf_token']
    ];

    $this->view('auth/resetPassword', $viewData, false);
  }

  public function updatePassword()
  {
    header('Content-Type: application/json');

    if (!$this->validateCsrf()) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
      return;
    }

    if (empty($_SESSION['reset_email']) || empty($_SESSION['otp_verified'])) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'Session expired. Please start over.']);
      return;
    }

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirmPassword)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Both passwords are required.']);
      return;
    }

    if ($password !== $confirmPassword) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
      return;
    }

    if (strlen($password) < 8) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
      return;
    }

    try {
      $email = $_SESSION['reset_email'];
      $user = $this->userRepo->findByEmail($email);

      if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        return;
      }

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $success = $this->userRepo->updatePassword($user['user_id'], $hashedPassword);

      if ($success) {
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_verified']);

        echo json_encode(['success' => true, 'message' => 'Password has been reset successfully.']);
      } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
      }
    } catch (\Throwable $e) {
      error_log("[ForgotPasswordController::updatePassword] " . $e->getMessage());
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'An internal error occurred.']);
    }
  }

  public function sendOTP()
  {
    header('Content-Type: application/json');

    if (!$this->validateCsrf()) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
      return;
    }

    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
      return;
    }

    $result = $this->_sendCode($email);

    if ($result) {
      $_SESSION['reset_email'] = $email;
    }

    echo json_encode(['success' => true, 'message' => 'If an account with that email exists, a code has been sent.']);
  }

  private function _sendCode(string $email): bool
  {
    $user = $this->userRepo->findByEmail($email);

    if ($user) {
      try {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = time() + 600; // 10 minutes expiry

        $this->tokenRepo->createToken($email, $otp, $expiry);

        $subject = "Your LibSys Password Reset Code";
        $body = "
              <p>Hello {$user['first_name']},</p>
              <p>You requested to reset your password. Use the code below to verify your identity.</p>
              <h2 style='font-size: 24px; letter-spacing: 2px; font-weight: bold;'>{$otp}</h2>
              <p>This code is valid for 10 minutes.</p>
          ";

        return $this->mailService->sendEmail($email, $subject, $body);
      } catch (\Throwable $e) {
        error_log("[ForgotPasswordController::_sendCode] " . $e->getMessage());
        return false;
      }
    }
    return false;
  }

  public function verifyOTPPage()
  {
    if (empty($_SESSION['reset_email'])) {
      header("Location: " . BASE_URL . "/forgot-password");
      exit;
    }

    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $viewData = [
      'csrf_token' => $_SESSION['csrf_token'],
      'email' => $_SESSION['reset_email']
    ];
    $this->view('auth/verifyOTP', $viewData, false);
  }

  public function resendOTP()
  {
    header('Content-Type: application/json');

    if (!$this->validateCsrf()) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
      return;
    }

    $email = $_SESSION['reset_email'] ?? null;
    if (!$email) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Session expired. Please start over.']);
      return;
    }

    $this->_sendCode($email);

    echo json_encode(['success' => true, 'message' => 'A new code has been sent to your email.']);
  }

  public function checkOTP()
  {
    header('Content-Type: application/json');

    if (!$this->validateCsrf()) {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
      return;
    }

    $email = $_SESSION['reset_email'] ?? null;
    $otp = trim($_POST['otp'] ?? '');

    if (!$email || empty($otp)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Invalid request. Please try again.']);
      return;
    }

    $token = $this->tokenRepo->findToken($otp);

    if (!$token) {
      echo json_encode(['success' => false, 'message' => 'Invalid code. Please try again.']);
      return;
    }

    if (strtolower($token['email']) !== strtolower($email)) {
      echo json_encode(['success' => false, 'message' => 'Invalid code. Code mismatch.']);
      return;
    }

    if (strtotime($token['expires_at']) < time()) {
      echo json_encode(['success' => false, 'message' => 'This code has expired. Please resend.']);
      return;
    }

    $_SESSION['otp_verified'] = true;

    $this->tokenRepo->deleteToken($email);

    echo json_encode(['success' => true]);
  }
}
