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
        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-sm">Donate Now</a>
        <button class="hamburger-btn" aria-label="Toggle Menu">☰</button>
      </div>
    </nav>
  </div>
</header>
