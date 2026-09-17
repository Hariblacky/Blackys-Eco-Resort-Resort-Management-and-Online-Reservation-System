<?php
$page_title = 'Gallery Image Form';
$active = 'gallery';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$id = (int)($_GET['id'] ?? 0);
$img = ['id' => 0, 'title' => '', 'category' => 'Resort', 'image' => '', 'status' => 'active'];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM gallery WHERE id = :id'); $stmt->execute(['id' => $id]);
    if ($found = $stmt->fetch()) $img = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Session expired — please try again.';
    $img['title'] = trim($_POST['title'] ?? '');
    $img['category'] = trim($_POST['category'] ?? 'Resort');
    $img['status'] = $_POST['status'] ?? 'active';

    if (mb_strlen($img['title']) < 2) $errors[] = 'Please enter a title.';

    $upload = handle_image_upload('image', 'gallery');
    if ($upload['ok']) {
        if ($img['image']) delete_uploaded_file($img['image'], 'gallery');
        $img['image'] = $upload['filename'];
    } elseif ($upload['error'] !== 'no_file') { $errors[] = $upload['error']; }
    elseif (!$id && empty($img['image'])) { $errors[] = 'Please upload an image.'; }

    if (!$errors) {
        if ($id) {
            $pdo->prepare("UPDATE gallery SET title=:title, category=:cat, image=:image, status=:status WHERE id=:id")
                ->execute(['title' => $img['title'], 'cat' => $img['category'], 'image' => $img['image'], 'status' => $img['status'], 'id' => $id]);
            flash('success', 'Image updated.');
        } else {
            $pdo->prepare("INSERT INTO gallery (title, category, image, status) VALUES (:title, :cat, :image, :status)")
                ->execute(['title' => $img['title'], 'cat' => $img['category'], 'image' => $img['image'], 'status' => $img['status']]);
            flash('success', 'Image added.');
        }
        redirect('gallery.php');
    }
}
?>
<div class="panel">
  <div class="panel-head"><h2><?= $id ? 'Edit Gallery Image' : 'Add Gallery Image' ?></h2><a href="gallery.php" class="btn btn-outline btn-sm">← Back to List</a></div>
  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e2) echo '<div>• ' . e($e2) . '</div>'; ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Title</label><input type="text" name="title" value="<?= e($img['title']) ?>" required></div>
      <div class="form-group"><label>Category</label><input type="text" name="category" value="<?= e($img['category']) ?>" placeholder="Rooms, Resort, Restaurant, Activities…" required></div>
      <div class="form-group full">
        <label>Image</label>
        <?php if ($img['image']): ?><div class="current-image"><img src="<?= e(resolve_image($img['image'], 'gallery')) ?>" alt=""><span class="form-hint">Current image — upload a new one to replace it.</span></div><?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
      </div>
      <div class="form-group"><label>Status</label><select name="status"><option value="active" <?= $img['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $img['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
    </div>
    <button type="submit" class="btn btn-gold"><?= $id ? 'Update Image' : 'Add Image' ?></button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
