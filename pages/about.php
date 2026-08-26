<?php
// pages/about.php - About Us Page

$pageTitle = 'About Us - Kamadhenu Goushala Mission & History';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
  <div class="container">
    <h1><?= __('about_page_heading', 'About Kamadhenu Goushala') ?></h1>
    <div class="breadcrumb">
      <a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a> / <span><?= __('nav_about', 'About Us') ?></span>
    </div>
  </div>
</div>

<section class="section-padding bg-white">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 50px; align-items: center;">
      <div>
        <span class="section-subtitle"><?= __('about_heritage', 'OUR HERITAGE') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(__('about_title', get_setting('about_page_title', 'Dedicated to the Eternal Sacred Service of Gau Mata'))) ?></h2>
        <p style="margin-bottom: 15px; color: var(--text-dark); line-height: 1.8;">
          <?= __('about_text_1', 'Founded over 15 years ago in the sacred land of Vrindavan Dham, Kamadhenu Goushala is home to over 450 indigenous Indian cows (Gir, Sahiwal, Tharparkar, Kankrej, Rathi).') ?>
        </p>
        <p style="margin-bottom: 15px; color: var(--text-dark); line-height: 1.8;">
          <?= __('about_text_2', 'Unlike commercial dairy farms, our sanctuary provides unconditional lifelong shelter to non-lactating, stray, aged, and accident-rescued cows. We treat every cow as a manifestation of sacred motherly warmth.') ?>
        </p>
        <p style="color: var(--text-muted); line-height: 1.8;">
          <?= __('about_text_3', 'Our sanctuary is equipped with round-the-clock veterinary doctors, emergency ambulances, rain-proof solar shelters, and sprawling green fodder pastures.') ?>
        </p>
      </div>
      <div>
        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid var(--border-light);">
          <?php $customAboutPageImg = get_setting('about_page_image', ''); ?>
          <img src="<?= !empty($customAboutPageImg) ? ($baseUrl . htmlspecialchars($customAboutPageImg)) : ($baseUrl . 'images/hero/about-hero.jpg') ?>" alt="Kamadhenu Goushala Pasture" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:420px; object-fit:cover;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission, Vision & Values -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 40px auto;">
      <span class="section-subtitle"><?= __('about_foundation', 'OUR FOUNDATION') ?></span>
      <h2 class="section-title center"><?= __('about_vision_mission', 'Vision, Mission & Core Values') ?></h2>
    </div>

    <div class="card-grid">
      <div class="card" style="padding: 30px;">
        <div style="font-size: 2.5rem; color: var(--accent-orange); margin-bottom: 15px;">🌟</div>
        <h3 class="card-title"><?= __('about_vision', 'Our Vision') ?></h3>
        <p class="card-text">
          <?= __('about_vision_text', 'To create a world where indigenous Desi cow breeds flourish without fear of slaughter, exploitation, or abandonment, serving as the cornerstone of organic agriculture and human wellness.') ?>
        </p>
      </div>

      <div class="card" style="padding: 30px;">
        <div style="font-size: 2.5rem; color: var(--primary-green); margin-bottom: 15px;">🌿</div>
        <h3 class="card-title"><?= __('about_mission', 'Our Mission') ?></h3>
        <p class="card-text">
          <?= __('about_mission_text', 'Rescue injured and abandoned stray cattle, provide 24/7 veterinary healthcare, cultivate organic green fodder, and promote Vedic Panchagavya products to achieve self-sustained Goushala management.') ?>
        </p>
      </div>

      <div class="card" style="padding: 30px;">
        <div style="font-size: 2.5rem; color: var(--primary-dark); margin-bottom: 15px;">🙏</div>
        <h3 class="card-title"><?= __('about_values', 'Our Core Values') ?></h3>
        <p class="card-text">
          <?= __('about_values_text', 'Ahimsa (Non-Violence), Pure Devotion, Complete Transparency in donor funds, and Environmental Sustainability through zero-waste organic farming.') ?>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Dynamic Statistics -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle"><?= __('about_impact_subtitle', 'IMPACT IN NUMBERS') ?></span>
      <h2 class="section-title center"><?= __('about_impact_title', 'Live Sanctuary Impact') ?></h2>
    </div>

    <div class="stats-grid">
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('total_cows_count', '450')) ?>+</div>
        <div class="stat-label"><?= __('about_stat_cows', 'Cows Under Sanctuary Care') ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('rescued_cows_count', '310')) ?>+</div>
        <div class="stat-label"><?= __('about_stat_rescued', 'Rescued From Accidents & Slaughter') ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('volunteers_count', '120')) ?>+</div>
        <div class="stat-label"><?= __('about_stat_volunteers', 'Active Sevaks & Volunteers') ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('years_of_service', '15')) ?>+</div>
        <div class="stat-label"><?= __('about_stat_years', 'Years of Uninterrupted Service') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- Facilities -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 40px auto;">
      <span class="section-subtitle"><?= __('about_infra_subtitle', 'INFRASTRUCTURE') ?></span>
      <h2 class="section-title center"><?= __('about_infra_title', 'World-Class Facilities') ?></h2>
    </div>

    <div class="card-grid">
      <div class="card" style="padding: 25px;">
        <h4><?= __('about_facility_1_title', '🏥 Veterinary Hospital & ICU') ?></h4>
        <p><?= __('about_facility_1_text', 'Outfitted with modern surgical units, X-ray facilities, ambulance vans, and resident doctors for immediate trauma treatment.') ?></p>
      </div>

      <div class="card" style="padding: 25px;">
        <h4><?= __('about_facility_2_title', '🌾 25-Acre Organic Fodder Farm') ?></h4>
        <p><?= __('about_facility_2_text', 'Cultivating pesticide-free Napier grass, Lucerne, Sorghum, and green crops watered via drip irrigation.') ?></p>
      </div>

      <div class="card" style="padding: 25px;">
        <h4><?= __('about_facility_3_title', '☀️ Solar Roofed Aerated Sheds') ?></h4>
        <p><?= __('about_facility_3_text', 'Spacious, clean, rubber-matted cow sheds with automated drinking water troughs and large ceiling fans.') ?></p>
      </div>

      <div class="card" style="padding: 25px;">
        <h4><?= __('about_facility_4_title', '🍯 A2 Bilona Processing Lab') ?></h4>
        <p><?= __('about_facility_4_text', 'Traditional wooden churner (Bilona) facility ensuring authentic A2 Gir Cow Ghee prepared in accordance with Charaka Samhita.') ?></p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
