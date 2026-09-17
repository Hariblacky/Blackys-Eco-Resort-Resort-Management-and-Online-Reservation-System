<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = get_pdo();
$page_title = 'Barefoot Luxury in the Rainforest';
$page_key = 'home';

$featuredRooms = $pdo->query("SELECT * FROM rooms WHERE status='active' AND featured=1 ORDER BY id LIMIT 6")->fetchAll();
if (!$featuredRooms) { $featuredRooms = $pdo->query("SELECT * FROM rooms WHERE status='active' ORDER BY id LIMIT 6")->fetchAll(); }
$specials = $pdo->query("SELECT * FROM menu_items WHERE status='active' AND is_special=1 ORDER BY id LIMIT 6")->fetchAll();
$topOffer = $pdo->query("SELECT * FROM offers WHERE status='active' ORDER BY id LIMIT 1")->fetch();

include __DIR__ . '/includes/header.php';
?>

<!-- VIDEO HERO -->
<section class="hero">
  <div class="hero-media">
    <video autoplay muted loop playsinline poster="https://loremflickr.com/1920/1080/rainforest,lagoon">
      <source src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4" type="video/mp4">
    </video>
  </div>
  <div id="hero-canvas"></div>
  <canvas class="leaves-canvas"></canvas>
  <div class="hero-content">
    <div class="hero-eyebrow">🌿 A Rainforest &amp; Lagoon Retreat</div>
    <h1 class="hero-title">Barefoot <span class="accent">Luxury.</span><br>Untouched <span class="accent">Nature.</span></h1>
    <p class="hero-desc">Treehouse villas, overwater suites and farm-to-table dining — woven into a living rainforest reserve, powered by the sun.</p>
    <div class="hero-actions">
      <a href="rooms.php" class="btn btn-gold">Explore Rooms</a>
      <a href="booking.php" class="btn btn-outline" style="color:#fff; border-color:rgba(255,255,255,.4);">Book a Stay</a>
    </div>
  </div>
  <div class="hero-scroll"><span>Scroll</span><span class="line"></span></div>
</section>

<!-- INTRO / STORY -->
<section class="section">
  <div class="container split">
    <div data-aos="fade-right">
      <div class="split-img"><img src="https://loremflickr.com/900/1100/eco,resort" alt="Blacky's Eco Resort nestled in the rainforest canopy"></div>
    </div>
    <div data-aos="fade-left">
      <div class="eyebrow">Our Philosophy</div>
      <h2 class="section-title">Where Luxury Meets <em>Conservation</em></h2>
      <p style="color:var(--text-soft); margin-bottom:22px;">Built without felling a single mature tree, Blacky's Eco Resort proves that barefoot luxury and genuine sustainability can share the same address. Solar power, greywater recycling and a zero-single-use-plastic kitchen run quietly behind every indulgent detail.</p>
      <div class="stat-row">
        <div><div class="stat-num" data-counter="42" data-suffix="">0</div><div class="stat-label">Private Villas</div></div>
        <div><div class="stat-num" data-counter="120" data-suffix="+">0</div><div class="stat-label">Acres Protected</div></div>
        <div><div class="stat-num" data-counter="98" data-suffix="%">0</div><div class="stat-label">Solar Powered</div></div>
      </div>
      <a href="about.php" class="btn btn-outline" style="margin-top:34px;">Our Story</a>
    </div>
  </div>
</section>

<!-- FEATURED ROOMS SWIPER -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <div class="leaf-divider"><span></span><span class="leaf-icon">🌴</span><span></span></div>
      <div class="eyebrow">Stay With Us</div>
      <h2 class="section-title">Featured <em>Villas</em></h2>
      <p class="section-sub" style="margin-left:auto;margin-right:auto;">A handful of our most-loved retreats, each built in harmony with the landscape around it.</p>
    </div>
    <div class="swiper rooms-swiper" data-aos="fade-up">
      <div class="swiper-wrapper">
        <?php foreach ($featuredRooms as $room): ?>
        <div class="swiper-slide">
          <article class="item-card">
            <div class="item-media">
              <img src="<?= e(resolve_image($room['image'], 'rooms')) ?>" alt="<?= e($room['name']) ?>">
              <span class="item-tag"><?= e($room['category']) ?></span>
            </div>
            <div class="item-body">
              <div class="item-top"><h3><?= e($room['name']) ?></h3><span class="item-price"><?= format_price($room['price_per_night']) ?>/night</span></div>
              <p><?= e(mb_strimwidth($room['description'], 0, 100, '…')) ?></p>
              <div class="item-meta">
                <span>👥 <?= (int)$room['capacity'] ?> Guests</span>
                <span>📐 <?= (int)$room['size_sqm'] ?> m²</span>
              </div>
              <div class="item-foot">
                <a href="rooms.php#<?= e($room['slug']) ?>" class="btn btn-outline btn-sm">Details</a>
                <a href="booking.php?room_id=<?= (int)$room['id'] ?>" class="btn btn-water btn-sm">Book</a>
              </div>
            </div>
          </article>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
  </div>
