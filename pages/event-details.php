<?php
// pages/event-details.php - Event Detail & RSVP Page

require_once __DIR__ . '/../includes/header.php';

$eventId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    echo "<div class='container text-center' style='padding:100px 0;'><h2>Event Not Found</h2><p>The requested event does not exist.</p><a href='events.php' class='btn btn-secondary'>Back to Events</a></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = htmlspecialchars($event['title']) . " - Kamadhenu Goushala Events";

$rsvpSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rsvp_submit'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $rsvpSuccess = true;
        set_flash('success', 'Thank you for registering to attend! We look forward to welcoming you to ' . htmlspecialchars($event['title']) . '.');
    }
}
?>

<div class="page-banner">
  <div class="container">
    <h1><?= htmlspecialchars($event['title']) ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <a href="events.php">Events</a> / <span><?= htmlspecialchars($event['title']) ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-white">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 50px;">
      
      <!-- Event Image & Info Card -->
      <div>
        <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--border-light); margin-bottom: 25px;">
          <img src="<?= $baseUrl . htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:380px; object-fit:cover;">
        </div>

        <div style="background: var(--bg-light-green); padding: 25px; border-radius: var(--radius-md); border: 1px solid var(--border-light); font-size: 1rem; line-height: 1.8;">
          <h4 style="color:var(--primary-dark); margin-bottom: 15px;">Event Information</h4>
          <p>📅 <strong>Date:</strong> <?= date('l, F j, Y', strtotime($event['event_date'])) ?></p>
          <p>⏰ <strong>Timing:</strong> <?= htmlspecialchars($event['event_time']) ?></p>
          <p>📍 <strong>Venue Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
          <p>🏷 <strong>Status:</strong> <span class="card-badge" style="position:static; display:inline-block;"><?= htmlspecialchars($event['status']) ?></span></p>
        </div>
      </div>

      <!-- Description & RSVP Form -->
      <div>
        <span class="section-subtitle">SANCTUARY CELEBRATION</span>
        <h2 style="font-size: 2.2rem; color: var(--primary-dark); margin-bottom: 15px;"><?= htmlspecialchars($event['title']) ?></h2>

        <div style="color: var(--text-dark); line-height: 1.8; margin-bottom: 35px;">
          <?= nl2br(htmlspecialchars($event['description'])) ?>
        </div>

        <!-- RSVP Form Box -->
        <div class="form-card" style="margin:0; max-width:100%;">
          <h3 style="margin-bottom:15px; color:var(--primary-dark);">RSVP / Register To Attend</h3>
          <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:20px;">Registration is free. Let us know how many guests will accompany you so we can arrange adequate seating and prasadam.</p>

          <?php if ($rsvpSuccess): ?>
            <div class="alert alert-success">
              ✓ Registration Confirmed! A confirmation message has been saved. See you at the sanctuary!
            </div>
          <?php endif; ?>

          <form method="POST" action="event-details.php?id=<?= $event['id'] ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="rsvp_submit" value="1">

            <div class="form-group">
              <label class="form-label">Your Name *</label>
              <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
              </div>
              <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210" required>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Number of Attendees</label>
              <input type="number" name="attendees" class="form-control" value="1" min="1" max="20" required>
            </div>

            <div style="display:flex; gap:15px;">
              <button type="submit" class="btn btn-primary btn-lg" style="flex:1;">Confirm Registration 🗓</button>
              <a href="donate.php?purpose=<?= urlencode($event['title']) ?>" class="btn btn-secondary btn-lg" style="flex:1;">Sponsor Event 💖</a>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
