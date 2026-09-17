<?php
$page_title = 'Dashboard';
$active = 'dashboard';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = get_pdo();

$totalBookings = (int)$pdo->query("SELECT COUNT(*) c FROM bookings")->fetch()['c'];
$pendingBookings = (int)$pdo->query("SELECT COUNT(*) c FROM bookings WHERE status='pending'")->fetch()['c'];
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) t FROM bookings WHERE status IN ('confirmed','completed')")->fetch()['t'];
$activeRooms = (int)$pdo->query("SELECT COUNT(*) c FROM rooms WHERE status='active'")->fetch()['c'];

// Bookings per month (last 6 months)
$monthly = $pdo->query("
  SELECT DATE_FORMAT(created_at, '%Y-%m') ym, COUNT(*) cnt, COALESCE(SUM(total_amount),0) rev
  FROM bookings
  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
  GROUP BY ym ORDER BY ym
")->fetchAll();
$monthLabels = []; $monthCounts = []; $monthRevenue = [];
$cursor = new DateTime('first day of -5 months');
for ($i = 0; $i < 6; $i++) {
    $key = $cursor->format('Y-m');
    $monthLabels[] = $cursor->format('M Y');
    $found = array_filter($monthly, fn($r) => $r['ym'] === $key);
    $row = reset($found);
    $monthCounts[] = $row ? (int)$row['cnt'] : 0;
    $monthRevenue[] = $row ? (float)$row['rev'] : 0;
    $cursor->modify('+1 month');
}

// Room popularity
$roomPop = $pdo->query("
  SELECT r.name, COUNT(b.id) cnt FROM rooms r
  LEFT JOIN bookings b ON b.room_id = r.id
  GROUP BY r.id ORDER BY cnt DESC LIMIT 6
")->fetchAll();

// Booking status breakdown
$statusBreakdown = $pdo->query("SELECT status, COUNT(*) cnt FROM bookings GROUP BY status")->fetchAll();

$recentBookings = $pdo->query("SELECT b.*, r.name AS room_name FROM bookings b LEFT JOIN rooms r ON r.id=b.room_id ORDER BY b.created_at DESC LIMIT 6")->fetchAll();
?>

<div class="stat-cards">
  <div class="stat-card gold"><div class="icon">📅</div><div class="num"><?= $totalBookings ?></div><div class="label">Total Bookings</div></div>
  <div class="stat-card warn"><div class="icon">⏳</div><div class="num"><?= $pendingBookings ?></div><div class="label">Pending Confirmation</div></div>
  <div class="stat-card water"><div class="icon">💰</div><div class="num"><?= format_price($totalRevenue) ?></div><div class="label">Confirmed Revenue</div></div>
  <div class="stat-card green"><div class="icon">🏡</div><div class="num"><?= $activeRooms ?></div><div class="label">Active Rooms &amp; Villas</div></div>
</div>

<div class="chart-grid">
  <div class="panel">
    <div class="panel-head"><h2>Bookings &amp; Revenue (Last 6 Months)</h2></div>
    <canvas id="monthlyChart" height="110"></canvas>
  </div>
  <div class="panel">
    <div class="panel-head"><h2>Booking Status</h2></div>
    <canvas id="statusChart" height="110"></canvas>
  </div>
</div>

<div class="chart-grid">
  <div class="panel">
    <div class="panel-head"><h2>Most Booked Rooms</h2></div>
    <canvas id="roomChart" height="120"></canvas>
  </div>
  <div class="panel">
    <div class="panel-head"><h2>Recent Bookings</h2><a href="bookings.php" class="btn btn-outline btn-sm">View All</a></div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead><tr><th>Ref</th><th>Guest</th><th>Room</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($recentBookings as $b): ?>
          <tr>
            <td><?= e($b['booking_ref']) ?></td>
            <td><?= e($b['full_name']) ?></td>
            <td><?= e($b['room_name']) ?></td>
            <td><span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$recentBookings): ?><tr><td colspan="4" style="text-align:center; color:var(--text-soft);">No bookings yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const goldGrad = '#C9A24B', waterGrad = '#4FB6C0', forestGrad = '#1F5A40';

new Chart(document.getElementById('monthlyChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($monthLabels) ?>,
    datasets: [
      { label: 'Bookings', data: <?= json_encode($monthCounts) ?>, backgroundColor: goldGrad, borderRadius: 6, yAxisID: 'y' },
      { label: 'Revenue', data: <?= json_encode($monthRevenue) ?>, type: 'line', borderColor: waterGrad, backgroundColor: waterGrad, tension: .35, yAxisID: 'y1' }
    ]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true, position: 'left', grid: { display: false } },
      y1: { beginAtZero: true, position: 'right', grid: { display: false } }
    }
  }
});

new Chart(document.getElementById('statusChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_map(fn($r) => ucfirst($r['status']), $statusBreakdown)) ?>,
    datasets: [{ data: <?= json_encode(array_map(fn($r) => (int)$r['cnt'], $statusBreakdown)) ?>, backgroundColor: ['#C98B1F', '#2F8F5B', '#c1554f', '#4FB6C0'] }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('roomChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_map(fn($r) => $r['name'], $roomPop)) ?>,
    datasets: [{ label: 'Bookings', data: <?= json_encode(array_map(fn($r) => (int)$r['cnt'], $roomPop)) ?>, backgroundColor: forestGrad, borderRadius: 6 }]
  },
  options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