</section>

<!-- WATER RIPPLE LAGOON SECTION -->
<section class="section ripple-section" style="min-height:70vh; display:flex; align-items:center;">
  <div class="ripple-section-bg page-hero-bg" style="background-image:url('https://loremflickr.com/1920/1000/lagoon,resort');"></div>
  <canvas id="water-ripple-canvas"></canvas>
  <div class="container text-center" data-aos="zoom-in">
    <div class="eyebrow" style="color:var(--gold-light);">The Lagoon</div>
    <h2 class="section-title" style="color:#fff;">Still Waters, <em>Endless Calm</em></h2>
    <p class="section-sub" style="color:rgba(255,255,255,.75); margin-left:auto; margin-right:auto;">Our private lagoon mirrors the canopy above it — swim at sunrise, kayak at dusk, or simply watch the ripples settle.</p>
    <a href="activities.php" class="btn btn-gold">Discover Activities</a>
  </div>
  <div class="ripple-hint">✦ move or tap the water ✦</div>
</section>

<!-- TODAY'S SPECIALS -->
<section class="section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <div class="eyebrow">From Our Kitchen</div>
      <h2 class="section-title">Chef's <em>Specials</em></h2>
    </div>
    <div class="swiper menu-swiper" data-aos="fade-up">
      <div class="swiper-wrapper">
        <?php foreach ($specials as $dish): ?>
        <div class="swiper-slide">
          <article class="item-card">
            <div class="item-media"><img src="<?= e(resolve_image($dish['image'], 'menu')) ?>" alt="<?= e($dish['name']) ?>"><span class="item-tag">Chef's Pick</span></div>
            <div class="item-body">
              <div class="item-top"><h3><?= e($dish['name']) ?></h3><span class="item-price"><?= format_price($dish['price']) ?></span></div>
              <p><?= e($dish['description']) ?></p>
            </div>
          </article>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
    <div class="text-center" style="margin-top:20px;" data-aos="fade-up">
      <a href="restaurant.php" class="btn btn-outline">View Full Menu</a>
    </div>
  </div>
</section>

<?php if ($topOffer): ?>
<!-- OFFER TEASER -->
<section class="section section-alt">
  <div class="container">
    <div class="offer-card" style="max-width:900px; margin:0 auto;" data-aos="fade-up">
      <img src="<?= e(resolve_image($topOffer['image'], 'offers')) ?>" alt="<?= e($topOffer['title']) ?>">
      <span class="offer-badge"><?= (int)$topOffer['discount_percent'] ?>% OFF</span>
      <div class="offer-content">
        <h3><?= e($topOffer['title']) ?></h3>
        <p><?= e($topOffer['description']) ?></p>
        <a href="offers.php" class="btn btn-gold btn-sm">View All Offers</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- TESTIMONIALS -->
<section class="section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <div class="eyebrow">Guest Voices</div>
      <h2 class="section-title">Stories From The <em>Canopy</em></h2>
    </div>
    <div class="swiper testi-swiper" data-aos="fade-up">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <div class="testi-card">
            <div class="testi-stars">★★★★★</div>
            <p>"We watched the sunrise from our treehouse deck with coffee grown a hillside away. Nothing about this place feels ordinary."</p>
            <div class="testi-person"><img src="https://loremflickr.com/100/100/portrait,woman" alt="Sofia Renz"><div><b>Sofia Renz</b><span>Canopy Treehouse Villa</span></div></div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="testi-card">
            <div class="testi-stars">★★★★★</div>
            <p>"The overwater villa's glass floor panel had our kids mesmerized for hours. Barefoot luxury, exactly as promised."</p>
            <div class="testi-person"><img src="https://loremflickr.com/100/100/portrait,man" alt="Daniel Osei"><div><b>Daniel Osei</b><span>Lagoon Water Villa</span></div></div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="testi-card">
            <div class="testi-stars">★★★★★</div>
            <p>"Farm-to-table isn't a slogan here — we picked the cacao ourselves the morning before dessert. Unforgettable."</p>
            <div class="testi-person"><img src="https://loremflickr.com/100/100/portrait,couple" alt="Priya &amp; Marcus"><div><b>Priya &amp; Marcus</b><span>Mangrove Garden Suite</span></div></div>
          </div>
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section section-alt" style="text-align:center;">
  <div class="container" data-aos="zoom-in">
    <div class="leaf-divider"><span></span><span class="leaf-icon">🌿</span><span></span></div>
    <h2 class="section-title">Ready For Your <em>Escape?</em></h2>
    <p class="section-sub" style="margin-left:auto;margin-right:auto;">Availability is limited to protect the quiet of every villa.</p>
    <a href="booking.php" class="btn btn-gold">Reserve Your Stay</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
