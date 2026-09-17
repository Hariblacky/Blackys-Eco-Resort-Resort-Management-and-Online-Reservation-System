<?php
$settings = get_settings();
$site_name = $settings['site_name'] ?? "Blacky's Eco Resort";
$wa_number = preg_replace('/\D/', '', $settings['whatsapp_number'] ?? '');
$wa_message = rawurlencode("Hello! I'd like to know more about a stay at " . $site_name . ".");
?>
  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <div class="brand">
          <span class="brand-mark">🌿</span>
          <span class="brand-text"><b><?= e($site_name) ?></b><span><?= e($settings['tagline'] ?? '') ?></span></span>
        </div>
        <p><?= e($settings['meta_description'] ?? '') ?></p>
        <div class="social-row">
          <a href="<?= e($settings['facebook'] ?? '#') ?>" class="social-icon" aria-label="Facebook">f</a>
          <a href="<?= e($settings['instagram'] ?? '#') ?>" class="social-icon" aria-label="Instagram">ig</a>
          <a href="https://wa.me/<?= e($wa_number) ?>" class="social-icon" aria-label="WhatsApp">wa</a>
          <a href="<?= e($settings['twitter'] ?? '#') ?>" class="social-icon" aria-label="X">x</a>
        </div>
      </div>
      <div>
        <h5>Explore</h5>
        <ul class="footer-links">
          <li><a href="rooms.php">Rooms &amp; Villas</a></li>
          <li><a href="restaurant.php">Restaurant</a></li>
          <li><a href="activities.php">Activities</a></li>
          <li><a href="gallery.php">Gallery</a></li>
        </ul>
      </div>
      <div>
        <h5>Guest Info</h5>
        <ul class="footer-links">
          <li><a href="offers.php">Offers</a></li>
          <li><a href="booking.php">Reservations</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="about.php">About Us</a></li>
        </ul>
      </div>
      <div>
        <h5>Newsletter</h5>
        <p style="color:rgba(237,234,224,.6); font-size:.88rem; margin-bottom:14px;">Sustainable travel stories and seasonal offers, once a month.</p>
        <form class="newsletter-form">
          <input type="email" placeholder="Your email address" required>
          <button type="submit" aria-label="Subscribe">→</button>
        </form>
      </div>
    </div>
    <div class="container footer-bottom">
      <span>© <?= date('Y') ?> <?= e($site_name) ?>. All rights reserved.</span>
      <div class="social-row">
        <a href="<?= e($settings['facebook'] ?? '#') ?>" class="social-icon" aria-label="Facebook">f</a>
        <a href="<?= e($settings['instagram'] ?? '#') ?>" class="social-icon" aria-label="Instagram">ig</a>
        <a href="https://wa.me/<?= e($wa_number) ?>" class="social-icon" aria-label="WhatsApp">wa</a>
        <a href="<?= e($settings['twitter'] ?? '#') ?>" class="social-icon" aria-label="X">x</a>
      </div>
    </div>
  </footer>

  <!-- NEWSLETTER POPUP -->
  <div class="newsletter-popup glass">
    <button class="np-close" aria-label="Close">&times;</button>
    <h4>Join the Grove</h4>
    <p>Seasonal offers, conservation stories &amp; new experiences.</p>
    <form>
      <input type="email" placeholder="Your email address" required>
      <button type="submit">Join</button>
    </form>
  </div>

  <!-- WHATSAPP FLOATING BUTTON -->
  <a class="whatsapp-float" href="https://wa.me/<?= e($wa_number) ?>?text=<?= $wa_message ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">💬</a>

  <button class="scroll-top" aria-label="Scroll to top">↑</button>

  <!-- SCRIPTS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/leaves.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/three-scene.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/water-ripple.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
