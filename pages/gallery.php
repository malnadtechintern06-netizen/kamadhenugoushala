<?php
// pages/gallery.php - Photo & Video Gallery

$pageTitle = 'Sanctuary Gallery - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

$catSlug = sanitize($_GET['category'] ?? 'all');

$query = "
    SELECT g.*, c.name as category_name, c.slug as category_slug 
    FROM gallery g 
    JOIN gallery_categories c ON g.category_id = c.id 
    WHERE 1=1
";
$params = [];

if ($catSlug !== 'all') {
    $query .= " AND c.slug = ?";
    $params[] = $catSlug;
}

$query .= " ORDER BY g.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$galleryImages = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM gallery_categories ORDER BY id ASC")->fetchAll();
?>

<div class="page-banner">
  <div class="container">
    <h1>Photo & Video Gallery</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Gallery</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <!-- Category Filter Bar -->
    <div class="filter-bar text-center" style="justify-content:center;">
      <div class="filter-buttons">
        <a href="gallery.php?category=all" class="filter-btn <?= $catSlug === 'all' ? 'active' : '' ?>">All Photos</a>
        <?php foreach ($categories as $cat): ?>
          <?php if ($cat['slug'] === 'all') continue; ?>
          <a href="gallery.php?category=<?= $cat['slug'] ?>" class="filter-btn <?= $catSlug === $cat['slug'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Gallery Grid -->
    <?php if (!empty($galleryImages)): ?>
      <div class="gallery-grid">
        <?php foreach ($galleryImages as $img): ?>
          <div class="gallery-item" data-image="<?= $baseUrl . htmlspecialchars($img['image_path']) ?>" data-title="<?= htmlspecialchars($img['title']) ?>">
            <img src="<?= $baseUrl . htmlspecialchars($img['image_path']) ?>" alt="<?= htmlspecialchars($img['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
            <div class="gallery-overlay">
              <h4><?= htmlspecialchars($img['title']) ?></h4>
              <p style="font-size:0.85rem; opacity:0.9;"><?= htmlspecialchars($img['description']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center" style="padding:60px; background:white; border-radius:var(--radius-md);">
        <h3>No photos found in this category.</h3>
        <a href="gallery.php" class="btn btn-secondary" style="margin-top:15px;">View All Photos</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- Lightbox Modal Container -->
<div class="lightbox-modal">
  <div class="lightbox-content">
    <button class="lightbox-close">&times;</button>
    <button class="lightbox-prev">&#10094;</button>
    <button class="lightbox-next">&#10095;</button>
    <img src="" class="lightbox-img" alt="Gallery Preview">
    <div class="lightbox-caption"></div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
