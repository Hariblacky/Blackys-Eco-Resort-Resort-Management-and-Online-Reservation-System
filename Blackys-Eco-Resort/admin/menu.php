<?php
$page_title = 'Restaurant Menu';
$active = 'menu';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verify_csrf()) {
        $id = (int)$_POST['delete_id'];
        $stmt = $pdo->prepare('SELECT image FROM menu_items WHERE id = :id'); $stmt->execute(['id' => $id]);
        if ($row = $stmt->fetch()) {
            delete_uploaded_file($row['image'], 'menu');
            $pdo->prepare('DELETE FROM menu_items WHERE id = :id')->execute(['id' => $id]);
            flash('success', 'Menu item deleted.');
        }
    } else { flash('error', 'Session expired.'); }
    redirect('menu.php');
}

$items = $pdo->query('SELECT * FROM menu_items ORDER BY category, id DESC')->fetchAll();
?>
<div class="panel">
  <div class="panel-head"><h2>Menu Items (<?= count($items) ?>)</h2><a href="menu_form.php" class="btn btn-gold">+ Add Menu Item</a></div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Special</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
          <td><img class="table-thumb" src="<?= e(resolve_image($item['image'], 'menu')) ?>" alt=""></td>
          <td><b><?= e($item['name']) ?></b></td>
          <td><?= e($item['category']) ?></td>
          <td><?= format_price($item['price']) ?></td>
          <td><?= $item['is_special'] ? '⭐ Yes' : '—' ?></td>
          <td><span class="badge badge-<?= e($item['status']) ?>"><?= e($item['status']) ?></span></td>
          <td><div class="row-actions">
            <a href="menu_form.php?id=<?= (int)$item['id'] ?>" class="icon-btn" title="Edit">✏️</a>
            <form method="post" class="confirm-delete" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="delete_id" value="<?= (int)$item['id'] ?>"><button type="submit" class="icon-btn danger" title="Delete">🗑️</button></form>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?><tr><td colspan="7" style="text-align:center; color:var(--text-soft);">No menu items yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
