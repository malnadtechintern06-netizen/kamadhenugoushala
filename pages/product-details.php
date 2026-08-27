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
    echo "<div class='container text-center' style='padding:100px 0;'><h2>" . __('product_not_found', 'Product Not Found') . "</h2><p>" . __('no_records_found', 'The requested product does not exist.') . "</p><a href='products.php' class='btn btn-secondary'>" . __('btn_browse_store', 'Back to Store') . "</a></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch related products
$stmtRel = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 3");
$stmtRel->execute([$prod['category_id'], $productId]);
$relatedProducts = $stmtRel->fetchAll();

$pageTitle = htmlspecialchars(__($prod['name'], $prod['name'])) . " - Kamadhenu Goushala Store";
?>

<div class="page-banner">
  <div class="container">
    <h1><?= htmlspecialchars(__($prod['name'], $prod['name'])) ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <a href="products.php"><?= __('nav_products', 'Products') ?></a> / <span><?= htmlspecialchars(__($prod['name'], $prod['name'])) ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-white">
  <div class="container">
    <div class="grid-responsive-2">
      
      <!-- Media -->
      <div>
        <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--border-light); background:#fdfbf7; display:flex; align-items:center; justify-content:center;">
          <img src="<?= $baseUrl . htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:400px; object-fit:contain; padding:8px;">
        </div>
      </div>

      <!-- Specs -->
      <div>
        <span class="section-subtitle"><?= htmlspecialchars(__($prod['category_name'], $prod['category_name'])) ?></span>
        <h2 style="font-size: 2.2rem; color: var(--primary-dark); margin-bottom: 10px;"><?= htmlspecialchars(__($prod['name'], $prod['name'])) ?></h2>
        
        <div style="display:flex; align-items:baseline; gap:15px; margin-bottom:20px;">
          <span style="font-size: 2.2rem; font-weight: bold; color: var(--primary-dark);">
            <?= format_currency($prod['sale_price'] ?: $prod['price']) ?>
          </span>
          <?php if ($prod['sale_price']): ?>
            <span style="font-size: 1.2rem; color: #999; text-decoration: line-through;"><?= format_currency($prod['price']) ?></span>
            <span style="background: var(--accent-orange); color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;"><?= __('special_offer', 'SPECIAL OFFER') ?></span>
          <?php endif; ?>
        </div>

        <p style="margin-bottom: 20px;">
          <?= __('availability_label', 'Availability:') ?> 
          <?php if ($prod['stock_quantity'] > 0): ?>
            <span style="color: #2E7D32; font-weight: bold;"><?= __('in_stock', 'In Stock') ?> (<?= $prod['stock_quantity'] ?>)</span>
          <?php else: ?>
            <span style="color: #C62828; font-weight: bold;"><?= __('out_of_stock', 'Out of Stock') ?></span>
          <?php endif; ?>
        </p>

        <p style="color: var(--text-dark); line-height: 1.8; margin-bottom: 30px;">
          <?= nl2br(htmlspecialchars(__($prod['description'], $prod['description']))) ?>
        </p>

        <!-- Quantity & Add to Cart / Order -->
        <?php 
          $prodMode = get_item_checkout_mode($prod, 'product'); 
          $waUrl = get_whatsapp_product_url($prod);
        ?>
        <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 30px; flex-wrap:wrap;">
          <?php if ($prodMode === 'both'): ?>
            <div class="quantity-control">
              <button class="qty-btn qty-minus">-</button>
              <input type="number" class="qty-input" data-product-id="<?= $prod['id'] ?>" value="1" min="1" max="<?= $prod['stock_quantity'] ?>">
              <button class="qty-btn qty-plus">+</button>
            </div>
            <button class="btn btn-primary btn-lg add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" style="flex:1;">
              <?= __('btn_add_to_cart', '🛒 Add to Cart') ?>
            </button>
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-lg" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:4px;">
              <?= get_whatsapp_icon_svg('1.25em') ?> WhatsApp 🛍️
            </a>
          <?php elseif ($prodMode === 'whatsapp'): ?>
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-primary btn-lg" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:4px;">
              <?= get_whatsapp_icon_svg('1.25em') ?> WhatsApp 🛍️
            </a>
          <?php else: ?>
            <div class="quantity-control">
              <button class="qty-btn qty-minus">-</button>
              <input type="number" class="qty-input" data-product-id="<?= $prod['id'] ?>" value="1" min="1" max="<?= $prod['stock_quantity'] ?>">
              <button class="qty-btn qty-plus">+</button>
            </div>
            <button class="btn btn-primary btn-lg add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" style="flex:1;">
              <?= __('btn_add_to_cart', '🛒 Add to Cart') ?>
            </button>
          <?php endif; ?>
        </div>

        <div style="padding:15px; background:var(--bg-light-green); border-radius:var(--radius-sm); font-size:0.88rem; color:var(--text-dark);">
          ✓ <?= __('product_guarantee_1', '100% Organic & Chemical-Free Panchagavya Product.') ?><br>
          ✓ <?= __('product_guarantee_2', 'Proceeds support Goushala cow feeding and veterinary care.') ?>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="section-padding bg-light">
  <div class="container">
    <h3 class="section-title"><?= __('related_products', 'Related Organic Products') ?></h3>
    <div class="card-grid">
      <?php foreach ($relatedProducts as $rel): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars(__($rel['name'], $rel['name'])) ?></h3>
            <div class="card-meta">
              <span class="card-price"><?= format_currency($rel['sale_price'] ?: $rel['price']) ?></span>
            </div>
            <a href="product-details.php?id=<?= $rel['id'] ?>" class="btn btn-outline btn-sm btn-block"><?= __('btn_details', 'View Details') ?></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
