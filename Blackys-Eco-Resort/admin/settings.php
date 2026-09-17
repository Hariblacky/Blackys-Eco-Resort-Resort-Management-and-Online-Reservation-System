<?php
$page_title = 'Site Settings';
$active = 'settings';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$settings = get_settings();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Session expired — please try again.';
    } else {
        $fields = [
            'site_name' => trim($_POST['site_name'] ?? ''),
            'tagline' => trim($_POST['tagline'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'facebook' => trim($_POST['facebook'] ?? ''),
            'instagram' => trim($_POST['instagram'] ?? ''),
            'twitter' => trim($_POST['twitter'] ?? ''),
            'latitude' => (float)($_POST['latitude'] ?? 0),
            'longitude' => (float)($_POST['longitude'] ?? 0),
            'currency' => trim($_POST['currency'] ?? '$'),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
        ];

        if (mb_strlen($fields['site_name']) < 2) $errors[] = 'Please enter a site name.';
        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid contact email.';

        $logo = $settings['logo'] ?? '';
        $upload = handle_image_upload('logo', 'settings');
        if ($upload['ok']) {
            if ($logo) delete_uploaded_file($logo, 'settings');
            $logo = $upload['filename'];
        } elseif ($upload['error'] !== 'no_file') {
            $errors[] = $upload['error'];
        }

        if (!$errors) {
            $fields['logo'] = $logo;
            $sql = "UPDATE settings SET site_name=:site_name, tagline=:tagline, phone=:phone, whatsapp_number=:whatsapp_number,
                    email=:email, address=:address, facebook=:facebook, instagram=:instagram, twitter=:twitter,
                    latitude=:latitude, longitude=:longitude, currency=:currency, meta_description=:meta_description,
                    meta_keywords=:meta_keywords, logo=:logo WHERE id = 1";
            $pdo->prepare($sql)->execute($fields);
            flash('success', 'Settings updated successfully.');
            redirect('settings.php');
        }
    }
    $settings = array_merge($settings, $fields ?? []);
}
?>
<div class="panel">
  <div class="panel-head"><h2>Website Settings</h2></div>
  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e2) echo '<div>• ' . e($e2) . '</div>'; ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Site Name</label><input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>" required></div>
      <div class="form-group"><label>Tagline</label><input type="text" name="tagline" value="<?= e($settings['tagline'] ?? '') ?>"></div>
      <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= e($settings['phone'] ?? '') ?>"></div>
      <div class="form-group"><label>WhatsApp Number (digits only, with country code)</label><input type="text" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '') ?>" placeholder="15552003000"></div>
      <div class="form-group"><label>Contact Email</label><input type="email" name="email" value="<?= e($settings['email'] ?? '') ?>" required></div>
      <div class="form-group"><label>Currency Symbol</label><input type="text" name="currency" value="<?= e($settings['currency'] ?? '$') ?>" maxlength="5"></div>
      <div class="form-group full"><label>Address</label><input type="text" name="address" value="<?= e($settings['address'] ?? '') ?>"></div>
      <div class="form-group"><label>Map Latitude</label><input type="text" name="latitude" value="<?= e((string)($settings['latitude'] ?? '')) ?>"></div>
      <div class="form-group"><label>Map Longitude</label><input type="text" name="longitude" value="<?= e((string)($settings['longitude'] ?? '')) ?>"></div>
      <div class="form-group"><label>Facebook URL</label><input type="text" name="facebook" value="<?= e($settings['facebook'] ?? '') ?>"></div>
      <div class="form-group"><label>Instagram URL</label><input type="text" name="instagram" value="<?= e($settings['instagram'] ?? '') ?>"></div>
      <div class="form-group"><label>X (Twitter) URL</label><input type="text" name="twitter" value="<?= e($settings['twitter'] ?? '') ?>"></div>
      <div class="form-group full"><label>SEO Meta Description</label><textarea name="meta_description"><?= e($settings['meta_description'] ?? '') ?></textarea></div>
      <div class="form-group full"><label>SEO Meta Keywords</label><input type="text" name="meta_keywords" value="<?= e($settings['meta_keywords'] ?? '') ?>"></div>
      <div class="form-group full">
        <label>Site Logo</label>
        <?php if (!empty($settings['logo'])): ?><div class="current-image"><img src="<?= e(resolve_image($settings['logo'], 'settings')) ?>" alt=""><span class="form-hint">Current logo — upload a new one to replace it.</span></div><?php endif; ?>
        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp">
      </div>
    </div>
    <button type="submit" class="btn btn-gold">Save Settings</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
