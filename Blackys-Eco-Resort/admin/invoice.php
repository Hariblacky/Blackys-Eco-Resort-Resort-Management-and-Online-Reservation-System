<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/mini_pdf.php';

$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT b.*, r.name, r.price_per_night FROM bookings b JOIN rooms r ON r.id = b.room_id WHERE b.id = :id");
$stmt->execute(['id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    http_response_code(404);
    die('Booking not found.');
}

$booking = $row;
$room = ['name' => $row['name'], 'price_per_night' => $row['price_per_night']];

$pdf = build_booking_invoice_pdf($booking, $room);
$pdf->download('invoice-' . $booking['booking_ref'] . '.pdf');
