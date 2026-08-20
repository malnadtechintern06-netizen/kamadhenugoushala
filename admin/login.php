<?php
// admin/login.php - Admin Portal Authentication

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_admin()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    }

    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? AND r.name = 'admin' AND u.status = 'active'
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = 'admin';

            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid admin credentials or unauthorized account.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Kamadhenu Goushala</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { background-color: var(--primary-dark); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  </style>
</head>
<body>

<div class="form-card" style="width: 100%; max-width: 440px; box-shadow: var(--shadow-lg);">
  <div class="text-center" style="margin-bottom: 25px;">
    <div style="font-size: 3rem;">🐄</div>
    <h2 style="color: var(--primary-dark); margin-bottom: 5px;">Admin Portal</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Kamadhenu Goushala Management</p>
  </div>

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
      <label class="form-label">Admin Email</label>
      <input type="email" name="email" class="form-control" placeholder="admin@kamadhenugoushala.org" required value="admin@kamadhenugoushala.org">
    </div>

    <div class="form-group">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" placeholder="••••••••" required value="Admin@12345">
      <small style="color:var(--text-muted); display:block; margin-top:4px;">Default Demo Credentials: admin@kamadhenugoushala.org / Admin@12345</small>
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 15px;">Login To Dashboard ⚙</button>
  </form>

  <div class="text-center" style="margin-top: 20px;">
    <a href="../index.php" style="font-size: 0.88rem; color: var(--text-muted);">← Return to Public Website</a>
  </div>
</div>

</body>
</html>
