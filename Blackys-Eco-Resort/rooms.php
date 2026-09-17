<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = get_pdo();
$page_title = 'Rooms & Villas';
$page_key = 'rooms';
$rooms = $pdo->query("SELECT * FROM rooms WHERE status='active' ORDER BY featured DESC, id")->fetchAll();
$categories = array_values(array_unique(array_column($rooms, 'category')));
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="page-hero-bg" style="background-image:url('https://loremflickr.com/1600/900/treehouse,villa');"></div>
  <div class="page-hero-content">
    <div class="breadcrumb">Home <span>/</span> Rooms</div>
    <h1>Rooms &amp; <span style="color:var(--gold-light); font-style:italic;">Villas</span></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="filter-bar" data-target=".item-card" data-aos="fade-up">
      <button class="filter-btn active" data-filter="all">All Villas</button>
      <?php foreach ($categories as $cat): ?>
        <button class="filter-btn" data-filter="<?= e($cat) ?>"><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="card-grid">
      <?php foreach ($rooms as $room): ?>
      <article class="item-card" id="<?= e($room['slug']) ?>" data-category="<?= e($room['category']) ?>" data-aos="fade-up">
        <div class="item-media">
          <img src="<?= e(resolve_image($room['image'], 'rooms')) ?>" alt="<?= e($room['name']) ?>">
          <span class="item-tag"><?= e($room['category']) ?></span>
        </div>
        <div class="item-body">
          <div class="item-top"><h3><?= e($room['name']) ?></h3><span class="item-price"><?= format_price($room['price_per_night']) ?>/night</span></div>
          <p><?= e($room['description']) ?></p>
          <div class="item-meta">
            <span>👥 <?= (int)$room['capacity'] ?> Guests</span>
            <span>📐 <?= (int)$room['size_sqm'] ?> m²</span>
            <span>🛏 <?= e($room['bed_type']) ?></span>
          </div>
          <div style="margin-top:14px;">
            <?php foreach (array_slice(array_filter(array_map('trim', explode(',', $room['amenities']))), 0, 4) as $am): ?>
              <span class="amenity-chip"><?= e($am) ?></span>
            <?php endforeach; ?>
          </div>
          <div class="item-foot">
            <a href="booking.php?room_id=<?= (int)$room['id'] ?>" class="btn btn-water btn-sm">Book This Villa</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt text-center">
  <div class="container" data-aos="zoom-in">
    <div class="eyebrow">Can't Decide?</div>
    <h2 class="section-title">Talk To Our <em>Reservations Team</em></h2>
    <p class="section-sub" style="margin-left:auto;margin-right:auto;">We're happy to help you match a villa to your trip.</p>
    <a href="contact.php" class="btn btn-outline">Contact Us</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
