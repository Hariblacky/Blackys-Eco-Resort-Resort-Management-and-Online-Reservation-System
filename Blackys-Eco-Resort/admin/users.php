<?php
$page_title = 'Admin Users';
$active = 'users';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();
$me = current_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!verify_csrf()) {
        flash('error', 'Session expired.');
    } elseif ($me['role'] !== 'superadmin') {
        flash('error', 'Only a superadmin can remove admin accounts.');
    } else {
        $id = (int)$_POST['delete_id'];
        if ($id === (int)$me['id']) {
            flash('error', 'You cannot delete your own account while logged in.');
        } else {
            $pdo->prepare('DELETE FROM admins WHERE id = :id')->execute(['id' => $id]);
            flash('success', 'Admin user removed.');
        }
    }
    redirect('users.php');
}

$users = $pdo->query('SELECT * FROM admins ORDER BY id')->fetchAll();
?>
<div class="panel">
  <div class="panel-head"><h2>Admin Users (<?= count($users) ?>)</h2><a href="user_form.php" class="btn btn-gold">+ Add Admin User</a></div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><b><?= e($u['name']) ?></b></td>
          <td><?= e($u['username']) ?></td>
          <td><?= e($u['email']) ?></td>
          <td><span class="badge badge-active" style="text-transform:capitalize;"><?= e($u['role']) ?></span></td>
          <td><span class="badge badge-<?= $u['status'] === 'active' ? 'active' : 'inactive' ?>"><?= e($u['status']) ?></span></td>
          <td><?= $u['last_login'] ? e(date('M j, Y g:ia', strtotime($u['last_login']))) : 'Never' ?></td>
          <td><div class="row-actions">
            <a href="user_form.php?id=<?= (int)$u['id'] ?>" class="icon-btn" title="Edit">✏️</a>
            <?php if ($me['role'] === 'superadmin' && $u['id'] != $me['id']): ?>
            <form method="post" class="confirm-delete" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="delete_id" value="<?= (int)$u['id'] ?>"><button type="submit" class="icon-btn danger" title="Delete">🗑️</button></form>
            <?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
