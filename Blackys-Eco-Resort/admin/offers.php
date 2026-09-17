<?php
$page_title = 'Offers';
$active = 'offers';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verify_csrf()) {
        $id = (int)$_POST['delete_id'];
        $stmt = $pdo->prepare('SELECT image FROM offers WHERE id = :id'); $stmt->execute(['id' => $id]);
        if ($row = $stmt->fetch()) {
            delete_uploaded_file($row['image'], 'offers');
            $pdo->prepare('DELETE FROM offers WHERE id = :id')->execute(['id' => $id]);
            flash('success', 'Offer deleted.');
        }
    } else { flash('error', 'Session expired.'); }
    redirect('offers.php');
}

$offers = $pdo->query('SELECT * FROM offers ORDER BY id DESC')->fetchAll();
?>
<div class="panel">
  <div class="panel-head"><h2>Offers (<?= count($offers) ?>)</h2><a href="offers_form.php" class="btn btn-gold">+ Add Offer</a></div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Image</th><th>Title</th><th>Discount</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($offers as $offer): ?>
        <tr>
          <td><img class="table-thumb" src="<?= e(resolve_image($offer['image'], 'offers')) ?>" alt=""></td>
          <td><b><?= e($offer['title']) ?></b></td>
          <td><?= (int)$offer['discount_percent'] ?>%</td>
          <td><?= e(date('M j, Y', strtotime($offer['valid_to']))) ?></td>
          <td><span class="badge badge-<?= e($offer['status']) ?>"><?= e($offer['status']) ?></span></td>
          <td><div class="row-actions">
            <a href="offers_form.php?id=<?= (int)$offer['id'] ?>" class="icon-btn" title="Edit">✏️</a>
            <form method="post" class="confirm-delete" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="delete_id" value="<?= (int)$offer['id'] ?>"><button type="submit" class="icon-btn danger" title="Delete">🗑️</button></form>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$offers): ?><tr><td colspan="6" style="text-align:center; color:var(--text-soft);">No offers yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
