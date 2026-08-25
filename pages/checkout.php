<?php
// pages/checkout.php - Secure Order Checkout Page

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login('Please log in or create an account to proceed with checkout.');

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
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'UPI / QR Code (GPay, PhonePe, Paytm)');

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

            $verifiedTotal = 0;
            $orderItemsToInsert = [];

            foreach ($items as $item) {
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
                    'unit_price' => $effectivePrice,
                    'quantity' => $item['quantity'],
                    'total_price' => $subtotal
                ];
            }

            // 2. Create Order Header
            $orderNumber = 'KG-ORD-' . strtoupper(bin2hex(random_bytes(4)));
            $userId = $_SESSION['user_id'] ?? null;

            $stmtOrd = $pdo->prepare("
                INSERT INTO orders (order_number, user_id, full_name, email, phone, address, city, state, pincode, total_amount, payment_method, payment_status, order_status, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Paid', 'Processing', ?)
            ");
            $stmtOrd->execute([$orderNumber, $userId, $fullName, $email, $phone, $address, $city, $state, $pincode, $verifiedTotal, $paymentMethod, $notes]);
            $orderId = $pdo->lastInsertId();

            // 3. Insert Order Line Items
            $stmtItem = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, total_price)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach ($orderItemsToInsert as $oItem) {
                $stmtItem->execute([$orderId, $oItem['product_id'], $oItem['product_name'], $oItem['unit_price'], $oItem['quantity'], $oItem['total_price']]);

                // 4. Deduct Inventory Stock
                $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?")
                    ->execute([$oItem['quantity'], $oItem['product_id']]);
            }

            // 5. Record Payment Record
            $txnId = 'TXN-ORD-' . time() . '-' . rand(1000, 9999);
            $stmtPay = $pdo->prepare("
                INSERT INTO payments (reference_type, reference_id, amount, payment_method, transaction_id, status)
                VALUES ('order', ?, ?, ?, ?, 'Success')
            ");
            $stmtPay->execute([$orderId, $verifiedTotal, $paymentMethod, $txnId]);

            // 6. Clear Cart
            $cartId = get_or_create_cart_id();
            $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cartId]);

            $pdo->commit();

            header("Location: success.php?type=order&number=" . urlencode($orderNumber));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Order placement failed: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Order Checkout - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';
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

            <!-- Payment Options Selection -->
            <div class="form-group" style="margin-top: 25px; margin-bottom: 20px;">
              <label class="form-label" style="font-size: 1.05rem; margin-bottom: 12px; display:flex; align-items:center; gap:8px;">
                <span>💳</span> Select Payment Method *
              </label>
              
              <div class="payment-options-grid">
                <label class="payment-option-card active">
                  <input type="radio" name="payment_method" value="UPI / QR Code (GPay, PhonePe, Paytm)" checked>
                  <div class="payment-option-content">
                    <div class="payment-option-header">
                      <span class="payment-icon">📱</span>
                      <span class="payment-title">UPI / QR Code</span>
                      <span class="payment-badge">Fast</span>
                    </div>
                    <div class="payment-subtext">Google Pay, PhonePe, Paytm, BHIM</div>
                  </div>
                </label>

                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="Credit / Debit Card (Visa, MasterCard, RuPay)">
                  <div class="payment-option-content">
                    <div class="payment-option-header">
                      <span class="payment-icon">💳</span>
                      <span class="payment-title">Credit / Debit Card</span>
                    </div>
                    <div class="payment-subtext">Visa, MasterCard, RuPay, Maestro</div>
                  </div>
                </label>

                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="Net Banking (SBI, HDFC, ICICI, etc.)">
                  <div class="payment-option-content">
                    <div class="payment-option-header">
                      <span class="payment-icon">🏛️</span>
                      <span class="payment-title">Net Banking</span>
                    </div>
                    <div class="payment-subtext">SBI, HDFC, ICICI & 50+ Banks</div>
                  </div>
                </label>

                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="Digital Wallets (Paytm, Amazon Pay, Mobikwik)">
                  <div class="payment-option-content">
                    <div class="payment-option-header">
                      <span class="payment-icon">👛</span>
                      <span class="payment-title">Digital Wallets</span>
                    </div>
                    <div class="payment-subtext">Paytm Wallet, Mobikwik, Amazon Pay</div>
                  </div>
                </label>

                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="Cash on Delivery (COD)">
                  <div class="payment-option-content">
                    <div class="payment-option-header">
                      <span class="payment-icon">💵</span>
                      <span class="payment-title">Cash on Delivery</span>
                    </div>
                    <div class="payment-subtext">Pay upon physical product delivery</div>
                  </div>
                </label>
              </div>

              <!-- Dynamic Sub-fields -->
              <div class="payment-details-container" style="margin-top: 15px;">
                <div id="payment-field-upi" class="payment-subfield" style="display: block;">
                  <div class="qr-payment-wrapper">
                    <div class="qr-payment-container">
                      
                      <!-- Scanner Viewfinder Box -->
                      <div class="qr-scanner-outer">
                        <div class="qr-scanner-bracket bracket-tl"></div>
                        <div class="qr-scanner-bracket bracket-tr"></div>
                        <div class="qr-scanner-bracket bracket-bl"></div>
                        <div class="qr-scanner-bracket bracket-br"></div>
                        
                        <div class="qr-code-box">
                          <?php 
                          $upiId = get_setting('payment_upi_id', 'kamadhenugoushala@sbi');
                          $customQr = get_setting('payment_qr_code_image', '');
                          $siteName = get_setting('site_name', 'Kamadhenu Goushala');
                          if (!empty($customQr)): ?>
                            <img src="<?= $baseUrl . htmlspecialchars($customQr, ENT_QUOTES, 'UTF-8') ?>" alt="UPI QR Code" style="width: 140px; height: 140px; display: block; object-fit: contain;">
                          <?php else: 
                            $qrData = "upi://pay?pa=" . $upiId . "&pn=" . urlencode($siteName) . "&cu=INR";
                            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&margin=0&data=" . urlencode($qrData);
                          ?>
                            <img src="<?= $qrApiUrl ?>" alt="Scan to Pay" style="width: 140px; height: 140px; display: block; object-fit: contain;">
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Payment Information Column -->
                      <div class="qr-payment-info">
                        <div class="qr-payment-title">
                          <span>📲</span> Scan &amp; Pay Instant UPI
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">
                          Use GPay, PhonePe, Paytm, BHIM, or any banking app to scan this QR code.
                        </p>
                        
                        <ol class="qr-payment-steps">
                          <li>Open your preferred UPI or mobile banking app</li>
                          <li>Scan the QR code displayed on the left</li>
                          <li>Complete the payment of your order amount</li>
                        </ol>

                        <!-- VPA Block -->
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 10px;">
                          <span style="font-weight: 600; font-size: 0.85rem; color: var(--primary-dark);">UPI VPA:</span>
                          <code style="background: rgba(0,0,0,0.06); padding: 4px 10px; border-radius: 6px; font-weight: bold; color: var(--accent-orange); font-size: 0.9rem;"><?= htmlspecialchars($upiId) ?></code>
                          <button type="button" class="btn btn-secondary btn-sm" onclick="copyUpiId('<?= htmlspecialchars($upiId, ENT_QUOTES, 'UTF-8') ?>')" style="padding: 3px 10px; font-size: 0.75rem;">Copy 📋</button>
                        </div>

                        <div class="qr-merchant-tag">
                          Verified Merchant: <strong>Kamadhenu Gau Seva Trust</strong>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

                <div id="payment-field-card" class="payment-subfield" style="display: none;">
                  <div class="card-details-grid">
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label" style="font-size:0.85rem;">Card Number</label>
                      <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label" style="font-size:0.85rem;">Expiry (MM/YY)</label>
                      <input type="text" class="form-control" placeholder="12/28" maxlength="5">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label" style="font-size:0.85rem;">CVV</label>
                      <input type="password" class="form-control" placeholder="•••" maxlength="3">
                    </div>
                  </div>
                </div>

                <div id="payment-field-netbanking" class="payment-subfield" style="display: none;">
                  <label class="form-label" style="font-size:0.85rem;">Select Bank</label>
                  <select class="form-control">
                    <option>State Bank of India (SBI)</option>
                    <option>HDFC Bank</option>
                    <option>ICICI Bank</option>
                    <option>Axis Bank</option>
                    <option>Punjab National Bank (PNB)</option>
                    <option>Bank of Baroda</option>
                  </select>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Place Order & Pay <?= format_currency($cartData['total']) ?> 🎉</button>
          </div>
        </div>

      </div>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
