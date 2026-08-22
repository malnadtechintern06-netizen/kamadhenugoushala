<?php
// includes/header.php - Global Header Include

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? 'Kamadhenu Goushala - Love, Care & Seva for Gau Mata';
$baseUrl = get_base_url();
$cartCount = get_cart_count();
$flashMessages = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="Kamadhenu Goushala - Non-profit sanctuary dedicated to cow protection, Gau Seva, organic Panchagavya products, and cow adoption in India.">
  <meta name="keywords" content="Goushala, Cow Protection, Gau Seva, Desi Cow, A2 Ghee, Gir Cow, Cow Adoption, Donation">
  
  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🐄</text></svg>">
  
  <!-- Master Stylesheet -->
  <link rel="stylesheet" href="<?= $baseUrl ?>css/style.css?v=<?= time() ?>">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>

<!-- Flash Toast Notifications -->
<?php if (!empty($flashMessages)): ?>
  <div class="toast-container">
    <?php foreach ($flashMessages as $msg): ?>
      <div class="toast <?= htmlspecialchars($msg['type']) ?>">
        <span><?= $msg['type'] === 'success' ? '✓' : '⚠' ?></span>
        <span><?= htmlspecialchars($msg['message']) ?></span>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<main class="main-content">
