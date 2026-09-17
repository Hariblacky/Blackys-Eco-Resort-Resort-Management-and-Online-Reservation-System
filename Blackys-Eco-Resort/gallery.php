<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = get_pdo();
$page_title = 'Gallery';
$page_key = 'gallery';
$images = $pdo->query("SELECT * FROM gallery WHERE status='active' ORDER BY id DESC")->fetchAll();
$categories = array_values(array_unique(array_column($images, 'category')));
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="page-hero-bg" style="background-image:url('https://loremflickr.com/1600/900/aerial,rainforest,resort');"></div>
  <div class="page-hero-content">
    <div class="breadcrumb">Home <span>/</span> Gallery</div>
    <h1>Our <span style="color:var(--gold-light); font-style:italic;">Gallery</span></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="filter-bar" data-target=".masonry-item" data-aos="fade-up">
      <button class="filter-btn active" data-filter="all">All</button>
      <?php foreach ($categories as $cat): ?>
        <button class="filter-btn" data-filter="<?= e($cat) ?>"><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="masonry" data-aos="fade-up">
      <?php foreach ($images as $img): ?>
      <div class="masonry-item" data-category="<?= e($img['category']) ?>">
        <img src="<?= e(resolve_image($img['image'], 'gallery')) ?>" alt="<?= e($img['title']) ?>">
        <div class="masonry-overlay"><div><span><?= e($img['category']) ?></span><b><?= e($img['title']) ?></b></div></div>
        <div class="masonry-zoom">⤢</div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt text-center">
  <div class="container" data-aos="zoom-in">
    <div class="eyebrow">Come See For Yourself</div>
    <h2 class="section-title">Reserve Your <em>Escape</em></h2>
    <a href="booking.php" class="btn btn-gold">Book Now</a>
  </div>
</section>

<!-- LIGHTBOX -->
<div class="lightbox">
  <button class="lightbox-close" aria-label="Close">&times;</button>
  <button class="lightbox-prev" aria-label="Previous image">&#8592;</button>
  <div class="lightbox-inner"><img src="" alt=""><div class="lightbox-caption"></div></div>
  <button class="lightbox-next" aria-label="Next image">&#8594;</button>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
