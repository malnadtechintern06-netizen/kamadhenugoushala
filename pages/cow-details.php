<?php
// pages/cow-details.php - Cow Details Page

require_once __DIR__ . '/../includes/header.php';

$cowId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cows WHERE id = ?");
$stmt->execute([$cowId]);
$cow = $stmt->fetch();

if (!$cow) {
    echo "<div class='container text-center' style='padding:100px 0;'><h2>Cow Not Found</h2><p>The requested cow record does not exist.</p><a href='cows.php' class='btn btn-secondary'>Back to Cows</a></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch additional gallery images for this cow if present
$stmtImg = $pdo->prepare("SELECT image_path FROM cow_images WHERE cow_id = ?");
$stmtImg->execute([$cowId]);
$additionalImages = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = htmlspecialchars($cow['name']) . " (" . htmlspecialchars($cow['breed']) . ") - Kamadhenu Goushala";
?>

<div class="page-banner">
  <div class="container">
    <h1><?= htmlspecialchars($cow['name']) ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <a href="cows.php">Our Cows</a> / <span><?= htmlspecialchars($cow['name']) ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-white">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 50px;">
      
      <!-- Cow Media Column -->
      <div>
        <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--border-light); margin-bottom: 20px; background:#fdfbf7; display:flex; align-items:center; justify-content:center;">
          <img id="main-cow-image" src="<?= $baseUrl . htmlspecialchars($cow['main_image']) ?>" alt="<?= htmlspecialchars($cow['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1570042707223-2cb2ed999557?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:400px; object-fit:contain; padding:8px;">
        </div>

        <?php if (!empty($additionalImages)): ?>
          <div style="display: flex; gap: 10px; overflow-x: auto;">
            <img src="<?= $baseUrl . htmlspecialchars($cow['main_image']) ?>" onclick="document.getElementById('main-cow-image').src=this.src" style="width:80px; height:80px; object-fit:cover; border-radius: var(--radius-sm); cursor:pointer;">
            <?php foreach ($additionalImages as $img): ?>
              <img src="<?= $baseUrl . htmlspecialchars($img) ?>" onclick="document.getElementById('main-cow-image').src=this.src" style="width:80px; height:80px; object-fit:cover; border-radius: var(--radius-sm); cursor:pointer;">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Details Column -->
      <div>
        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
          <span class="card-badge status-<?= strtolower(str_replace(' ', '-', $cow['health_status'])) ?>">
            <?= htmlspecialchars($cow['health_status']) ?>
          </span>
          <span style="font-size: 0.9rem; color: var(--text-muted);">Tag #: <strong><?= htmlspecialchars($cow['tag_number']) ?></strong></span>
        </div>

        <h2 style="font-size: 2.5rem; color: var(--primary-dark); margin-bottom: 10px;"><?= htmlspecialchars($cow['name']) ?></h2>
        
        <div style="background: var(--bg-light-green); padding: 20px; border-radius: var(--radius-md); margin-bottom: 25px; border: 1px solid var(--border-light);">
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; font-size: 0.95rem;">
            <div><strong>Breed:</strong> <?= htmlspecialchars($cow['breed']) ?></div>
            <div><strong>Age:</strong> <?= $cow['age_years'] ?> Years</div>
            <div><strong>Gender:</strong> <?= htmlspecialchars($cow['gender']) ?></div>
            <div><strong>Adoption Status:</strong> <span style="color:var(--primary-dark); font-weight:bold;"><?= htmlspecialchars($cow['adoption_status']) ?></span></div>
            <div style="grid-column: span 2;"><strong>Monthly Support Fee:</strong> <span style="color:var(--accent-orange); font-size: 1.3rem; font-weight:bold;"><?= format_currency($cow['monthly_adoption_fee']) ?> / Month</span></div>
          </div>
        </div>

        <h3>Biography & Story</h3>
        <p style="color: var(--text-dark); line-height: 1.8; margin-bottom: 30px;">
          <?= nl2br(htmlspecialchars($cow['bio'])) ?>
        </p>

        <?php 
          $cowMode = get_item_checkout_mode($cow, 'cow');
          $waUrl = get_whatsapp_cow_url($cow);
          $siteUrl = get_base_url() . 'pages/adopt.php?cow_id=' . $cow['id'];
        ?>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
          <?php if ($cowMode === 'both'): ?>
            <a href="<?= $siteUrl ?>" class="btn btn-primary btn-lg" style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
              🌐 Adopt Online
            </a>
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-lg" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
              <?= get_whatsapp_icon_svg('1.25em') ?> Adopt via WhatsApp
            </a>
          <?php elseif ($cowMode === 'whatsapp'): ?>
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-lg" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
              <?= get_whatsapp_icon_svg('1.25em') ?> Adopt <?= htmlspecialchars($cow['name']) ?> on WhatsApp
            </a>
          <?php else: ?>
            <a href="<?= $siteUrl ?>" class="btn btn-primary btn-lg" style="flex:1;">🐄 Adopt <?= htmlspecialchars($cow['name']) ?> Online</a>
          <?php endif; ?>
          <a href="<?= get_donation_action_url('Fodder Seva for ' . $cow['name']) ?>" <?= get_checkout_mode('donation') === 'whatsapp' ? 'target="_blank"' : '' ?> class="btn btn-secondary btn-lg" style="flex:1;">Sponsor Fodder 🌾</a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
