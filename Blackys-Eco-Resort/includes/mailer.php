<?php
/**
 * Email notifications.
 *
 * Uses PHP's built-in mail() by default, which works out of the box on most
 * Linux hosting but is BLOCKED by many local dev environments and some
 * shared hosts. For production, swap send_mail() below to use PHPMailer
 * with SMTP, e.g.:
 *
 *   require 'PHPMailer/src/PHPMailer.php';
 *   $mail = new PHPMailer\PHPMailer\PHPMailer();
 *   $mail->isSMTP();
 *   $mail->Host = 'smtp.yourprovider.com';
 *   $mail->SMTPAuth = true;
 *   $mail->Username = 'your-smtp-user';
 *   $mail->Password = 'your-smtp-password';
 *   $mail->SMTPSecure = 'tls';
 *   $mail->Port = 587;
 *   ... then set From/addAddress/Subject/Body and $mail->send();
 */
require_once __DIR__ . '/functions.php';

function send_mail(string $to, string $subject, string $htmlBody): bool
{
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . ">\r\n";
    $headers .= 'Reply-To: ' . MAIL_FROM_ADDRESS . "\r\n";

    // In debug/local environments mail() usually fails silently — log instead.
    if (APP_DEBUG) {
        $log = "---- MAIL (" . date('c') . ") ----\nTo: $to\nSubject: $subject\n$htmlBody\n\n";
        @file_put_contents(__DIR__ . '/../uploads/mail_debug.log', $log, FILE_APPEND);
    }

    return @mail($to, $subject, $htmlBody, $headers);
}

function send_booking_confirmation(array $booking, array $room): void
{
    $settings = get_settings();
    $subject = 'Your Reservation Confirmation — ' . $booking['booking_ref'];
    $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
        <h2 style='color:#1F4A3A;'>Thank you for booking with " . e($settings['site_name'] ?? "Blacky's Eco Resort") . "</h2>
        <p>Dear " . e($booking['full_name']) . ",</p>
        <p>Your reservation request has been received and is currently <b>pending confirmation</b>. Our reservations team will contact you within 24 hours.</p>
        <table style='width:100%;border-collapse:collapse;margin:20px 0;'>
          <tr><td style='padding:8px;border-bottom:1px solid #eee;'>Booking Reference</td><td style='padding:8px;border-bottom:1px solid #eee;'><b>" . e($booking['booking_ref']) . "</b></td></tr>
          <tr><td style='padding:8px;border-bottom:1px solid #eee;'>Room</td><td style='padding:8px;border-bottom:1px solid #eee;'>" . e($room['name']) . "</td></tr>
          <tr><td style='padding:8px;border-bottom:1px solid #eee;'>Check-in</td><td style='padding:8px;border-bottom:1px solid #eee;'>" . e($booking['check_in']) . "</td></tr>
          <tr><td style='padding:8px;border-bottom:1px solid #eee;'>Check-out</td><td style='padding:8px;border-bottom:1px solid #eee;'>" . e($booking['check_out']) . "</td></tr>
          <tr><td style='padding:8px;border-bottom:1px solid #eee;'>Guests</td><td style='padding:8px;border-bottom:1px solid #eee;'>" . e((string)$booking['guests']) . "</td></tr>
          <tr><td style='padding:8px;'>Estimated Total</td><td style='padding:8px;'><b>" . format_price($booking['total_amount']) . "</b></td></tr>
        </table>
        <p>We can't wait to welcome you to the rainforest.</p>
        <p style='color:#888;font-size:12px;'>" . e($settings['site_name'] ?? '') . " &middot; " . e($settings['address'] ?? '') . "</p>
        </div>";
    send_mail($booking['email'], $subject, $body);
}

function send_admin_booking_alert(array $booking, array $room): void
{
    $subject = 'New Booking Received — ' . $booking['booking_ref'];
    $body = "<p>A new booking was submitted.</p>
        <ul>
          <li><b>Reference:</b> " . e($booking['booking_ref']) . "</li>
          <li><b>Guest:</b> " . e($booking['full_name']) . " (" . e($booking['email']) . ", " . e($booking['phone']) . ")</li>
          <li><b>Room:</b> " . e($room['name']) . "</li>
          <li><b>Dates:</b> " . e($booking['check_in']) . ' to ' . e($booking['check_out']) . "</li>
          <li><b>Guests:</b> " . e((string)$booking['guests']) . "</li>
          <li><b>Total:</b> " . format_price($booking['total_amount']) . "</li>
        </ul>";
    send_mail(MAIL_ADMIN_ADDRESS, $subject, $body);
}

function send_contact_notification(array $data): void
{
    $subject = 'New Contact Message: ' . ($data['subject'] ?: 'Website Inquiry');
    $body = "<p><b>From:</b> " . e($data['name']) . " (" . e($data['email']) . ")</p>
             <p>" . nl2br(e($data['message'])) . "</p>";
    send_mail(MAIL_ADMIN_ADDRESS, $subject, $body);
}
