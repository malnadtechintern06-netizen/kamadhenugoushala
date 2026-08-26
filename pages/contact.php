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
    <h1><?= __('contact_heading', 'Contact & Visit Us') ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <span><?= __('nav_contact', 'Contact') ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
      
      <!-- Contact Info Cards -->
      <div>
        <span class="section-subtitle"><?= __('get_in_touch', 'VISIT & CONNECT') ?></span>
        <h2 style="color:var(--primary-dark); margin-bottom:20px;"><?= __('send_us_message', 'We Would Love To Hear From You') ?></h2>
        <p style="color:var(--text-dark); margin-bottom:30px; line-height:1.8;">
          <?= __('contact_intro_text', 'Visitors, volunteers, and devotees are welcome to visit our sanctuary. Experience the joy of feeding green grass and petting gentle Gir cows in person.') ?>
        </p>

        <div style="display:flex; flex-direction:column; gap:20px;">
          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--accent-orange);">📍</div>
            <div>
              <h4 style="margin-bottom:2px;"><?= __('sanctuary_address', 'Sanctuary Address') ?></h4>
              <p style="font-size:0.9rem; color:var(--text-muted);"><?= htmlspecialchars(get_setting('contact_address', 'Vrinda Dham, Mathura Highway, UP')) ?></p>
            </div>
          </div>

          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--primary-green);">📞</div>
            <div>
              <h4 style="margin-bottom:2px;"><?= __('helpline_whatsapp', 'Helpline & WhatsApp') ?></h4>
              <p style="font-size:0.9rem; color:var(--text-muted);"><?= htmlspecialchars(get_setting('contact_phone', '+91 98765 43210')) ?></p>
            </div>
          </div>

          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--primary-dark);">📧</div>
            <div>
              <h4 style="margin-bottom:2px;"><?= __('email_support', 'Email Support') ?></h4>
              <p style="font-size:0.9rem; color:var(--text-muted);"><?= htmlspecialchars(get_setting('contact_email', 'info@kamadhenugoushala.org')) ?></p>
            </div>
          </div>

          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--accent-gold);">⏰</div>
            <div>
              <h4 style="margin-bottom:2px;"><?= __('visiting_hours', 'Visiting Hours') ?></h4>
              <p style="font-size:0.9rem; color:var(--text-muted);"><?= __('visiting_hours_time', 'Open Daily: 7:00 AM – 12:00 PM & 4:00 PM – 7:00 PM') ?></p>
            </div>
          </div>

          <div style="background:white; padding:20px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light); display:flex; gap:15px; align-items:center;">
            <div style="font-size:2rem; color:var(--primary-dark); display: flex; align-items: center;">📱</div>
            <div>
              <h4 style="margin-bottom:6px;"><?= __('follow_us_online', 'Follow Us Online') ?></h4>
              <div style="display:flex; gap:15px; align-items:center;">
                <a href="<?= htmlspecialchars(get_setting('facebook_url', 'https://facebook.com')) ?>" class="social-fb" target="_blank" title="Facebook" style="color:var(--primary-dark); font-size:1.4rem; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h137.9V327.7h-63v-72.2h63v-55c0-62.2 38-96.4 93.6-96.4 26.6 0 54.7 4.7 54.7 4.7v60h-30.8c-30.8 0-40.4 19.1-40.4 38.7v46.4h67.8l-10.8 72.2h-57V480H400c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48z"></path></svg></a>
                <a href="<?= htmlspecialchars(get_setting('instagram_url', 'https://instagram.com')) ?>" class="social-ig" target="_blank" title="Instagram" style="color:var(--primary-dark); font-size:1.4rem; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.8 9.9 67.6 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg></a>
                <a href="<?= htmlspecialchars(get_setting('youtube_url', 'https://youtube.com')) ?>" class="social-yt" target="_blank" title="YouTube" style="color:var(--primary-dark); font-size:1.4rem; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" height="1.1em" width="1.1em" xmlns="http://www.w3.org/2000/svg"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-12.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"></path></svg></a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Column -->
      <div>
        <div class="form-card" style="max-width:100%; margin:0;">
          <h3 style="margin-bottom:20px; color:var(--primary-dark);"><?= __('send_us_message', 'Send Us a Message') ?></h3>

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
              <label class="form-label"><?= __('full_name', 'Your Name *') ?></label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label"><?= __('email_address', 'Email Address *') ?></label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label"><?= __('phone_number', 'Phone Number') ?></label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label"><?= __('subject', 'Subject *') ?></label>
              <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" placeholder="e.g. Fodder Donation / Goushala Visit" required>
            </div>

            <div class="form-group">
              <label class="form-label"><?= __('your_message', 'Message *') ?></label>
              <textarea name="message" class="form-control" rows="5" required placeholder="Write your message here..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block"><?= __('btn_send_message', 'Send Message 📩') ?></button>
          </form>
        </div>
      </div>

    </div>

    <!-- Map Location Section -->
    <div style="margin-top: 50px; border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--border-light);">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d113171.14498871954!2d77.5898867332301!3d27.575971510464295!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39736e6559d8036d%3A0xc4eb09fc87a74659!2sVrindavan%2C%20Uttar%20Pradesh!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="450" style="border:0; display: block;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
