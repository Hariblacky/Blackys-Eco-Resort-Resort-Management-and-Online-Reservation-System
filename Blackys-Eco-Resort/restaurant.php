<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = get_pdo();
$page_title = 'Restaurant';
$page_key = 'restaurant';
$items = $pdo->query("SELECT * FROM menu_items WHERE status='active' ORDER BY category, id")->fetchAll();
$categories = array_values(array_unique(array_column($items, 'category')));
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="page-hero-bg" style="background-image:url('https://loremflickr.com/1600/900/farmtotable,dining');"></div>
  <div class="page-hero-content">
    <div class="breadcrumb">Home <span>/</span> Restaurant</div>
    <h1>Farm-to-Table <span style="color:var(--gold-light); font-style:italic;">Dining</span></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <div class="eyebrow">Our Kitchen</div>
      <h2 class="section-title">Grown Here, <em>Served Here</em></h2>
      <p class="section-sub" style="margin-left:auto;margin-right:auto;">Our menu changes with our on-site permaculture farm — most produce travels less than a mile to your plate.</p>
    </div>

    <div class="filter-bar" data-target=".item-card" data-aos="fade-up">
      <button class="filter-btn active" data-filter="all">All</button>
      <?php foreach ($categories as $cat): ?>
        <button class="filter-btn" data-filter="<?= e($cat) ?>"><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="card-grid">
      <?php foreach ($items as $dish): ?>
      <article class="item-card" data-category="<?= e($dish['category']) ?>" data-aos="fade-up">
        <div class="item-media">
          <img src="<?= e(resolve_image($dish['image'], 'menu')) ?>" alt="<?= e($dish['name']) ?>">
          <span class="item-tag"><?= e($dish['category']) ?><?= $dish['is_special'] ? ' · Special' : '' ?></span>
        </div>
        <div class="item-body">
          <div class="item-top"><h3><?= e($dish['name']) ?></h3><span class="item-price"><?= format_price($dish['price']) ?></span></div>
          <p><?= e($dish['description']) ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt text-center">
  <div class="container" data-aos="zoom-in">
    <div class="leaf-divider"><span></span><span class="leaf-icon">🍽️</span><span></span></div>
    <h2 class="section-title">Reserve a <em>Table</em></h2>
    <p class="section-sub" style="margin-left:auto;margin-right:auto;">Dining is available to overnight guests and, space permitting, day visitors by reservation.</p>
    <a href="contact.php" class="btn btn-gold">Enquire Now</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
