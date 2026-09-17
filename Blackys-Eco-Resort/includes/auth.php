<?php
/**
 * Admin authentication helpers.
 * Password hashing uses PHP's native password_hash()/password_verify()
 * (bcrypt) — never store or compare plain-text passwords.
 */
require_once __DIR__ . '/functions.php';

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_SECONDS', 300);

function admin_attempt_login(string $username, string $password): array
{
    $pdo = get_pdo();

    // simple brute-force throttle stored in session
    $attempts = $_SESSION['login_attempts'] ?? 0;
    $lockedUntil = $_SESSION['login_locked_until'] ?? 0;
    if ($lockedUntil > time()) {
        $wait = $lockedUntil - time();
        return ['ok' => false, 'error' => "Too many attempts. Try again in {$wait} seconds."];
    }

    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :u1 OR email = :u2 LIMIT 1');
    $stmt->execute(['u1' => $username, 'u2' => $username]);
    $admin = $stmt->fetch();

    if (!$admin || $admin['status'] !== 'active' || !password_verify($password, $admin['password_hash'])) {
        $_SESSION['login_attempts'] = $attempts + 1;
        if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION['login_locked_until'] = time() + LOGIN_LOCKOUT_SECONDS;
            $_SESSION['login_attempts'] = 0;
        }
        return ['ok' => false, 'error' => 'Invalid username or password.'];
    }

    // successful login
    unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
    session_regenerate_id(true);
    $_SESSION['admin_id']       = $admin['id'];
    $_SESSION['admin_name']     = $admin['name'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_role']     = $admin['role'];

    $upd = $pdo->prepare('UPDATE admins SET last_login = NOW() WHERE id = :id');
    $upd->execute(['id' => $admin['id']]);

    // rehash transparently if the cost/algorithm has changed
    if (password_needs_rehash($admin['password_hash'], PASSWORD_BCRYPT)) {
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE admins SET password_hash = :h WHERE id = :id')
            ->execute(['h' => $newHash, 'id' => $admin['id']]);
    }

    return ['ok' => true];
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin_login(): void
{
    if (!admin_logged_in()) {
        redirect('index.php?expired=1');
    }
}

function require_role(string ...$roles): void
{
    require_admin_login();
    if (!in_array($_SESSION['admin_role'] ?? '', $roles, true)) {
        http_response_code(403);
        die('You do not have permission to access this page.');
    }
}

function current_admin(): ?array
{
    if (!admin_logged_in()) {
        return null;
    }
    static $admin = null;
    if ($admin === null) {
        $stmt = get_pdo()->prepare('SELECT id, name, username, email, role FROM admins WHERE id = :id');
        $stmt->execute(['id' => $_SESSION['admin_id']]);
        $admin = $stmt->fetch();
    }
    return $admin;
}

function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
