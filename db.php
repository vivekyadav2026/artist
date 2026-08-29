<?php
$config_file = __DIR__ . '/config.php';

if (!file_exists($config_file)) {
    // Redirect to installer if config is missing
    header("Location: install.php");
    exit;
}

require_once $config_file;

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage() . ". <br><br>If you haven't installed the database yet, please run <a href='install.php'>install.php</a>.");
}

// Helper function to get settings from settings table
function getSetting($key, $default = '') {
    global $pdo;
    static $settings = [];
    
    if (empty($settings)) {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            return $default;
        }
    }
    
    return isset($settings[$key]) ? $settings[$key] : $default;
}
