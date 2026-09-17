<?php
$page_title = 'Bookings';
$active = 'bookings';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        flash('error', 'Session expired, please try again.');
    } elseif (isset($_POST['update_status'])) {
        $id = (int)$_POST['booking_id'];
        $status = $_POST['status'];
        if (in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
            $pdo->prepare('UPDATE bookings SET status = :s WHERE id = :id')->execute(['s' => $status, 'id' => $id]);
            flash('success', 'Booking status updated.');
        }
    } elseif (isset($_POST['delete_id'])) {
        $pdo->prepare('DELETE FROM bookings WHERE id = :id')->execute(['id' => (int)$_POST['delete_id']]);
        flash('success', 'Booking deleted.');
    }
    redirect('bookings.php');
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT b.*, r.name AS room_name FROM bookings b LEFT JOIN rooms r ON r.id = b.room_id";
$params = [];
if ($statusFilter && in_array($statusFilter, ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
    $sql .= " WHERE b.status = :status";
    $params['status'] = $statusFilter;
}
$sql .= " ORDER BY b.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<div class="panel">
  <div class="panel-head">
    <h2>All Bookings (<?= count($bookings) ?>)</h2>
    <div style="display:flex; gap:8px;">
      <a href="bookings.php" class="btn btn-outline btn-sm <?= !$statusFilter ? 'btn-gold' : '' ?>">All</a>
      <a href="bookings.php?status=pending" class="btn btn-outline btn-sm">Pending</a>
      <a href="bookings.php?status=confirmed" class="btn btn-outline btn-sm">Confirmed</a>
      <a href="bookings.php?status=cancelled" class="btn btn-outline btn-sm">Cancelled</a>
      <a href="bookings.php?status=completed" class="btn btn-outline btn-sm">Completed</a>
    </div>
  </div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Ref</th><th>Guest</th><th>Room</th><th>Dates</th><th>Guests</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr>
          <td><b><?= e($b['booking_ref']) ?></b></td>
          <td><?= e($b['full_name']) ?><br><small style="color:var(--text-soft);"><?= e($b['email']) ?> · <?= e($b['phone']) ?></small></td>
          <td><?= e($b['room_name'] ?? '—') ?></td>
          <td><?= e($b['check_in']) ?> → <?= e($b['check_out']) ?><br><small style="color:var(--text-soft);"><?= (int)$b['nights'] ?> nights</small></td>
          <td><?= (int)$b['guests'] ?></td>
          <td><?= format_price($b['total_amount']) ?></td>
          <td>
            <form method="post" style="display:flex; gap:6px; align-items:center;">
              <?= csrf_field() ?>
              <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
              <select name="status" onchange="this.form.submit()" style="padding:6px 8px; border-radius:6px; border:1px solid var(--border); font-size:.78rem;">
                <?php foreach (['pending', 'confirmed', 'cancelled', 'completed'] as $s): ?>
                  <option value="<?= $s ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
              <input type="hidden" name="update_status" value="1">
            </form>
          </td>
          <td>
            <div class="row-actions">
              <a href="invoice.php?id=<?= (int)$b['id'] ?>" target="_blank" class="icon-btn" title="Download Invoice PDF">🧾</a>
              <form method="post" class="confirm-delete" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="delete_id" value="<?= (int)$b['id'] ?>">
                <button type="submit" class="icon-btn danger" title="Delete">🗑️</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?><tr><td colspan="8" style="text-align:center; color:var(--text-soft);">No bookings found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
