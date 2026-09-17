<?php
/**
 * Shared admin layout header. Expects $page_title and $active set before include.
 * Every protected admin page must require_once this AFTER requiring auth.php
 * and calling require_admin_login() (or require_role()).
 */
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();

$admin = current_admin();
$settings = get_settings();
$active = $active ?? '';
$page_title = ($page_title ?? 'Dashboard') . ' | Admin';

$navItems = [
    ['dashboard', 'dashboard.php', '📊', 'Dashboard'],
    ['bookings', 'bookings.php', '📅', 'Bookings'],
    ['rooms', 'rooms.php', '🏡', 'Rooms & Villas'],
    ['menu', 'menu.php', '🍽️', 'Restaurant Menu'],
    ['activities', 'activities.php', '🌿', 'Activities'],
    ['gallery', 'gallery.php', '🖼️', 'Gallery'],
    ['offers', 'offers.php', '🏷️', 'Offers'],
    ['users', 'users.php', '👤', 'Admin Users'],
    ['settings', 'settings.php', '⚙️', 'Site Settings'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= e($page_title) ?></title>
<link rel="stylesheet" href="assets/css/admin.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌿</text></svg>">
</head>
<body>
<div class="app-shell">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <span class="leaf">🌿</span>
      <div><b><?= e($settings['site_name'] ?? "Blacky's Eco Resort") ?></b><small>Admin Dashboard</small></div>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($navItems as [$key, $href, $icon, $label]): ?>
        <a href="<?= $href ?>" class="<?= $active === $key ? 'active' : '' ?>"><span class="nav-icon"><?= $icon ?></span> <?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">Logged in as <b style="color:#fff;"><?= e($admin['name'] ?? '') ?></b></div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div style="display:flex; align-items:center; gap:16px;">
        <button class="menu-toggle" id="menuToggle">☰</button>
        <h1><?= e(strtok($page_title, '|')) ?></h1>
      </div>
      <div class="topbar-right">
        <a href="../index.php" target="_blank" class="view-site-link">View Site ↗</a>
        <div class="admin-chip">
          <div class="avatar"><?= e(strtoupper(substr($admin['name'] ?? 'A', 0, 1))) ?></div>
          <span><?= e($admin['name'] ?? '') ?></span>
        </div>
        <a href="logout.php" class="logout-link">Log Out</a>
      </div>
    </header>
    <div class="content">
      <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
      <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
