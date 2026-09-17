<?php
$page_title = 'Gallery';
$active = 'gallery';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verify_csrf()) {
        $id = (int)$_POST['delete_id'];
        $stmt = $pdo->prepare('SELECT image FROM gallery WHERE id = :id'); $stmt->execute(['id' => $id]);
        if ($row = $stmt->fetch()) {
            delete_uploaded_file($row['image'], 'gallery');
            $pdo->prepare('DELETE FROM gallery WHERE id = :id')->execute(['id' => $id]);
            flash('success', 'Image deleted.');
        }
    } else { flash('error', 'Session expired.'); }
    redirect('gallery.php');
}

$images = $pdo->query('SELECT * FROM gallery ORDER BY id DESC')->fetchAll();
?>
<div class="panel">
  <div class="panel-head"><h2>Gallery Images (<?= count($images) ?>)</h2><a href="gallery_form.php" class="btn btn-gold">+ Add Image</a></div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Image</th><th>Title</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($images as $img): ?>
        <tr>
          <td><img class="table-thumb" src="<?= e(resolve_image($img['image'], 'gallery')) ?>" alt=""></td>
          <td><b><?= e($img['title']) ?></b></td>
          <td><?= e($img['category']) ?></td>
          <td><span class="badge badge-<?= e($img['status']) ?>"><?= e($img['status']) ?></span></td>
          <td><div class="row-actions">
            <a href="gallery_form.php?id=<?= (int)$img['id'] ?>" class="icon-btn" title="Edit">✏️</a>
            <form method="post" class="confirm-delete" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="delete_id" value="<?= (int)$img['id'] ?>"><button type="submit" class="icon-btn danger" title="Delete">🗑️</button></form>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$images): ?><tr><td colspan="5" style="text-align:center; color:var(--text-soft);">No gallery images yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
