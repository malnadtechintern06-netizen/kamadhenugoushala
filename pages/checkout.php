<?php
// pages/checkout.php - Secure Order Checkout Page

$pageTitle = 'Order Checkout - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

$cartData = get_cart_details();
$items = $cartData['items'];

if (empty($items)) {
    set_flash('error', 'Your cart is empty. Please add products before checking out.');
    header('Location: products.php');
    exit;
}

$currentUser = get_current_user_data();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try submitting again.';
    }

    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');

    if (empty($fullName)) $errors[] = 'Full Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email Address is required.';
    if (empty($phone)) $errors[] = 'Phone Number is required.';
    if (empty($address)) $errors[] = 'Delivery Address is required.';
    if (empty($city)) $errors[] = 'City is required.';
    if (empty($state)) $errors[] = 'State is required.';
    if (empty($pincode)) $errors[] = 'Pincode is required.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. Re-verify stock & total amount directly from Database to prevent tampering
            $verifiedTotal = 0;
            $orderItemsToInsert = [];

            foreach ($items as $item) {
                // Fetch fresh database record
                $stmtP = $pdo->prepare("SELECT id, name, price, sale_price, stock_quantity FROM products WHERE id = ? FOR UPDATE");
                $stmtP->execute([$item['product_id']]);
                $freshP = $stmtP->fetch();

                if (!$freshP || $freshP['stock_quantity'] < $item['quantity']) {
                    throw new Exception("Product '" . ($freshP['name'] ?? 'Item') . "' is out of stock.");
                }

                $effectivePrice = ($freshP['sale_price'] > 0 && $freshP['sale_price'] < $freshP['price']) ? $freshP['sale_price'] : $freshP['price'];
                $subtotal = $effectivePrice * $item['quantity'];
                $verifiedTotal += $subtotal;

                $orderItemsToInsert[] = [
                    'product_id' => $freshP['id'],
                    'product_name' => $freshP['name'],
                    'price' => $effectivePrice,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ];

                // Deduct stock
                $stmtDeduct = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmtDeduct->execute([$item['quantity'], $freshP['id']]);
            }

            // 2. Generate unique Order Number
            $orderNumber = 'KG-ORD-' . strtoupper(bin2hex(random_bytes(4)));
            $userId = $_SESSION['user_id'] ?? null;

            // 3. Insert into `orders`
            $stmtOrder = $pdo->prepare("
                INSERT INTO orders (order_number, user_id, full_name, email, phone, address, city, state, pincode, total_amount, payment_method, payment_status, order_status, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Online Payment Simulation', 'Paid', 'Confirmed', ?)
            ");
            $stmtOrder->execute([$orderNumber, $userId, $fullName, $email, $phone, $address, $city, $state, $pincode, $verifiedTotal, $notes]);
            $orderId = $pdo->lastInsertId();

            // 4. Insert into `order_items`
            $stmtItem = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach ($orderItemsToInsert as $oi) {
                $stmtItem->execute([$orderId, $oi['product_id'], $oi['product_name'], $oi['price'], $oi['quantity'], $oi['subtotal']]);
            }

            // 5. Record Payment Simulation
            $txnId = 'TXN-ORD-' . time() . '-' . rand(1000, 9999);
            $stmtPay = $pdo->prepare("
                INSERT INTO payments (reference_type, reference_id, amount, payment_method, transaction_id, status)
                VALUES ('order', ?, ?, 'Online Gateway Simulation', ?, 'Success')
            ");
            $stmtPay->execute([$orderId, $verifiedTotal, $txnId]);

            // 6. Clear Cart
            $cartId = get_or_create_cart_id();
            $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cartId]);

            $pdo->commit();

            // Redirect to Success Page
            header("Location: success.php?type=order&number=" . urlencode($orderNumber));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Checkout failed: ' . $e->getMessage();
        }
    }
}
?>

<div class="page-banner">
  <div class="container">
    <h1>Checkout & Shipping Details</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <a href="cart.php">Cart</a> / <span>Checkout</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="checkout.php">
      <?= csrf_field() ?>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; align-items: start;">
        
        <!-- Shipping Details Form -->
        <div class="form-card" style="margin:0; max-width:100%;">
          <h3 style="margin-bottom:20px; border-bottom:2px solid var(--border-light); padding-bottom:10px;">Shipping Information</h3>
          
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($_POST['full_name'] ?? ($currentUser['full_name'] ?? '')) ?>" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Email Address *</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? ($currentUser['email'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone Number *</label>
              <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? ($currentUser['phone'] ?? '')) ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Delivery Address *</label>
            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($_POST['address'] ?? ($currentUser['address'] ?? '')) ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">City *</label>
              <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($_POST['city'] ?? ($currentUser['city'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">State *</label>
              <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($_POST['state'] ?? ($currentUser['state'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Pincode *</label>
              <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($_POST['pincode'] ?? ($currentUser['pincode'] ?? '')) ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Order Notes / Instructions (Optional)</label>
            <input type="text" name="notes" class="form-control" placeholder="Special delivery notes...">
          </div>
        </div>

        <!-- Order Items & Payment Review -->
        <div>
          <div class="form-card" style="margin:0; max-width:100%;">
            <h3 style="margin-bottom:20px; border-bottom:2px solid var(--border-light); padding-bottom:10px;">Your Items (<?= count($items) ?>)</h3>

            <div style="margin-bottom:20px;">
              <?php foreach ($items as $item): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:1px solid var(--border-light); padding-bottom:8px;">
                  <div>
                    <strong style="color:var(--primary-dark);"><?= htmlspecialchars($item['name']) ?></strong>
                    <div style="font-size:0.85rem; color:var(--text-muted);"><?= $item['quantity'] ?> x <?= format_currency($item['unit_price']) ?></div>
                  </div>
                  <strong style="color:var(--primary-dark);"><?= format_currency($item['total_price']) ?></strong>
                </div>
              <?php endforeach; ?>
            </div>

            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:1rem;">
              <span>Subtotal:</span>
              <strong><?= format_currency($cartData['subtotal']) ?></strong>
            </div>

            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:1rem;">
              <span>Shipping Fee:</span>
              <strong style="color:#2E7D32;">FREE</strong>
            </div>

            <div style="display:flex; justify-content:space-between; margin-bottom:25px; padding-top:12px; border-top:2px solid var(--border-light); font-size:1.3rem;">
              <strong>Total Payable:</strong>
              <strong style="color:var(--accent-orange);"><?= format_currency($cartData['total']) ?></strong>
            </div>

            <div style="background:var(--bg-light-green); padding:15px; border-radius:var(--radius-sm); margin-bottom:20px; font-size:0.85rem;">
              💳 <strong>Payment Method:</strong> Secure Online Gateway Simulation (Razorpay / Cashfree ready).
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Place Order & Pay <?= format_currency($cartData['total']) ?> 🎉</button>
          </div>
        </div>

      </div>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
