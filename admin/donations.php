<?php
// admin/donations.php - Admin Donations Log

$adminPageTitle = 'Manage Donations';
require_once __DIR__ . '/header_sidebar.php';

$donations = $pdo->query("SELECT * FROM donations ORDER BY id DESC")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <h3>Donation Receipts Log (<?= count($donations) ?>)</h3>
</div>

<div class="table-responsive">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Donation #</th>
        <th>Donor Name</th>
        <th>Email & Phone</th>
        <th>Program / Purpose</th>
        <th>Amount</th>
        <th>Payment Status</th>
        <th>Txn ID</th>
        <th>Date & Time</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($donations as $d): ?>
        <tr>
          <td><strong><?= htmlspecialchars($d['donation_number']) ?></strong></td>
          <td><?= htmlspecialchars($d['donor_name']) ?></td>
          <td><?= htmlspecialchars($d['email']) ?><br><small><?= htmlspecialchars($d['phone']) ?></small></td>
          <td><?= htmlspecialchars($d['purpose']) ?></td>
          <td style="color:var(--accent-orange); font-weight:bold;"><?= format_currency($d['amount']) ?></td>
          <td><span style="color:#2E7D32; font-weight:bold;"><?= htmlspecialchars($d['payment_status']) ?></span></td>
          <td><small><?= htmlspecialchars($d['transaction_id']) ?></small></td>
          <td><?= date('d M Y, h:i A', strtotime($d['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</main>
</div>
</body>
</html>
