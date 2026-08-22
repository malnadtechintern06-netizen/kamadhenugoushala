<?php
// admin/settings.php - Site Settings Management Panel

$adminPageTitle = '⚙️ Site Settings';
require_once __DIR__ . '/header_sidebar.php';

$successMsg = '';
$errors = [];

// ── Handle Form Submission ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid. Please resubmit.';
    } else {
        // All writable setting keys
        $keys = [
            'site_name', 'site_tagline',
            'total_cows_count', 'rescued_cows_count', 'volunteers_count', 'years_of_service',
            'contact_phone', 'contact_email', 'contact_address',
            'facebook_url', 'instagram_url', 'youtube_url',
        ];

        $stmt = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        foreach ($keys as $key) {
            $value = sanitize($_POST[$key] ?? '');
            $stmt->execute([$key, $value]);
        }

        $successMsg = 'Settings saved successfully! Changes are live on the public website.';
    }
}

// ── Load Current Settings ──────────────────────────────────────────────────
$settingsRaw = $pdo->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);

function s($key, $default = '') {
    global $settingsRaw;
    return htmlspecialchars($settingsRaw[$key] ?? $default);
}
?>

<style>
  .settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 28px;
    margin-bottom: 30px;
  }
  .settings-card {
    background: #fff;
    border-radius: 16px;
    padding: 28px 28px 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    border: 1px solid #f0e6ed;
    transition: box-shadow 0.25s;
  }
  .settings-card:hover {
    box-shadow: 0 8px 32px rgba(132, 65, 142, 0.13);
  }
  .settings-card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f5eaf8;
  }
  .settings-card-title span.icon {
    font-size: 1.4rem;
  }
  .settings-field {
    margin-bottom: 18px;
  }
  .settings-field label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #7D4F5A;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .settings-field input,
  .settings-field textarea {
    width: 100%;
    padding: 10px 14px;
    font-family: var(--font-body);
    font-size: 0.92rem;
    border: 1.5px solid #e8d0e2;
    border-radius: 10px;
    background: #fdf8fb;
    color: var(--text-dark);
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
  }
  .settings-field input:focus,
  .settings-field textarea:focus {
    outline: none;
    border-color: var(--primary-dark);
    box-shadow: 0 0 0 3px rgba(132, 65, 142, 0.12);
    background: #fff;
  }
  .settings-field textarea {
    resize: vertical;
    min-height: 80px;
  }
  .social-field input {
    padding-left: 42px !important;
  }
  .social-field-wrap {
    position: relative;
  }
  .social-field-wrap .social-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.1rem;
    pointer-events: none;
  }
  .settings-save-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border-radius: 14px;
    padding: 18px 28px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    border: 1px solid #f0e6ed;
    margin-bottom: 28px;
  }
  .settings-save-bar p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-muted);
  }
  .alert-success-settings {
    background: linear-gradient(135deg, #e8f5e9, #f1fff1);
    border: 1.5px solid #81c784;
    color: #2E7D32;
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 24px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .alert-error-settings {
    background: #fff3f3;
    border: 1.5px solid #ef9a9a;
    color: #c62828;
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 24px;
    font-weight: 600;
  }
</style>

<!-- Page Header -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
  <div>
    <h3 style="margin:0 0 4px; font-size:1.5rem;">Site Settings</h3>
    <p style="margin:0; font-size:0.88rem; color:var(--text-muted);">All changes are saved directly to the database and reflected live on the public website.</p>
  </div>
</div>

<?php if ($successMsg): ?>
  <div class="alert-success-settings">✅ <?= htmlspecialchars($successMsg) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="alert-error-settings">
    <?php foreach ($errors as $e): ?>⚠️ <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="POST" action="settings.php">
  <?= csrf_field() ?>

  <!-- Save Bar (top) -->
  <div class="settings-save-bar">
    <p>💡 Tip: Update social links to point to your official pages. Visitors will see them in the header and footer.</p>
    <button type="submit" class="btn btn-primary">💾 Save All Settings</button>
  </div>

  <div class="settings-grid">

    <!-- ① General -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">🌐</span> General Configuration
      </div>
      <div class="settings-field">
        <label>Site Name</label>
        <input type="text" name="site_name" value="<?= s('site_name', 'Kamadhenu Goushala') ?>" placeholder="Kamadhenu Goushala">
      </div>
      <div class="settings-field">
        <label>Site Tagline</label>
        <input type="text" name="site_tagline" value="<?= s('site_tagline', 'Love, Care & Seva for Gau Mata') ?>" placeholder="Love, Care & Seva for Gau Mata">
      </div>
    </div>

    <!-- ② Statistics -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">📊</span> Sanctuary Statistics
      </div>
      <div class="settings-field">
        <label>🐄 Total Cows Under Care</label>
        <input type="number" name="total_cows_count" value="<?= s('total_cows_count', '450') ?>" placeholder="450" min="0">
      </div>
      <div class="settings-field">
        <label>🏥 Rescued & Medical Care</label>
        <input type="number" name="rescued_cows_count" value="<?= s('rescued_cows_count', '310') ?>" placeholder="310" min="0">
      </div>
      <div class="settings-field">
        <label>🙌 Dedicated Volunteers</label>
        <input type="number" name="volunteers_count" value="<?= s('volunteers_count', '120') ?>" placeholder="120" min="0">
      </div>
      <div class="settings-field">
        <label>📅 Years of Pure Seva</label>
        <input type="number" name="years_of_service" value="<?= s('years_of_service', '15') ?>" placeholder="15" min="0">
      </div>
    </div>

    <!-- ③ Contact Details -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">📞</span> Contact Details
      </div>
      <div class="settings-field">
        <label>📞 Helpline / WhatsApp</label>
        <input type="text" name="contact_phone" value="<?= s('contact_phone', '+91 98765 43210') ?>" placeholder="+91 98765 43210">
      </div>
      <div class="settings-field">
        <label>📧 Support Email</label>
        <input type="email" name="contact_email" value="<?= s('contact_email', 'info@kamadhenugoushala.org') ?>" placeholder="info@kamadhenugoushala.org">
      </div>
      <div class="settings-field">
        <label>📍 Physical Address</label>
        <textarea name="contact_address" placeholder="Kamadhenu Dham, Vrindavan Highway, Mathura, UP"><?= s('contact_address', 'Kamadhenu Dham, Vrindavan Highway, Mathura, UP') ?></textarea>
      </div>
    </div>

    <!-- ④ Social Media Links -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">📱</span> Social Media Links
      </div>
      <div class="settings-field social-field">
        <label>Facebook Page URL</label>
        <div class="social-field-wrap">
          <span class="social-icon" style="color:#1877F2;">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h137.9V327.7h-63v-72.2h63v-55c0-62.2 38-96.4 93.6-96.4 26.6 0 54.7 4.7 54.7 4.7v60h-30.8c-30.8 0-40.4 19.1-40.4 38.7v46.4h67.8l-10.8 72.2h-57V480H400c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48z"></path></svg>
          </span>
          <input type="url" name="facebook_url" value="<?= s('facebook_url', 'https://facebook.com') ?>" placeholder="https://facebook.com/yourpage">
        </div>
      </div>
      <div class="settings-field social-field">
        <label>Instagram Profile URL</label>
        <div class="social-field-wrap">
          <span class="social-icon" style="color:#E1306C;">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.8 9.9 67.6 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg>
          </span>
          <input type="url" name="instagram_url" value="<?= s('instagram_url', 'https://instagram.com') ?>" placeholder="https://instagram.com/yourprofile">
        </div>
      </div>
      <div class="settings-field social-field">
        <label>YouTube Channel URL</label>
        <div class="social-field-wrap">
          <span class="social-icon" style="color:#FF0000;">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-12.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"></path></svg>
          </span>
          <input type="url" name="youtube_url" value="<?= s('youtube_url', 'https://youtube.com') ?>" placeholder="https://youtube.com/@yourchannel">
        </div>
      </div>
    </div>

  </div><!-- /.settings-grid -->

  <!-- Save Bar (bottom) -->
  <div class="settings-save-bar">
    <p>Changes go live instantly — no cache to clear.</p>
    <button type="submit" class="btn btn-primary btn-lg">💾 Save All Settings</button>
  </div>

</form>

</main>
</div>
</body>
</html>
