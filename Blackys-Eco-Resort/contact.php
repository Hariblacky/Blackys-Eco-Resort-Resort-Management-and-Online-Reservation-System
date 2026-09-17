<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mailer.php';
$pdo = get_pdo();
$page_title = 'Contact Us';
$page_key = 'contact';
$settings = get_settings();

$sent = false;
$errors = [];
$formData = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Your session expired — please try again.';
    $formData['name'] = trim($_POST['name'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['subject'] = trim($_POST['subject'] ?? '');
    $formData['message'] = trim($_POST['message'] ?? '');

    if (mb_strlen($formData['name']) < 2) $errors[] = 'Please enter your name.';
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (mb_strlen($formData['message']) < 5) $errors[] = 'Please enter a message.';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (:n, :e, :s, :m)");
        $stmt->execute(['n' => $formData['name'], 'e' => $formData['email'], 's' => $formData['subject'], 'm' => $formData['message']]);
        send_contact_notification($formData);
        $sent = true;
        $formData = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
    }
}

include __DIR__ . '/includes/header.php';
$lat = $settings['latitude'] ?? 9.92809;
$lng = $settings['longitude'] ?? -84.09074;
?>

<section class="page-hero">
  <div class="page-hero-bg" style="background-image:url('https://loremflickr.com/1600/900/resort,night');"></div>
  <div class="page-hero-content">
    <div class="breadcrumb">Home <span>/</span> Contact</div>
    <h1>Get In <span style="color:var(--gold-light); font-style:italic;">Touch</span></h1>
  </div>
</section>

<section class="section">
  <div class="container contact-grid">
    <div data-aos="fade-right">
      <div class="glass contact-info-card">
        <div class="contact-icon">📍</div>
        <div><h4>Our Address</h4><p><?= e($settings['address'] ?? '') ?></p></div>
      </div>
      <div class="glass contact-info-card">
        <div class="contact-icon">📞</div>
        <div><h4>Phone</h4><p><?= e($settings['phone'] ?? '') ?></p></div>
      </div>
      <div class="glass contact-info-card">
        <div class="contact-icon">✉</div>
        <div><h4>Email</h4><p><?= e($settings['email'] ?? '') ?></p></div>
      </div>
      <div class="glass contact-info-card">
        <div class="contact-icon">🕐</div>
        <div><h4>Reception Hours</h4><p>Open 24 hours for guests<br>Day visits: 9:00 AM – 5:00 PM</p></div>
      </div>
      <div class="glass contact-info-card" style="border:none; background:transparent; padding-left:0;">
        <div>
          <h4>Follow Us</h4>
          <div class="social-row">
            <a href="<?= e($settings['facebook'] ?? '#') ?>" class="social-icon" aria-label="Facebook">f</a>
            <a href="<?= e($settings['instagram'] ?? '#') ?>" class="social-icon" aria-label="Instagram">ig</a>
            <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $settings['whatsapp_number'] ?? '')) ?>" class="social-icon" aria-label="WhatsApp">wa</a>
            <a href="<?= e($settings['twitter'] ?? '#') ?>" class="social-icon" aria-label="X">x</a>
          </div>
        </div>
      </div>
    </div>

    <div data-aos="fade-left">
      <div class="map-embed">
        <iframe src="https://www.google.com/maps?q=<?= e($lat) ?>,<?= e($lng) ?>&z=12&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Blacky's Eco Resort location on Google Maps"></iframe>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container" style="max-width:760px;">
    <div class="section-head" data-aos="fade-up">
      <div class="eyebrow">Send a Message</div>
      <h2 class="section-title">We'd Love To <em>Hear From You</em></h2>
    </div>

    <?php if ($sent): ?>
      <div class="glass" style="padding:30px; text-align:center; margin-bottom:30px; border-color: var(--gold);">
        <p style="color:var(--text);">✓ Thank you — your message has been sent. We'll respond within one business day.</p>
      </div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div style="background:rgba(193,85,79,.12); border:1px solid rgba(193,85,79,.4); color:#c1554f; padding:16px 20px; border-radius:10px; margin-bottom:26px; font-size:.88rem;">
        <?php foreach ($errors as $err): ?><div>• <?= e($err) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form id="contactForm" class="glass" style="padding:44px;" method="post" novalidate data-aos="fade-up">
      <?= csrf_field() ?>
      <div class="form-row">
        <div class="field"><input type="text" name="name" placeholder=" " value="<?= e($formData['name']) ?>" required><label>Full Name</label><span class="field-error"></span></div>
        <div class="field"><input type="email" name="email" placeholder=" " value="<?= e($formData['email']) ?>" required><label>Email Address</label><span class="field-error"></span></div>
      </div>
      <div class="field"><input type="text" name="subject" placeholder=" " value="<?= e($formData['subject']) ?>"><label>Subject (optional)</label></div>
      <div class="field"><textarea name="message" placeholder=" " required><?= e($formData['message']) ?></textarea><label>Your Message</label><span class="field-error"></span></div>
      <button type="submit" class="btn btn-water btn-block">Send Message</button>
    </form>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <div class="eyebrow">Good to Know</div>
      <h2 class="section-title">Frequently Asked <em>Questions</em></h2>
    </div>
    <div class="faq" data-aos="fade-up">
      <div class="faq-item open">
        <button class="faq-q">What is included in the nightly rate?<span class="icon"></span></button>
        <div class="faq-a" style="max-height:200px;"><p>All villas include daily breakfast, WiFi, guided sunrise yoga, and use of kayaks and snorkeling gear.</p></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">Is the resort suitable for children?<span class="icon"></span></button>
        <div class="faq-a"><p>Yes — our Family Lodge and select bungalows are family-friendly, with a supervised nature club for ages 5-12.</p></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">How do I get to the resort?<span class="icon"></span></button>
        <div class="faq-a"><p>We arrange private transfers from the regional airport; details are sent after your reservation is confirmed.</p></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">What is your cancellation policy?<span class="icon"></span></button>
        <div class="faq-a"><p>Free cancellation up to 14 days before arrival. Within 14 days, one night's rate applies.</p></div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
