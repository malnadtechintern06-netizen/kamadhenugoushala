<?php
// admin/seva.php - Admin Seva CRUD

$adminPageTitle = 'Manage Seva Opportunities';
require_once __DIR__ . '/header_sidebar.php';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$errors = [];

if ($action === 'delete' && $editId > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("DELETE FROM seva WHERE id = ?");
        $stmt->execute([$editId]);
        set_flash('success', 'Seva program deleted.');
    }
    header('Location: seva.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    }

    $title = sanitize($_POST['title'] ?? '');
    $subtitle = sanitize($_POST['subtitle'] ?? '');
    $category = sanitize($_POST['category'] ?? 'Daily');
    $amount = (float)($_POST['suggested_amount'] ?? 500.00);
    $description = sanitize($_POST['description'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    $imagePath = $_POST['existing_image'] ?? 'images/seva/seva-default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = upload_file($_FILES['image'], 'images/seva');
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filepath'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }

    if (empty($title)) $errors[] = 'Title is required.';

    if (empty($errors)) {
        if ($editId > 0) {
            $stmt = $pdo->prepare("UPDATE seva SET title = ?, subtitle = ?, category = ?, suggested_amount = ?, description = ?, image = ?, is_featured = ? WHERE id = ?");
            $stmt->execute([$title, $subtitle, $category, $amount, $description, $imagePath, $isFeatured, $editId]);
            set_flash('success', 'Seva program updated.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO seva (title, subtitle, category, suggested_amount, description, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $subtitle, $category, $amount, $description, $imagePath, $isFeatured]);
            set_flash('success', 'New Seva program added.');
        }
        header('Location: seva.php');
        exit;
    }
}

$editSeva = null;
if ($action === 'edit' && $editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM seva WHERE id = ?");
    $stmt->execute([$editId]);
    $editSeva = $stmt->fetch();
}

$sevaList = $pdo->query("SELECT * FROM seva ORDER BY id ASC")->fetchAll();
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  
  <div style="background:white; padding:30px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); max-width:800px;">
    <h3><?= $action === 'edit' ? 'Edit Seva Program' : 'Add New Seva Program' ?></h3>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" action="seva.php?action=<?= $action ?>&id=<?= $editId ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editSeva['image'] ?? 'images/seva/seva-default.jpg') ?>">

      <div class="form-group">
        <label class="form-label">Title *</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($editSeva['title'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Subtitle</label>
        <input type="text" name="subtitle" class="form-control" value="<?= htmlspecialchars($editSeva['subtitle'] ?? '') ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category" class="form-control">
            <option value="Daily" <?= ($editSeva['category'] ?? '') === 'Daily' ? 'selected' : '' ?>>Daily</option>
            <option value="Medical" <?= ($editSeva['category'] ?? '') === 'Medical' ? 'selected' : '' ?>>Medical</option>
            <option value="Feeding" <?= ($editSeva['category'] ?? '') === 'Feeding' ? 'selected' : '' ?>>Feeding</option>
            <option value="Shelter" <?= ($editSeva['category'] ?? '') === 'Shelter' ? 'selected' : '' ?>>Shelter</option>
            <option value="Special" <?= ($editSeva['category'] ?? '') === 'Special' ? 'selected' : '' ?>>Special</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Suggested Amount (₹)</label>
          <input type="number" step="0.01" name="suggested_amount" class="form-control" value="<?= (float)($editSeva['suggested_amount'] ?? 501.00) ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($editSeva['description'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px;">
        <input type="checkbox" name="is_featured" value="1" id="feat" <?= !isset($editSeva) || $editSeva['is_featured'] ? 'checked' : '' ?>>
        <label for="feat">Display on Home Page</label>
      </div>

      <div style="display:flex; gap:15px;">
        <button type="submit" class="btn btn-primary">Save Seva Program</button>
        <a href="seva.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>

<?php else: ?>

  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h3>Seva Programs (<?= count($sevaList) ?>)</h3>
    <a href="seva.php?action=add" class="btn btn-primary">+ Add New Seva</a>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Title</th>
          <th>Category</th>
          <th>Suggested Fee</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($sevaList as $s): ?>
          <tr>
            <td><img src="<?= '../' . htmlspecialchars($s['image']) ?>" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=100&q=80'" style="width:50px; height:50px; object-fit:cover; border-radius:var(--radius-sm);"></td>
            <td><strong><?= htmlspecialchars($s['title']) ?></strong></td>
            <td><?= htmlspecialchars($s['category']) ?></td>
            <td><?= format_currency($s['suggested_amount']) ?></td>
            <td><?= $s['is_featured'] ? 'Yes' : 'No' ?></td>
            <td>
              <a href="seva.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="seva.php?action=delete&id=<?= $s['id'] ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Delete this Seva?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828;">Delete</a>
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
