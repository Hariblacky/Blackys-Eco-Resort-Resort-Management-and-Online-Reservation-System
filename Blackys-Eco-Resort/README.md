# Blacky's Eco Resort — Full Resort Management Website

A complete luxury eco-resort website built with **HTML, CSS, JavaScript, PHP and MySQL** —
no frameworks required. Includes a public marketing site and a secure admin dashboard
with full CRUD, image uploads, analytics, and PDF invoices.

This project has been **functionally tested end-to-end** (database import, every public
page, the booking + contact forms, admin login, and full create/edit/delete/upload flows)
against a live PHP + MySQL server before delivery.

---

## 1. Requirements

- PHP 8.0+ with extensions: `pdo_mysql`, `mysqli`, `gd`, `mbstring`, `fileinfo`
- MySQL 5.7+ or MariaDB 10.3+
- Apache (with `mod_rewrite`/`mod_headers`) or Nginx — Apache assumed below
- A mail-capable host for booking/contact notifications (see Section 5)

## 2. Installation

1. **Upload the files** to your web root (or a subfolder), e.g. `public_html/`.
2. **Create the database**: import `database/schema.sql` via phpMyAdmin ("Import") or the CLI:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
   This creates the `blackys_eco_resort` database, all tables, and demo seed data
   (6 rooms, 9 menu items, 6 activities, 12 gallery images, 3 offers, 1 admin user).
3. **Configure the connection** in `config.php`:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_NAME', 'blackys_eco_resort');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   ```
   Also update `MAIL_FROM_ADDRESS`, `MAIL_ADMIN_ADDRESS`, and `APP_SECRET`
   (set `APP_DEBUG` to `false` once live).
4. **Set folder permissions** so PHP can write uploads:
   ```bash
   chmod -R 755 uploads/
   ```
5. **Visit the site** at your domain, and the admin panel at `/admin/`.

## 3. Default Admin Login

```
URL:      /admin/
Username: admin
Password: Admin@123
```
**Change this password immediately** via Admin → Admin Users → edit your account.
Passwords are hashed with PHP's native `password_hash()` (bcrypt) — never stored in plain text.

## 4. What's Included

### Public site (9 pages)
Home · About · Rooms · Restaurant · Activities · Gallery · Offers · Booking · Contact
— fully responsive, dark/light mode, GSAP + AOS scroll animations, Swiper sliders,
a Three.js ambient particle hero, canvas water-ripple and floating-leaf effects,
a video hero section, WhatsApp booking button, embedded Google Maps, and SEO meta
tags (Open Graph, sitemap.xml, robots.txt) throughout.

### Admin Dashboard (`/admin`)
- Secure login with hashed passwords, session auth, CSRF protection, and brute-force lockout
- Analytics dashboard (Chart.js): bookings/revenue trend, status breakdown, room popularity
- Full CRUD + image upload for: **Rooms, Restaurant Menu, Gallery, Offers, Activities**
- **Bookings**: view, filter by status, update status, delete, and download a **PDF invoice**
  (generated with a dependency-free custom PDF writer — no Composer/library needed)
- **Admin Users**: add/edit/delete admin accounts with role-based permissions (superadmin/manager)
- **Site Settings**: business info, WhatsApp number, socials, map coordinates, SEO fields, logo upload

## 5. Email Notifications

By default the site uses PHP's built-in `mail()` function (see `includes/mailer.php`).
This works on most standard Linux hosting but is often blocked on local/dev environments
and some shared hosts. For production reliability, swap in SMTP via PHPMailer:

```php
require 'PHPMailer/src/PHPMailer.php';
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.yourprovider.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-smtp-user';
$mail->Password = 'your-smtp-password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```
Full instructions are commented directly inside `includes/mailer.php`.
When `APP_DEBUG` is `true`, every outgoing email is also logged to
`uploads/mail_debug.log` so you can verify content without a working mail server.

## 6. Folder Structure

```
blackys-eco-resort/
├── index.php, about.php, rooms.php, restaurant.php,   ← 9 public pages
│   activities.php, gallery.php, offers.php,
│   booking.php, contact.php
├── config.php                  ← DB credentials & site constants (EDIT THIS)
├── includes/
│   ├── db.php                  ← PDO connection
│   ├── functions.php           ← helpers: settings, uploads, CSRF, pricing
│   ├── auth.php                ← admin authentication (bcrypt + lockout)
│   ├── mailer.php              ← booking/contact email notifications
│   ├── mini_pdf.php            ← dependency-free PDF invoice generator
│   ├── header.php / footer.php ← shared public site layout
├── admin/
│   ├── index.php (login), logout.php, dashboard.php
│   ├── rooms.php + room_form.php
│   ├── menu.php + menu_form.php
│   ├── gallery.php + gallery_form.php
│   ├── offers.php + offers_form.php
│   ├── activities.php + activities_form.php
│   ├── bookings.php, invoice.php (PDF), users.php + user_form.php, settings.php
│   ├── includes/ (admin_header.php, admin_footer.php)
│   └── assets/css/admin.css
├── assets/
│   ├── css/style.css           ← full design system + dark mode + animations
│   ├── js/main.js              ← nav, forms, sliders, filters, dark mode
│   ├── js/three-scene.js       ← Three.js ambient particle hero effect
│   ├── js/water-ripple.js      ← canvas water ripple interaction
│   └── js/leaves.js            ← floating leaves canvas animation
├── uploads/{rooms,gallery,menu,offers,settings}/  ← user-uploaded images
├── database/schema.sql         ← full schema + seed data
├── robots.txt, sitemap.xml, .htaccess
```

## 7. Customizing

- **Branding/colors**: CSS variables at the top of `assets/css/style.css` (and `admin/assets/css/admin.css`).
- **Hero video**: replace the placeholder `<source>` URL in `index.php`'s hero section with your
  own `assets/video/hero.mp4`.
- **Images**: seed data uses LoremFlickr placeholder photography so every image works immediately.
  Replace with your own via the admin panel's upload fields at any time.
- **WhatsApp number / map location / socials / SEO text**: all editable from
  Admin → Site Settings — no code changes needed.

## 8. Security Notes

- All admin forms are CSRF-protected; all database queries use PDO prepared statements.
- Passwords are hashed with bcrypt (`password_hash()`); never stored or logged in plain text.
- `config.php` and the `database/` and `uploads/` folders are blocked from web/script access via `.htaccess`.
- Set `APP_DEBUG` to `false` in `config.php` before going live to hide internal error details.
