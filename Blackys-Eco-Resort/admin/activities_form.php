<?php
$page_title = 'Activity Form';
$active = 'activities';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$id = (int)($_GET['id'] ?? 0);
$act = ['id' => 0, 'name' => '', 'description' => '', 'duration' => '2 Hours', 'price' => '', 'image' => '', 'status' => 'active'];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM activities WHERE id = :id'); $stmt->execute(['id' => $id]);
    if ($found = $stmt->fetch()) $act = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Session expired — please try again.';
    $act['name'] = trim($_POST['name'] ?? '');
    $act['description'] = trim($_POST['description'] ?? '');
    $act['duration'] = trim($_POST['duration'] ?? '');
    $act['price'] = (float)($_POST['price'] ?? 0);
    $act['status'] = $_POST['status'] ?? 'active';

    if (mb_strlen($act['name']) < 2) $errors[] = 'Please enter an activity name.';

    $upload = handle_image_upload('image', 'settings');
    if ($upload['ok']) {
        if ($act['image']) delete_uploaded_file($act['image'], 'settings');
        $act['image'] = $upload['filename'];
    } elseif ($upload['error'] !== 'no_file') { $errors[] = $upload['error']; }
    elseif (!$id && empty($act['image'])) { $errors[] = 'Please upload an image.'; }

    if (!$errors) {
        if ($id) {
            $pdo->prepare("UPDATE activities SET name=:name, description=:desc, duration=:dur, price=:price, image=:image, status=:status WHERE id=:id")
                ->execute(['name' => $act['name'], 'desc' => $act['description'], 'dur' => $act['duration'], 'price' => $act['price'], 'image' => $act['image'], 'status' => $act['status'], 'id' => $id]);
            flash('success', 'Activity updated.');
        } else {
            $pdo->prepare("INSERT INTO activities (name, description, duration, price, image, status) VALUES (:name, :desc, :dur, :price, :image, :status)")
                ->execute(['name' => $act['name'], 'desc' => $act['description'], 'dur' => $act['duration'], 'price' => $act['price'], 'image' => $act['image'], 'status' => $act['status']]);
            flash('success', 'Activity created.');
        }
        redirect('activities.php');
    }
}
?>
<div class="panel">
  <div class="panel-head"><h2><?= $id ? 'Edit Activity' : 'Add Activity' ?></h2><a href="activities.php" class="btn btn-outline btn-sm">← Back to List</a></div>
  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e2) echo '<div>• ' . e($e2) . '</div>'; ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Activity Name</label><input type="text" name="name" value="<?= e($act['name']) ?>" required></div>
      <div class="form-group"><label>Duration</label><input type="text" name="duration" value="<?= e($act['duration']) ?>" placeholder="2 Hours" required></div>
      <div class="form-group"><label>Price</label><input type="number" step="0.01" min="0" name="price" value="<?= e((string)$act['price']) ?>" required></div>
      <div class="form-group"><label>Status</label><select name="status"><option value="active" <?= $act['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $act['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
      <div class="form-group full"><label>Description</label><textarea name="description" required><?= e($act['description']) ?></textarea></div>
      <div class="form-group full">
        <label>Image</label>
        <?php if ($act['image']): ?><div class="current-image"><img src="<?= e(resolve_image($act['image'], 'settings')) ?>" alt=""><span class="form-hint">Current image — upload a new one to replace it.</span></div><?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
      </div>
    </div>
    <button type="submit" class="btn btn-gold"><?= $id ? 'Update Activity' : 'Create Activity' ?></button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
