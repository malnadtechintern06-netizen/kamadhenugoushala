<?php
// includes/navbar.php - Top Bar & Sticky Navigation

$scriptName = basename($_SERVER['SCRIPT_NAME']);
$currentDir = basename(dirname($_SERVER['SCRIPT_NAME']));

function isActive($page, $dir = '') {
    global $scriptName, $currentDir;
    if ($dir && $currentDir !== $dir) return '';
    return ($scriptName === $page) ? 'active' : '';
}
?>
<!-- Top Info Bar -->
<div class="top-bar">
  <div class="container">
    <div class="top-info">
      <span>📞 Helpline: <?= htmlspecialchars(get_setting('contact_phone', '+91 98765 43210')) ?></span>
      <span>📧 <?= htmlspecialchars(get_setting('contact_email', 'info@kamadhenugoushala.org')) ?></span>
      <span>📍 Vrindavan Dham, Mathura, India</span>
    </div>
    <div class="top-links">
      <?php if (is_logged_in()): ?>
        <a href="<?= $baseUrl ?>pages/profile.php">👤 My Profile (<?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>)</a>
        <a href="<?= $baseUrl ?>pages/login.php?action=logout">Logout</a>
      <?php else: ?>
        <a href="<?= $baseUrl ?>pages/login.php">Login</a>
        <a href="<?= $baseUrl ?>pages/register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Main Sticky Navbar -->
<header class="navbar-wrapper">
  <div class="container">
    <nav class="navbar">
      <!-- Logo -->
      <a href="<?= $baseUrl ?>index.php" class="logo-brand">
        <div class="logo-icon">🐄</div>
        <div class="logo-text">
          <h2>Kamadhenu</h2>
          <p>GOUSHALA SANCTUARY</p>
        </div>
      </a>

      <!-- Desktop & Mobile Navigation Menu -->
      <ul class="nav-menu">
        <li><a href="<?= $baseUrl ?>index.php" class="nav-link <?= isActive('index.php') ?>">Home</a></li>
        <li><a href="<?= $baseUrl ?>pages/about.php" class="nav-link <?= isActive('about.php', 'pages') ?>">About</a></li>
        <li><a href="<?= $baseUrl ?>pages/cows.php" class="nav-link <?= isActive('cows.php', 'pages') ?>">Our Cows</a></li>
        <li><a href="<?= $baseUrl ?>pages/seva.php" class="nav-link <?= isActive('seva.php', 'pages') ?>">Seva</a></li>
        <li><a href="<?= $baseUrl ?>pages/products.php" class="nav-link <?= isActive('products.php', 'pages') ?>">Products</a></li>
        <li><a href="<?= $baseUrl ?>pages/events.php" class="nav-link <?= isActive('events.php', 'pages') ?>">Events</a></li>
        <li><a href="<?= $baseUrl ?>pages/gallery.php" class="nav-link <?= isActive('gallery.php', 'pages') ?>">Gallery</a></li>
        <li><a href="<?= $baseUrl ?>pages/contact.php" class="nav-link <?= isActive('contact.php', 'pages') ?>">Contact</a></li>
      </ul>

      <!-- Navbar Actions (Cart & Donate CTA) -->
      <div class="nav-actions">
        <a href="<?= $baseUrl ?>pages/cart.php" class="cart-icon-btn" title="Shopping Cart">
          🛒
          <span class="cart-badge" style="<?= $cartCount > 0 ? 'display:flex;' : 'display:none;' ?>"><?= $cartCount ?></span>
        </a>
        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-sm">Donate Now</a>
        <button class="hamburger-btn" aria-label="Toggle Menu">☰</button>
      </div>
    </nav>
  </div>
</header>
