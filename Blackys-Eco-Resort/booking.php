<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mailer.php';
$pdo = get_pdo();
$page_title = 'Book Your Stay';
$page_key = 'booking';

$rooms = $pdo->query("SELECT * FROM rooms WHERE status='active' ORDER BY name")->fetchAll();
$selectedRoomId = (int)($_GET['room_id'] ?? 0);

$errors = [];
$success = null;
$formData = ['full_name' => '', 'email' => '', 'phone' => '', 'room_id' => $selectedRoomId, 'check_in' => '', 'check_out' => '', 'guests' => 2, 'special_request' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Your session expired — please try submitting again.';
    }

    $formData['full_name'] = trim($_POST['full_name'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['phone'] = trim($_POST['phone'] ?? '');
    $formData['room_id'] = (int)($_POST['room_id'] ?? 0);
    $formData['check_in'] = trim($_POST['check_in'] ?? '');
    $formData['check_out'] = trim($_POST['check_out'] ?? '');
    $formData['guests'] = (int)($_POST['guests'] ?? 1);
    $formData['special_request'] = trim($_POST['special_request'] ?? '');

    if (mb_strlen($formData['full_name']) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (!preg_match('/^[0-9+\-\s()]{7,16}$/', $formData['phone'])) $errors[] = 'Please enter a valid phone number.';
    if ($formData['room_id'] <= 0) $errors[] = 'Please select a room or villa.';
    if (!$formData['check_in'] || !$formData['check_out']) $errors[] = 'Please select check-in and check-out dates.';
    if ($formData['guests'] < 1) $errors[] = 'Please select the number of guests.';

    $room = null;
    if ($formData['room_id'] > 0) {
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id AND status='active'");
        $stmt->execute(['id' => $formData['room_id']]);
        $room = $stmt->fetch();
        if (!$room) $errors[] = 'The selected room is not available.';
    }

    if (!$errors && $formData['check_in'] && $formData['check_out']) {
        $checkInDate = DateTime::createFromFormat('Y-m-d', $formData['check_in']);
        $checkOutDate = DateTime::createFromFormat('Y-m-d', $formData['check_out']);
        $today = new DateTime('today');
        if (!$checkInDate || $checkInDate < $today) $errors[] = 'Check-in date must be today or later.';
        if (!$checkOutDate || $checkOutDate <= $checkInDate) $errors[] = 'Check-out date must be after check-in.';
    }

    if (!$errors) {
        $nights = nights_between($formData['check_in'], $formData['check_out']);
        $total = $nights * (float)$room['price_per_night'];
        $ref = generate_booking_ref();

        $stmt = $pdo->prepare("INSERT INTO bookings
            (booking_ref, room_id, full_name, email, phone, check_in, check_out, guests, special_request, nights, total_amount, status)
            VALUES (:ref, :room_id, :name, :email, :phone, :in, :out, :guests, :req, :nights, :total, 'pending')");
        $stmt->execute([
            'ref' => $ref, 'room_id' => $room['id'], 'name' => $formData['full_name'], 'email' => $formData['email'],
            'phone' => $formData['phone'], 'in' => $formData['check_in'], 'out' => $formData['check_out'],
            'guests' => $formData['guests'], 'req' => $formData['special_request'], 'nights' => $nights, 'total' => $total,
        ]);

        $booking = $formData;
        $booking['booking_ref'] = $ref;
        $booking['nights'] = $nights;
        $booking['total_amount'] = $total;
        $booking['status'] = 'pending';

        send_booking_confirmation($booking, $room);
        send_admin_booking_alert($booking, $room);

        $success = $booking;
    }
}

include __DIR__ . '/includes/header.php';
$settings = get_settings();
$waNumber = preg_replace('/\D/', '', $settings['whatsapp_number'] ?? '');
?>

<section class="page-hero" style="min-height:42vh;">
  <div class="page-hero-bg" style="background-image:url('https://loremflickr.com/1600/700/villa,sunset');"></div>
  <div class="page-hero-content">
    <div class="breadcrumb">Home <span>/</span> Booking</div>
    <h1>Reserve Your <span style="color:var(--gold-light); font-style:italic;">Stay</span></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="booking-wrap">

      <div class="glass form-card" data-aos="fade-right">
        <div class="eyebrow">Reservation Details</div>
        <h2 class="section-title" style="font-size:2rem; margin-bottom:8px;">Plan Your <em>Escape</em></h2>
        <p style="color:var(--text-soft); font-size:.9rem; margin-bottom:32px;">Submit your details below — our reservations team confirms every booking personally within 24 hours.</p>

        <?php if ($errors): ?>
          <div style="background:rgba(193,85,79,.12); border:1px solid rgba(193,85,79,.4); color:#c1554f; padding:16px 20px; border-radius:10px; margin-bottom:26px; font-size:.88rem;">
            <?php foreach ($errors as $err): ?><div>• <?= e($err) ?></div><?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form id="bookingForm" method="post" novalidate>
          <?= csrf_field() ?>
          <div class="form-row">
            <div class="field <?= $formData['full_name'] ? 'filled' : '' ?>">
              <input type="text" name="full_name" placeholder=" " value="<?= e($formData['full_name']) ?>" required>
              <label>Full Name</label><span class="field-error"></span>
            </div>
            <div class="field <?= $formData['email'] ? 'filled' : '' ?>">
              <input type="email" name="email" placeholder=" " value="<?= e($formData['email']) ?>" required>
              <label>Email Address</label><span class="field-error"></span>
            </div>
          </div>
          <div class="form-row">
            <div class="field <?= $formData['phone'] ? 'filled' : '' ?>">
              <input type="tel" name="phone" placeholder=" " value="<?= e($formData['phone']) ?>" required>
              <label>Phone Number</label><span class="field-error"></span>
            </div>
            <div class="field filled">
              <select name="guests" required>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                  <option value="<?= $i ?>" <?= $formData['guests'] == $i ? 'selected' : '' ?>><?= $i ?> Guest<?= $i > 1 ? 's' : '' ?></option>
                <?php endfor; ?>
              </select>
              <label>Guests</label><span class="select-arrow">▾</span><span class="field-error"></span>
            </div>
          </div>
          <div class="field filled">
            <select name="room_id" required>
              <option value="">— Select a Room / Villa —</option>
              <?php foreach ($rooms as $room): ?>
                <option value="<?= (int)$room['id'] ?>" data-price="<?= (float)$room['price_per_night'] ?>" <?= $formData['room_id'] == $room['id'] ? 'selected' : '' ?>>
                  <?= e($room['name']) ?> — <?= format_price($room['price_per_night']) ?>/night
                </option>
              <?php endforeach; ?>
            </select>
            <label>Room / Villa</label><span class="select-arrow">▾</span><span class="field-error"></span>
          </div>
          <div class="form-row">
            <div class="field <?= $formData['check_in'] ? 'filled' : '' ?>">
              <input type="date" name="check_in" value="<?= e($formData['check_in']) ?>" required>
              <label>Check-in</label><span class="field-error"></span>
            </div>
            <div class="field <?= $formData['check_out'] ? 'filled' : '' ?>">
              <input type="date" name="check_out" value="<?= e($formData['check_out']) ?>" required>
              <label>Check-out</label><span class="field-error"></span>
            </div>
          </div>
          <div class="field <?= $formData['special_request'] ? 'filled' : '' ?>">
            <textarea name="special_request" placeholder=" "><?= e($formData['special_request']) ?></textarea>
            <label>Special Request (optional)</label><span class="field-error"></span>
          </div>
          <button type="submit" class="btn btn-water btn-block">Confirm Reservation</button>
        </form>
      </div>

      <div class="glass booking-summary" data-aos="fade-left">
        <h4>Your Stay Summary</h4>
        <div class="summary-row"><span>Room / Villa</span><b id="sumRoom">—</b></div>
        <div class="summary-row"><span>Nights</span><b id="sumNights">1</b></div>
        <div class="summary-row"><span>Rate / Night</span><b id="sumRate"><?= e($settings['currency'] ?? '$') ?>0.00</b></div>
        <div class="summary-total"><span>Estimated Total</span><b id="sumTotal"><?= e($settings['currency'] ?? '$') ?>0.00</b></div>
        <p style="color:var(--text-soft); font-size:.8rem; margin-top:22px;">Final pricing, taxes and any resort fees are confirmed by our reservations team before charge. No payment is taken on this form.</p>
        <a href="https://wa.me/<?= e($waNumber) ?>?text=<?= rawurlencode("Hi, I'd like help booking a stay at " . ($settings['site_name'] ?? '') . ".") ?>" target="_blank" rel="noopener" class="whatsapp-confirm">💬 Book via WhatsApp Instead</a>
      </div>

    </div>
  </div>
</section>

<?php if ($success): ?>
<div class="success-popup active">
  <div class="glass success-box">
    <button class="popup-close-x" onclick="document.querySelector('.success-popup').classList.remove('active')">&times;</button>
    <div class="success-icon">✓</div>
    <h3>Reservation Received</h3>
    <p>Thank you, <?= e($success['full_name']) ?>. Your reference is <b><?= e($success['booking_ref']) ?></b>. A confirmation email is on its way — our team will confirm final details shortly.</p>
    <a href="https://wa.me/<?= e($waNumber) ?>?text=<?= rawurlencode('Hi! I just submitted booking ' . $success['booking_ref'] . ' and wanted to confirm the details.') ?>" target="_blank" rel="noopener" class="whatsapp-confirm">💬 Confirm via WhatsApp</a>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
