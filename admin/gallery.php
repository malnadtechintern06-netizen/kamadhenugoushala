<?php
// admin/gallery.php - Admin Gallery Photo Management (Upload, Edit, Delete)

$adminPageTitle = 'Manage Gallery Photos';
require_once __DIR__ . '/header_sidebar.php';

$errors = [];
$editId = (int)($_GET['edit_id'] ?? 0);

// Delete action
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

// Fetch photo for editing if edit_id is set
$editItem = null;
if ($editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

// Handle Form Submission (Create & Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    }

    $postEditId = (int)($_POST['edit_id'] ?? 0);
    $title = sanitize($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $description = sanitize($_POST['description'] ?? '');
    $existingImage = $_POST['existing_image'] ?? '';

    if (empty($title)) $errors[] = 'Title is required.';

    $imagePath = $existingImage;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = upload_file($_FILES['image'], 'images/gallery');
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filepath'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    } elseif ($postEditId <= 0 && empty($imagePath)) {
        $errors[] = 'Valid image file is required for new photos.';
    }

    if (empty($errors)) {
        if ($postEditId > 0) {
            $stmt = $pdo->prepare("UPDATE gallery SET category_id = ?, title = ?, image_path = ?, description = ? WHERE id = ?");
            $stmt->execute([$categoryId, $title, $imagePath, $description, $postEditId]);
            set_flash('success', 'Gallery photo updated successfully.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO gallery (category_id, title, image_path, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$categoryId, $title, $imagePath, $description]);
            set_flash('success', 'New photo uploaded to gallery.');
        }
        header('Location: gallery.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM gallery_categories ORDER BY id ASC")->fetchAll();
$galleryPhotos = $pdo->query("SELECT g.*, c.name as category_name FROM gallery g JOIN gallery_categories c ON g.category_id = c.id ORDER BY g.id DESC")->fetchAll();
?>

<div class="grid-responsive-2" style="align-items:start;">
  
  <!-- Upload / Edit Form -->
  <div style="background:white; padding:25px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light);">
    <h3 style="margin-bottom:20px;"><?= $editItem ? 'Edit Gallery Photo ✏️' : 'Upload New Gallery Photo 📤' ?></h3>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" action="gallery.php" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="edit_id" value="<?= $editItem['id'] ?? 0 ?>">
      <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editItem['image_path'] ?? '') ?>">

      <div class="form-group">
        <label class="form-label">Photo Title *</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($editItem['title'] ?? ($_POST['title'] ?? '')) ?>" placeholder="e.g. Gir Cows Morning Pasture" required>
      </div>

      <div class="form-group">
        <label class="form-label">Category *</label>
        <select name="category_id" class="form-control" required>
          <?php foreach ($categories as $cat): ?>
            <?php if ($cat['slug'] === 'all') continue; ?>
            <option value="<?= $cat['id'] ?>" <?= (isset($editItem['category_id']) && $editItem['category_id'] == $cat['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Image File <?= $editItem ? '(Leave blank to keep current image)' : '*' ?></label>
        <?php if (!empty($editItem['image_path'])): ?>
          <div style="margin-bottom:10px;">
            <img src="<?= '../' . htmlspecialchars($editItem['image_path']) ?>" style="width:100px; height:70px; object-fit:cover; border-radius:6px; border:1px solid var(--border-light);" alt="Current Image">
          </div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control" <?= $editItem ? '' : 'required' ?>>
      </div>

      <div class="form-group">
        <label class="form-label">Description / Caption</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the photo..."><?= htmlspecialchars($editItem['description'] ?? ($_POST['description'] ?? '')) ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary btn-block"><?= $editItem ? 'Update Photo ✏️' : 'Upload Photo 📤' ?></button>

      <?php if ($editItem): ?>
        <a href="gallery.php" class="btn btn-outline btn-block" style="margin-top:10px; text-align:center; display:block;">Cancel Edit</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Photos Table -->
  <div style="grid-column: span 2;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
      <h3 style="margin:0;">Gallery Photos Log (<?= count($galleryPhotos) ?>)</h3>
    </div>

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
            <tr style="<?= ($editId === (int)$g['id']) ? 'background: rgba(255, 152, 0, 0.08);' : '' ?>">
              <td><img src="<?= '../' . htmlspecialchars($g['image_path']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'" style="width:60px; height:50px; object-fit:cover; border-radius:var(--radius-sm);"></td>
              <td>
                <strong><?= htmlspecialchars($g['title']) ?></strong>
                <?php if (!empty($g['description'])): ?>
                  <br><small style="color:var(--text-muted);"><?= htmlspecialchars(mb_strimwidth($g['description'], 0, 50, '...')) ?></small>
                <?php endif; ?>
              </td>
              <td><span class="card-badge" style="position:static;"><?= htmlspecialchars($g['category_name']) ?></span></td>
              <td><?= date('d M Y', strtotime($g['created_at'])) ?></td>
              <td>
                <div style="display:flex; gap:6px;">
                  <a href="gallery.php?edit_id=<?= $g['id'] ?>" class="btn btn-primary btn-sm">Edit ✏️</a>
                  <a href="gallery.php?action=delete&id=<?= $g['id'] ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Delete photo?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828;">Delete 🗑️</a>
                </div>
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
