<?php
// pages/donate.php - Online Donation Form

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$presetAmount = (int)($_GET['amount'] ?? 501);
$selectedPurpose = sanitize($_GET['purpose'] ?? 'General Gau Seva');
$currentUser = get_current_user_data();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please resubmit.';
    }

    $donorName = sanitize($_POST['donor_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $purpose = sanitize($_POST['purpose'] ?? 'General Gau Seva');
    $message = sanitize($_POST['message'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'UPI / QR Code (GPay, PhonePe, Paytm)');

    if (empty($donorName)) $errors[] = 'Donor Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email Address is required.';
    if (empty($phone)) $errors[] = 'Phone Number is required.';
    if ($amount <= 0) $errors[] = 'Donation Amount must be greater than zero.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $donationNumber = 'KG-DON-' . strtoupper(bin2hex(random_bytes(4)));
            $userId = $_SESSION['user_id'] ?? null;
            $txnId = 'TXN-DON-' . time() . '-' . rand(1000, 9999);

            $stmtDon = $pdo->prepare("
                INSERT INTO donations (donation_number, user_id, donor_name, email, phone, amount, purpose, message, payment_status, transaction_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Completed', ?)
            ");
            $stmtDon->execute([$donationNumber, $userId, $donorName, $email, $phone, $amount, $purpose, $message, $txnId]);
            $donationId = $pdo->lastInsertId();

            $stmtPay = $pdo->prepare("
                INSERT INTO payments (reference_type, reference_id, amount, payment_method, transaction_id, status)
                VALUES ('donation', ?, ?, ?, ?, 'Success')
            ");
            $stmtPay->execute([$donationId, $amount, $paymentMethod, $txnId]);

            $pdo->commit();

            header("Location: success.php?type=donation&number=" . urlencode($donationNumber));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Donation processing failed: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Donate Now - Support Gau Seva at Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
  <div class="container">
    <h1>Make a Sacred Donation</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Donate</span>
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
          <span class="section-subtitle">80G TAX EXEMPTED</span>
          <h2 style="color:var(--primary-dark);">Offer Your Seva For Gau Mata</h2>
        <?php $isDonWA = (get_checkout_mode('donation') === 'whatsapp'); ?>
        <?php if ($isDonWA): ?>
          <div style="background:#E8F5E9; border: 2px dashed #25D366; padding: 20px; border-radius: var(--radius-md); margin-bottom: 25px; text-align: center;">
            <span style="font-weight: 700; color: #1B5E20; font-size: 1.1rem;">💬 Direct WhatsApp Donation Seva Active</span>
            <p style="font-size: 0.9rem; color: #2E7D32; margin: 6px 0 15px 0;">Admin setting is configured to WhatsApp Donation Mode. Connect directly with our sanctuary manager on WhatsApp to offer your donation and receive an 80G tax receipt.</p>
            <a href="<?= get_whatsapp_donation_url($selectedPurpose) ?>" target="_blank" class="btn btn-primary btn-lg" style="background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:4px;">
              <?= get_whatsapp_icon_svg('1.2em') ?> Offer Donation on WhatsApp 📱
            </a>
          </div>
        <?php endif; ?>

        <form method="POST" action="donate.php">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label">Select Contribution Amount (INR ₹) *</label>
            <div class="donation-presets">
              <button type="button" class="preset-btn <?= $presetAmount === 101 ? 'active' : '' ?>" data-amount="101">₹101</button>
              <button type="button" class="preset-btn <?= $presetAmount === 501 ? 'active' : '' ?>" data-amount="501">₹501</button>
              <button type="button" class="preset-btn <?= $presetAmount === 1001 ? 'active' : '' ?>" data-amount="1001">₹1,001</button>
              <button type="button" class="preset-btn <?= $presetAmount === 2501 ? 'active' : '' ?>" data-amount="2501">₹2,501</button>
              <button type="button" class="preset-btn <?= $presetAmount === 5001 ? 'active' : '' ?>" data-amount="5001">₹5,001</button>
            </div>
            <input type="number" id="custom-amount" name="amount" class="form-control" placeholder="Or enter custom amount in ₹" value="<?= $presetAmount ?>" min="1" required style="font-size:1.2rem; font-weight:bold; color:var(--primary-dark);">
          </div>

          <div class="form-group">
            <label class="form-label">Seva Purpose / Program *</label>
            <select name="purpose" class="form-control" required>
              <option value="General Gau Seva" <?= $selectedPurpose === 'General Gau Seva' ? 'selected' : '' ?>>General Gau Seva & Sanctuary Maintenance</option>
              <option value="Gau Grass & Fodder Seva" <?= $selectedPurpose === 'Gau Grass & Fodder Seva' ? 'selected' : '' ?>>Gau Grass & Green Fodder Seva</option>
              <option value="Medical Treatment & Healthcare" <?= $selectedPurpose === 'Medical Treatment & Healthcare' ? 'selected' : '' ?>>Emergency Veterinary Treatment & Medicine</option>
              <option value="Nitya Gau Seva (Daily Care)" <?= $selectedPurpose === 'Nitya Gau Seva (Daily Care)' ? 'selected' : '' ?>>Nitya Gau Seva (Daily Grooming & Water)</option>
              <option value="Goushala Shelter Construction" <?= $selectedPurpose === 'Goushala Shelter Construction' ? 'selected' : '' ?>>Shelter Shed Construction Fund</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Full Name *</label>
              <input type="text" name="donor_name" class="form-control" value="<?= htmlspecialchars($_POST['donor_name'] ?? ($currentUser['full_name'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email Address *</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? ($currentUser['email'] ?? '')) ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Phone Number *</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? ($currentUser['phone'] ?? '')) ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label">Message / Prayer Note (Optional)</label>
            <textarea name="message" class="form-control" rows="3" placeholder="Add a prayer note or dedication for your donation..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
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
                <div class="qr-payment-card" style="background: var(--bg-light-green); border: 2px dashed var(--accent-orange); padding: 20px; border-radius: var(--radius-md); text-align: center;">
                  <div style="font-weight: 700; font-size: 1.05rem; color: var(--primary-dark); margin-bottom: 4px;">
                    📲 Scan QR Code To Pay (GPay / PhonePe / Paytm / BHIM)
                  </div>
                  <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 15px;">
                    Scan with any UPI Scanner app or copy the official Goushala VPA below
                  </div>

                  <!-- Visual QR Code Badge -->
                  <div class="qr-code-box" style="display: inline-block; background: white; padding: 16px; border-radius: 16px; box-shadow: var(--shadow-md); border: 3px solid var(--primary-dark); position: relative;">
                    <svg width="160" height="160" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                      <rect width="200" height="200" fill="#ffffff" rx="10"/>
                      <!-- Top Left Marker -->
                      <rect x="15" y="15" width="45" height="45" fill="#84418e" rx="6"/>
                      <rect x="23" y="23" width="29" height="29" fill="#ffffff" rx="3"/>
                      <rect x="29" y="29" width="17" height="17" fill="#84418e" rx="2"/>
                      <!-- Top Right Marker -->
                      <rect x="140" y="15" width="45" height="45" fill="#84418e" rx="6"/>
                      <rect x="148" y="23" width="29" height="29" fill="#ffffff" rx="3"/>
                      <rect x="154" y="29" width="17" height="17" fill="#84418e" rx="2"/>
                      <!-- Bottom Left Marker -->
                      <rect x="15" y="140" width="45" height="45" fill="#84418e" rx="6"/>
                      <rect x="23" y="148" width="29" height="29" fill="#ffffff" rx="3"/>
                      <rect x="29" y="154" width="17" height="17" fill="#84418e" rx="2"/>
                      <!-- QR Data Grid Pattern -->
                      <g fill="#2B121A">
                        <rect x="70" y="15" width="9" height="9" rx="2"/>
                        <rect x="86" y="15" width="9" height="9" rx="2"/>
                        <rect x="102" y="15" width="9" height="9" rx="2"/>
                        <rect x="118" y="15" width="9" height="9" rx="2"/>
                        <rect x="70" y="31" width="9" height="9" rx="2"/>
                        <rect x="94" y="31" width="9" height="9" rx="2"/>
                        <rect x="110" y="31" width="9" height="9" rx="2"/>
                        <rect x="126" y="31" width="9" height="9" rx="2"/>
                        <rect x="78" y="47" width="9" height="9" rx="2"/>
                        <rect x="94" y="47" width="9" height="9" rx="2"/>
                        <rect x="118" y="47" width="9" height="9" rx="2"/>
                        <rect x="15" y="70" width="9" height="9" rx="2"/>
                        <rect x="31" y="70" width="9" height="9" rx="2"/>
                        <rect x="47" y="70" width="9" height="9" rx="2"/>
                        <rect x="140" y="70" width="9" height="9" rx="2"/>
                        <rect x="156" y="70" width="9" height="9" rx="2"/>
                        <rect x="172" y="70" width="9" height="9" rx="2"/>
                        <rect x="15" y="86" width="9" height="9" rx="2"/>
                        <rect x="39" y="86" width="9" height="9" rx="2"/>
                        <rect x="55" y="86" width="9" height="9" rx="2"/>
                        <rect x="148" y="86" width="9" height="9" rx="2"/>
                        <rect x="164" y="86" width="9" height="9" rx="2"/>
                        <rect x="23" y="102" width="9" height="9" rx="2"/>
                        <rect x="47" y="102" width="9" height="9" rx="2"/>
                        <rect x="140" y="102" width="9" height="9" rx="2"/>
                        <rect x="172" y="102" width="9" height="9" rx="2"/>
                        <rect x="15" y="118" width="9" height="9" rx="2"/>
                        <rect x="31" y="118" width="9" height="9" rx="2"/>
                        <rect x="55" y="118" width="9" height="9" rx="2"/>
                        <rect x="148" y="118" width="9" height="9" rx="2"/>
                        <rect x="164" y="118" width="9" height="9" rx="2"/>
                        <rect x="70" y="140" width="9" height="9" rx="2"/>
                        <rect x="94" y="140" width="9" height="9" rx="2"/>
                        <rect x="110" y="140" width="9" height="9" rx="2"/>
                        <rect x="126" y="140" width="9" height="9" rx="2"/>
                        <rect x="140" y="140" width="9" height="9" rx="2"/>
                        <rect x="156" y="140" width="9" height="9" rx="2"/>
                        <rect x="172" y="140" width="9" height="9" rx="2"/>
                        <rect x="78" y="156" width="9" height="9" rx="2"/>
                        <rect x="102" y="156" width="9" height="9" rx="2"/>
                        <rect x="118" y="156" width="9" height="9" rx="2"/>
                        <rect x="148" y="156" width="9" height="9" rx="2"/>
                        <rect x="164" y="156" width="9" height="9" rx="2"/>
                        <rect x="70" y="172" width="9" height="9" rx="2"/>
                        <rect x="86" y="172" width="9" height="9" rx="2"/>
                        <rect x="110" y="172" width="9" height="9" rx="2"/>
                        <rect x="126" y="172" width="9" height="9" rx="2"/>
                        <rect x="140" y="172" width="9" height="9" rx="2"/>
                        <rect x="172" y="172" width="9" height="9" rx="2"/>
                      </g>
                      <!-- Center Gau Emblem -->
                      <circle cx="100" cy="100" r="22" fill="#ffffff" stroke="#D9A441" stroke-width="2"/>
                      <text x="100" y="106" font-size="18" text-anchor="middle">🐄</text>
                    </svg>
                  </div>

                  <!-- UPI VPA and Copy Button -->
                  <div style="margin-top: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-weight: 600; font-size: 0.9rem; color: var(--primary-dark);">Official UPI VPA:</span>
                    <code style="background: rgba(0,0,0,0.06); padding: 4px 12px; border-radius: 6px; font-weight: bold; color: var(--accent-orange); font-size: 1rem;">kamadhenugoushala@sbi</code>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="copyUpiId()" style="padding: 4px 12px; font-size: 0.8rem;">Copy 📋</button>
                  </div>
                  <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 6px;">
                    Verified Merchant: <strong>Kamadhenu Gau Seva Trust</strong> (Instant 80G Receipt)
                  </div>
                </div>
              </div>

              <div id="payment-field-card" class="payment-subfield" style="display: none;">
                <div class="form-row">
                  <div class="form-group" style="flex:2;">
                    <label class="form-label" style="font-size:0.85rem;">Card Number</label>
                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                  </div>
                  <div class="form-group" style="flex:1;">
                    <label class="form-label" style="font-size:0.85rem;">Expiry (MM/YY)</label>
                    <input type="text" class="form-control" placeholder="12/28" maxlength="5">
                  </div>
                  <div class="form-group" style="flex:1;">
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
                <div style="background: var(--bg-light-green); padding: 15px; border-radius: var(--radius-sm); font-size: 0.9rem;">
                  <div><strong>Account Name:</strong> Kamadhenu Goushala Trust</div>
                  <div><strong>Bank Name:</strong> State Bank of India (Vrindavan Branch)</div>
                  <div><strong>Account No:</strong> 398745612301</div>
                  <div><strong>IFSC Code:</strong> SBIN0001234</div>
                </div>
              </div>
            </div>
          </div>

          <div style="background:var(--bg-light-green); padding:15px; border-radius:var(--radius-sm); margin-bottom:20px; font-size:0.88rem;">
            🛡 <strong>Tax Benefit:</strong> 80G Certificate will be emailed automatically to your registered address upon successful verification.
          </div>

          <button type="submit" class="btn btn-primary btn-lg btn-block">Complete Donation & Proceed 💖</button>
        </form>
      </div>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
