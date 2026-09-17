<?php
$page_title = 'Room Form';
$active = 'rooms';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$id = (int)($_GET['id'] ?? 0);
$room = ['id' => 0, 'name' => '', 'category' => 'Villa', 'description' => '', 'price_per_night' => '', 'capacity' => 2, 'size_sqm' => 40, 'bed_type' => 'King Bed', 'amenities' => '', 'image' => '', 'featured' => 0, 'status' => 'active'];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();
    if ($found) $room = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Session expired — please try again.';
    }
    $room['name'] = trim($_POST['name'] ?? '');
    $room['category'] = trim($_POST['category'] ?? 'Villa');
    $room['description'] = trim($_POST['description'] ?? '');
    $room['price_per_night'] = (float)($_POST['price_per_night'] ?? 0);
    $room['capacity'] = (int)($_POST['capacity'] ?? 1);
    $room['size_sqm'] = (int)($_POST['size_sqm'] ?? 0);
    $room['bed_type'] = trim($_POST['bed_type'] ?? '');
    $room['amenities'] = trim($_POST['amenities'] ?? '');
    $room['featured'] = isset($_POST['featured']) ? 1 : 0;
    $room['status'] = $_POST['status'] ?? 'active';

    if (mb_strlen($room['name']) < 2) $errors[] = 'Please enter a room name.';
    if ($room['price_per_night'] <= 0) $errors[] = 'Please enter a valid price.';

    $upload = handle_image_upload('image', 'rooms');
    if ($upload['ok']) {
        if ($room['image']) delete_uploaded_file($room['image'], 'rooms');
        $room['image'] = $upload['filename'];
    } elseif ($upload['error'] !== 'no_file') {
        $errors[] = $upload['error'];
    } elseif (!$id && empty($room['image'])) {
        $errors[] = 'Please upload a room image.';
    }

    if (!$errors) {
        $slug = slugify($room['name']);
        if ($id) {
            $stmt = $pdo->prepare("UPDATE rooms SET name=:name, slug=:slug, category=:category, description=:description,
                price_per_night=:price, capacity=:capacity, size_sqm=:size, bed_type=:bed, amenities=:amenities,
                image=:image, featured=:featured, status=:status WHERE id=:id");
            $stmt->execute(['name' => $room['name'], 'slug' => $slug, 'category' => $room['category'], 'description' => $room['description'],
                'price' => $room['price_per_night'], 'capacity' => $room['capacity'], 'size' => $room['size_sqm'], 'bed' => $room['bed_type'],
                'amenities' => $room['amenities'], 'image' => $room['image'], 'featured' => $room['featured'], 'status' => $room['status'], 'id' => $id]);
            flash('success', 'Room updated successfully.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO rooms (name, slug, category, description, price_per_night, capacity, size_sqm, bed_type, amenities, image, featured, status)
                VALUES (:name, :slug, :category, :description, :price, :capacity, :size, :bed, :amenities, :image, :featured, :status)");
            $stmt->execute(['name' => $room['name'], 'slug' => $slug, 'category' => $room['category'], 'description' => $room['description'],
                'price' => $room['price_per_night'], 'capacity' => $room['capacity'], 'size' => $room['size_sqm'], 'bed' => $room['bed_type'],
                'amenities' => $room['amenities'], 'image' => $room['image'], 'featured' => $room['featured'], 'status' => $room['status']]);
            flash('success', 'Room created successfully.');
        }
        redirect('rooms.php');
    }
}
?>

<div class="panel">
  <div class="panel-head"><h2><?= $id ? 'Edit Room' : 'Add New Room' ?></h2><a href="rooms.php" class="btn btn-outline btn-sm">← Back to List</a></div>

  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e2) echo '<div>• ' . e($e2) . '</div>'; ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Room / Villa Name</label><input type="text" name="name" value="<?= e($room['name']) ?>" required></div>
      <div class="form-group"><label>Category</label><input type="text" name="category" value="<?= e($room['category']) ?>" placeholder="Villa, Suite, Bungalow…" required></div>
      <div class="form-group"><label>Price per Night</label><input type="number" step="0.01" min="0" name="price_per_night" value="<?= e((string)$room['price_per_night']) ?>" required></div>
      <div class="form-group"><label>Capacity (Guests)</label><input type="number" min="1" name="capacity" value="<?= (int)$room['capacity'] ?>" required></div>
      <div class="form-group"><label>Size (m²)</label><input type="number" min="1" name="size_sqm" value="<?= (int)$room['size_sqm'] ?>" required></div>
      <div class="form-group"><label>Bed Type</label><input type="text" name="bed_type" value="<?= e($room['bed_type']) ?>" required></div>
      <div class="form-group full"><label>Description</label><textarea name="description" required><?= e($room['description']) ?></textarea></div>
      <div class="form-group full"><label>Amenities (comma separated)</label><input type="text" name="amenities" value="<?= e($room['amenities']) ?>" placeholder="Private Pool, Free WiFi, Ocean View"></div>
      <div class="form-group full">
        <label>Room Image</label>
        <?php if ($room['image']): ?><div class="current-image"><img src="<?= e(resolve_image($room['image'], 'rooms')) ?>" alt=""><span class="form-hint">Current image — upload a new one to replace it.</span></div><?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <p class="form-hint">JPG, PNG or WEBP, up to 5MB.</p>
      </div>
      <div class="form-group"><label>Status</label>
        <select name="status"><option value="active" <?= $room['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $room['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select>
      </div>
      <div class="form-group">
        <label>&nbsp;</label>
        <div class="checkbox-row"><input type="checkbox" id="featured" name="featured" <?= $room['featured'] ? 'checked' : '' ?>><label for="featured" style="text-transform:none; font-size:.92rem;">Feature on homepage</label></div>
      </div>
    </div>
    <button type="submit" class="btn btn-gold"><?= $id ? 'Update Room' : 'Create Room' ?></button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
