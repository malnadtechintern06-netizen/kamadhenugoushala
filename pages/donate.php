<?php
// pages/donate.php - WhatsApp Donation Page

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login('Please log in or create an account to offer a donation.');

$presetAmount = (int)($_GET['amount'] ?? 501);
$selectedPurpose = sanitize($_GET['purpose'] ?? 'General Gau Seva');
$currentUser = get_current_user_data();

$pageTitle = 'Offer Donation via WhatsApp - Kamadhenu Goushala';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
  <div class="container">
    <h1><?= __('banner_donate', 'Make a Sacred Donation') ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <span><?= __('nav_donate', 'Donate') ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-light">
  <div class="container">
    
    <div style="max-width: 680px; margin: 0 auto;">
      
      <div class="form-card" style="max-width: 100%; padding: 35px 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); background: #FFFFFF;">
        
        <!-- Header Badge -->
        <div class="text-center" style="margin-bottom: 25px;">
          <div style="width: 75px; height: 75px; background: #25D366; color: #FFFFFF; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 12px; box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);">
            <?= get_whatsapp_icon_svg('1.2em') ?>
          </div>
          <span class="section-subtitle" style="color: #2E7D32;"><?= __('direct_whatsapp_seva', 'DIRECT WHATSAPP SEVA') ?></span>
          <h2 style="color: var(--primary-dark); margin-top: 4px; font-size: 1.8rem;"><?= __('donate_heading', 'Donate For Gau Mata') ?></h2>
          <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 6px;"><?= __('donate_intro_text', 'Connect directly with our sanctuary manager on WhatsApp to offer your contribution and receive an 80G tax receipt.') ?></p>
        </div>

        <div style="background: #F4FBF7; border: 1px dashed #25D366; padding: 20px; border-radius: var(--radius-md); margin-bottom: 25px;">
          
          <div class="form-group" style="margin-bottom: 18px;">
            <label class="form-label" style="font-weight: 600;"><?= __('select_amount', 'Select Contribution Amount (INR ₹)') ?></label>
            <div class="donation-presets" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;">
              <button type="button" class="preset-btn <?= $presetAmount === 101 ? 'active' : '' ?>" onclick="setWaAmount(101)">₹101</button>
              <button type="button" class="preset-btn <?= $presetAmount === 501 ? 'active' : '' ?>" onclick="setWaAmount(501)">₹501</button>
              <button type="button" class="preset-btn <?= $presetAmount === 1001 ? 'active' : '' ?>" onclick="setWaAmount(1001)">₹1,001</button>
              <button type="button" class="preset-btn <?= $presetAmount === 2501 ? 'active' : '' ?>" onclick="setWaAmount(2501)">₹2,501</button>
              <button type="button" class="preset-btn <?= $presetAmount === 5001 ? 'active' : '' ?>" onclick="setWaAmount(5001)">₹5,001</button>
            </div>
            <input type="number" id="wa-amount-input" class="form-control" placeholder="<?= __('enter_custom_amount', 'Or enter custom amount in ₹') ?>" value="<?= $presetAmount ?>" min="1" style="font-size: 1.15rem; font-weight: bold; color: var(--primary-dark);">
          </div>

          <div class="form-group" style="margin-bottom: 18px;">
            <label class="form-label" style="font-weight: 600;"><?= __('seva_purpose', 'Seva Purpose / Program') ?></label>
            <select id="wa-purpose-select" class="form-control" style="font-size: 0.95rem;">
              <option value="General Gau Seva" <?= $selectedPurpose === 'General Gau Seva' ? 'selected' : '' ?>><?= __('purpose_general', 'General Gau Seva & Sanctuary Maintenance') ?></option>
              <option value="Gau Grass & Fodder Seva" <?= $selectedPurpose === 'Gau Grass & Fodder Seva' ? 'selected' : '' ?>><?= __('purpose_fodder', 'Gau Grass & Green Fodder Seva') ?></option>
              <option value="Medical Treatment & Healthcare" <?= $selectedPurpose === 'Medical Treatment & Healthcare' ? 'selected' : '' ?>><?= __('purpose_medical', 'Emergency Veterinary Treatment & Medicine') ?></option>
              <option value="Nitya Gau Seva (Daily Care)" <?= $selectedPurpose === 'Nitya Gau Seva (Daily Care)' ? 'selected' : '' ?>><?= __('purpose_daily', 'Nitya Gau Seva (Daily Grooming & Water)') ?></option>
              <option value="Goushala Shelter Construction" <?= $selectedPurpose === 'Goushala Shelter Construction' ? 'selected' : '' ?>><?= __('purpose_shelter', 'Shelter Shed Construction Fund') ?></option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-weight: 600;"><?= __('full_name', 'Your Name') ?></label>
            <input type="text" id="wa-donor-name" class="form-control" value="<?= htmlspecialchars($currentUser['full_name'] ?? '') ?>" placeholder="<?= __('full_name', 'Enter your full name') ?>">
          </div>

        </div>

        <!-- Big WhatsApp Button -->
        <a id="wa-donate-btn" href="#" target="_blank" class="btn btn-lg btn-block" style="background: #25D366; border-color: #25D366; color: #FFFFFF; font-size: 1.15rem; font-weight: 700; padding: 14px 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 6px 18px rgba(37, 211, 102, 0.35); text-decoration: none; border-radius: var(--radius-md);">
          <?= get_whatsapp_icon_svg('1.3em') ?> <?= __('btn_donate_whatsapp', 'Donate via WhatsApp 📱') ?>
        </a>

        <div style="background: #FFFDF5; border: 1px solid var(--border-light); padding: 15px 20px; border-radius: var(--radius-sm); margin-top: 25px; font-size: 0.88rem; color: var(--text-dark); line-height: 1.6;">
          🛡️ <?= __('tax_exemption_note_full', '80G Tax Exemption: 100% Tax benefit applies under Section 80G. Our manager will provide payment details & your official tax receipt via WhatsApp.') ?>
        </div>

      </div>

    </div>

  </div>
</section>

<?php 
  $waPhone = get_setting('whatsapp_number', '919876543210'); 
  $waPhoneClean = preg_replace('/[^0-9]/', '', $waPhone);
?>

<script>
function setWaAmount(amt) {
  document.getElementById('wa-amount-input').value = amt;
  const buttons = document.querySelectorAll('.preset-btn');
  buttons.forEach(btn => {
    btn.classList.toggle('active', parseInt(btn.getAttribute('data-amount')) === amt);
  });
  updateWaLink();
}

function updateWaLink() {
  const amt = document.getElementById('wa-amount-input').value || 501;
  const purpose = document.getElementById('wa-purpose-select').value || 'General Gau Seva';
  const name = document.getElementById('wa-donor-name').value || 'Devotee';
  const phone = '<?= $waPhoneClean ?>';
  
  const msg = `Jai Shree Krishna! 💖 My name is ${name}. I would like to offer a donation of ₹${amt} for ${purpose} at Kamadhenu Goushala. Please share payment details and 80G tax receipt.`;
  const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
  
  document.getElementById('wa-donate-btn').href = waUrl;
}

document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('wa-amount-input').addEventListener('input', updateWaLink);
  document.getElementById('wa-purpose-select').addEventListener('change', updateWaLink);
  document.getElementById('wa-donor-name').addEventListener('input', updateWaLink);
  updateWaLink();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
