<?php
// admin/gallery.php - Admin Gallery Photo Management

$adminPageTitle = 'Manage Gallery Photos';
require_once __DIR__ . '/header_sidebar.php';

$errors = [];

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $delId = (int)($_GET['id'] ?? 0);
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$delId]);
        set_flash('success', 'Photo removed from gallery.');
    }
    header('Location: gallery.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    }

    $title = sanitize($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $description = sanitize($_POST['description'] ?? '');

    if (empty($title)) $errors[] = 'Title is required.';
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Valid image file is required.';
    }

    if (empty($errors)) {
        $uploadResult = upload_file($_FILES['image'], 'images/gallery');
        if ($uploadResult['success']) {
            $stmt = $pdo->prepare("INSERT INTO gallery (category_id, title, image_path, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$categoryId, $title, $uploadResult['filepath'], $description]);
            set_flash('success', 'New photo uploaded to gallery.');
            header('Location: gallery.php');
            exit;
        } else {
            $errors[] = $uploadResult['error'];
        }
    }
}

$categories = $pdo->query("SELECT * FROM gallery_categories ORDER BY id ASC")->fetchAll();
$galleryPhotos = $pdo->query("SELECT g.*, c.name as category_name FROM gallery g JOIN gallery_categories c ON g.category_id = c.id ORDER BY g.id DESC")->fetchAll();
?>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:30px; align-items:start;">
  
  <!-- Upload Form -->
  <div style="background:white; padding:25px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light);">
    <h3 style="margin-bottom:20px;">Upload New Gallery Photo</h3>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" action="gallery.php" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label">Photo Title *</label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Gir Cows Morning Pasture" required>
      </div>

      <div class="form-group">
        <label class="form-label">Category *</label>
        <select name="category_id" class="form-control" required>
          <?php foreach ($categories as $cat): ?>
            <?php if ($cat['slug'] === 'all') continue; ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Image File *</label>
        <input type="file" name="image" class="form-control" required>
      </div>

      <div class="form-group">
        <label class="form-label">Description / Caption</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the photo..."></textarea>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Upload Photo 📤</button>
    </form>
  </div>

  <!-- Photos Table -->
  <div style="grid-column: span 2;">
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Category</th>
            <th>Uploaded Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($galleryPhotos as $g): ?>
            <tr>
              <td><img src="<?= '../' . htmlspecialchars($g['image_path']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'" style="width:60px; height:50px; object-fit:cover; border-radius:var(--radius-sm);"></td>
              <td><strong><?= htmlspecialchars($g['title']) ?></strong></td>
              <td><?= htmlspecialchars($g['category_name']) ?></td>
              <td><?= date('d M Y', strtotime($g['created_at'])) ?></td>
              <td>
                <a href="gallery.php?action=delete&id=<?= $g['id'] ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Delete photo?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828;">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

</main>
</div>
</body>
</html>
