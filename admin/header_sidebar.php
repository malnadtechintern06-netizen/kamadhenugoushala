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
  <link rel="stylesheet" href="../css/style.css">
</head>
<body style="background: #F8F9FA;">

<div class="admin-layout">
  
  <!-- Admin Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-sidebar-header">
      <a href="dashboard.php" style="color:white; display:flex; align-items:center; gap:10px;">
        <span style="font-size:1.8rem;">🐄</span>
        <div>
          <h3 style="color:white; font-size:1.1rem; margin:0;">Kamadhenu</h3>
          <span style="font-size:0.75rem; color:var(--accent-gold);">ADMIN PANEL</span>
        </div>
      </a>
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
      <a href="events.php" class="admin-nav-item <?= navActive('events.php') ?>">📅 Manage Events</a>
      <a href="messages.php" class="admin-nav-item <?= navActive('messages.php') ?>">📩 Messages</a>
      <a href="users.php" class="admin-nav-item <?= navActive('users.php') ?>">👥 Manage Users</a>
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
      <div>
        <a href="../index.php" class="btn btn-outline btn-sm">View Public Site</a>
      </div>
    </div>
