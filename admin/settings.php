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
            // ① General & Logo
            'site_name', 'site_tagline', 'site_logo_icon', 'site_logo_subtext',
            
            // ② Homepage Hero Banner
            'hero_badge_text', 'hero_title', 'hero_description',
            'hero_primary_btn_text', 'hero_secondary_btn_text', 'about_page_title',
            
            // ③ Video & About Mission
            'homepage_youtube_url', 'homepage_youtube_urls', 'about_section_title', 'about_section_text',
            
            // ④ Homepage Section Titles & Subtitles
            'cows_section_title', 'cows_section_subtitle',
            'seva_section_title', 'seva_section_subtitle',
            'products_section_title', 'products_section_subtitle',
            'gallery_section_title', 'gallery_section_subtitle',
            
            // ⑤ Sanctuary Statistics
            'total_cows_count', 'rescued_cows_count', 'volunteers_count', 'years_of_service',
            
            // ⑥ Contact Details & Footer Text
            'contact_phone', 'whatsapp_phone', 'contact_email', 'contact_address',
            'footer_about_text', 'footer_copyright_text',
            'facebook_url', 'instagram_url', 'youtube_url',
            
            // Multi-WhatsApp Helpline Directory
            'whatsapp_label_1', 'whatsapp_phone_1',
            'whatsapp_label_2', 'whatsapp_phone_2',
            'whatsapp_label_3', 'whatsapp_phone_3',
            'whatsapp_label_4', 'whatsapp_phone_4',
            'active_default_whatsapp',

            // ⑦ Checkout Modes
            'cow_checkout_mode', 'product_checkout_mode', 'donation_checkout_mode',

            // ⑧ Payment & QR Code Settings
            'payment_upi_id',
        ];

        $stmt = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        foreach ($keys as $key) {
            if ($key === 'homepage_youtube_urls') {
                $urls = $_POST[$key] ?? [];
                if (is_array($urls)) {
                    $urls = array_filter(array_map('trim', $urls));
                    $value = json_encode(array_values($urls));
                } else {
                    $value = '[]';
                }
            } else {
                $value = trim((string)($_POST[$key] ?? ''));
            }
            $stmt->execute([$key, $value]);
        }

        // Handle Image File Uploads for Site Settings
        $imageKeys = ['site_logo_image', 'hero_bg_image', 'about_section_image', 'site_favicon_image', 'payment_qr_code_image', 'about_page_image'];
        foreach ($imageKeys as $imgKey) {
            if (isset($_FILES[$imgKey]) && $_FILES[$imgKey]['error'] === UPLOAD_ERR_OK) {
                $upload = upload_file($_FILES[$imgKey], 'images/site');
                if ($upload['success']) {
                    $stmt->execute([$imgKey, $upload['filepath']]);
                } else {
                    $errors[] = "Error uploading " . $imgKey . ": " . $upload['error'];
                }
            }
        }

        if (empty($errors)) {
            $_SESSION['settings_success_msg'] = 'Settings saved successfully';
            header('Location: settings.php');
            exit;
        }
    }
}

$successMsg = $_SESSION['settings_success_msg'] ?? '';
unset($_SESSION['settings_success_msg']);

// ── Load Current Settings ──────────────────────────────────────────────────
$settingsRaw = $pdo->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);

function s($key, $default = '') {
    global $settingsRaw;
    $val = $settingsRaw[$key] ?? $default;
    while ($val !== html_entity_decode($val, ENT_QUOTES, 'UTF-8')) {
        $val = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
?>

<style>
  .settings-section-title {
    grid-column: 1 / -1;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-dark);
    margin-top: 15px;
    padding-bottom: 8px;
    border-bottom: 2.5px solid #2E7D32;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 28px;
    margin-bottom: 30px;
  }
  .settings-card {
    background: #fff;
    border-radius: 16px;
    padding: 28px 28px 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #e0e7de;
    transition: box-shadow 0.25s;
  }
  .settings-card:hover {
    box-shadow: 0 8px 32px rgba(46, 125, 50, 0.12);
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
    border-bottom: 2px solid #f4f8f3;
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
    color: #334e32;
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
    border: 1.5px solid #d0ded0;
    border-radius: 10px;
    background: #ffffff;
    color: var(--text-dark);
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
  }
  .settings-field input:focus,
  .settings-field textarea:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.12);
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



