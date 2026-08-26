<?php
// pages/events.php - Events & Festivals Catalog Page

$pageTitle = 'Events & Festivals - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';

$search = sanitize($_GET['search'] ?? '');
$statusFilter = sanitize($_GET['status'] ?? '');

$query = "SELECT * FROM events WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR location LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($statusFilter)) {
    $query .= " AND status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY event_date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll();
?>

<div class="page-banner">
  <div class="container">
    <h1><?= __('banner_events', 'Sanctuary Events & Festivals') ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <span><?= __('nav_events', 'Events') ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <!-- Filter Bar -->
    <form method="GET" action="events.php" class="filter-bar">
      <div class="filter-buttons">
        <a href="events.php" class="filter-btn <?= empty($statusFilter) ? 'active' : '' ?>"><?= __('all_events', 'All Events') ?></a>
        <a href="events.php?status=Upcoming" class="filter-btn <?= $statusFilter === 'Upcoming' ? 'active' : '' ?>"><?= __('upcoming', 'Upcoming') ?></a>
        <a href="events.php?status=Ongoing" class="filter-btn <?= $statusFilter === 'Ongoing' ? 'active' : '' ?>"><?= __('ongoing', 'Ongoing') ?></a>
        <a href="events.php?status=Completed" class="filter-btn <?= $statusFilter === 'Completed' ? 'active' : '' ?>"><?= __('completed', 'Completed') ?></a>
      </div>

      <div class="search-input-group">
        <input type="text" name="search" class="form-control" placeholder="<?= __('search_events_ph', 'Search events...') ?>" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-secondary btn-sm"><?= __('btn_filter', 'Search') ?></button>
      </div>
    </form>

    <!-- Events Grid -->
    <?php if (count($events) > 0): ?>
      <div class="card-grid">
        <?php foreach ($events as $event): ?>
          <div class="card">
            <div class="card-img-wrapper">
              <img src="<?= $baseUrl . htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
              <span class="card-badge" style="background-color: <?= $event['status'] === 'Upcoming' ? 'var(--accent-orange)' : ($event['status'] === 'Ongoing' ? '#2E7D32' : '#757575') ?>;">
                <?= htmlspecialchars(__($event['status'], $event['status'])) ?>
              </span>
            </div>
            <div class="card-body">
              <div style="font-size:0.85rem; color:var(--accent-orange); font-weight:bold; margin-bottom:6px;">
                📅 <?= date('l, d M Y', strtotime($event['event_date'])) ?>
              </div>
              <h3 class="card-title"><?= htmlspecialchars(__($event['title'], $event['title'])) ?></h3>
              <p class="card-subtitle">
                ⏰ <?= htmlspecialchars($event['event_time']) ?><br>
                📍 <?= htmlspecialchars(__($event['location'], $event['location'])) ?>
              </p>
              <p class="card-text"><?= htmlspecialchars(mb_strimwidth(__($event['description'], $event['description']), 0, 110, '...')) ?></p>
              
              <div class="card-actions" style="margin-top:auto;">
                <a href="event-details.php?id=<?= $event['id'] ?>" class="btn btn-primary btn-sm btn-block"><?= __('btn_view_event', 'View Event & Attend 🗓') ?></a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center" style="padding:60px; background:white; border-radius:var(--radius-md);">
        <h3><?= __('no_events_found', 'No events found.') ?></h3>
        <p style="color:var(--text-muted); margin-bottom:20px;"><?= __('no_records_found', 'Try adjusting your search query or filter.') ?></p>
        <a href="events.php" class="btn btn-secondary"><?= __('btn_view_all_events', 'View All Events') ?></a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
