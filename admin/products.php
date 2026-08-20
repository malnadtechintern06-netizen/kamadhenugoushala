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
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, sale_price = ?, stock_quantity = ?, image = ?, is_featured = ? WHERE id = ?");
            $stmt->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stock, $imagePath, $isFeatured, $editId]);
            set_flash('success', 'Product updated.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, sale_price, stock_quantity, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stock, $imagePath, $isFeatured]);
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
  
  <div style="background:white; padding:30px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); max-width:800px;">
    <h3><?= $action === 'edit' ? 'Edit Product' : 'Add New Product' ?></h3>

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

      <div class="form-group">
        <label class="form-label">Product Image</label>
        <input type="file" name="image" class="form-control">
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

</main>
</div>
</body>
</html>
