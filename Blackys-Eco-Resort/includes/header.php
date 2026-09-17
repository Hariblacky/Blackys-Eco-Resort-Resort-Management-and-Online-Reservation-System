<?php
/**
 * Shared <head> + navbar for every public page.
 * Expects (optional) variables set before include:
 *   $page_title        string
 *   $meta_description  string
 *   $page_key          string matching a data-page value (home, about, rooms, ...)
 */
require_once __DIR__ . '/functions.php';
$settings = get_settings();
$site_name = $settings['site_name'] ?? "Blacky's Eco Resort";
$page_title = isset($page_title) ? $page_title . ' | ' . $site_name : $site_name;
$meta_description = $meta_description ?? ($settings['meta_description'] ?? '');
$page_key = $page_key ?? '';
$canonical = BASE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($meta_description) ?>">
<meta name="keywords" content="<?= e($settings['meta_keywords'] ?? '') ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="robots" content="index, follow">

<!-- Open Graph / Social -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($meta_description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(resolve_image($settings['logo'] ?? '', 'settings')) ?>">
<meta name="twitter:card" content="summary_large_image">

<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌿</text></svg>">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

<!-- AOS (scroll animations) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<!-- Swiper -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css">
</head>
<body data-currency="<?= e($settings['currency'] ?? '$') ?>" data-page="<?= e($page_key) ?>">

<!-- LOADING SCREEN -->
<div class="loader" aria-hidden="true">
  <div class="loader-logo"><span class="leaf">🌿</span> <?= e($site_name) ?></div>
  <div class="loader-bar"></div>
  <div class="loader-text">Barefoot Luxury Loading</div>
</div>

<!-- NAVBAR -->
<header class="navbar">
  <div class="container nav-inner">
    <a href="index.php" class="brand">
      <span class="brand-mark">🌿</span>
      <span class="brand-text"><b><?= e($site_name) ?></b><span><?= e($settings['tagline'] ?? '') ?></span></span>
    </a>
    <nav class="nav-links" aria-label="Primary">
      <a href="index.php" class="<?= $page_key === 'home' ? 'active' : '' ?>">Home</a>
      <a href="about.php" class="<?= $page_key === 'about' ? 'active' : '' ?>">About</a>
      <a href="rooms.php" class="<?= $page_key === 'rooms' ? 'active' : '' ?>">Rooms</a>
      <a href="restaurant.php" class="<?= $page_key === 'restaurant' ? 'active' : '' ?>">Restaurant</a>
      <a href="activities.php" class="<?= $page_key === 'activities' ? 'active' : '' ?>">Activities</a>
      <a href="gallery.php" class="<?= $page_key === 'gallery' ? 'active' : '' ?>">Gallery</a>
      <a href="offers.php" class="<?= $page_key === 'offers' ? 'active' : '' ?>">Offers</a>
      <a href="contact.php" class="<?= $page_key === 'contact' ? 'active' : '' ?>">Contact</a>
    </nav>
    <div class="nav-cta">
      <button class="theme-toggle" aria-label="Toggle dark mode">
        <span class="icon-light">🌞</span><span class="icon-dark">🌙</span>
      </button>
      <a href="booking.php" class="btn btn-water">Book Now</a>
      <button class="hamburger" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
