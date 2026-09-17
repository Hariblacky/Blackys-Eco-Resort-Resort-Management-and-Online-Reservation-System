<?php
$page_title = 'Offer Form';
$active = 'offers';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$id = (int)($_GET['id'] ?? 0);
$offer = ['id' => 0, 'title' => '', 'description' => '', 'discount_percent' => '', 'image' => '', 'valid_from' => date('Y-m-d'), 'valid_to' => date('Y-m-d', strtotime('+3 months')), 'status' => 'active'];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM offers WHERE id = :id'); $stmt->execute(['id' => $id]);
    if ($found = $stmt->fetch()) $offer = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Session expired — please try again.';
    $offer['title'] = trim($_POST['title'] ?? '');
    $offer['description'] = trim($_POST['description'] ?? '');
    $offer['discount_percent'] = (float)($_POST['discount_percent'] ?? 0);
    $offer['valid_from'] = $_POST['valid_from'] ?? date('Y-m-d');
    $offer['valid_to'] = $_POST['valid_to'] ?? date('Y-m-d');
    $offer['status'] = $_POST['status'] ?? 'active';

    if (mb_strlen($offer['title']) < 2) $errors[] = 'Please enter an offer title.';
    if ($offer['discount_percent'] <= 0 || $offer['discount_percent'] > 100) $errors[] = 'Discount must be between 1 and 100.';
    if (strtotime($offer['valid_to']) < strtotime($offer['valid_from'])) $errors[] = 'Valid-to date must be after valid-from date.';

    $upload = handle_image_upload('image', 'offers');
    if ($upload['ok']) {
        if ($offer['image']) delete_uploaded_file($offer['image'], 'offers');
        $offer['image'] = $upload['filename'];
    } elseif ($upload['error'] !== 'no_file') { $errors[] = $upload['error']; }
    elseif (!$id && empty($offer['image'])) { $errors[] = 'Please upload an offer image.'; }

    if (!$errors) {
        if ($id) {
            $pdo->prepare("UPDATE offers SET title=:title, description=:desc, discount_percent=:disc, image=:image, valid_from=:vf, valid_to=:vt, status=:status WHERE id=:id")
                ->execute(['title' => $offer['title'], 'desc' => $offer['description'], 'disc' => $offer['discount_percent'], 'image' => $offer['image'], 'vf' => $offer['valid_from'], 'vt' => $offer['valid_to'], 'status' => $offer['status'], 'id' => $id]);
            flash('success', 'Offer updated.');
        } else {
            $pdo->prepare("INSERT INTO offers (title, description, discount_percent, image, valid_from, valid_to, status) VALUES (:title, :desc, :disc, :image, :vf, :vt, :status)")
                ->execute(['title' => $offer['title'], 'desc' => $offer['description'], 'disc' => $offer['discount_percent'], 'image' => $offer['image'], 'vf' => $offer['valid_from'], 'vt' => $offer['valid_to'], 'status' => $offer['status']]);
            flash('success', 'Offer created.');
        }
        redirect('offers.php');
    }
}
?>
<div class="panel">
  <div class="panel-head"><h2><?= $id ? 'Edit Offer' : 'Add Offer' ?></h2><a href="offers.php" class="btn btn-outline btn-sm">← Back to List</a></div>
  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e2) echo '<div>• ' . e($e2) . '</div>'; ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Offer Title</label><input type="text" name="title" value="<?= e($offer['title']) ?>" required></div>
      <div class="form-group"><label>Discount (%)</label><input type="number" step="0.01" min="1" max="100" name="discount_percent" value="<?= e((string)$offer['discount_percent']) ?>" required></div>
      <div class="form-group"><label>Valid From</label><input type="date" name="valid_from" value="<?= e($offer['valid_from']) ?>" required></div>
      <div class="form-group"><label>Valid To</label><input type="date" name="valid_to" value="<?= e($offer['valid_to']) ?>" required></div>
      <div class="form-group full"><label>Description</label><textarea name="description" required><?= e($offer['description']) ?></textarea></div>
      <div class="form-group full">
        <label>Offer Image</label>
        <?php if ($offer['image']): ?><div class="current-image"><img src="<?= e(resolve_image($offer['image'], 'offers')) ?>" alt=""><span class="form-hint">Current image — upload a new one to replace it.</span></div><?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
      </div>
      <div class="form-group"><label>Status</label><select name="status"><option value="active" <?= $offer['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $offer['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
    </div>
    <button type="submit" class="btn btn-gold"><?= $id ? 'Update Offer' : 'Create Offer' ?></button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
