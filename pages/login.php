<?php
// pages/login.php - User Login & Logout Controller

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['role']);
    set_flash('success', 'You have logged out successfully.');
    header('Location: ' . get_base_url() . 'pages/login.php');
    exit;
}

if (is_logged_in()) {
    header('Location: profile.php');
    exit;
}

$pageTitle = 'Login - Kamadhenu Goushala';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security verification failed.';
    }

    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) $errors[] = 'Email Address is required.';
    if (empty($password)) $errors[] = 'Password is required.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? AND u.status = 'active'
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role_name'];

            set_flash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');

            if ($user['role_name'] === 'admin') {
                header('Location: ' . get_base_url() . 'admin/dashboard.php');
            } else {
                header('Location: profile.php');
            }
            exit;
        } else {
            $errors[] = 'Invalid email address or password.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
  <div class="container">
    <h1>Login To Your Account</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Login</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    <div class="form-card" style="max-width: 480px;">
      <h2 class="text-center" style="margin-bottom: 20px; color: var(--primary-dark);">Sign In</h2>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="name@example.com" required>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 15px;">Log In</button>
      </form>

      <div class="text-center" style="margin-top: 25px;">
        <p>Don't have an account yet? <a href="register.php" style="font-weight:bold;">Register Here</a></p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
