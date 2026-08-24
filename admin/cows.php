<?php
// admin/cows.php - Admin Cow Management CRUD

$adminPageTitle = 'Manage Cows';
require_once __DIR__ . '/header_sidebar.php';

$errors = [];
$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// Handle Delete Action
if ($action === 'delete' && $editId > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("DELETE FROM cows WHERE id = ?");
        $stmt->execute([$editId]);
        set_flash('success', 'Cow record deleted successfully.');
    }
    header('Location: cows.php');
    exit;
}

// Handle Form Submissions (Create / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    }

    $tagNumber = sanitize($_POST['tag_number'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $breed = sanitize($_POST['breed'] ?? '');
    $ageYears = (int)($_POST['age_years'] ?? 1);
    $gender = sanitize($_POST['gender'] ?? 'Female');
    $healthStatus = sanitize($_POST['health_status'] ?? 'Healthy');
    $adoptionStatus = sanitize($_POST['adoption_status'] ?? 'Available');
    $fee = (float)($_POST['monthly_adoption_fee'] ?? 1500.00);
    $bio = sanitize($_POST['bio'] ?? '');
    $checkoutMode = sanitize($_POST['checkout_mode'] ?? 'default');
    $whatsappNumber = sanitize($_POST['whatsapp_number'] ?? '');

    if (empty($tagNumber)) $errors[] = 'Tag Number is required.';
    if (empty($name)) $errors[] = 'Cow Name is required.';
    if (empty($breed)) $errors[] = 'Breed is required.';

    // Image Upload Handling
    $imagePath = $_POST['existing_image'] ?? 'images/cows/cow-default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = upload_file($_FILES['image'], 'images/cows');
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filepath'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }

    if (empty($errors)) {
        if ($editId > 0) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE cows SET tag_number = ?, name = ?, breed = ?, age_years = ?, gender = ?, health_status = ?, adoption_status = ?, monthly_adoption_fee = ?, bio = ?, main_image = ?, checkout_mode = ?, whatsapp_number = ?
                WHERE id = ?
            ");
            $stmt->execute([$tagNumber, $name, $breed, $ageYears, $gender, $healthStatus, $adoptionStatus, $fee, $bio, $imagePath, $checkoutMode, $whatsappNumber, $editId]);
            set_flash('success', 'Cow record updated.');
        } else {
            // Insert
            $stmt = $pdo->prepare("
                INSERT INTO cows (tag_number, name, breed, age_years, gender, health_status, adoption_status, monthly_adoption_fee, bio, main_image, checkout_mode, whatsapp_number)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$tagNumber, $name, $breed, $ageYears, $gender, $healthStatus, $adoptionStatus, $fee, $bio, $imagePath, $checkoutMode, $whatsappNumber]);
            set_flash('success', 'New cow added successfully.');
        }
        header('Location: cows.php');
        exit;
    }
}

// Fetch Cow for Edit
$editCow = null;
if ($action === 'edit' && $editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM cows WHERE id = ?");
    $stmt->execute([$editId]);
    $editCow = $stmt->fetch();
}

