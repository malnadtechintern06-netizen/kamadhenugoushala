<?php
// pages/seva-details.php - Seva Opportunity Detail Page

require_once __DIR__ . '/../includes/header.php';

$sevaId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM seva WHERE id = ?");
$stmt->execute([$sevaId]);
$seva = $stmt->fetch();

if (!$seva) {
    echo "<div class='container text-center' style='padding:100px 0;'><h2>" . __('seva_not_found', 'Seva Program Not Found') . "</h2><p>" . __('no_records_found', 'The requested Seva program does not exist.') . "</p><a href='seva.php' class='btn btn-secondary'>" . __('nav_seva', 'Back to Seva') . "</a></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = htmlspecialchars(__($seva['title'], $seva['title'])) . " - Kamadhenu Goushala Seva";
?>

<div class="page-banner">
  <div class="container">
    <h1><?= htmlspecialchars(__($seva['title'], $seva['title'])) ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <a href="seva.php"><?= __('nav_seva', 'Seva') ?></a> / <span><?= htmlspecialchars(__($seva['title'], $seva['title'])) ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-white">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 50px;">
      
      <div>
        <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--border-light); margin-bottom: 20px; background:#fdfbf7; display:flex; align-items:center; justify-content:center;">
          <img src="<?= $baseUrl . htmlspecialchars($seva['image']) ?>" alt="<?= htmlspecialchars($seva['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:380px; object-fit:contain; padding:8px;">
        </div>
      </div>

      <div>
        <span class="section-subtitle"><?= htmlspecialchars(__($seva['category'], $seva['category'])) ?> SEVA</span>
        <h2 style="font-size: 2.2rem; color: var(--primary-dark); margin-bottom: 10px;"><?= htmlspecialchars(__($seva['title'], $seva['title'])) ?></h2>
        <p style="font-size: 1.1rem; color: var(--accent-orange); font-weight: 600; margin-bottom: 20px;"><?= htmlspecialchars(__($seva['subtitle'], $seva['subtitle'])) ?></p>

        <div style="background: var(--bg-light-green); padding: 20px; border-radius: var(--radius-md); margin-bottom: 25px; border: 1px solid var(--border-light);">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span><?= __('suggested_seva', 'Suggested Seva Offering:') ?></span>
            <span style="font-size: 1.8rem; font-weight: bold; color: var(--primary-dark);"><?= format_currency($seva['suggested_amount']) ?></span>
          </div>
        </div>

        <h3><?= __('program_impact_heading', 'Program Impact') ?></h3>
        <p style="color: var(--text-dark); line-height: 1.8; margin-bottom: 30px;">
          <?= nl2br(htmlspecialchars(__($seva['description'], $seva['description']))) ?>
        </p>

        <?php 
          $sevaMode = get_item_checkout_mode($seva, 'donation'); 
          $waUrl = get_whatsapp_donation_url($seva['title'], $seva);
          $donateUrl = get_base_url() . 'pages/donate.php?amount=' . (int)$seva['suggested_amount'] . '&purpose=' . urlencode($seva['title']);
        ?>
        <div style="display:flex; gap:15px; flex-wrap:wrap;">
          <?php if ($sevaMode === 'both'): ?>
            <a href="<?= $donateUrl ?>" class="btn btn-primary btn-lg" style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
              <?= __('btn_donate_online', '🌐 Sponsor Online 💖') ?>
            </a>
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-lg" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
              <?= get_whatsapp_icon_svg('1.2em') ?> WhatsApp 📱
            </a>
          <?php elseif ($sevaMode === 'whatsapp'): ?>
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-primary btn-lg btn-block" style="background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:4px;">
              <?= get_whatsapp_icon_svg('1.2em') ?> WhatsApp 📱
            </a>
          <?php else: ?>
            <a href="<?= $donateUrl ?>" class="btn btn-primary btn-lg btn-block"><?= __('btn_donate_online', 'Sponsor This Seva Now 💖') ?></a>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
