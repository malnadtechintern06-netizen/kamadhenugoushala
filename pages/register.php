<?php
// pages/register.php - User Registration

$pageTitle = 'User Registration - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

if (is_logged_in()) {
    header('Location: profile.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security verification failed.';
    }

    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');

    if (empty($fullName)) $errors[] = 'Full Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required.';
    if (empty($password) || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters long.';
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';

    // Check if email already registered
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetch()) {
            $errors[] = 'This email address is already registered. Please log in.';
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $roleId = 2; // User role

        $stmtInsert = $pdo->prepare("
            INSERT INTO users (role_id, full_name, email, phone, address, city, state, pincode, password_hash, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ");
        $stmtInsert->execute([$roleId, $fullName, $email, $phone, $address, $city, $state, $pincode, $passwordHash]);
        
        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['role'] = 'user';

        set_flash('success', 'Registration successful! Welcome to Kamadhenu Goushala.');
        header('Location: profile.php');
        exit;
    }
}
?>

<div class="page-banner">
  <div class="container">
    <h1>Create Your Account</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Register</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    <div class="form-card">
      <h2 class="text-center" style="margin-bottom: 20px; color: var(--primary-dark);">Register</h2>
      
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="register.php">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password * (Min 6 chars)</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">State</label>
            <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($_POST['state'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($_POST['pincode'] ?? '') ?>">
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 10px;">Register Account</button>
      </form>

      <div class="text-center" style="margin-top: 20px;">
        <p>Already have an account? <a href="login.php" style="font-weight:bold;">Log In Here</a></p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