$cowsList = $pdo->query("SELECT * FROM cows ORDER BY id DESC")->fetchAll();
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  
  <div style="background:white; padding:35px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); width:100%; margin-bottom:30px;">
    <h3 style="margin-bottom:20px; font-size:1.4rem; color:var(--primary-dark); border-bottom:2px solid var(--bg-light-green); padding-bottom:12px;"><?= $action === 'edit' ? 'Edit Cow Details' : 'Add New Cow Record' ?></h3>
    
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="cows.php?action=<?= $action ?>&id=<?= $editId ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editCow['main_image'] ?? 'images/cows/cow-default.jpg') ?>">

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tag Number *</label>
          <input type="text" name="tag_number" class="form-control" value="<?= htmlspecialchars($editCow['tag_number'] ?? ('KG-0' . rand(100, 999))) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Cow Name *</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($editCow['name'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Breed *</label>
          <input type="text" name="breed" class="form-control" value="<?= htmlspecialchars($editCow['breed'] ?? 'Gir') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Age (Years) *</label>
          <input type="number" name="age_years" class="form-control" value="<?= (int)($editCow['age_years'] ?? 2) ?>" min="1" required>
        </div>
        <div class="form-group">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-control">
            <option value="Female" <?= ($editCow['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Male" <?= ($editCow['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Calf" <?= ($editCow['gender'] ?? '') === 'Calf' ? 'selected' : '' ?>>Calf</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Health Status</label>
          <select name="health_status" class="form-control">
            <option value="Healthy" <?= ($editCow['health_status'] ?? '') === 'Healthy' ? 'selected' : '' ?>>Healthy</option>
            <option value="Under Treatment" <?= ($editCow['health_status'] ?? '') === 'Under Treatment' ? 'selected' : '' ?>>Under Treatment</option>
            <option value="Rescued - Recovering" <?= ($editCow['health_status'] ?? '') === 'Rescued - Recovering' ? 'selected' : '' ?>>Rescued - Recovering</option>
            <option value="Special Care" <?= ($editCow['health_status'] ?? '') === 'Special Care' ? 'selected' : '' ?>>Special Care</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Adoption Status</label>
          <select name="adoption_status" class="form-control">
            <option value="Available" <?= ($editCow['adoption_status'] ?? '') === 'Available' ? 'selected' : '' ?>>Available</option>
            <option value="Adopted" <?= ($editCow['adoption_status'] ?? '') === 'Adopted' ? 'selected' : '' ?>>Adopted</option>
            <option value="Pending" <?= ($editCow['adoption_status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Monthly Adoption Fee (₹)</label>
          <input type="number" step="0.01" name="monthly_adoption_fee" class="form-control" value="<?= (float)($editCow['monthly_adoption_fee'] ?? 1500.00) ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" style="font-weight:bold; color:var(--accent-orange);">🛒 Checkout / Adoption Option for this Cow</label>
        <select name="checkout_mode" class="form-control" style="font-weight:bold; background:#fffcf6; border:1.5px solid var(--accent-orange);">
          <option value="default" <?= ($editCow['checkout_mode'] ?? 'default') === 'default' ? 'selected' : '' ?>>⚙️ Global Setting Default (Use Admin Settings Mode)</option>
          <option value="both" <?= ($editCow['checkout_mode'] ?? 'default') === 'both' ? 'selected' : '' ?>>🤝 Both Options (Show Both Website &amp; WhatsApp Adoption Buttons)</option>
          <option value="whatsapp" <?= ($editCow['checkout_mode'] ?? 'default') === 'whatsapp' ? 'selected' : '' ?>>📱 WhatsApp Checkout Only (Direct WhatsApp Chat Link)</option>
          <option value="website" <?= ($editCow['checkout_mode'] ?? 'default') === 'website' ? 'selected' : '' ?>>🌐 Website Checkout Only (Online Form &amp; Payment)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" style="font-weight:bold; color:#2E7D32;">📱 Select WhatsApp Helpline Number for this Cow</label>
        <select name="whatsapp_number" class="form-control" style="font-weight:bold;">
          <option value="">⚙️ Default Primary WhatsApp Number (Use Admin Settings Default)</option>
          <?php foreach (get_whatsapp_numbers_list() as $key => $phoneInfo): ?>
            <option value="<?= htmlspecialchars($phoneInfo['number']) ?>" <?= ($editCow['whatsapp_number'] ?? '') === $phoneInfo['number'] ? 'selected' : '' ?>>
              📞 <?= htmlspecialchars($phoneInfo['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small style="color:var(--text-muted); font-size:0.8rem; display:block; margin-top:4px;">Choose which helpline number receives WhatsApp adoption inquiries for this specific cow.</small>
      </div>

      <div class="form-group">
        <label class="form-label">Biography & Rescue Details</label>
        <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($editCow['bio'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Cow Image</label>
        <input type="file" name="image" class="form-control">
      </div>

      <div style="display:flex; gap:15px;">
        <button type="submit" class="btn btn-primary">Save Cow Record</button>
        <a href="cows.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>

<?php else: ?>

  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h3>Sanctuary Cow Registry (<?= count($cowsList) ?>)</h3>
    <a href="cows.php?action=add" class="btn btn-primary">+ Add New Cow</a>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Tag #</th>
          <th>Name</th>
          <th>Breed / Age</th>
          <th>Health Status</th>
          <th>Adoption</th>
          <th>Monthly Fee</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cowsList as $cow): ?>
          <tr>
            <td>
              <img src="<?= '../' . htmlspecialchars($cow['main_image']) ?>" onerror="this.src='https://images.unsplash.com/photo-1570042707223-2cb2ed999557?auto=format&fit=crop&w=100&q=80'" style="width:50px; height:50px; object-fit:cover; border-radius:var(--radius-sm);">
            </td>
            <td><strong><?= htmlspecialchars($cow['tag_number']) ?></strong></td>
            <td><?= htmlspecialchars($cow['name']) ?></td>
            <td><?= htmlspecialchars($cow['breed']) ?> (<?= $cow['age_years'] ?> Yrs)</td>
            <td><span class="card-badge status-<?= strtolower(str_replace(' ', '-', $cow['health_status'])) ?>" style="position:static;"><?= htmlspecialchars($cow['health_status']) ?></span></td>
            <td><strong><?= htmlspecialchars($cow['adoption_status']) ?></strong></td>
            <td><?= format_currency($cow['monthly_adoption_fee']) ?></td>
            <td>
              <a href="cows.php?action=edit&id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="cows.php?action=delete&id=<?= $cow['id'] ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Are you sure you want to delete this cow?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828;">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
