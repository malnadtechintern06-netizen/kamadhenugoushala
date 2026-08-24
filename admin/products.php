<?php
// admin/products.php - Admin Organic Products CRUD

$adminPageTitle = 'Manage Organic Products Store';
require_once __DIR__ . '/header_sidebar.php';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$errors = [];

if ($action === 'delete' && $editId > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$editId]);
        set_flash('success', 'Product deleted.');
    }
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    }

    $name = sanitize($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);
    $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock = (int)($_POST['stock_quantity'] ?? 50);
    $description = sanitize($_POST['description'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $checkoutMode = sanitize($_POST['checkout_mode'] ?? 'default');
    $whatsappNumber = sanitize($_POST['whatsapp_number'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

    $imagePath = $_POST['existing_image'] ?? 'images/products/product-default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = upload_file($_FILES['image'], 'images/products');
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filepath'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }

    if (empty($name)) $errors[] = 'Product Name is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than zero.';

    if (empty($errors)) {
        if ($editId > 0) {
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, sale_price = ?, stock_quantity = ?, image = ?, is_featured = ?, checkout_mode = ?, whatsapp_number = ? WHERE id = ?");
            $stmt->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stock, $imagePath, $isFeatured, $checkoutMode, $whatsappNumber, $editId]);
            set_flash('success', 'Product updated.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, sale_price, stock_quantity, image, is_featured, checkout_mode, whatsapp_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stock, $imagePath, $isFeatured, $checkoutMode, $whatsappNumber]);
            set_flash('success', 'New product added.');
        }
        header('Location: products.php');
        exit;
    }
}

$editProduct = null;
if ($action === 'edit' && $editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
}

$categories = $pdo->query("SELECT * FROM product_categories ORDER BY name ASC")->fetchAll();
$productsList = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN product_categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  
  <div style="background:white; padding:35px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); width:100%; margin-bottom:30px;">
    <h3 style="margin-bottom:20px; font-size:1.4rem; color:var(--primary-dark); border-bottom:2px solid var(--bg-light-green); padding-bottom:12px;"><?= $action === 'edit' ? 'Edit Product' : 'Add New Product' ?></h3>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" action="products.php?action=<?= $action ?>&id=<?= $editId ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProduct['image'] ?? 'images/products/product-default.jpg') ?>">

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Product Name *</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-control" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id'] ?? 0) === $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Regular Price (₹) *</label>
          <input type="number" step="0.01" name="price" class="form-control" value="<?= (float)($editProduct['price'] ?? 199.00) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Sale Price (₹ Optional)</label>
          <input type="number" step="0.01" name="sale_price" class="form-control" value="<?= (float)($editProduct['sale_price'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Stock Quantity *</label>
          <input type="number" name="stock_quantity" class="form-control" value="<?= (int)($editProduct['stock_quantity'] ?? 50) ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
      </div>

      <!-- 📸 Product Photo Uploading Option -->
      <div class="form-group" style="background:#fdfbf7; border:2px dashed #e2c08d; padding:20px; border-radius:12px; margin-bottom:20px;">
        <label class="form-label" style="font-weight:bold; font-size:1.02rem; color:var(--primary-dark); display:flex; align-items:center; gap:8px;">
          📸 Upload Product Photo (Store Display Image)
        </label>
        
        <?php $currentImg = !empty($editProduct['image']) ? $editProduct['image'] : 'images/products/product-default.jpg'; ?>
        <div style="display:flex; gap:20px; align-items:center; margin-bottom:10px; flex-wrap:wrap;">
          <div style="width:110px; height:110px; border-radius:10px; overflow:hidden; border:2px solid #ddd; background:#fff; position:relative; flex-shrink:0;">
            <img id="prod-img-preview" src="<?= $baseUrl . htmlspecialchars($currentImg) ?>" alt="Product Preview" style="width:100%; height:100%; object-fit:cover;" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
          </div>
          <div style="flex:1; min-width:240px;">
            <p style="margin:0 0 6px 0; font-size:0.85rem; color:var(--text-muted);">Current Photo File: <code><?= htmlspecialchars($currentImg) ?></code></p>
            <input type="file" name="image" id="product-photo-input" class="form-control" accept="image/*" onchange="previewProductPhoto(this)" style="padding:8px 12px; background:white;">
            <small style="color:#7D4F5A; display:block; margin-top:6px; font-size:0.8rem;">Select a new high-resolution photo file (JPG, PNG, WEBP, GIF up to 5MB) to update the store product display.</small>
          </div>
        </div>
      </div>

      <script>
        function previewProductPhoto(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('prod-img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
          }
        }
      </script>

      <div class="form-group">
        <label class="form-label" style="font-weight:bold; color:var(--accent-orange);">🛒 Checkout Option for this Product</label>
        <select name="checkout_mode" class="form-control" style="font-weight:bold; background:#fffcf6; border:1.5px solid var(--accent-orange);">
          <option value="default" <?= ($editProduct['checkout_mode'] ?? 'default') === 'default' ? 'selected' : '' ?>>⚙️ Global Setting Default (Use Admin Settings Mode)</option>
          <option value="both" <?= ($editProduct['checkout_mode'] ?? 'default') === 'both' ? 'selected' : '' ?>>🤝 Both Options (Show Add to Cart &amp; WhatsApp Order Buttons)</option>
          <option value="website" <?= ($editProduct['checkout_mode'] ?? 'default') === 'website' ? 'selected' : '' ?>>🌐 Website Checkout Only (Shopping Cart &amp; Online Checkout)</option>
          <option value="whatsapp" <?= ($editProduct['checkout_mode'] ?? 'default') === 'whatsapp' ? 'selected' : '' ?>>📱 WhatsApp Checkout Only (Direct WhatsApp Order Chat)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" style="font-weight:bold; color:#2E7D32;">📱 Select WhatsApp Order Number for this Product</label>
        <select name="whatsapp_number" class="form-control" style="font-weight:bold;">
          <option value="">⚙️ Default Primary WhatsApp Number (Use Admin Settings Default)</option>
          <?php foreach (get_whatsapp_numbers_list() as $key => $phoneInfo): ?>
            <option value="<?= htmlspecialchars($phoneInfo['number']) ?>" <?= ($editProduct['whatsapp_number'] ?? '') === $phoneInfo['number'] ? 'selected' : '' ?>>
              📞 <?= htmlspecialchars($phoneInfo['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small style="color:var(--text-muted); font-size:0.8rem; display:block; margin-top:4px;">Choose which helpline number receives WhatsApp orders for this specific product.</small>
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px;">
        <input type="checkbox" name="is_featured" value="1" id="pfeat" <?= !isset($editProduct) || $editProduct['is_featured'] ? 'checked' : '' ?>>
        <label for="pfeat">Featured Product</label>
      </div>

      <div style="display:flex; gap:15px;">
        <button type="submit" class="btn btn-primary">Save Product</button>
        <a href="products.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>

<?php else: ?>

  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h3>Products Registry (<?= count($productsList) ?>)</h3>
    <a href="products.php?action=add" class="btn btn-primary">+ Add New Product</a>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productsList as $p): ?>
          <tr>
            <td><img src="<?= '../' . htmlspecialchars($p['image']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=100&q=80'" style="width:50px; height:50px; object-fit:cover; border-radius:var(--radius-sm);"></td>
            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
            <td><?= htmlspecialchars($p['category_name']) ?></td>
            <td>
              <?= format_currency($p['sale_price'] ?: $p['price']) ?>
              <?php if ($p['sale_price']): ?><span style="font-size:0.75rem; color:#999; text-decoration:line-through;"><?= format_currency($p['price']) ?></span><?php endif; ?>
            </td>
            <td><strong style="color:<?= $p['stock_quantity'] > 0 ? '#2E7D32' : '#C62828'; ?>"><?= $p['stock_quantity'] ?></strong></td>
            <td>
              <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="products.php?action=delete&id=<?= $p['id'] ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Delete this product?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828;">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
