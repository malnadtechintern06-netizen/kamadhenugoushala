<?php
// admin/events.php - Admin Events CRUD Management

$adminPageTitle = 'Manage Sanctuary Events';
require_once __DIR__ . '/header_sidebar.php';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$errors = [];

if ($action === 'delete' && $editId > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$editId]);
        set_flash('success', 'Event deleted.');
    }
    header('Location: events.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    }

    $title = sanitize($_POST['title'] ?? '');
    $eventDate = sanitize($_POST['event_date'] ?? '');
    $eventTime = sanitize($_POST['event_time'] ?? '09:00 AM - 01:00 PM');
    $location = sanitize($_POST['location'] ?? 'Kamadhenu Dham, Vrindavan');
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Upcoming');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

    $imagePath = $_POST['existing_image'] ?? 'images/events/event-default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = upload_file($_FILES['image'], 'images/events');
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filepath'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }

    if (empty($title)) $errors[] = 'Event Title is required.';
    if (empty($eventDate)) $errors[] = 'Event Date is required.';

    if (empty($errors)) {
        if ($editId > 0) {
            $stmt = $pdo->prepare("UPDATE events SET title = ?, slug = ?, event_date = ?, event_time = ?, location = ?, description = ?, image = ?, is_featured = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $eventDate, $eventTime, $location, $description, $imagePath, $isFeatured, $status, $editId]);
            set_flash('success', 'Event updated successfully.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO events (title, slug, event_date, event_time, location, description, image, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $eventDate, $eventTime, $location, $description, $imagePath, $isFeatured, $status]);
            set_flash('success', 'New event created successfully.');
        }
        header('Location: events.php');
        exit;
    }
}

$editEvent = null;
if ($action === 'edit' && $editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$editId]);
    $editEvent = $stmt->fetch();
}

$eventsList = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll();
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  
  <div style="background:white; padding:30px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); max-width:800px;">
    <h3><?= $action === 'edit' ? 'Edit Event' : 'Create New Event' ?></h3>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" action="events.php?action=<?= $action ?>&id=<?= $editId ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editEvent['image'] ?? 'images/events/event-default.jpg') ?>">

      <div class="form-group">
        <label class="form-label">Event Title *</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($editEvent['title'] ?? '') ?>" required placeholder="e.g. Gopashtami Mahotsav & Cow Pooja">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Event Date *</label>
          <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($editEvent['event_date'] ?? date('Y-m-d')) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Timing *</label>
          <input type="text" name="event_time" class="form-control" value="<?= htmlspecialchars($editEvent['event_time'] ?? '09:00 AM - 01:00 PM') ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Location / Venue *</label>
          <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($editEvent['location'] ?? 'Kamadhenu Dham, Vrindavan') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="Upcoming" <?= ($editEvent['status'] ?? '') === 'Upcoming' ? 'selected' : '' ?>>Upcoming</option>
            <option value="Ongoing" <?= ($editEvent['status'] ?? '') === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
            <option value="Completed" <?= ($editEvent['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Event Description</label>
        <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($editEvent['description'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Event Banner Image</label>
        <input type="file" name="image" class="form-control">
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px;">
        <input type="checkbox" name="is_featured" value="1" id="efeat" <?= !isset($editEvent) || $editEvent['is_featured'] ? 'checked' : '' ?>>
        <label for="efeat">Display on Home Page</label>
      </div>

      <div style="display:flex; gap:15px;">
        <button type="submit" class="btn btn-primary">Save Event</button>
        <a href="events.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>

<?php else: ?>

  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h3>Events Registry (<?= count($eventsList) ?>)</h3>
    <a href="events.php?action=add" class="btn btn-primary">+ Create New Event</a>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Banner</th>
          <th>Title</th>
          <th>Date & Time</th>
          <th>Location</th>
          <th>Status</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($eventsList as $ev): ?>
          <tr>
            <td><img src="<?= '../' . htmlspecialchars($ev['image']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=100&q=80'" style="width:60px; height:45px; object-fit:cover; border-radius:var(--radius-sm);"></td>
            <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
            <td><?= date('d M Y', strtotime($ev['event_date'])) ?><br><small><?= htmlspecialchars($ev['event_time']) ?></small></td>
            <td><?= htmlspecialchars($ev['location']) ?></td>
            <td><span class="card-badge" style="position:static; background:<?= $ev['status'] === 'Upcoming' ? 'var(--accent-orange)' : '#757575'; ?>;"><?= htmlspecialchars($ev['status']) ?></span></td>
            <td><?= $ev['is_featured'] ? 'Yes' : 'No' ?></td>
            <td>
              <a href="events.php?action=edit&id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="events.php?action=delete&id=<?= $ev['id'] ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Delete event?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828;">Delete</a>
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
