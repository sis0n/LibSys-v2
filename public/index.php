<?php
/**
 * ==========================================================
 * 🚀 LIB-SYS V2 - UNIFIED FRONT CONTROLLER
 * Supports: Local Subfolders & Production Subdomains
 * ==========================================================
 */

// [1] Secure Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'cookie_samesite' => 'Lax',
    ]);
}

// [2] Production Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// [3] Define Constants
define('ROOT_PATH', dirname(__DIR__));

// [4] Load Autoloader & Environment
require ROOT_PATH . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\RouteConfig;

date_default_timezone_set('Asia/Manila');

// I-load ang .env (Kung wala, hindi mag-e-error ang system pero kailangan ito)
if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

// [5] Debugging & Error Reporting (Based on APP_DEBUG)
$isDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
if ($isDebug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// [6] Define App URLs (Important for Routing & Assets)
if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'));
}

if (!defined('STORAGE_URL')) {
    // Kung walang STORAGE_URL sa .env, gagamit ng default
    define('STORAGE_URL', rtrim($_ENV['STORAGE_URL'] ?? BASE_URL . '/storage', '/'));
}

/**
 * ==========================================================
 * 🗺️ ADVANCED ROUTE CALCULATION
 * ==========================================================
 */

// A. Kunin ang Request URI (e.g., /dashboard o /libsys-v2/public/dashboard)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// B. Kunin ang Base Path mula sa BASE_URL (e.g., /libsys-v2/public o empty sa subdomain)
$baseUrlPath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
$basePathToRemove = rtrim($baseUrlPath, '/') . '/';

// C. Clean the Route
if ($basePathToRemove !== '/' && str_starts_with($uri, $basePathToRemove)) {
    // Local: Tanggalin ang subfolder path
    $route = substr($uri, strlen($basePathToRemove));
} else {
    // Production: Tanggalin lang ang leading slash
    $route = ltrim($uri, '/');
}

// D. Normalize (default to empty string if root)
$route = trim($route, '/');

/**
 * ==========================================================
 * 🚦 DISPATCH ROUTER
 * ==========================================================
 */
$method = $_SERVER['REQUEST_METHOD'];
$router = RouteConfig::register();
$router->resolve($route, $method);
