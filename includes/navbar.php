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

<!-- Main Sticky Navbar -->
<header class="navbar-wrapper">
  <div class="container">
    <nav class="navbar">
      <!-- Logo -->
      <a href="<?= $baseUrl ?>index.php" class="logo-brand">
        <?php $customLogoImg = get_setting('site_logo_image', ''); ?>
        <?php if (!empty($customLogoImg)): ?>
          <img src="<?= $baseUrl . htmlspecialchars($customLogoImg) ?>" alt="Logo" style="height:48px; width:auto; max-width:140px; object-fit:contain;">
        <?php else: ?>
          <div class="logo-icon"><?= htmlspecialchars(get_setting('site_logo_icon', '🐄')) ?></div>
        <?php endif; ?>
        <div class="logo-text">
          <h2><?= htmlspecialchars(get_setting('site_name', 'Kamadhenu')) ?></h2>
          <p><?= htmlspecialchars(get_setting('site_logo_subtext', 'GOUSHALA SANCTUARY')) ?></p>
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
        <?php if (is_logged_in()): ?>
          <li><a href="<?= $baseUrl ?>pages/profile.php" class="nav-link <?= isActive('profile.php', 'pages') ?>">My Account</a></li>
        <?php else: ?>
          <li><a href="<?= $baseUrl ?>pages/login.php" class="nav-link <?= isActive('login.php', 'pages') ?>">Login</a></li>
        <?php endif; ?>
        <li class="mobile-only-action" style="margin-top: 15px; width: 100%;">
          <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-block text-center" style="display: block; width: 100%;">Donate Now 💖</a>
        </li>
      </ul>

      <!-- Navbar Actions (Cart & Donate CTA) -->
      <div class="nav-actions">
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Dark Mode" title="Toggle Dark Mode">
          🌙
        </button>
        <a href="<?= $baseUrl ?>pages/cart.php" class="cart-icon-btn" title="Shopping Cart">
          🛒
          <span class="cart-badge" style="<?= $cartCount > 0 ? 'display:flex;' : 'display:none;' ?>"><?= $cartCount ?></span>
        </a>

        <?php if (is_logged_in()): ?>
          <a href="<?= $baseUrl ?>pages/profile.php" class="user-account-btn" title="My Profile" style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:50px; background-color:var(--bg-light-green); color:var(--primary-dark); font-weight:600; text-decoration:none; font-size:0.9rem; transition:var(--transition-fast);">
            👤 <?= htmlspecialchars(mb_strimwidth($_SESSION['user_name'] ?? 'Account', 0, 10, '..')) ?>
          </a>
        <?php else: ?>
          <a href="<?= $baseUrl ?>pages/login.php" class="user-account-btn" title="Login / Register" style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:50px; background-color:var(--bg-light-green); color:var(--primary-dark); font-weight:600; text-decoration:none; font-size:0.9rem; transition:var(--transition-fast);">
            👤 Login
          </a>
        <?php endif; ?>

        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-sm">Donate Now</a>
        <button class="hamburger-btn" aria-label="Toggle Menu">☰</button>
      </div>
    </nav>
  </div>
</header>
<div class="nav-backdrop"></div>
