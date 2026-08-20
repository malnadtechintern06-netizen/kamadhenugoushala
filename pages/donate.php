<?php
// pages/donate.php - Online Donation Form

$pageTitle = 'Donate Now - Support Gau Seva at Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

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
                VALUES ('donation', ?, ?, 'Online Gateway Simulation', ?, 'Success')
            ");
            $stmtPay->execute([$donationId, $amount, $txnId]);

            $pdo->commit();

            header("Location: success.php?type=donation&number=" . urlencode($donationNumber));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Donation processing failed: ' . $e->getMessage();
        }
    }
}
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
          <p style="color:var(--text-muted);">Your contribution provides fresh green fodder, veterinary ICU care, and roofed shelter.</p>
        </div>

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

          <div style="background:var(--bg-light-green); padding:15px; border-radius:var(--radius-sm); margin-bottom:20px; font-size:0.88rem;">
            🛡 <strong>Tax Benefit:</strong> 80G Certificate will be emailed automatically to your registered address upon successful verification.
          </div>

          <button type="submit" class="btn btn-primary btn-lg btn-block">Complete Donation & Proceed to Payment 💖</button>
        </form>
      </div>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
