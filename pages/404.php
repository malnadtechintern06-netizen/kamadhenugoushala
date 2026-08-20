<?php
// pages/404.php - Custom 404 Error Page

$pageTitle = 'Page Not Found - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
  <div class="container">
    <h1>404 - Page Not Found</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>404 Error</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light text-center">
  <div class="container" style="max-width: 600px;">
    <div style="font-size: 5rem; color: var(--accent-orange); margin-bottom: 20px;">🐄</div>
    <h2 style="color: var(--primary-dark); margin-bottom: 15px;">Oops! Sacred Path Not Found</h2>
    <p style="color: var(--text-dark); margin-bottom: 30px; font-size: 1.1rem;">
      The page or record you were looking for does not exist or has been moved.
    </p>
    <a href="<?= $baseUrl ?>index.php" class="btn btn-primary btn-lg">Return to Home Page</a>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
