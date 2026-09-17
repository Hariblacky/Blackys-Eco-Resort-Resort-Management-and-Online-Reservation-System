<?php
/**
 * Database connection (PDO, MySQL).
 * Include this file wherever database access is needed:
 *   require_once __DIR__ . '/includes/db.php';
 * A ready-to-use PDO instance is available as $pdo.
 */

require_once __DIR__ . '/../config.php';

function get_pdo(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
            }
            die('We are unable to connect right now. Please try again shortly.');
        }
    }
    return $pdo;
}

$pdo = get_pdo();
