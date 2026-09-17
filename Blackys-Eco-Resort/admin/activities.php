<?php
$page_title = 'Activities';
$active = 'activities';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verify_csrf()) {
        $id = (int)$_POST['delete_id'];
        $stmt = $pdo->prepare('SELECT image FROM activities WHERE id = :id'); $stmt->execute(['id' => $id]);
        if ($row = $stmt->fetch()) {
            delete_uploaded_file($row['image'], 'settings');
            $pdo->prepare('DELETE FROM activities WHERE id = :id')->execute(['id' => $id]);
            flash('success', 'Activity deleted.');
        }
    } else { flash('error', 'Session expired.'); }
    redirect('activities.php');
}

$activities = $pdo->query('SELECT * FROM activities ORDER BY id DESC')->fetchAll();
?>
<div class="panel">
  <div class="panel-head"><h2>Activities (<?= count($activities) ?>)</h2><a href="activities_form.php" class="btn btn-gold">+ Add Activity</a></div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Image</th><th>Name</th><th>Duration</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($activities as $act): ?>
        <tr>
          <td><img class="table-thumb" src="<?= e(resolve_image($act['image'], 'settings')) ?>" alt=""></td>
          <td><b><?= e($act['name']) ?></b></td>
          <td><?= e($act['duration']) ?></td>
          <td><?= format_price($act['price']) ?></td>
          <td><span class="badge badge-<?= e($act['status']) ?>"><?= e($act['status']) ?></span></td>
          <td><div class="row-actions">
            <a href="activities_form.php?id=<?= (int)$act['id'] ?>" class="icon-btn" title="Edit">✏️</a>
            <form method="post" class="confirm-delete" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="delete_id" value="<?= (int)$act['id'] ?>"><button type="submit" class="icon-btn danger" title="Delete">🗑️</button></form>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$activities): ?><tr><td colspan="6" style="text-align:center; color:var(--text-soft);">No activities yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