<?php if ($successMsg): ?>
  <div class="alert-success-settings">✅ <?= htmlspecialchars($successMsg) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="alert-error-settings">
    <?php foreach ($errors as $e): ?>⚠️ <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="POST" action="settings.php" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <!-- Save Bar (top) -->
  <div class="settings-save-bar" style="justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">💾 Save All Settings</button>
  </div>

  <div class="settings-grid">

    <!-- Category 1: Branding & Hero Banner -->
    <div class="settings-section-title">
      🌐 1. General Branding &amp; Hero Banner Settings
    </div>

    <!-- ① General Site & Logo Configuration -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">🌐</span> General &amp; Logo Settings
      </div>
      <div class="settings-field">
        <label>Site Name</label>
        <input type="text" name="site_name" value="<?= s('site_name', 'Kamadhenu Goushala') ?>" placeholder="Kamadhenu Goushala">
      </div>
      <div class="settings-field">
        <label>Site Tagline</label>
        <input type="text" name="site_tagline" value="<?= s('site_tagline', 'Love, Care & Seva for Gau Mata') ?>" placeholder="Love, Care & Seva for Gau Mata">
      </div>
      <div class="settings-field">
        <label>Logo Icon Emoji / Character</label>
        <input type="text" name="site_logo_icon" value="<?= s('site_logo_icon', '🐄') ?>" placeholder="🐄">
      </div>
      <div class="settings-field">
        <label>Logo Subtitle / Subtext</label>
        <input type="text" name="site_logo_subtext" value="<?= s('site_logo_subtext', 'GOUSHALA SANCTUARY') ?>" placeholder="GOUSHALA SANCTUARY">
      </div>
      <div class="settings-field" style="background:#fdfbf7; padding:12px; border-radius:8px; border:1px dashed #d0ded0;">
        <label>📸 Custom Logo Image File (Optional)</label>
        <?php $logoImg = s('site_logo_image', ''); ?>
        <?php if (!empty($logoImg)): ?>
          <div style="margin-bottom:8px;"><img src="<?= $baseUrl . $logoImg ?>" alt="Logo" style="height:40px;"></div>
        <?php endif; ?>
        <input type="file" name="site_logo_image" accept="image/*">
        <small style="color:var(--text-muted); display:block; margin-top:4px;">Upload a PNG/WEBP image logo to replace default icon.</small>
      </div>
      <div class="settings-field" style="background:#fdfbf7; padding:12px; border-radius:8px; border:1px dashed #d0ded0; margin-top:12px;">
        <label>✨ Custom Browser Tab Icon (Favicon Image File)</label>
        <?php $favImg = s('site_favicon_image', ''); ?>
        <?php if (!empty($favImg)): ?>
          <div style="margin-bottom:8px;"><img src="<?= $baseUrl . $favImg ?>" alt="Favicon" style="height:32px; width:32px; object-fit:contain;"></div>
        <?php endif; ?>
        <input type="file" name="site_favicon_image" accept="image/*">
        <small style="color:var(--text-muted); display:block; margin-top:4px;">Upload a custom PNG/ICO/SVG icon image for browser tabs. If blank, uses logo image or logo emoji icon.</small>
      </div>
    </div>

    <!-- ② Homepage Hero Banner Configuration -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">🚩</span> Homepage Hero Banner Text &amp; Image
      </div>
      <div class="settings-field">
        <label>Hero Badge Text</label>
        <input type="text" name="hero_badge_text" value="<?= s('hero_badge_text', 'SACRED VEDIC SANCTUARY') ?>" placeholder="SACRED VEDIC SANCTUARY">
      </div>
      <div class="settings-field">
        <label>Hero Main Heading (Title)</label>
        <input type="text" name="hero_title" value="<?= s('hero_title', 'Love, Care & Seva for Gau Mata') ?>" placeholder="Love, Care & Seva for Gau Mata">
      </div>
      <div class="settings-field">
        <label>Hero Description Paragraph</label>
        <textarea name="hero_description" rows="3" placeholder="Join our holy endeavor to protect..."><?= s('hero_description', 'Join our holy endeavor to protect, shelter, nurse, and feed indigenous Indian cows in Vrindavan Dham. Every small contribution supports lifelong medical care and green fodder.') ?></textarea>
      </div>
      <div class="settings-field">
        <label>Primary CTA Button Text</label>
        <input type="text" name="hero_primary_btn_text" value="<?= s('hero_primary_btn_text', 'Donate Now 💖') ?>" placeholder="Donate Now 💖">
      </div>
      <div class="settings-field">
        <label>Secondary CTA Button Text</label>
        <input type="text" name="hero_secondary_btn_text" value="<?= s('hero_secondary_btn_text', 'Meet Our Cows 🐄') ?>" placeholder="Meet Our Cows 🐄">
      </div>
      <div class="settings-field" style="background:#fdfbf7; padding:12px; border-radius:8px; border:1px dashed #d0ded0;">
        <label>📸 Custom Hero Background Photo (Optional)</label>
        <?php $heroImg = s('hero_bg_image', ''); ?>
        <?php if (!empty($heroImg)): ?>
          <div style="margin-bottom:8px;"><img src="<?= $baseUrl . $heroImg ?>" alt="Hero BG" style="height:60px; border-radius:6px; object-fit:cover;"></div>
        <?php endif; ?>
        <input type="file" name="hero_bg_image" accept="image/*">
        <small style="color:var(--text-muted); display:block; margin-top:4px;">Upload a high-res photo to replace background image.</small>
      </div>
      <div class="settings-field" style="background:#fdfbf7; padding:12px; border-radius:8px; border:1px dashed #d0ded0; margin-top:12px;">
        <label>📖 About Page Main Heading</label>
        <input type="text" name="about_page_title" value="<?= s('about_page_title', 'Dedicated to the Eternal Sacred Service of Gau Mata') ?>" placeholder="Dedicated to the Eternal Sacred Service of Gau Mata">
      </div>
      <div class="settings-field" style="background:#fdfbf7; padding:12px; border-radius:8px; border:1px dashed #d0ded0; margin-top:12px;">
        <label>📸 Custom About Page Photo (Optional)</label>
        <?php $aboutPageImg = s('about_page_image', ''); ?>
        <?php if (!empty($aboutPageImg)): ?>
          <div style="margin-bottom:8px;"><img src="<?= $baseUrl . $aboutPageImg ?>" alt="About Page Photo" style="height:60px; border-radius:6px; object-fit:cover;"></div>
        <?php endif; ?>
        <input type="file" name="about_page_image" accept="image/*">
        <small style="color:var(--text-muted); display:block; margin-top:4px;">Upload a custom image for the About Page. Replaces the default about-hero.jpg.</small>
      </div>
    </div>


    <!-- Category 2: WhatsApp & Checkout Modes -->
    <div class="settings-section-title">
      📱 2. WhatsApp Numbers Directory &amp; Checkout Modes
    </div>

    <!-- ⑧ WhatsApp Numbers Management -->
    <div class="settings-card" style="border: 2px solid #2E7D32; background: #f4f8f3;">
      <div class="settings-card-title" style="color: #2E7D32; border-bottom-color: rgba(46,125,50,0.2);">
        <span class="icon">📱</span> WhatsApp Numbers Directory
      </div>
      <p style="font-size:0.83rem; color:var(--text-muted); margin-bottom:18px; line-height:1.4;">
        Add your WhatsApp numbers here. Choose which default number to use globally, or assign specific numbers to individual cows, products, or seva items!
      </p>

      <div class="settings-field" style="background:#fff; padding:14px; border-radius:10px; border:1px solid #d0ded0; margin-bottom:20px;">
        <label style="color:#2E7D32; font-weight:bold;">⚙️ Active Default WhatsApp Number</label>
        <select name="active_default_whatsapp" class="form-control" style="font-weight:bold;">
          <option value="phone_1" <?= s('active_default_whatsapp', 'phone_1') === 'phone_1' ? 'selected' : '' ?>>1️⃣ WhatsApp Number 1 (<?= s('whatsapp_phone_1', s('contact_phone', '+91 98765 43210')) ?>)</option>
          <option value="phone_2" <?= s('active_default_whatsapp', 'phone_1') === 'phone_2' ? 'selected' : '' ?>>2️⃣ WhatsApp Number 2 (<?= s('whatsapp_phone_2', '+91 98765 11111') ?>)</option>
          <option value="phone_3" <?= s('active_default_whatsapp', 'phone_1') === 'phone_3' ? 'selected' : '' ?>>3️⃣ WhatsApp Number 3 (<?= s('whatsapp_phone_3', '+91 98765 22222') ?>)</option>
          <option value="phone_4" <?= s('active_default_whatsapp', 'phone_1') === 'phone_4' ? 'selected' : '' ?>>4️⃣ WhatsApp Number 4 (<?= s('whatsapp_phone_4', '+91 98765 33333') ?>)</option>
        </select>
      </div>

      <div class="settings-field">
        <label>📱 WhatsApp Number 1</label>
        <input type="text" name="whatsapp_phone_1" value="<?= s('whatsapp_phone_1', s('contact_phone', '+91 98765 43210')) ?>" placeholder="+91 98765 43210">
      </div>

      <div class="settings-field">
        <label>📱 WhatsApp Number 2</label>
        <input type="text" name="whatsapp_phone_2" value="<?= s('whatsapp_phone_2', '+91 98765 11111') ?>" placeholder="+91 98765 11111">
      </div>

      <div class="settings-field">
        <label>📱 WhatsApp Number 3</label>
        <input type="text" name="whatsapp_phone_3" value="<?= s('whatsapp_phone_3', '+91 98765 22222') ?>" placeholder="+91 98765 22222">
      </div>

      <div class="settings-field">
        <label>📱 WhatsApp Number 4</label>
        <input type="text" name="whatsapp_phone_4" value="<?= s('whatsapp_phone_4', '+91 98765 33333') ?>" placeholder="+91 98765 33333">
      </div>
    </div>

    <!-- ⑥ Checkout & Ordering Mode Options -->
    <div class="settings-card" style="border: 2px solid var(--accent-orange); background: #fffdf9;">
      <div class="settings-card-title" style="color: var(--accent-orange); border-bottom-color: rgba(255,152,0,0.2);">
        <span class="icon">🛒</span> Checkout &amp; Order Mode Options
      </div>
      <p style="font-size:0.83rem; color:var(--text-muted); margin-bottom:18px; line-height:1.4;">
        Choose whether visitors check out via direct WhatsApp messaging, website checkout, or both options together.
      </p>

      <div class="settings-field">
        <label>🐄 Cow Adoption Section Checkout Mode</label>
        <select name="cow_checkout_mode" class="form-control" style="font-weight:bold; padding: 10px 12px;">
          <option value="both" <?= s('cow_checkout_mode', 'both') === 'both' ? 'selected' : '' ?>>🤝 Both Options (Show Both Website &amp; WhatsApp Adoption Buttons)</option>
          <option value="whatsapp" <?= s('cow_checkout_mode', 'both') === 'whatsapp' ? 'selected' : '' ?>>📱 WhatsApp Checkout Only (Direct WhatsApp Chat)</option>
          <option value="website" <?= s('cow_checkout_mode', 'both') === 'website' ? 'selected' : '' ?>>🌐 Website Checkout Only (Online Adoption Form)</option>
        </select>
      </div>

      <div class="settings-field">
        <label>🛍️ Organic Store Products Checkout Mode</label>
        <select name="product_checkout_mode" class="form-control" style="font-weight:bold; padding: 10px 12px;">
          <option value="both" <?= s('product_checkout_mode', 'both') === 'both' ? 'selected' : '' ?>>🤝 Both Options (Show Add to Cart &amp; WhatsApp Order Buttons)</option>
          <option value="website" <?= s('product_checkout_mode', 'both') === 'website' ? 'selected' : '' ?>>🌐 Website Checkout Only (Shopping Cart &amp; Payment)</option>
          <option value="whatsapp" <?= s('product_checkout_mode', 'both') === 'whatsapp' ? 'selected' : '' ?>>📱 WhatsApp Checkout Only (Direct WhatsApp Order Chat)</option>
        </select>
      </div>

      <div class="settings-field">
        <label>💖 Donation Seva Section Checkout Mode</label>
        <select name="donation_checkout_mode" class="form-control" style="font-weight:bold; padding: 10px 12px;">
          <option value="both" <?= s('donation_checkout_mode', 'both') === 'both' ? 'selected' : '' ?>>🤝 Both Options (Show Website Payment &amp; WhatsApp Buttons)</option>
          <option value="website" <?= s('donation_checkout_mode', 'both') === 'website' ? 'selected' : '' ?>>🌐 Website Checkout Only (Online Payment &amp; QR Code)</option>
          <option value="whatsapp" <?= s('donation_checkout_mode', 'both') === 'whatsapp' ? 'selected' : '' ?>>📱 WhatsApp Checkout Only (Direct WhatsApp Donation Chat)</option>
        </select>
      </div>
    </div>

    <!-- 💰 Payment & UPI QR Code Settings -->
    <div class="settings-card" style="border: 2px solid #84418e; background: #faf4fc;">
      <div class="settings-card-title" style="color: #84418e; border-bottom-color: rgba(132,65,142,0.2);">
        <span class="icon">💰</span> Payment &amp; UPI QR Code Settings
      </div>
      <p style="font-size:0.83rem; color:var(--text-muted); margin-bottom:18px; line-height:1.4;">
        Configure the official merchant payment details. If a custom QR Code image is not uploaded, an actual UPI QR Code will be dynamically generated for your UPI ID on checkout/donation pages!
      </p>
      
      <div class="settings-field">
        <label>📲 Official UPI VPA (UPI ID)</label>
        <input type="text" name="payment_upi_id" value="<?= s('payment_upi_id', 'kamadhenugoushala@sbi') ?>" placeholder="e.g. kamadhenugoushala@sbi">
        <small style="color:var(--text-muted); font-size:0.8rem; display:block; margin-top:4px;">This UPI ID is copied by users and used to generate the dynamic QR Code.</small>
      </div>

      <div class="settings-field" style="background:#fff; padding:12px; border-radius:8px; border:1px dashed #84418e;">
        <label>📸 Custom Merchant QR Code Image (Optional)</label>
        <?php $qrImg = s('payment_qr_code_image', ''); ?>
        <?php if (!empty($qrImg)): ?>
          <div style="margin-bottom:8px;">
            <img src="<?= $baseUrl . $qrImg ?>" alt="Merchant QR Code" style="height:120px; border: 1px solid #ddd; padding: 4px; background: #fff; border-radius: 4px;">
          </div>
        <?php endif; ?>
        <input type="file" name="payment_qr_code_image" accept="image/*">
        <small style="color:var(--text-muted); display:block; margin-top:4px;">Upload your official UPI/Merchant QR Code (PNG, JPG or WEBP). If left blank, a dynamic QR code will be generated from the UPI VPA above.</small>
      </div>
    </div>

    <!-- Category 3: Titles, Video & Mission -->
    <div class="settings-section-title">
      🏷️ 3. Homepage Headings, Video &amp; About Mission
    </div>

    <!-- ③ Homepage Section Titles & Subtitles -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">🏷️</span> Homepage Section Titles &amp; Headings
      </div>
      <div class="settings-field">
        <label>🐄 Cows Section Title</label>
        <input type="text" name="cows_section_title" value="<?= s('cows_section_title', 'Meet Our Rescued Gau Mata') ?>" placeholder="Meet Our Rescued Gau Mata">
      </div>
      <div class="settings-field">
        <label>🐄 Cows Section Subtitle</label>
        <input type="text" name="cows_section_subtitle" value="<?= s('cows_section_subtitle', 'LIFELONG PATRONAGE') ?>" placeholder="LIFELONG PATRONAGE">
      </div>
      <div class="settings-field">
        <label>💖 Seva Section Title</label>
        <input type="text" name="seva_section_title" value="<?= s('seva_section_title', 'Sacred Ways To Offer Gau Seva') ?>" placeholder="Sacred Ways To Offer Gau Seva">
      </div>
      <div class="settings-field">
        <label>💖 Seva Section Subtitle</label>
        <input type="text" name="seva_section_subtitle" value="<?= s('seva_section_subtitle', 'HOLY SERVICE') ?>" placeholder="HOLY SERVICE">
      </div>
      <div class="settings-field">
        <label>🛍️ Organic Store Section Title</label>
        <input type="text" name="products_section_title" value="<?= s('products_section_title', 'Pure Organic Panchagavya Store') ?>" placeholder="Pure Organic Panchagavya Store">
      </div>
      <div class="settings-field">
        <label>🛍️ Organic Store Section Subtitle</label>
        <input type="text" name="products_section_subtitle" value="<?= s('products_section_subtitle', 'SACRED & CHEMICAL-FREE') ?>" placeholder="SACRED & CHEMICAL-FREE">
      </div>
      <div class="settings-field">
        <label>📸 Sanctuary Gallery Title</label>
        <input type="text" name="gallery_section_title" value="<?= s('gallery_section_title', 'Sanctuary Photo Gallery') ?>" placeholder="Sanctuary Photo Gallery">
      </div>
      <div class="settings-field">
        <label>📸 Sanctuary Gallery Subtitle</label>
        <input type="text" name="gallery_section_subtitle" value="<?= s('gallery_section_subtitle', 'VISUAL MEMORIES') ?>" placeholder="VISUAL MEMORIES">
      </div>
    </div>

    <!-- ④ Video & About Section Configuration -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">📺</span> Video &amp; About Mission Section
      </div>
      <!-- YouTube Video URLs list -->
      <div class="settings-field" id="youtube-videos-container" style="background:#fffdf9; padding:15px; border-radius:10px; border:1px solid #ffe0b2; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 10px;">
          <label style="font-weight:bold; color:var(--accent-orange); margin:0;">📺 Homepage YouTube Presentation Videos</label>
          <button type="button" class="btn btn-secondary btn-sm" id="add-video-btn" style="padding:4px 12px; font-size:0.78rem; border-color:var(--accent-orange); color:var(--accent-orange);">+ Add Video</button>
        </div>
        <div id="video-inputs-list">
          <?php 
          $rawUrls = get_setting('homepage_youtube_urls', '');
          $videoUrls = json_decode($rawUrls, true);
          if (!is_array($videoUrls) || empty($videoUrls)) {
              $oldUrl = get_setting('homepage_youtube_url', 'https://www.youtube.com/watch?v=pRsrn9THN8Q');
              $videoUrls = [$oldUrl];
          }
          foreach ($videoUrls as $index => $url): ?>
            <div class="video-input-item" style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
              <input type="url" name="homepage_youtube_urls[]" value="<?= htmlspecialchars($url) ?>" placeholder="https://www.youtube.com/watch?v=..." class="form-control" style="flex:1;">
              <button type="button" class="btn btn-danger btn-sm remove-video-btn" style="padding: 10px 12px; border-radius: 8px;">❌</button>
            </div>
          <?php endforeach; ?>
        </div>
        <small style="color:var(--text-muted); display:block; margin-top:6px; line-height:1.4;">Add one or more YouTube video links. They will display as a beautiful video carousel/gallery on the homepage. Keep at least one video.</small>
      </div>
      <div class="settings-field">
        <label>About Mission Section Title</label>
        <input type="text" name="about_section_title" value="<?= s('about_section_title', 'Dedicated to Lifelong Gau Seva & Protection') ?>" placeholder="Dedicated to Lifelong Gau Seva & Protection">
      </div>
      <div class="settings-field">
        <label>About Mission Paragraph Text</label>
        <textarea name="about_section_text" rows="4" placeholder="Kamadhenu Goushala was established with the sole mission..."><?= s('about_section_text', 'Kamadhenu Goushala was established with the sole mission of serving, feeding, and providing emergency medical treatment to abandoned, old, and injured cows in Vrindavan Dham.') ?></textarea>
      </div>
      <div class="settings-field" style="background:#fdfbf7; padding:12px; border-radius:8px; border:1px dashed #d0ded0;">
        <label>📸 Custom About Section Photo (Optional)</label>
        <?php $aboutImg = s('about_section_image', ''); ?>
        <?php if (!empty($aboutImg)): ?>
          <div style="margin-bottom:8px;"><img src="<?= $baseUrl . $aboutImg ?>" alt="About Photo" style="height:60px; border-radius:6px; object-fit:cover;"></div>
        <?php endif; ?>
        <input type="file" name="about_section_image" accept="image/*">
        <small style="color:var(--text-muted); display:block; margin-top:4px;">Upload a custom image for About Section.</small>
      </div>
    </div>


    <!-- Category 4: Counters, Contact & Social Links -->
    <div class="settings-section-title">
      📊 4. Sanctuary Counters, Contact &amp; Footer Text
    </div>

    <!-- ⑤ Sanctuary Statistics Counters -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">📊</span> Sanctuary Statistics Counters
      </div>
      <div class="settings-field">
        <label>🐄 Total Cows Under Care</label>
        <input type="number" name="total_cows_count" value="<?= s('total_cows_count', '450') ?>" placeholder="450" min="0">
      </div>
      <div class="settings-field">
        <label>🏥 Rescued &amp; Medical Care</label>
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

    <!-- ⑦ Contact Details & Footer Content -->
    <div class="settings-card">
      <div class="settings-card-title">
        <span class="icon">📞</span> Contact Details &amp; Footer Text
      </div>
      <div class="settings-field">
        <label>📞 Support Helpline Phone</label>
        <input type="text" name="contact_phone" value="<?= s('contact_phone', '+91 98765 43210') ?>" placeholder="+91 98765 43210">
      </div>
      <div class="settings-field">
        <label>📱 Dedicated WhatsApp Helpline (Optional)</label>
        <input type="text" name="whatsapp_phone" value="<?= s('whatsapp_phone', '') ?>" placeholder="e.g. +91 98765 43210 (Leave blank to use main helpline)">
        <small style="color:var(--text-muted); font-size:0.8rem; display:block; margin-top:4px;">Direct WhatsApp helpline number.</small>
      </div>
      <div class="settings-field">
        <label>📧 Support Email</label>
        <input type="email" name="contact_email" value="<?= s('contact_email', 'info@kamadhenugoushala.org') ?>" placeholder="info@kamadhenugoushala.org">
      </div>
      <div class="settings-field">
        <label>📍 Physical Address</label>
        <textarea name="contact_address" placeholder="Kamadhenu Dham, Vrindavan Highway, Mathura, UP"><?= s('contact_address', 'Kamadhenu Dham, Vrindavan Highway, Mathura, UP') ?></textarea>
      </div>
      <div class="settings-field">
        <label>Footer About Paragraph</label>
        <textarea name="footer_about_text" rows="3" placeholder="Dedicated to the protection..."><?= s('footer_about_text', 'Dedicated to the protection, rescue, medical care, and lifelong service of indigenous Indian cows. Operating with pure devotion in sacred Vrindavan Dham.') ?></textarea>
      </div>
      <div class="settings-field">
        <label>Footer Copyright Text Line</label>
        <input type="text" name="footer_copyright_text" value="<?= s('footer_copyright_text', 'Kamadhenu Goushala Sanctuary. All Rights Reserved.') ?>" placeholder="Kamadhenu Goushala Sanctuary. All Rights Reserved.">
      </div>
    </div>

    <!-- ⑧ Social Media Links -->
    <div class="settings-card" style="grid-column: 1 / -1;">
      <div class="settings-card-title">
        <span class="icon">📱</span> Social Media Links
      </div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
        <div class="settings-field social-field" style="margin-bottom:0;">
          <label>Facebook Page URL</label>
          <div class="social-field-wrap">
            <span class="social-icon" style="color:#1877F2;">
              <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h137.9V327.7h-63v-72.2h63v-55c0-62.2 38-96.4 93.6-96.4 26.6 0 54.7 4.7 54.7 4.7v60h-30.8c-30.8 0-40.4 19.1-40.4 38.7v46.4h67.8l-10.8 72.2h-57V480H400c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48z"></path></svg>
            </span>
            <input type="url" name="facebook_url" value="<?= s('facebook_url', 'https://facebook.com') ?>" placeholder="https://facebook.com/yourpage">
          </div>
        </div>
        <div class="settings-field social-field" style="margin-bottom:0;">
          <label>Instagram Profile URL</label>
          <div class="social-field-wrap">
            <span class="social-icon" style="color:#E1306C;">
              <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.8 9.9 67.6 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg>
            </span>
            <input type="url" name="instagram_url" value="<?= s('instagram_url', 'https://instagram.com') ?>" placeholder="https://instagram.com/yourprofile">
          </div>
        </div>
        <div class="settings-field social-field" style="margin-bottom:0;">
          <label>YouTube Channel URL</label>
          <div class="social-field-wrap">
            <span class="social-icon" style="color:#FF0000;">
              <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-12.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"></path></svg>
            </span>
            <input type="url" name="youtube_url" value="<?= s('youtube_url', 'https://youtube.com') ?>" placeholder="https://youtube.com/@yourchannel">
          </div>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('video-inputs-list');
  const addBtn = document.getElementById('add-video-btn');

  if (addBtn && container) {
    addBtn.addEventListener('click', () => {
      const div = document.createElement('div');
      div.className = 'video-input-item';
      div.style.display = 'flex';
      div.style.alignItems = 'center';
      div.style.gap = '8px';
      div.style.marginBottom = '8px';
      div.innerHTML = `
        <input type="url" name="homepage_youtube_urls[]" value="" placeholder="https://www.youtube.com/watch?v=..." class="form-control" style="flex:1;">
        <button type="button" class="btn btn-danger btn-sm remove-video-btn" style="padding: 10px 12px; border-radius: 8px;">❌</button>
      `;
      container.appendChild(div);
    });

    container.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-video-btn') || e.target.closest('.remove-video-btn')) {
        const item = e.target.closest('.video-input-item');
        if (container.querySelectorAll('.video-input-item').length > 1) {
          item.remove();
        } else {
          item.querySelector('input').value = '';
        }
      }
    });
  }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
