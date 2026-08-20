<?php
// pages/success.php - Order / Donation / Adoption Confirmation Receipt Page

$pageTitle = 'Confirmation & Receipt - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

$type = sanitize($_GET['type'] ?? 'order');
$number = sanitize($_GET['number'] ?? '');

$record = null;
$title = 'Thank You For Your Support!';
$subtitle = 'Your transaction has been verified successfully.';

if ($type === 'order') {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
    $stmt->execute([$number]);
    $record = $stmt->fetch();
    
    if ($record) {
        $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmtItems->execute([$record['id']]);
        $record['items'] = $stmtItems->fetchAll();
    }
    $title = 'Order Placed Successfully! 🎉';
} elseif ($type === 'donation') {
    $stmt = $pdo->prepare("SELECT * FROM donations WHERE donation_number = ?");
    $stmt->execute([$number]);
    $record = $stmt->fetch();
    $title = 'Thank You For Your Noble Donation! 💖';
} elseif ($type === 'adoption') {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as cow_name, c.breed as cow_breed, c.tag_number as cow_tag, c.main_image
        FROM adoptions a 
        JOIN cows c ON a.cow_id = c.id 
        WHERE a.adoption_number = ?
    ");
    $stmt->execute([$number]);
    $record = $stmt->fetch();
    $title = 'Cow Adoption Confirmed! 🐄';
}
?>

<div class="page-banner">
  <div class="container">
    <h1><?= htmlspecialchars($title) ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Receipt</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <div style="max-width: 750px; margin: 0 auto;">
      
      <?php if ($record): ?>
        <div class="form-card" style="max-width: 100%; text-align: center;">
          
          <div style="width: 70px; height: 70px; background: #2E7D32; color: white; font-size: 2.2rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
            ✓
          </div>

          <h2 style="color:var(--primary-dark); margin-bottom: 10px;"><?= htmlspecialchars($title) ?></h2>
          <p style="color:var(--text-muted); margin-bottom: 30px;"><?= htmlspecialchars($subtitle) ?></p>

          <!-- Receipt Box -->
          <div style="background: var(--bg-cream); border: 1px solid var(--border-light); padding: 25px; border-radius: var(--radius-md); text-align: left; margin-bottom: 30px;">
            
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid var(--border-light); padding-bottom: 15px; margin-bottom: 15px;">
              <div>
                <h4 style="margin-bottom: 2px;">Kamadhenu Goushala</h4>
                <p style="font-size: 0.85rem; color: var(--text-muted);">Vrindavan Dham, Mathura, UP, India</p>
              </div>
              <div style="text-align: right;">
                <strong style="color: var(--accent-orange); font-size: 1.1rem;"><?= htmlspecialchars($number) ?></strong>
                <p style="font-size: 0.85rem; color: var(--text-muted);"><?= date('d M Y, h:i A', strtotime($record['created_at'])) ?></p>
              </div>
            </div>

            <?php if ($type === 'order'): ?>
              <div style="margin-bottom: 15px;">
                <p><strong>Customer:</strong> <?= htmlspecialchars($record['full_name']) ?> (<?= htmlspecialchars($record['email']) ?>, <?= htmlspecialchars($record['phone']) ?>)</p>
                <p><strong>Shipping Address:</strong> <?= htmlspecialchars($record['address']) ?>, <?= htmlspecialchars($record['city']) ?>, <?= htmlspecialchars($record['state']) ?> - <?= htmlspecialchars($record['pincode']) ?></p>
                <p><strong>Payment Status:</strong> <span style="color:#2E7D32; font-weight:bold;"><?= htmlspecialchars($record['payment_status']) ?></span></p>
              </div>

              <table class="custom-table" style="margin-bottom: 15px;">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($record['items'] as $it): ?>
                    <tr>
                      <td><?= htmlspecialchars($it['product_name']) ?></td>
                      <td><?= $it['quantity'] ?></td>
                      <td><?= format_currency($it['subtotal']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

              <div style="text-align: right; font-size: 1.3rem;">
                <strong>Grand Total Paid: <span style="color:var(--accent-orange);"><?= format_currency($record['total_amount']) ?></span></strong>
              </div>

            <?php elseif ($type === 'donation'): ?>
              <div style="margin-bottom: 15px; line-height: 1.8;">
                <p><strong>Donor Name:</strong> <?= htmlspecialchars($record['donor_name']) ?></p>
                <p><strong>Email / Phone:</strong> <?= htmlspecialchars($record['email']) ?> | <?= htmlspecialchars($record['phone']) ?></p>
                <p><strong>Seva Program:</strong> <?= htmlspecialchars($record['purpose']) ?></p>
                <p><strong>Transaction ID:</strong> <?= htmlspecialchars($record['transaction_id']) ?></p>
                <p><strong>Payment Status:</strong> <span style="color:#2E7D32; font-weight:bold;"><?= htmlspecialchars($record['payment_status']) ?></span></p>
              </div>
              <div style="text-align: right; font-size: 1.4rem; font-weight: bold; border-top: 1px dashed var(--border-light); padding-top: 10px;">
                Donation Amount: <span style="color:var(--accent-orange);"><?= format_currency($record['amount']) ?></span>
              </div>
              <div style="background:#E8F5E9; color:#1B5E20; padding:12px; border-radius:var(--radius-sm); margin-top:15px; font-size:0.88rem;">
                📜 80G Tax Exemption Certificate receipt generated. Keep this record for tax benefits.
              </div>

            <?php elseif ($type === 'adoption'): ?>
              <div style="display:flex; gap:20px; align-items:center; margin-bottom:20px;">
                <img src="<?= $baseUrl . htmlspecialchars($record['main_image']) ?>" style="width:100px; height:100px; object-fit:cover; border-radius:var(--radius-md);">
                <div>
                  <h4>Adopted Cow: <?= htmlspecialchars($record['cow_name']) ?> (<?= htmlspecialchars($record['cow_breed']) ?>)</h4>
                  <p style="font-size:0.9rem;">Tag #: <strong><?= htmlspecialchars($record['cow_tag']) ?></strong></p>
                  <p style="font-size:0.9rem;">Duration: <strong><?= $record['duration_months'] ?> Months</strong></p>
                </div>
              </div>
              <div style="margin-bottom: 15px;">
                <p><strong>Adopter Name:</strong> <?= htmlspecialchars($record['adopter_name']) ?></p>
                <p><strong>Contact:</strong> <?= htmlspecialchars($record['email']) ?> | <?= htmlspecialchars($record['phone']) ?></p>
              </div>
              <div style="text-align: right; font-size: 1.4rem; font-weight: bold; border-top: 1px dashed var(--border-light); padding-top: 10px;">
                Total Adoption Fee Paid: <span style="color:var(--accent-orange);"><?= format_currency($record['total_amount']) ?></span>
              </div>
            <?php endif; ?>

          </div>

          <div style="display: flex; gap: 15px; justify-content: center;">
            <button onclick="window.print()" class="btn btn-outline">🖨 Print Receipt</button>
            <a href="<?= $baseUrl ?>index.php" class="btn btn-primary">Return to Home Page</a>
          </div>

        </div>
      <?php else: ?>
        <div class="text-center" style="padding: 60px; background: white; border-radius: var(--radius-md);">
          <h2>Invalid Receipt Reference</h2>
          <p>No transaction details found for the provided number.</p>
          <a href="<?= $baseUrl ?>index.php" class="btn btn-secondary">Go Home</a>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
