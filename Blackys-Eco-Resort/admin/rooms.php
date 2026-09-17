<?php
$page_title = 'Rooms & Villas';
$active = 'rooms';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!verify_csrf()) {
        flash('error', 'Session expired, please try again.');
    } else {
        $id = (int)$_POST['delete_id'];
        $stmt = $pdo->prepare('SELECT image FROM rooms WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if ($row = $stmt->fetch()) {
            delete_uploaded_file($row['image'], 'rooms');
            $pdo->prepare('DELETE FROM rooms WHERE id = :id')->execute(['id' => $id]);
            flash('success', 'Room deleted successfully.');
        }
    }
    redirect('rooms.php');
}

$rooms = $pdo->query('SELECT * FROM rooms ORDER BY id DESC')->fetchAll();
?>

<div class="panel">
  <div class="panel-head">
    <h2>All Rooms &amp; Villas (<?= count($rooms) ?>)</h2>
    <a href="room_form.php" class="btn btn-gold">+ Add New Room</a>
  </div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price/Night</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($rooms as $room): ?>
        <tr>
          <td><img class="table-thumb" src="<?= e(resolve_image($room['image'], 'rooms')) ?>" alt=""></td>
          <td><b><?= e($room['name']) ?></b></td>
          <td><?= e($room['category']) ?></td>
          <td><?= format_price($room['price_per_night']) ?></td>
          <td><?= (int)$room['capacity'] ?> guests</td>
          <td><span class="badge badge-<?= e($room['status']) ?>"><?= e($room['status']) ?></span></td>
          <td>
            <div class="row-actions">
              <a href="room_form.php?id=<?= (int)$room['id'] ?>" class="icon-btn" title="Edit">✏️</a>
              <form method="post" class="confirm-delete" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="delete_id" value="<?= (int)$room['id'] ?>">
                <button type="submit" class="icon-btn danger" title="Delete">🗑️</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rooms): ?><tr><td colspan="7" style="text-align:center; color:var(--text-soft);">No rooms yet — add your first one.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
