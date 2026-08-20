<?php
// pages/adopt.php - Cow Adoption Page

$pageTitle = 'Adopt a Cow - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

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

            // Payment simulation
            $stmtPay = $pdo->prepare("
                INSERT INTO payments (reference_type, reference_id, amount, payment_method, transaction_id, status)
                VALUES ('adoption', ?, ?, 'Online Gateway Simulation', ?, 'Success')
            ");
            $stmtPay->execute([$adoptionId, $totalAmount, $txnId]);

            $pdo->commit();

            header("Location: success.php?type=adoption&number=" . urlencode($adoptionNumber));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Adoption failed: ' . $e->getMessage();
        }
    }
}
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
