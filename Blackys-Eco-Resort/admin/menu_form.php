<?php
$page_title = 'Menu Item Form';
$active = 'menu';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$id = (int)($_GET['id'] ?? 0);
$item = ['id' => 0, 'category' => 'Starters', 'name' => '', 'description' => '', 'price' => '', 'image' => '', 'is_special' => 0, 'status' => 'active'];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = :id'); $stmt->execute(['id' => $id]);
    if ($found = $stmt->fetch()) $item = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Session expired — please try again.';
    $item['category'] = trim($_POST['category'] ?? '');
    $item['name'] = trim($_POST['name'] ?? '');
    $item['description'] = trim($_POST['description'] ?? '');
    $item['price'] = (float)($_POST['price'] ?? 0);
    $item['is_special'] = isset($_POST['is_special']) ? 1 : 0;
    $item['status'] = $_POST['status'] ?? 'active';

    if (mb_strlen($item['name']) < 2) $errors[] = 'Please enter a dish name.';
    if ($item['price'] <= 0) $errors[] = 'Please enter a valid price.';

    $upload = handle_image_upload('image', 'menu');
    if ($upload['ok']) {
        if ($item['image']) delete_uploaded_file($item['image'], 'menu');
        $item['image'] = $upload['filename'];
    } elseif ($upload['error'] !== 'no_file') { $errors[] = $upload['error']; }
    elseif (!$id && empty($item['image'])) { $errors[] = 'Please upload a dish image.'; }

    if (!$errors) {
        if ($id) {
            $pdo->prepare("UPDATE menu_items SET category=:cat, name=:name, description=:desc, price=:price, image=:image, is_special=:special, status=:status WHERE id=:id")
                ->execute(['cat' => $item['category'], 'name' => $item['name'], 'desc' => $item['description'], 'price' => $item['price'], 'image' => $item['image'], 'special' => $item['is_special'], 'status' => $item['status'], 'id' => $id]);
            flash('success', 'Menu item updated.');
        } else {
            $pdo->prepare("INSERT INTO menu_items (category, name, description, price, image, is_special, status) VALUES (:cat, :name, :desc, :price, :image, :special, :status)")
                ->execute(['cat' => $item['category'], 'name' => $item['name'], 'desc' => $item['description'], 'price' => $item['price'], 'image' => $item['image'], 'special' => $item['is_special'], 'status' => $item['status']]);
            flash('success', 'Menu item created.');
        }
        redirect('menu.php');
    }
}
?>
<div class="panel">
  <div class="panel-head"><h2><?= $id ? 'Edit Menu Item' : 'Add Menu Item' ?></h2><a href="menu.php" class="btn btn-outline btn-sm">← Back to List</a></div>
  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e2) echo '<div>• ' . e($e2) . '</div>'; ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Dish Name</label><input type="text" name="name" value="<?= e($item['name']) ?>" required></div>
      <div class="form-group"><label>Category</label><input type="text" name="category" value="<?= e($item['category']) ?>" placeholder="Starters, Main Course, Desserts, Drinks…" required></div>
      <div class="form-group"><label>Price</label><input type="number" step="0.01" min="0" name="price" value="<?= e((string)$item['price']) ?>" required></div>
      <div class="form-group"><label>Status</label><select name="status"><option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
      <div class="form-group full"><label>Description</label><textarea name="description" required><?= e($item['description']) ?></textarea></div>
      <div class="form-group full">
        <label>Dish Image</label>
        <?php if ($item['image']): ?><div class="current-image"><img src="<?= e(resolve_image($item['image'], 'menu')) ?>" alt=""><span class="form-hint">Current image — upload a new one to replace it.</span></div><?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
      </div>
      <div class="form-group"><div class="checkbox-row"><input type="checkbox" id="special" name="is_special" <?= $item['is_special'] ? 'checked' : '' ?>><label for="special" style="text-transform:none; font-size:.92rem;">Mark as Chef's Special</label></div></div>
    </div>
    <button type="submit" class="btn btn-gold"><?= $id ? 'Update Item' : 'Create Item' ?></button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
