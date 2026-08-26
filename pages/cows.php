<?php
// pages/cows.php - Cows Catalog with Search & Filter

$pageTitle = 'Our Cows - Kamadhenu Goushala Sanctuary';
require_once __DIR__ . '/../includes/header.php';

// Filtering parameters
$search = sanitize($_GET['search'] ?? '');
$breedFilter = sanitize($_GET['breed'] ?? '');
$statusFilter = sanitize($_GET['status'] ?? '');

// Build dynamic query
$query = "SELECT * FROM cows WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR tag_number LIKE ? OR breed LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($breedFilter)) {
    $query .= " AND breed = ?";
    $params[] = $breedFilter;
}

if (!empty($statusFilter)) {
    $query .= " AND health_status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY id ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cows = $stmt->fetchAll();

// Get unique breeds & statuses for filter dropdowns
$breeds = $pdo->query("SELECT DISTINCT breed FROM cows ORDER BY breed ASC")->fetchAll(PDO::FETCH_COLUMN);
$statuses = $pdo->query("SELECT DISTINCT health_status FROM cows ORDER BY health_status ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="page-banner">
  <div class="container">
    <h1><?= __('banner_cows', 'Our Beloved Cows') ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <span><?= __('nav_cows', 'Our Cows') ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <!-- Search & Filter Controls -->
    <form method="GET" action="cows.php" class="filter-bar">
      <div class="search-input-group" style="flex: 2;">
        <input type="text" name="search" class="form-control" placeholder="<?= __('search_cows_ph', 'Search by Cow Name, Tag #, or Breed...') ?>" value="<?= htmlspecialchars($search) ?>">
      </div>

      <div style="flex: 1; min-width: 160px;">
        <select name="breed" class="form-control" onchange="this.form.submit()">
          <option value=""><?= __('all_breeds', 'All Breeds') ?></option>
          <?php foreach ($breeds as $b): ?>
            <option value="<?= htmlspecialchars($b) ?>" <?= $breedFilter === $b ? 'selected' : '' ?>><?= htmlspecialchars(__($b, $b)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="flex: 1; min-width: 160px;">
        <select name="status" class="form-control" onchange="this.form.submit()">
          <option value=""><?= __('all_statuses', 'All Health Statuses') ?></option>
          <?php foreach ($statuses as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= htmlspecialchars(__($s, $s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <button type="submit" class="btn btn-secondary"><?= __('btn_filter', 'Filter') ?></button>
        <?php if (!empty($search) || !empty($breedFilter) || !empty($statusFilter)): ?>
          <a href="cows.php" class="btn btn-outline"><?= __('btn_reset', 'Reset') ?></a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Cards Grid -->
    <?php if (count($cows) > 0): ?>
      <div class="card-grid">
        <?php foreach ($cows as $cow): ?>
          <div class="card">
            <div class="card-img-wrapper">
              <img src="<?= $baseUrl . htmlspecialchars($cow['main_image']) ?>" alt="<?= htmlspecialchars($cow['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1570042707223-2cb2ed999557?auto=format&fit=crop&w=600&q=80'">
              <span class="card-badge status-<?= strtolower(str_replace(' ', '-', $cow['health_status'])) ?>">
                <?= htmlspecialchars(__($cow['health_status'], $cow['health_status'])) ?>
              </span>
            </div>
            <div class="card-body">
              <h3 class="card-title"><?= htmlspecialchars($cow['name']) ?></h3>
              <p class="card-subtitle">
                <?= __('breed_label', 'Breed') ?>: <strong><?= htmlspecialchars(__($cow['breed'], $cow['breed'])) ?></strong> | <?= __('age_label', 'Age') ?>: <?= $cow['age_years'] ?> <?= __('years_label', 'Yrs') ?> (<?= htmlspecialchars(__($cow['gender'], $cow['gender'])) ?>)
              </p>
              <p class="card-text"><?= htmlspecialchars(mb_strimwidth($cow['bio'], 0, 110, '...')) ?></p>
              <div class="card-meta">
                <span><?= __('tag_label', 'Tag:') ?> <strong><?= htmlspecialchars($cow['tag_number']) ?></strong></span>
                <span style="color:var(--accent-orange); font-weight:bold;"><?= format_currency($cow['monthly_adoption_fee']) ?><?= __('mo_label', '/mo') ?></span>
              </div>
              <?php 
                $cowMode = get_item_checkout_mode($cow, 'cow'); 
                $waUrl = get_whatsapp_cow_url($cow);
                $siteUrl = get_base_url() . 'pages/adopt.php?cow_id=' . $cow['id'];
              ?>
              <div class="card-actions" style="display:flex; flex-direction:column; gap:6px;">
                <?php if ($cowMode === 'both'): ?>
                  <div style="display:flex; gap:6px;">
                    <a href="<?= $siteUrl ?>" class="btn btn-primary btn-sm" style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:3px;">
                      🌐 <?= __('btn_adopt_online', 'Adopt Online') ?>
                    </a>
                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:3px;">
                      <?= get_whatsapp_icon_svg() ?> WhatsApp
                    </a>
                  </div>
                  <a href="cow-details.php?id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm text-center"><?= __('btn_view_profile', 'View Profile') ?></a>
                <?php elseif ($cowMode === 'whatsapp'): ?>
                  <div style="display:flex; gap:6px;">
                    <a href="cow-details.php?id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;"><?= __('btn_details', 'Details') ?></a>
                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:3px;">
                      <?= get_whatsapp_icon_svg() ?> WhatsApp
                    </a>
                  </div>
                <?php else: ?>
                  <div style="display:flex; gap:6px;">
                    <a href="cow-details.php?id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;"><?= __('btn_details', 'Details') ?></a>
                    <a href="<?= $siteUrl ?>" class="btn btn-primary btn-sm" style="flex:1;"><?= __('btn_adopt_online', 'Adopt Online') ?></a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center" style="padding: 60px; background: white; border-radius: var(--radius-md);">
        <h3 style="color: var(--primary-dark);">No cows found matching your criteria.</h3>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Try clearing filters or searching for another breed.</p>
        <a href="cows.php" class="btn btn-secondary">View All Cows</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
