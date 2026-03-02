<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Repositories\BackupRepository;
use App\Repositories\AuditLogRepository;
use App\Services\MailService;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$backupRepo = new BackupRepository();
$auditRepo = new AuditLogRepository();
$mailService = new MailService();

// Email kung saan ipapadala ang notification
$adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'figmaUsers01@gmail.com'; 

echo "--- Library System CLI Backup ---\n";
echo "Starting backup at " . date('Y-m-d H:i:s') . "\n";

$dbName = $_ENV['DB_DATABASE'] ?? 'Database';
$filename = "auto_backup_" . date('Ymd_His') . ".sql.gz";
$backupDir = __DIR__ . '/../backups/';

if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
$path = $backupDir . $filename;

$zp = gzopen($path, 'w9');
if (!$zp) {
    $errorMsg = "Error: Could not create backup file at $path";
    $mailService->sendEmail($adminEmail, "LibSys Backup FAILED", "System could not create backup file. Path: $path");
    die($errorMsg . "\n");
}

try {
    gzwrite($zp, "-- Full Database Backup (Automated CLI): $dbName\n");
    gzwrite($zp, "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n");

    $tables = $backupRepo->getAllTableNames();
    foreach ($tables as $table) {
        echo "Backing up table: $table...\n";
        $backupRepo->exportTableSql($table, function ($data) use ($zp) {
            gzwrite($zp, $data);
        });
    }
    gzclose($zp);

    $sizeBytes = filesize($path);
    $size = round($sizeBytes / 1024 / 1024, 2) . ' MB';

    // Log to Database
    $backupRepo->logBackup($filename, 'SQL.GZ', 'SYSTEM_AUTO', $size);
    $auditRepo->log(0, 'BACKUP', 'SYSTEM', $filename, "Automated 9PM backup completed ($size)");

    echo "Success! Backup saved: $filename ($size)\n";

    // --- Cleanup: Delete backups older than 30 days ---
    echo "Cleaning up old backups...\n";
    $days = 30;
    $files = glob($backupDir . "*.sql.gz");
    $now = time();
    $deletedCount = 0;

    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                unlink($file);
                echo "Deleted old backup: " . basename($file) . "\n";
                $deletedCount++;
            }
        }
    }

    // --- Send Email Notification ---
    $subject = "LibSys Automated Backup SUCCESS (" . date('Y-m-d') . ")";
    $body = "
        <div style='font-family: Arial, sans-serif; color: #333;'>
            <h2 style='color: #2c5282;'>Automated Backup Completed</h2>
            <p>Ang inyong daily backup ay matagumpay na naisagawa.</p>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Database:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$dbName</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Filename:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$filename</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Size:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$size</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Old Backups Deleted:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$deletedCount</td></tr>
            </table>
            <p style='margin-top: 20px; font-size: 0.8em; color: #718096;'>LibSys Automation System</p>
        </div>
    ";
    $mailService->sendEmail($adminEmail, $subject, $body);

} catch (\Throwable $e) {
    if (isset($zp)) gzclose($zp);
    if (file_exists($path)) unlink($path);
    $errorMsg = "Error during backup: " . $e->getMessage();
    echo $errorMsg . "\n";
    
    $mailService->sendEmail($adminEmail, "LibSys Backup FAILED", "An error occurred during the automated backup: " . $e->getMessage());
    exit(1);
}
