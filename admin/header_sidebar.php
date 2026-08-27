<?php
// admin/header_sidebar.php - Shared Sidebar & Admin Layout Header

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$pageTitle = $adminPageTitle ?? 'Admin Dashboard - Kamadhenu Goushala';
$currentScript = basename($_SERVER['SCRIPT_NAME']);

function navActive($page) {
    global $currentScript;
    return ($currentScript === $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <!-- Favicon / Tab Icon -->
  <?php 
    $baseUrlAdmin = get_base_url();
    $faviconImg = get_setting('site_favicon_image', get_setting('site_logo_image', '')); 
    $favIconEmoji = get_setting('site_logo_icon', '🐄');
  ?>
  <?php if (!empty($faviconImg)): ?>
    <link rel="icon" href="<?= $baseUrlAdmin . htmlspecialchars($faviconImg) ?>">
  <?php else: ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22><?= rawurlencode($favIconEmoji) ?></text></svg>">
  <?php endif; ?>

  <link rel="stylesheet" href="../css/style.css">
  <script>
    (function() {
      var savedTheme = localStorage.getItem('theme');
      if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
      }
    })();
  </script>
</head>
<body>

<div class="admin-layout">
  
  <!-- Admin Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-sidebar-header" style="display:flex; justify-content:space-between; align-items:center;">
      <a href="dashboard.php" style="color:white; display:flex; align-items:center; gap:10px;">
        <span style="font-size:1.8rem;">🐄</span>
        <div>
          <h3 style="color:white; font-size:1.1rem; margin:0;">Kamadhenu</h3>
          <span style="font-size:0.75rem; color:var(--accent-gold);">ADMIN PANEL</span>
        </div>
      </a>
      <button class="admin-mobile-nav-toggle" onclick="document.querySelector('.admin-nav').classList.toggle('active')" style="display:none; background:rgba(255,255,255,0.15); color:white; border:none; border-radius:6px; padding:6px 12px; font-size:1.2rem; cursor:pointer;" aria-label="Toggle Navigation">☰ Navigation</button>
    </div>

    <nav class="admin-nav">
      <a href="dashboard.php" class="admin-nav-item <?= navActive('dashboard.php') ?>">📊 Dashboard</a>
      <a href="cows.php" class="admin-nav-item <?= navActive('cows.php') ?>">🐄 Manage Cows</a>
      <a href="seva.php" class="admin-nav-item <?= navActive('seva.php') ?>">🙏 Seva Programs</a>
      <a href="products.php" class="admin-nav-item <?= navActive('products.php') ?>">📦 Products Store</a>
      <a href="donations.php" class="admin-nav-item <?= navActive('donations.php') ?>">💖 Donations</a>
      <a href="adoptions.php" class="admin-nav-item <?= navActive('adoptions.php') ?>">🤝 Cow Adoptions</a>
      <a href="orders.php" class="admin-nav-item <?= navActive('orders.php') ?>">🛍 Product Orders</a>
      <a href="gallery.php" class="admin-nav-item <?= navActive('gallery.php') ?>">🖼 Gallery Photos</a>
      <a href="videos.php" class="admin-nav-item <?= navActive('videos.php') ?>">📺 Manage Videos</a>
      <a href="events.php" class="admin-nav-item <?= navActive('events.php') ?>">📅 Manage Events</a>
      <a href="messages.php" class="admin-nav-item <?= navActive('messages.php') ?>">📩 Messages</a>
      <a href="users.php" class="admin-nav-item <?= navActive('users.php') ?>">👥 Manage Users</a>
      <a href="settings.php" class="admin-nav-item <?= navActive('settings.php') ?>">⚙️ Site Settings</a>
      <a href="../index.php" class="admin-nav-item" target="_blank" style="margin-top:20px; color:var(--accent-gold);">🌐 Public Website ↗</a>
      <a href="logout.php" class="admin-nav-item" style="color:#FF8A80;">🚪 Logout</a>
    </nav>
  </aside>

  <!-- Admin Main Content Area -->
  <main class="admin-main">
    <div class="admin-header">
      <div>
        <h2 style="margin:0; font-size:1.6rem;"><?= htmlspecialchars($adminPageTitle ?? 'Dashboard') ?></h2>
        <p style="margin:0; font-size:0.88rem; color:var(--text-muted);">Logged in as Administrator (<?= htmlspecialchars($_SESSION['user_name']) ?>)</p>
      </div>
      <div style="display:flex; align-items:center; gap:12px;">
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Dark Mode" title="Toggle Dark Mode">
          🌙
        </button>
        <a href="../index.php" class="btn btn-outline btn-sm" target="_blank">🌐 View Public Site</a>
      </div>
    </div>

    <!-- Flash Alerts -->
    <?php 
    $flashMessages = get_flash();
    if (!empty($flashMessages)): 
    ?>
      <div style="margin-top: 20px; margin-bottom: 5px;">
        <?php foreach ($flashMessages as $msg): ?>
          <div class="alert alert-<?= $msg['type'] === 'error' ? 'danger' : htmlspecialchars($msg['type']) ?>" style="padding: 12px 16px; border-radius: 8px; margin-bottom: 10px; font-weight: 500; display: flex; align-items: center; justify-content: space-between; background: <?= $msg['type'] === 'error' ? '#FFEBEE' : '#E8F5E9' ?>; color: <?= $msg['type'] === 'error' ? '#C62828' : '#2E7D32' ?>; border: 1px solid <?= $msg['type'] === 'error' ? '#FFCDD2' : '#C8E6C9' ?>;">
            <span><?= htmlspecialchars($msg['message']) ?></span>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: inherit; padding: 0 4px; line-height: 1;">&times;</button>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
