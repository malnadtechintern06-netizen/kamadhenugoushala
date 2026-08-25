<?php
// pages/adopt.php - Cow Adoption Page

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login('Please log in or create an account to adopt a cow.');

$selectedCowId = (int)($_GET['cow_id'] ?? 0);
$allCows = $pdo->query("SELECT id, tag_number, name, breed, monthly_adoption_fee, main_image FROM cows ORDER BY name ASC")->fetchAll();
$currentUser = get_current_user_data();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please resubmit.';
    }

    $cowId = (int)($_POST['cow_id'] ?? 0);
    $adopterName = sanitize($_POST['adopter_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $duration = (int)($_POST['duration_months'] ?? 1);
    $message = sanitize($_POST['message'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'UPI / QR Code (GPay, PhonePe, Paytm)');

    // Fetch cow details to verify fee
    $stmtC = $pdo->prepare("SELECT id, name, monthly_adoption_fee FROM cows WHERE id = ?");
    $stmtC->execute([$cowId]);
    $targetCow = $stmtC->fetch();

    if (!$targetCow) $errors[] = 'Please select a valid cow for adoption.';
    if (empty($adopterName)) $errors[] = 'Adopter Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email Address is required.';
    if (empty($phone)) $errors[] = 'Phone Number is required.';
    if (empty($address)) $errors[] = 'Full Address is required.';
    if ($duration < 1 || $duration > 24) $errors[] = 'Duration must be between 1 and 24 months.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $monthlyFee = (float)$targetCow['monthly_adoption_fee'];
            $totalAmount = $monthlyFee * $duration;
            $adoptionNumber = 'KG-ADP-' . strtoupper(bin2hex(random_bytes(4)));
            $userId = $_SESSION['user_id'] ?? null;
            $txnId = 'TXN-ADP-' . time() . '-' . rand(1000, 9999);

            $stmtAdp = $pdo->prepare("
                INSERT INTO adoptions (adoption_number, cow_id, user_id, adopter_name, email, phone, address, duration_months, monthly_amount, total_amount, message, payment_status, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Completed', 'Active')
            ");
            $stmtAdp->execute([$adoptionNumber, $cowId, $userId, $adopterName, $email, $phone, $address, $duration, $monthlyFee, $totalAmount, $message]);
            $adoptionId = $pdo->lastInsertId();

            // Update cow status
            $pdo->prepare("UPDATE cows SET adoption_status = 'Adopted' WHERE id = ?")->execute([$cowId]);

            // Payment record
            $stmtPay = $pdo->prepare("
                INSERT INTO payments (reference_type, reference_id, amount, payment_method, transaction_id, status)
                VALUES ('adoption', ?, ?, ?, ?, 'Success')
            ");
            $stmtPay->execute([$adoptionId, $totalAmount, $paymentMethod, $txnId]);

            $pdo->commit();

            header("Location: success.php?type=adoption&number=" . urlencode($adoptionNumber));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Adoption failed: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Adopt a Cow - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
  <div class="container">
    <h1>Adopt a Gau Mata</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <a href="cows.php">Our Cows</a> / <span>Adopt</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    <div style="max-width: 800px; margin: 0 auto;">
      
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="form-card" style="max-width: 100%;">
        <div class="text-center" style="margin-bottom: 30px;">
          <span class="section-subtitle">LIFELONG PATRONAGE</span>
          <h2 style="color:var(--primary-dark);">Become a Cow Guardian</h2>
          <p style="color:var(--text-muted);">Sponsor monthly food, medicine, and care. Receive monthly health updates and video clips of your adopted cow.</p>
        <!-- WhatsApp Direct Adopt Banner -->
        <div style="background:#E8F5E9; border: 2px dashed #25D366; padding: 18px; border-radius: var(--radius-md); margin-bottom: 25px; text-align: center;">
          <span style="font-weight: 700; color: #1B5E20; font-size: 1.05rem;">💬 Prefer Quick Adoption via WhatsApp?</span>
          <p style="font-size: 0.88rem; color: #2E7D32; margin: 4px 0 12px 0;">Connect directly with our sanctuary manager on WhatsApp to confirm your cow adoption details instantly.</p>
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', get_setting('contact_phone', '919876543210')) ?>?text=<?= urlencode('Jai Shree Krishna! 🙏 I want to adopt a cow from Kamadhenu Goushala. Please assist me with the adoption details.') ?>" target="_blank" class="btn btn-primary btn-sm" style="background:#25D366; border-color:#25D366; color:white; padding: 8px 20px; font-weight: 700; display:inline-flex; align-items:center; justify-content:center; gap:3px;">
            <?= get_whatsapp_icon_svg() ?> Chat &amp; Adopt on WhatsApp
          </a>
        </div>

        <form method="POST" action="adopt.php">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label">Select Cow To Adopt *</label>
            <select name="cow_id" id="cow-select" class="form-control" required style="font-size:1.05rem; font-weight:bold;">
              <option value="">-- Choose a Cow --</option>
              <?php foreach ($allCows as $c): ?>
                <option value="<?= $c['id'] ?>" data-fee="<?= $c['monthly_adoption_fee'] ?>" <?= $selectedCowId === $c['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['breed']) ?> - Tag #<?= htmlspecialchars($c['tag_number']) ?>) - <?= format_currency($c['monthly_adoption_fee']) ?>/mo
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Adoption Duration (Months) *</label>
            <select name="duration_months" id="duration-select" class="form-control" required>
              <option value="1">1 Month</option>
              <option value="3">3 Months</option>
              <option value="6">6 Months (Popular)</option>
              <option value="12">1 Year (12 Months - Preferred)</option>
            </select>
          </div>

          <div style="background:var(--bg-light-green); padding:20px; border-radius:var(--radius-md); margin-bottom:25px; border:1px solid var(--border-light);">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="font-size:1.1rem; font-weight:600;">Total Support Amount:</span>
              <span id="calculated-total" style="font-size:1.8rem; font-weight:bold; color:var(--accent-orange);">₹0.00</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Your Full Name *</label>
              <input type="text" name="adopter_name" class="form-control" value="<?= htmlspecialchars($_POST['adopter_name'] ?? ($currentUser['full_name'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email Address *</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? ($currentUser['email'] ?? '')) ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Phone Number *</label>
              <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? ($currentUser['phone'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Address *</label>
              <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? ($currentUser['address'] ?? '')) ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Special Message / Note (Optional)</label>
            <textarea name="message" class="form-control" rows="3" placeholder="Add a note or occasion (e.g. Birthday, Anniversary)..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>

          <!-- Payment Options Selection -->
          <div class="form-group" style="margin-top: 25px;">
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
                <input type="radio" name="payment_method" value="Direct Bank Transfer (NEFT / RTGS)">
                <div class="payment-option-content">
                  <div class="payment-option-header">
                    <span class="payment-icon">🏦</span>
                    <span class="payment-title">Bank NEFT / RTGS</span>
                  </div>
                  <div class="payment-subtext">Direct Goushala Account Transfer</div>
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
                        <li>Complete the payment of your adoption fee</li>
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

              <div id="payment-field-bank" class="payment-subfield" style="display: none;">
                <div class="bank-transfer-details">
                  <div class="bank-detail-item">
                    <span class="bank-detail-label">Beneficiary Name</span>
                    <span class="bank-detail-value">Kamadhenu Goushala Trust</span>
                  </div>
                  <div class="bank-detail-item">
                    <span class="bank-detail-label">Bank &amp; Branch</span>
                    <span class="bank-detail-value">State Bank of India (Vrindavan)</span>
                  </div>
                  <div class="bank-detail-item">
                    <span class="bank-detail-label">Account Number</span>
                    <span class="bank-detail-value" style="font-family: monospace; font-size: 1rem;">398745612301</span>
                  </div>
                  <div class="bank-detail-item">
                    <span class="bank-detail-label">IFSC Code</span>
                    <span class="bank-detail-value" style="font-family: monospace; font-size: 1rem; color: var(--accent-orange);">SBIN0001234</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-lg btn-block">Confirm Adoption & Pay 🐄</button>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const cowSelect = document.getElementById('cow-select');
  const durationSelect = document.getElementById('duration-select');
  const totalDisplay = document.getElementById('calculated-total');

  function updateAdoptionTotal() {
    const selectedOption = cowSelect.options[cowSelect.selectedIndex];
    const fee = parseFloat(selectedOption ? selectedOption.dataset.fee : 0) || 0;
    const months = parseInt(durationSelect.value, 10) || 1;
    const total = fee * months;
    totalDisplay.textContent = '₹' + total.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  cowSelect.addEventListener('change', updateAdoptionTotal);
  durationSelect.addEventListener('change', updateAdoptionTotal);
  updateAdoptionTotal();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
