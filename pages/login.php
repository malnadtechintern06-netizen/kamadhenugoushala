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

            if (!empty($_SESSION['redirect_after_login'])) {
                $redirectUrl = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirectUrl);
            } else if ($user['role_name'] === 'admin') {
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
    <div class="form-card" style="max-width: 480px; margin: 0 auto; padding: 35px 30px; box-shadow: var(--shadow-md); border-radius: var(--radius-lg);">
      
      <!-- Top Avatar Badge -->
      <div class="text-center" style="margin-bottom: 25px;">
        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-dark), var(--primary-green)); color: var(--bg-white); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 12px; box-shadow: var(--shadow-sm);">
          👤
        </div>
        <h2 style="color: var(--primary-dark); font-size: 1.75rem; margin-bottom: 5px;">Welcome Back</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Sign in to manage your donations, adoptions & orders</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;">
          <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 20px;">
          <label class="form-label" style="font-weight: 600;">Email Address</label>
          <div style="position: relative;">
            <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 1.1rem; opacity: 0.7;">✉️</span>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="name@example.com" required style="padding-left: 45px;">
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 25px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <label class="form-label" style="font-weight: 600; margin-bottom: 0;">Password</label>
          </div>
          <div style="position: relative;">
            <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 1.1rem; opacity: 0.7;">🔒</span>
            <input type="password" id="user-password-input" name="password" class="form-control" placeholder="••••••••" required style="padding-left: 45px; padding-right: 45px;">
            <button type="button" id="toggle-password-btn" aria-label="Toggle Password Visibility" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.1rem; opacity: 0.7;">
              👁️
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block" style="font-weight: 600; font-size: 1.05rem; padding: 12px 0;">Log In to Account ➔</button>
      </form>

      <div style="border-top: 1px solid var(--border-light); margin-top: 25px; padding-top: 20px; text-align: center;">
        <p style="margin-bottom: 0; color: var(--text-dark);">
          Don't have an account yet? <a href="register.php" style="font-weight: bold; color: var(--accent-orange);">Register New Account</a>
        </p>
      </div>

    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const pwdInput = document.getElementById('user-password-input');
  const toggleBtn = document.getElementById('toggle-password-btn');
  if (pwdInput && toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      const type = pwdInput.getAttribute('type') === 'password' ? 'text' : 'password';
      pwdInput.setAttribute('type', type);
      toggleBtn.textContent = type === 'password' ? '👁️' : '🙈';
    });
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
