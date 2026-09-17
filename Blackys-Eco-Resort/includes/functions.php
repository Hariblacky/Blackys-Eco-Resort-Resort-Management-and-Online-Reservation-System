<?php
/**
 * Shared helper functions.
 */
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------
// General utilities
// ---------------------------------------------------------------------
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return $text ?: 'item';
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

// ---------------------------------------------------------------------
// CSRF protection
// ---------------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// ---------------------------------------------------------------------
// Settings (single-row settings table, cached per request)
// ---------------------------------------------------------------------
function get_settings(): array
{
    static $settings = null;
    if ($settings === null) {
        $pdo = get_pdo();
        $stmt = $pdo->query('SELECT * FROM settings WHERE id = 1 LIMIT 1');
        $settings = $stmt->fetch() ?: [];
    }
    return $settings;
}

function setting(string $key, $default = '')
{
    $settings = get_settings();
    return $settings[$key] ?? $default;
}

function format_price($amount): string
{
    return setting('currency', '$') . number_format((float)$amount, 2);
}

// ---------------------------------------------------------------------
// Image upload handling
// ---------------------------------------------------------------------
/**
 * Handles a single <input type="file"> upload.
 * @return array{ok: bool, filename?: string, error?: string}
 */
function handle_image_upload(string $fieldName, string $subfolder): array
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'error' => 'no_file'];
    }
    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload error code ' . $file['error']];
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['ok' => false, 'error' => 'File exceeds the 5MB size limit.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) {
        return ['ok' => false, 'error' => 'Only JPG, PNG or WEBP images are allowed.'];
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => 'jpg',
    };

    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    $destDir = UPLOAD_PATH . '/' . $subfolder;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $destPath = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['ok' => false, 'error' => 'Could not save the uploaded file.'];
    }

    return ['ok' => true, 'filename' => $filename];
}

/** Resolves a stored image value (filename OR full URL) to a displayable URL. */
function resolve_image(?string $value, string $subfolder): string
{
    if (empty($value)) {
        return 'https://loremflickr.com/700/500/nature,resort';
    }
    if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
        return $value;
    }
    return UPLOAD_URL . '/' . $subfolder . '/' . $value;
}

function delete_uploaded_file(?string $filename, string $subfolder): void
{
    if ($filename && !str_starts_with($filename, 'http')) {
        $path = UPLOAD_PATH . '/' . $subfolder . '/' . $filename;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

// ---------------------------------------------------------------------
// Booking helpers
// ---------------------------------------------------------------------
function generate_booking_ref(): string
{
    return 'BER-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}

function nights_between(string $checkIn, string $checkOut): int
{
    $in = new DateTime($checkIn);
    $out = new DateTime($checkOut);
    $diff = $out->diff($in)->days;
    return max(1, $diff);
}
