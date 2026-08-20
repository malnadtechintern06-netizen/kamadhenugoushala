<?php
// pages/contact.php - Contact & Inquiry Page

$pageTitle = 'Contact Us - Kamadhenu Goushala Sanctuary';
require_once __DIR__ . '/../includes/header.php';

$errors = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid. Please resubmit.';
    }

    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name)) $errors[] = 'Your Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email Address is required.';
    if (empty($subject)) $errors[] = 'Subject is required.';
    if (empty($message)) $errors[] = 'Message content is required.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message, is_read)
            VALUES (?, ?, ?, ?, ?, 0)
        ");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        
        $successMsg = 'Thank you for reaching out to Kamadhenu Goushala! Your message has been sent successfully. Our team will get back to you shortly.';
        $_POST = []; // Clear inputs
    }
}
?>

<div class="page-banner">
  <div class="container">
    <h1>Contact & Visit Us</h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php">Home</a> / <span>Contact</span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
      
      <!-- Contact Info Cards -->
      <div>
        <span class="section-subtitle">VISIT & CONNECT</span>
        <h2 style="color:var(--primary-dark); margin-bottom:20px;">We Would Love To Hear From You</h2>
        <p style="color:var(--text-dark); margin-bottom:30px; line-height:1.8;">
          Visitors, volunteers, and devotees are welcome to visit our sanctuary. Experience the joy of feeding green grass and petting gentle Gir cows in person.
        </p>

        <div style="display:flex; flex-direction:column; gap:20px;">
          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--accent-orange);">📍</div>
            <div>
              <h4 style="margin-bottom:2px;">Sanctuary Address</h4>
              <p style="font-size:0.9rem; color:var(--text-muted);"><?= htmlspecialchars(get_setting('contact_address', 'Vrinda Dham, Mathura Highway, UP')) ?></p>
            </div>
          </div>

          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--primary-green);">📞</div>
            <div>
              <h4 style="margin-bottom:2px;">Helpline & WhatsApp</h4>
              <p style="font-size:0.9rem; color:var(--text-muted);"><?= htmlspecialchars(get_setting('contact_phone', '+91 98765 43210')) ?></p>
            </div>
          </div>

          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--primary-dark);">📧</div>
            <div>
              <h4 style="margin-bottom:2px;">Email Support</h4>
              <p style="font-size:0.9rem; color:var(--text-muted);"><?= htmlspecialchars(get_setting('contact_email', 'info@kamadhenugoushala.org')) ?></p>
            </div>
          </div>

          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--accent-gold);">⏰</div>
            <div>
              <h4 style="margin-bottom:2px;">Visiting Hours</h4>
              <p style="font-size:0.9rem; color:var(--text-muted);">Open Daily: 7:00 AM – 12:00 PM & 4:00 PM – 7:00 PM</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Column -->
      <div>
        <div class="form-card" style="max-width:100%; margin:0;">
          <h3 style="margin-bottom:20px; color:var(--primary-dark);">Send Us a Message</h3>

          <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success">
              <?= htmlspecialchars($successMsg) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
              <ul>
                <?php foreach ($errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST" action="contact.php">
            <?= csrf_field() ?>

            <div class="form-group">
              <label class="form-label">Your Name *</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Subject *</label>
              <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" placeholder="e.g. Fodder Donation / Goushala Visit" required>
            </div>

            <div class="form-group">
              <label class="form-label">Message *</label>
              <textarea name="message" class="form-control" rows="5" required placeholder="Write your message here..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Send Message 📩</button>
          </form>
        </div>
      </div>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
