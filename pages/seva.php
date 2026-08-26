<?php
// pages/seva.php - Seva Opportunities Page

$pageTitle = 'Seva Opportunities - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT * FROM seva ORDER BY id ASC");
$sevas = $stmt->fetchAll();
?>

<div class="page-banner">
  <div class="container">
    <h1><?= __('banner_seva', 'Sacred Seva Opportunities') ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <span><?= __('nav_seva', 'Seva') ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 650px; margin: 0 auto 40px auto;">
      <span class="section-subtitle"><?= __('seva_subtitle', 'HOLY SERVICE') ?></span>
      <h2 class="section-title center"><?= __('seva_title', 'Sponsor Feeding & Care For Gau Mata') ?></h2>
      <p><?= __('seva_desc', 'Select a Seva program below to support our daily fodder, emergency veterinary medicines, and solar shed maintenance.') ?></p>
    </div>

    <div class="card-grid">
      <?php foreach ($sevas as $seva): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($seva['image']) ?>" alt="<?= htmlspecialchars($seva['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge"><?= htmlspecialchars(__($seva['category'], $seva['category'])) ?></span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars(__($seva['title'], $seva['title'])) ?></h3>
            <p class="card-subtitle"><?= htmlspecialchars(__($seva['subtitle'], $seva['subtitle'])) ?></p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($seva['description'], 0, 120, '...')) ?></p>
            <div class="card-meta">
              <span><?= __('suggested_seva', 'Suggested Contribution:') ?></span>
              <span class="card-price"><?= format_currency($seva['suggested_amount']) ?></span>
            </div>
            <?php 
              $sevaMode = get_item_checkout_mode($seva, 'donation'); 
              $waUrl = get_whatsapp_donation_url($seva['title'], $seva);
              $siteUrl = get_base_url() . 'pages/seva-details.php?id=' . $seva['id'];
            ?>
            <div class="card-actions" style="display:flex; flex-direction:column; gap:6px;">
              <?php if ($sevaMode === 'both'): ?>
                <div style="display:flex; gap:6px;">
                  <a href="<?= $siteUrl ?>" class="btn btn-primary btn-sm" style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:3px;">
                    <?= __('btn_donate_online', '🌐 Donate Online') ?>
                  </a>
                  <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:3px;">
                    <?= get_whatsapp_icon_svg() ?> WhatsApp
                  </a>
                </div>
              <?php elseif ($sevaMode === 'whatsapp'): ?>
                <a href="<?= $waUrl ?>" target="_blank" class="btn btn-primary btn-sm" style="width:100%; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:3px;">
                  <?= get_whatsapp_icon_svg() ?> <?= __('btn_offer_whatsapp', 'Offer Seva on WhatsApp') ?>
                </a>
              <?php else: ?>
                <a href="<?= $siteUrl ?>" class="btn btn-primary btn-sm" style="width:100%;"><?= __('btn_view_details_sponsor', 'View Details & Offer Seva') ?></a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
