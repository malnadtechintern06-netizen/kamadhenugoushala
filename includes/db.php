<?php
// includes/db.php - PDO Database Connection with auto-initialization check

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'kamadhenu_goushala');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Try connecting without DB_NAME to check if database exists, create if missing
        try {
            $rootDsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Reconnect
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Execute SQL schema file if present
            $sqlFile = __DIR__ . '/../database/database.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $pdo->exec($sql);
            }
            return $pdo;
        } catch (PDOException $ex) {
            error_log("Database Connection Failed: " . $ex->getMessage());
            die("<div style='font-family:sans-serif; text-align:center; padding:50px; background:#FFFDF7; color:#26351D;'>
                <h1 style='color:#315A1C;'>Kamadhenu Goushala</h1>
                <h2>Database Connection Error</h2>
                <p>Could not connect to MySQL server. Please ensure MySQL is running in XAMPP.</p>
                <p><small>" . htmlspecialchars($ex->getMessage()) . "</small></p>
            </div>");
        }
    }
}

// Global handle
$pdo = getDBConnection();
