<?php
$page_title = 'Admin User Form';
$active = 'users';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$id = (int)($_GET['id'] ?? 0);
$user = ['id' => 0, 'name' => '', 'username' => '', 'email' => '', 'role' => 'manager', 'status' => 'active'];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = :id'); $stmt->execute(['id' => $id]);
    if ($found = $stmt->fetch()) $user = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Session expired — please try again.';
    $user['name'] = trim($_POST['name'] ?? '');
    $user['username'] = trim($_POST['username'] ?? '');
    $user['email'] = trim($_POST['email'] ?? '');
    $user['role'] = $_POST['role'] ?? 'manager';
    $user['status'] = $_POST['status'] ?? 'active';
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (mb_strlen($user['name']) < 2) $errors[] = 'Please enter a full name.';
    if (mb_strlen($user['username']) < 3) $errors[] = 'Username must be at least 3 characters.';
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (!$id && mb_strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password && $password !== $passwordConfirm) $errors[] = 'Passwords do not match.';
    if ($password && mb_strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';

    // uniqueness check
    $check = $pdo->prepare('SELECT id FROM admins WHERE (username = :u OR email = :e) AND id != :id');
    $check->execute(['u' => $user['username'], 'e' => $user['email'], 'id' => $id]);
    if ($check->fetch()) $errors[] = 'That username or email is already in use by another admin.';

    if (!$errors) {
        if ($id) {
            if ($password) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE admins SET name=:name, username=:username, email=:email, role=:role, status=:status, password_hash=:hash WHERE id=:id")
                    ->execute(['name' => $user['name'], 'username' => $user['username'], 'email' => $user['email'], 'role' => $user['role'], 'status' => $user['status'], 'hash' => $hash, 'id' => $id]);
            } else {
                $pdo->prepare("UPDATE admins SET name=:name, username=:username, email=:email, role=:role, status=:status WHERE id=:id")
                    ->execute(['name' => $user['name'], 'username' => $user['username'], 'email' => $user['email'], 'role' => $user['role'], 'status' => $user['status'], 'id' => $id]);
            }
            flash('success', 'Admin user updated.');
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO admins (name, username, email, password_hash, role, status) VALUES (:name, :username, :email, :hash, :role, :status)")
                ->execute(['name' => $user['name'], 'username' => $user['username'], 'email' => $user['email'], 'hash' => $hash, 'role' => $user['role'], 'status' => $user['status']]);
            flash('success', 'Admin user created.');
        }
        redirect('users.php');
    }
}
?>
<div class="panel">
  <div class="panel-head"><h2><?= $id ? 'Edit Admin User' : 'Add Admin User' ?></h2><a href="users.php" class="btn btn-outline btn-sm">← Back to List</a></div>
  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e2) echo '<div>• ' . e($e2) . '</div>'; ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= e($user['name']) ?>" required></div>
      <div class="form-group"><label>Username</label><input type="text" name="username" value="<?= e($user['username']) ?>" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>" required></div>
      <div class="form-group"><label>Role</label>
        <select name="role"><option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option><option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option></select>
      </div>
      <div class="form-group"><label>Password <?= $id ? '(leave blank to keep current)' : '' ?></label><input type="password" name="password" autocomplete="new-password"></div>
      <div class="form-group"><label>Confirm Password</label><input type="password" name="password_confirm" autocomplete="new-password"></div>
      <div class="form-group"><label>Status</label><select name="status"><option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="disabled" <?= $user['status'] === 'disabled' ? 'selected' : '' ?>>Disabled</option></select></div>
    </div>
    <p class="form-hint" style="margin-bottom:20px;">Passwords are stored using bcrypt hashing (PHP's <code>password_hash()</code>) — never in plain text.</p>
    <button type="submit" class="btn btn-gold"><?= $id ? 'Update User' : 'Create User' ?></button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
