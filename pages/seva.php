<?php
// pages/seva.php - Seva Opportunities Page

$pageTitle = 'Seva Opportunities - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT * FROM seva ORDER BY id ASC");
$sevas = $stmt->fetchAll();
?>

<div class="page-banner">
  <div class="container">
    <h1>Sacred Seva Opportunities</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Seva</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 650px; margin: 0 auto 40px auto;">
      <span class="section-subtitle">HOLY SERVICE</span>
      <h2 class="section-title center">Sponsor Feeding & Care For Gau Mata</h2>
      <p>Select a Seva program below to support our daily fodder, emergency veterinary medicines, and solar shed maintenance.</p>
    </div>

    <div class="card-grid">
      <?php foreach ($sevas as $seva): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($seva['image']) ?>" alt="<?= htmlspecialchars($seva['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge"><?= htmlspecialchars($seva['category']) ?></span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($seva['title']) ?></h3>
            <p class="card-subtitle"><?= htmlspecialchars($seva['subtitle']) ?></p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($seva['description'], 0, 120, '...')) ?></p>
            <div class="card-meta">
              <span>Suggested Contribution:</span>
              <span class="card-price"><?= format_currency($seva['suggested_amount']) ?></span>
            </div>
            <div class="card-actions">
              <a href="seva-details.php?id=<?= $seva['id'] ?>" class="btn btn-primary btn-sm" style="width:100%;">View Details & Offer Seva</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
