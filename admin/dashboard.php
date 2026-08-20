<?php
// admin/dashboard.php - Admin Dashboard Overview

$adminPageTitle = 'Dashboard Overview';
require_once __DIR__ . '/header_sidebar.php';

// Calculate Totals
$totalCows = (int)$pdo->query("SELECT COUNT(*) FROM cows")->fetchColumn();
$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalDonations = (int)$pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();
$totalDonationRev = (float)($pdo->query("SELECT SUM(amount) FROM donations WHERE payment_status = 'Completed'")->fetchColumn() ?: 0);
$totalOrderRev = (float)($pdo->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'Paid'")->fetchColumn() ?: 0);
$totalAdoptions = (int)$pdo->query("SELECT COUNT(*) FROM adoptions")->fetchColumn();
$unreadMessages = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$totalEvents = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();

// Recent Orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

// Recent Donations
$recentDonations = $pdo->query("SELECT * FROM donations ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!-- Metric Cards -->
<div class="stats-grid" style="margin-top:0; margin-bottom:30px;">
  <div class="stat-box">
    <div class="stat-number"><?= $totalCows ?></div>
    <div class="stat-label">Total Cows Registered</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= format_currency($totalDonationRev + $totalOrderRev) ?></div>
    <div class="stat-label">Total Funds Raised</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= $totalEvents ?></div>
    <div class="stat-label">Sanctuary Events</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= $totalOrders ?></div>
    <div class="stat-label">Product Orders</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= $totalDonations ?></div>
    <div class="stat-label">Donations Received</div>
  </div>
  <div class="stat-box">
    <div class="stat-number"><?= $totalAdoptions ?></div>
    <div class="stat-label">Active Cow Adoptions</div>
  </div>
  <div class="stat-box">
    <div class="stat-number" style="color:<?= $unreadMessages > 0 ? '#C62828' : 'var(--primary-dark)'; ?>"><?= $unreadMessages ?></div>
    <div class="stat-label">Unread Messages</div>
  </div>
</div>

<!-- Recent Tables Grid -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap:30px;">
  
  <!-- Recent Orders Table -->
  <div style="background:white; padding:25px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
      <h3 style="margin:0; font-size:1.15rem;">Recent Product Orders</h3>
      <a href="orders.php" style="font-size:0.85rem; font-weight:bold;">View All →</a>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $o): ?>
            <tr>
              <td><strong><?= htmlspecialchars($o['order_number']) ?></strong></td>
              <td><?= htmlspecialchars($o['full_name']) ?></td>
              <td><?= format_currency($o['total_amount']) ?></td>
              <td><span class="card-badge" style="position:static;"><?= htmlspecialchars($o['order_status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Donations Table -->
  <div style="background:white; padding:25px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
      <h3 style="margin:0; font-size:1.15rem;">Recent Donations</h3>
      <a href="donations.php" style="font-size:0.85rem; font-weight:bold;">View All →</a>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Donor Name</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentDonations as $d): ?>
            <tr>
              <td><strong><?= htmlspecialchars($d['donor_name']) ?></strong></td>
              <td><?= htmlspecialchars($d['purpose']) ?></td>
              <td style="color:var(--accent-orange); font-weight:bold;"><?= format_currency($d['amount']) ?></td>
              <td><?= date('d M', strtotime($d['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

</main>
</div>
</body>
</html>
