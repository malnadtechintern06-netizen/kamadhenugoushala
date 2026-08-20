<?php
// pages/product-details.php - Product Details Page

require_once __DIR__ . '/../includes/header.php';

$productId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN product_categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$productId]);
$prod = $stmt->fetch();

if (!$prod) {
    echo "<div class='container text-center' style='padding:100px 0;'><h2>Product Not Found</h2><p>The requested product does not exist.</p><a href='products.php' class='btn btn-secondary'>Back to Store</a></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch related products
$stmtRel = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 3");
$stmtRel->execute([$prod['category_id'], $productId]);
$relatedProducts = $stmtRel->fetchAll();

$pageTitle = htmlspecialchars($prod['name']) . " - Kamadhenu Goushala Store";
?>

<div class="page-banner">
  <div class="container">
    <h1><?= htmlspecialchars($prod['name']) ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <a href="products.php">Products</a> / <span><?= htmlspecialchars($prod['name']) ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-white">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 50px;">
      
      <!-- Media -->
      <div>
        <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--border-light);">
          <img src="<?= $baseUrl . htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:400px; object-fit:cover;">
        </div>
      </div>

      <!-- Specs -->
      <div>
        <span class="section-subtitle"><?= htmlspecialchars($prod['category_name']) ?></span>
        <h2 style="font-size: 2.2rem; color: var(--primary-dark); margin-bottom: 10px;"><?= htmlspecialchars($prod['name']) ?></h2>
        
        <div style="display:flex; align-items:baseline; gap:15px; margin-bottom:20px;">
          <span style="font-size: 2.2rem; font-weight: bold; color: var(--primary-dark);">
            <?= format_currency($prod['sale_price'] ?: $prod['price']) ?>
          </span>
          <?php if ($prod['sale_price']): ?>
            <span style="font-size: 1.2rem; color: #999; text-decoration: line-through;"><?= format_currency($prod['price']) ?></span>
            <span style="background: var(--accent-orange); color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">SPECIAL OFFER</span>
          <?php endif; ?>
        </div>

        <p style="margin-bottom: 20px;">
          Availability: 
          <?php if ($prod['stock_quantity'] > 0): ?>
            <span style="color: #2E7D32; font-weight: bold;">In Stock (<?= $prod['stock_quantity'] ?> units remaining)</span>
          <?php else: ?>
            <span style="color: #C62828; font-weight: bold;">Out of Stock</span>
          <?php endif; ?>
        </p>

        <p style="color: var(--text-dark); line-height: 1.8; margin-bottom: 30px;">
          <?= nl2br(htmlspecialchars($prod['description'])) ?>
        </p>

        <!-- Quantity & Add to Cart -->
        <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 30px;">
          <div class="quantity-control">
            <button class="qty-btn qty-minus">-</button>
            <input type="number" class="qty-input" data-product-id="<?= $prod['id'] ?>" value="1" min="1" max="<?= $prod['stock_quantity'] ?>">
            <button class="qty-btn qty-plus">+</button>
          </div>

          <button class="btn btn-primary btn-lg add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" style="flex:1;">
            🛒 Add to Cart
          </button>
        </div>

        <div style="padding:15px; background:var(--bg-light-green); border-radius:var(--radius-sm); font-size:0.88rem; color:var(--text-dark);">
          ✓ 100% Organic & Chemical-Free Panchagavya Product.<br>
          ✓ Proceeds support Goushala cow feeding and veterinary care.
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="section-padding bg-light">
  <div class="container">
    <h3 class="section-title">Related Organic Products</h3>
    <div class="card-grid">
      <?php foreach ($relatedProducts as $rel): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($rel['name']) ?></h3>
            <div class="card-meta">
              <span class="card-price"><?= format_currency($rel['sale_price'] ?: $rel['price']) ?></span>
            </div>
            <a href="product-details.php?id=<?= $rel['id'] ?>" class="btn btn-outline btn-sm btn-block">View Details</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
