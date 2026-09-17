<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = get_pdo();
$page_title = 'Activities';
$page_key = 'activities';
$activities = $pdo->query("SELECT * FROM activities WHERE status='active' ORDER BY id")->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="page-hero-bg" style="background-image:url('https://loremflickr.com/1600/900/kayak,rainforest');"></div>
  <div class="page-hero-content">
    <div class="breadcrumb">Home <span>/</span> Activities</div>
    <h1>Rainforest &amp; Lagoon <span style="color:var(--gold-light); font-style:italic;">Activities</span></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <div class="eyebrow">Beyond The Villa</div>
      <h2 class="section-title">Guided <em>Experiences</em></h2>
      <p class="section-sub" style="margin-left:auto;margin-right:auto;">Every excursion is led by resident naturalists and local guides who know this reserve better than anyone.</p>
    </div>

    <div class="card-grid">
      <?php foreach ($activities as $act): ?>
      <article class="item-card" data-aos="fade-up">
        <div class="item-media">
          <img src="<?= e(resolve_image($act['image'], 'settings')) ?>" alt="<?= e($act['name']) ?>">
          <span class="item-tag"><?= e($act['duration']) ?></span>
        </div>
        <div class="item-body">
          <div class="item-top"><h3><?= e($act['name']) ?></h3><span class="item-price"><?= format_price($act['price']) ?></span></div>
          <p><?= e($act['description']) ?></p>
          <div class="item-foot">
            <a href="contact.php" class="btn btn-outline btn-sm">Enquire</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt text-center">
  <div class="container" data-aos="zoom-in">
    <div class="leaf-divider"><span></span><span class="leaf-icon">🦋</span><span></span></div>
    <h2 class="section-title">Build Your <em>Itinerary</em></h2>
    <p class="section-sub" style="margin-left:auto;margin-right:auto;">Let our concierge team curate a full day of experiences for your stay.</p>
    <a href="contact.php" class="btn btn-gold">Talk To Concierge</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
