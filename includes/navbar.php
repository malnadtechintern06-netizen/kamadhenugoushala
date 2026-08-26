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
          <img src="<?= $baseUrl . htmlspecialchars($customLogoImg) ?>" alt="Logo" style="height:54px; width:auto; max-width:160px; object-fit:contain;">
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
        <li><a href="<?= $baseUrl ?>index.php" class="nav-link <?= isActive('index.php') ?>"><?= __('nav_home', 'Home') ?></a></li>
        <li><a href="<?= $baseUrl ?>pages/about.php" class="nav-link <?= isActive('about.php', 'pages') ?>"><?= __('nav_about', 'About') ?></a></li>
        <li><a href="<?= $baseUrl ?>pages/cows.php" class="nav-link <?= isActive('cows.php', 'pages') ?>"><?= __('nav_cows', 'Our Cows') ?></a></li>
        <li><a href="<?= $baseUrl ?>pages/seva.php" class="nav-link <?= isActive('seva.php', 'pages') ?>"><?= __('nav_seva', 'Seva') ?></a></li>
        <li><a href="<?= $baseUrl ?>pages/products.php" class="nav-link <?= isActive('products.php', 'pages') ?>"><?= __('nav_products', 'Products') ?></a></li>
        <li><a href="<?= $baseUrl ?>pages/events.php" class="nav-link <?= isActive('events.php', 'pages') ?>"><?= __('nav_events', 'Events') ?></a></li>
        <li><a href="<?= $baseUrl ?>pages/gallery.php" class="nav-link <?= isActive('gallery.php', 'pages') ?>"><?= __('nav_gallery', 'Gallery') ?></a></li>
        <li><a href="<?= $baseUrl ?>pages/contact.php" class="nav-link <?= isActive('contact.php', 'pages') ?>"><?= __('nav_contact', 'Contact') ?></a></li>
        <li class="mobile-only-action" style="margin-top: 20px; width: 100%;">
          <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-block text-center" style="display: block; width: 100%; padding: 14px; font-size: 1rem;"><?= __('btn_donate_now', 'Donate Now 💖') ?></a>
        </li>
      </ul>

      <!-- Navbar Actions (Cart & Donate CTA) -->
      <div class="nav-actions">
        <?php $currentLang = get_current_lang(); ?>
        <div class="custom-lang-switcher" title="Select Language">
          <span class="lang-icon">🌐</span>
          <select id="custom-lang-select" aria-label="Select Language">
            <option value="en" <?= $currentLang === 'en' ? 'selected' : '' ?>>English</option>
            <option value="hi" <?= $currentLang === 'hi' ? 'selected' : '' ?>>हिंदी</option>
            <option value="kn" <?= $currentLang === 'kn' ? 'selected' : '' ?>>ಕನ್ನಡ</option>
          </select>
        </div>
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Dark Mode" title="Toggle Dark Mode">
          🌙
        </button>
        <a href="<?= $baseUrl ?>pages/cart.php" class="cart-icon-btn" title="Shopping Cart">
          🛒
          <span class="cart-badge" style="<?= $cartCount > 0 ? 'display:flex;' : 'display:none;' ?>"><?= $cartCount ?></span>
        </a>

        <?php if (is_logged_in()): ?>
          <a href="<?= $baseUrl ?>pages/profile.php" class="user-account-btn" title="My Profile">
            👤 <?= htmlspecialchars(mb_strimwidth($_SESSION['user_name'] ?? __('nav_my_account', 'Account'), 0, 10, '..')) ?>
          </a>
        <?php else: ?>
          <a href="<?= $baseUrl ?>pages/login.php" class="user-account-btn" title="Login / Register">
            👤 <?= __('nav_login', 'Login') ?>
          </a>
        <?php endif; ?>

        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-sm"><?= __('nav_donate', 'Donate Now') ?></a>
        <button class="hamburger-btn" aria-label="Toggle Menu">☰</button>
      </div>
    </nav>
  </div>
</header>
<div class="nav-backdrop"></div>
