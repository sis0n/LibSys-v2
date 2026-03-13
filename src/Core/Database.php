<?php

namespace App\Core;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $driver = $_ENV['DB_CONNECTION'] ?? 'mysql';
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $db = $_ENV['DB_DATABASE'] ?? 'library-mobile';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? '';

        try {
            $this->connection = new PDO("$driver:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
                die("<h1>Database Connection Error</h1><p>" . $e->getMessage() . "</p>");
            }

            http_response_code(500);
            $error_view = __DIR__ . '/../Views/errors/500.php';
            if (file_exists($error_view)) {
                include $error_view;
            } else {
                echo "<h1>500 Internal Server Error</h1><p>Database connection failed.</p>";
            }
            exit;
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
