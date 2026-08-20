<?php
// pages/products.php - Organic Products Catalog Page

$pageTitle = 'Organic Panchagavya Store - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

$search = sanitize($_GET['search'] ?? '');
$catId = (int)($_GET['category'] ?? 0);

$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
          FROM products p 
          JOIN product_categories c ON p.category_id = c.id 
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($catId > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $catId;
}

$query .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Categories for filter bar
$categories = $pdo->query("SELECT * FROM product_categories ORDER BY name ASC")->fetchAll();
?>

<div class="page-banner">
  <div class="container">
    <h1>Organic Panchagavya Store</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Products</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <!-- Filter & Search Bar -->
    <div class="filter-bar">
      <div class="filter-buttons">
        <a href="products.php" class="filter-btn <?= $catId === 0 ? 'active' : '' ?>">All Products</a>
        <?php foreach ($categories as $cat): ?>
          <a href="products.php?category=<?= $cat['id'] ?>" class="filter-btn <?= $catId === $cat['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <form method="GET" action="products.php" class="search-input-group">
        <?php if ($catId > 0): ?>
          <input type="hidden" name="category" value="<?= $catId ?>">
        <?php endif; ?>
        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
      </form>
    </div>

    <!-- Product Cards Grid -->
    <?php if (count($products) > 0): ?>
      <div class="card-grid">
        <?php foreach ($products as $prod): ?>
          <div class="card">
            <div class="card-img-wrapper">
              <img src="<?= $baseUrl . htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
              <span class="card-badge"><?= htmlspecialchars($prod['category_name']) ?></span>
            </div>
            <div class="card-body">
              <h3 class="card-title"><?= htmlspecialchars($prod['name']) ?></h3>
              <p class="card-text"><?= htmlspecialchars(mb_strimwidth($prod['description'], 0, 95, '...')) ?></p>
              <div class="card-meta">
                <div class="card-price">
                  <?= format_currency($prod['sale_price'] ?: $prod['price']) ?>
                  <?php if ($prod['sale_price']): ?>
                    <span class="original-price"><?= format_currency($prod['price']) ?></span>
                  <?php endif; ?>
                </div>
                <span style="font-size:0.85rem; color:#2E7D32;">In Stock (<?= $prod['stock_quantity'] ?>)</span>
              </div>
              <div class="card-actions">
                <a href="product-details.php?id=<?= $prod['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;">View Product</a>
                <button class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" style="flex:1;">Add to Cart</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center" style="padding:60px; background:white; border-radius:var(--radius-md);">
        <h3>No organic products found.</h3>
        <p style="color:var(--text-muted); margin-bottom:20px;">Try searching for another product category.</p>
        <a href="products.php" class="btn btn-secondary">View All Products</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
