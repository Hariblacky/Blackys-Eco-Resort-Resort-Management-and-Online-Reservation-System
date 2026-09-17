<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = get_pdo();
$page_title = 'Offers';
$page_key = 'offers';
$offers = $pdo->query("SELECT * FROM offers WHERE status='active' AND valid_to >= CURDATE() ORDER BY id DESC")->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="page-hero-bg" style="background-image:url('https://loremflickr.com/1600/900/tropical,sunset,resort');"></div>
  <div class="page-hero-content">
    <div class="breadcrumb">Home <span>/</span> Offers</div>
    <h1>Seasonal <span style="color:var(--gold-light); font-style:italic;">Offers</span></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <div class="eyebrow">Limited Time</div>
      <h2 class="section-title">Save On Your Next <em>Escape</em></h2>
    </div>

    <div class="card-grid">
      <?php foreach ($offers as $offer): ?>
      <div class="offer-card" data-aos="fade-up">
        <img src="<?= e(resolve_image($offer['image'], 'offers')) ?>" alt="<?= e($offer['title']) ?>">
        <span class="offer-badge"><?= (int)$offer['discount_percent'] ?>% OFF</span>
        <div class="offer-content">
          <span class="offer-valid">Valid until <?= e(date('M j, Y', strtotime($offer['valid_to']))) ?></span>
          <h3><?= e($offer['title']) ?></h3>
          <p><?= e($offer['description']) ?></p>
          <a href="booking.php" class="btn btn-gold btn-sm">Book This Offer</a>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$offers): ?>
        <p style="color:var(--text-soft); grid-column:1/-1; text-align:center;">No active offers right now — check back soon, or contact us for bespoke packages.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section section-alt text-center">
  <div class="container" data-aos="zoom-in">
    <div class="leaf-divider"><span></span><span class="leaf-icon">🌿</span><span></span></div>
    <h2 class="section-title">Questions About An <em>Offer?</em></h2>
    <a href="contact.php" class="btn btn-outline">Contact Reservations</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
