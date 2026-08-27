<?php
// pages/profile.php - User Account Profile & History

$pageTitle = 'My Profile & History - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

require_login();

$user = get_current_user_data();
$userId = $_SESSION['user_id'];

// Fetch User Orders
$stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmtOrders->execute([$userId]);
$orders = $stmtOrders->fetchAll();

// Fetch User Donations
$stmtDon = $pdo->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY id DESC");
$stmtDon->execute([$userId]);
$donations = $stmtDon->fetchAll();

// Fetch User Cow Adoptions
$stmtAdp = $pdo->prepare("
    SELECT a.*, c.name as cow_name, c.breed as cow_breed, c.main_image
    FROM adoptions a 
    JOIN cows c ON a.cow_id = c.id 
    WHERE a.user_id = ? 
    ORDER BY a.id DESC
");
$stmtAdp->execute([$userId]);
$adoptions = $stmtAdp->fetchAll();
?>

<div class="page-banner">
  <div class="container">
    <h1><?= __('welcome_user', 'Welcome') ?>, <?= htmlspecialchars($user['full_name']) ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <span><?= __('nav_my_account', 'My Profile') ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <div class="grid-responsive-2" style="align-items: start;">
      
      <!-- Account Info Sidebar -->
      <div>
        <div class="form-card" style="margin:0; width:100%; max-width:100%;">
          <div style="text-align:center; margin-bottom:20px;">
            <div style="width:80px; height:80px; background:var(--primary-dark); color:white; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold;">
              <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
            </div>
            <h3 style="margin-top:10px;"><?= htmlspecialchars($user['full_name']) ?></h3>
            <p style="font-size:0.85rem; color:var(--text-muted);"><?= htmlspecialchars($user['email']) ?></p>
          </div>

          <div style="font-size:0.95rem; border-top:1px solid var(--border-light); padding-top:15px; line-height:1.8;">
            <p><strong><?= __('phone_label', 'Phone:') ?></strong> <?= htmlspecialchars($user['phone'] ?: 'N/A') ?></p>
            <p><strong><?= __('address_label', 'Address:') ?></strong> <?= htmlspecialchars($user['address'] ?: 'N/A') ?></p>
            <p><strong><?= __('location_label', 'Location:') ?></strong> <?= htmlspecialchars($user['city'] ?: '') ?>, <?= htmlspecialchars($user['state'] ?: '') ?></p>
            <p><strong><?= __('member_since_label', 'Member Since:') ?></strong> <?= date('d M Y', strtotime($user['created_at'])) ?></p>
          </div>

          <div style="margin-top: 20px; border-top: 1px solid var(--border-light); padding-top: 15px;">
            <a href="<?= $baseUrl ?>pages/login.php?action=logout" class="btn btn-outline btn-block" style="color: #d32f2f; border-color: #d32f2f; display: flex; align-items: center; justify-content: center; gap: 6px; font-weight: 600; text-decoration: none;">
              🚪 <?= __('logout_btn', 'Log Out of Account') ?>
            </a>
          </div>
        </div>
      </div>

      <!-- History Tabs / Tables -->
      <div style="grid-column: span 2;">
        
        <!-- Orders History -->
        <div style="background:white; border-radius:var(--radius-md); padding:25px; box-shadow:var(--shadow-sm); margin-bottom:30px; border:1px solid var(--border-light);">
          <h3 style="margin-bottom:15px; color:var(--primary-dark);">🛍 <?= __('your_orders', 'Your Product Orders') ?> (<?= count($orders) ?>)</h3>
          <?php if (!empty($orders)): ?>
            <div class="table-responsive">
              <table class="custom-table">
                <thead>
                  <tr>
                    <th><?= __('order_num', 'Order #') ?></th>
                    <th><?= __('date_time', 'Date & Time') ?></th>
                    <th><?= __('total', 'Total') ?></th>
                    <th><?= __('payment', 'Payment') ?></th>
                    <th><?= __('status', 'Status') ?></th>
                    <th><?= __('action', 'Action') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $o): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($o['order_number']) ?></strong></td>
                      <td><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></td>
                      <td><?= format_currency($o['total_amount']) ?></td>
                      <td><span style="color:#2E7D32; font-weight:bold;"><?= htmlspecialchars(__($o['payment_status'], $o['payment_status'])) ?></span></td>
                      <td><span class="card-badge" style="position:static;"><?= htmlspecialchars(__($o['order_status'], $o['order_status'])) ?></span></td>
                      <td><a href="success.php?type=order&number=<?= urlencode($o['order_number']) ?>" class="btn btn-outline btn-sm"><?= __('receipt_btn', 'Receipt') ?></a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p style="color:var(--text-muted);"><?= __('no_orders_yet', 'No product orders placed yet.') ?></p>
          <?php endif; ?>
        </div>

        <!-- Donations History -->
        <div style="background:white; border-radius:var(--radius-md); padding:25px; box-shadow:var(--shadow-sm); margin-bottom:30px; border:1px solid var(--border-light);">
          <h3 style="margin-bottom:15px; color:var(--primary-dark);">💖 <?= __('your_donations', 'Your Donations & Seva') ?> (<?= count($donations) ?>)</h3>
          <?php if (!empty($donations)): ?>
            <div class="table-responsive">
              <table class="custom-table">
                <thead>
                  <tr>
                    <th><?= __('donation_num', 'Donation #') ?></th>
                    <th><?= __('program', 'Program') ?></th>
                    <th><?= __('amount', 'Amount') ?></th>
                    <th><?= __('date_time', 'Date & Time') ?></th>
                    <th><?= __('view_80g_btn', '80G Receipt') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($donations as $d): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($d['donation_number']) ?></strong></td>
                      <td><?= htmlspecialchars(__($d['purpose'], $d['purpose'])) ?></td>
                      <td style="font-weight:bold; color:var(--accent-orange);"><?= format_currency($d['amount']) ?></td>
                      <td><?= date('d M Y, h:i A', strtotime($d['created_at'])) ?></td>
                      <td><a href="success.php?type=donation&number=<?= urlencode($d['donation_number']) ?>" class="btn btn-outline btn-sm"><?= __('view_80g_btn', 'View 80G') ?></a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p style="color:var(--text-muted);"><?= __('no_donations_yet', 'No donations recorded yet under this account.') ?></p>
          <?php endif; ?>
        </div>

        <!-- Cow Adoptions History -->
        <div style="background:white; border-radius:var(--radius-md); padding:25px; box-shadow:var(--shadow-sm); border:1px solid var(--border-light);">
          <h3 style="margin-bottom:15px; color:var(--primary-dark);">🐄 <?= __('your_adoptions', 'Your Cow Adoptions') ?> (<?= count($adoptions) ?>)</h3>
          <?php if (!empty($adoptions)): ?>
            <div class="table-responsive">
              <table class="custom-table">
                <thead>
                  <tr>
                    <th><?= __('adoption_num', 'Adoption #') ?></th>
                    <th><?= __('cow_name', 'Cow Name') ?></th>
                    <th><?= __('duration', 'Duration') ?></th>
                    <th><?= __('total', 'Total') ?></th>
                    <th><?= __('status', 'Status') ?></th>
                    <th><?= __('date_time', 'Date & Time') ?></th>
                    <th><?= __('certificate', 'Certificate') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($adoptions as $a): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($a['adoption_number']) ?></strong></td>
                      <td><?= htmlspecialchars(__($a['cow_name'], $a['cow_name'])) ?> (<?= htmlspecialchars(__($a['cow_breed'], $a['cow_breed'])) ?>)</td>
                      <td><?= $a['duration_months'] ?> <?= __('mo_label', 'Months') ?></td>
                      <td style="font-weight:bold;"><?= format_currency($a['total_amount']) ?></td>
                      <td><span style="color:#2E7D32; font-weight:bold;"><?= htmlspecialchars(__($a['status'], $a['status'])) ?></span></td>
                      <td><?= date('d M Y, h:i A', strtotime($a['created_at'])) ?></td>
                      <td><a href="success.php?type=adoption&number=<?= urlencode($a['adoption_number']) ?>" class="btn btn-outline btn-sm"><?= __('view_certificate_btn', 'View Certificate') ?></a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p style="color:var(--text-muted);"><?= __('no_adoptions_yet', 'You have not adopted any cows yet.') ?></p>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
