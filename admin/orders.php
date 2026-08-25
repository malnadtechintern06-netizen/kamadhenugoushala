<?php
// admin/orders.php - Admin Product Orders Management

$adminPageTitle = 'Manage Product Orders';
require_once __DIR__ . '/header_sidebar.php';

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $orderId = (int)$_POST['order_id'];
        $status = sanitize($_POST['order_status']);
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        set_flash('success', 'Order status updated.');
    }
    header('Location: orders.php');
    exit;
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <h3>Product Orders Log (<?= count($orders) ?>)</h3>
</div>

<div class="table-responsive">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Order #</th>
        <th>Customer</th>
        <th>Phone & City</th>
        <th>Total Amount</th>
        <th>Payment</th>
        <th>Order Status</th>
        <th>Date & Time</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td><strong><?= htmlspecialchars($o['order_number']) ?></strong></td>
          <td><?= htmlspecialchars($o['full_name']) ?><br><small><?= htmlspecialchars($o['email']) ?></small></td>
          <td><?= htmlspecialchars($o['phone']) ?><br><small><?= htmlspecialchars($o['city']) ?>, <?= htmlspecialchars($o['state']) ?></small></td>
          <td style="font-weight:bold; color:var(--accent-orange);"><?= format_currency($o['total_amount']) ?></td>
          <td><span style="color:#2E7D32; font-weight:bold;"><?= htmlspecialchars($o['payment_status']) ?></span></td>
          <td>
            <form method="POST" action="orders.php">
              <?= csrf_field() ?>
              <input type="hidden" name="update_order_status" value="1">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <select name="order_status" class="form-control" style="padding:4px 8px; font-size:0.82rem;" onchange="this.form.submit()">
                <option value="Pending" <?= $o['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Confirmed" <?= $o['order_status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="Processing" <?= $o['order_status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                <option value="Shipped" <?= $o['order_status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="Delivered" <?= $o['order_status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="Cancelled" <?= $o['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
              </select>
            </form>
          </td>
          <td><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></td>
          <td>
            <a href="../pages/success.php?type=order&number=<?= urlencode($o['order_number']) ?>" target="_blank" class="btn btn-outline btn-sm">View Receipt</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</main>
</div>
</body>
</html>
