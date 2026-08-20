<?php
// pages/cart.php - Shopping Cart Page

$pageTitle = 'Shopping Cart - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

// Handle POST actions (Update quantity or remove item)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $itemId = (int)($_POST['item_id'] ?? 0);
    $cartId = get_or_create_cart_id();

    if ($action === 'update') {
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?");
        $stmt->execute([$qty, $itemId, $cartId]);
        set_flash('success', 'Cart updated successfully.');
    } elseif ($action === 'remove') {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
        $stmt->execute([$itemId, $cartId]);
        set_flash('success', 'Item removed from cart.');
    } elseif ($action === 'clear') {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cartId]);
        set_flash('success', 'Cart emptied.');
    }
    header('Location: cart.php');
    exit;
}

$cartData = get_cart_details();
$items = $cartData['items'];
?>

<div class="page-banner">
  <div class="container">
    <h1>Your Shopping Cart</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Cart</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    <?php if (!empty($items)): ?>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; align-items: start;">
        
        <!-- Cart Table Column -->
        <div style="grid-column: span 2;">
          <div class="table-responsive">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Subtotal</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td>
                      <div style="display:flex; align-items:center; gap:15px;">
                        <img src="<?= $baseUrl . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=100&q=80'" style="width:60px; height:60px; object-fit:cover; border-radius:var(--radius-sm);">
                        <div>
                          <a href="product-details.php?id=<?= $item['product_id'] ?>" style="font-weight:bold; color:var(--primary-dark);">
                            <?= htmlspecialchars($item['name']) ?>
                          </a>
                        </div>
                      </div>
                    </td>
                    <td><?= format_currency($item['unit_price']) ?></td>
                    <td>
                      <form method="POST" action="cart.php" style="display:flex; align-items:center; gap:5px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                        <div class="quantity-control">
                          <button type="button" class="qty-btn qty-minus">-</button>
                          <input type="number" name="quantity" class="qty-input" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>" onchange="this.form.submit()">
                          <button type="button" class="qty-btn qty-plus">+</button>
                        </div>
                      </form>
                    </td>
                    <td style="font-weight:bold; color:var(--primary-dark);"><?= format_currency($item['total_price']) ?></td>
                    <td>
                      <form method="POST" action="cart.php">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                        <button type="submit" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828; padding:4px 10px;">🗑</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div style="display:flex; justify-content:space-between; margin-top:20px;">
            <a href="products.php" class="btn btn-outline">← Continue Shopping</a>
            <form method="POST" action="cart.php">
              <input type="hidden" name="action" value="clear">
              <button type="submit" class="btn btn-outline" style="color:#C62828; border-color:#C62828;">Empty Cart</button>
            </form>
          </div>
        </div>

        <!-- Summary Column -->
        <div>
          <div class="form-card" style="margin:0; width:100%; max-width:100%;">
            <h3 style="margin-bottom:20px; border-bottom:2px solid var(--border-light); padding-bottom:10px;">Order Summary</h3>
            
            <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:1rem;">
              <span>Subtotal:</span>
              <strong style="color:var(--primary-dark);"><?= format_currency($cartData['subtotal']) ?></strong>
            </div>

            <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:1rem;">
              <span>Shipping / Handling:</span>
              <span style="color:#2E7D32; font-weight:bold;">FREE (Sacred Seva)</span>
            </div>

            <div style="display:flex; justify-content:space-between; margin-bottom:20px; padding-top:12px; border-top:1px dashed var(--border-light); font-size:1.25rem;">
              <strong>Total:</strong>
              <strong style="color:var(--accent-orange);"><?= format_currency($cartData['total']) ?></strong>
            </div>

            <a href="checkout.php" class="btn btn-primary btn-lg btn-block">Proceed to Checkout →</a>
          </div>
        </div>

      </div>
    <?php else: ?>
      <div class="text-center" style="padding:80px 0; background:white; border-radius:var(--radius-md);">
        <h2 style="color:var(--primary-dark); margin-bottom:10px;">Your Shopping Cart is Empty</h2>
        <p style="color:var(--text-muted); margin-bottom:25px;">Support Gau Seva by purchasing pure organic Panchagavya products.</p>
        <a href="products.php" class="btn btn-primary btn-lg">Browse Organic Store</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
