<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (admin_logged_in()) {
    redirect('dashboard.php');
}

$error = null;
$expired = isset($_GET['expired']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Your session expired — please try again.';
    } else {
        $result = admin_attempt_login(trim($_POST['username'] ?? ''), $_POST['password'] ?? '');
        if ($result['ok']) {
            redirect('dashboard.php');
        }
        $error = $result['error'];
    }
}
$settings = get_settings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Login | <?= e($settings['site_name'] ?? "Blacky's Eco Resort") ?></title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">🌿</div>
    <h1><?= e($settings['site_name'] ?? "Blacky's Eco Resort") ?></h1>
    <p class="sub">Admin Dashboard Sign In</p>

    <?php if ($expired): ?><div class="login-error">Please sign in to continue.</div><?php endif; ?>
    <?php if ($error): ?><div class="login-error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" novalidate>
      <?= csrf_field() ?>
      <div class="login-field">
        <label>Username or Email</label>
        <input type="text" name="username" required autofocus autocomplete="username">
      </div>
      <div class="login-field">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="login-btn">Sign In</button>
    </form>
    <p class="login-hint">Default demo login — username: <b>admin</b> · password: <b>Admin@123</b><br>Change this immediately after first login.</p>
  </div>
</div>
</body>
</html>
