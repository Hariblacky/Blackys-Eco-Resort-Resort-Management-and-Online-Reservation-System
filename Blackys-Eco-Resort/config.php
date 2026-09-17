<?php
/**
 * Blacky's Eco Resort — Global Configuration
 * Update the values below to match your hosting environment.
 */

// ---- Environment -----------------------------------------------------
// Set to false on your live production server.
define('APP_DEBUG', true);

// ---- Database ----------------------------------------------------------
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'blackys_eco_resort');
define('DB_USER', 'blackys_user');
define('DB_PASS', 'StrongPass123!');
define('DB_CHARSET', 'utf8mb4');

// ---- Site paths ----------------------------------------------------------
// BASE_URL must NOT have a trailing slash. Example: https://blackysecoresort.com
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('BASE_PATH', dirname(__FILE__));
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// ---- Mail (booking + contact notifications) -------------------------------
// PHP's built-in mail() is used by default (see includes/mailer.php).
// Most production hosts require SMTP — see includes/mailer.php for the
// PHPMailer/SMTP swap-in instructions.
define('MAIL_FROM_ADDRESS', 'no-reply@blackysecoresort.com');
define('MAIL_FROM_NAME', "Blacky's Eco Resort");
define('MAIL_ADMIN_ADDRESS', 'reservations@blackysecoresort.com');

// ---- Security ----------------------------------------------------------
// Change this to a long random string in production.
define('APP_SECRET', 'change-this-to-a-long-random-string-in-production');

// ---- Uploads -----------------------------------------------------------
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// ---- Error display -------------------------------------------------------
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

date_default_timezone_set('UTC');
