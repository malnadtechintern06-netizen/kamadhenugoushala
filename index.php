<?php
// index.php - Home Page

$pageTitle = 'Kamadhenu Goushala - Love, Care & Seva for Gau Mata';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/preloader.php';

// Fetch Featured Cows from MySQL
$stmtCows = $pdo->query("SELECT * FROM cows ORDER BY id ASC LIMIT 3");
$featuredCows = $stmtCows->fetchAll();

// Fetch Featured Seva Opportunities from MySQL
$stmtSeva = $pdo->query("SELECT * FROM seva WHERE is_featured = 1 ORDER BY id ASC LIMIT 6");
$sevaItems = $stmtSeva->fetchAll();

// Fetch Featured Products from MySQL
$stmtProducts = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN product_categories c ON p.category_id = c.id 
    WHERE p.is_featured = 1 
    LIMIT 6
");
$featuredProducts = $stmtProducts->fetchAll();

// Fetch Gallery Preview
$stmtGallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC LIMIT 8");
$galleryItems = $stmtGallery->fetchAll();

// Fetch Testimonials
$stmtTestimonials = $pdo->query("SELECT * FROM testimonials WHERE is_approved = 1 LIMIT 3");
$testimonials = $stmtTestimonials->fetchAll();

// Fetch Upcoming Events from MySQL
$stmtEvents = $pdo->query("SELECT * FROM events WHERE is_featured = 1 ORDER BY event_date ASC LIMIT 3");
$upcomingEvents = $stmtEvents->fetchAll();
?>

<!-- 2. Hero Section -->
<?php 
  $customHeroImg = get_setting('hero_bg_image', ''); 
  $heroBgStyle = !empty($customHeroImg) ? "background: linear-gradient(135deg, rgba(36, 69, 38, 0.72), rgba(56, 142, 60, 0.5)), url('" . $baseUrl . htmlspecialchars($customHeroImg) . "') center/cover no-repeat;" : "";
?>
<section class="hero-section">
  <!-- 3D Animated Background Photo Layer -->
  <div class="hero-bg-photo" style="<?= $heroBgStyle ?>"></div>

  <!-- Subtle Background Animation -->
  <div class="bg-animation">
    <div class="circle circle-1"></div>
    <div class="circle circle-2"></div>
    <div class="circle circle-3"></div>
  </div>
  <div class="container" style="position:relative; z-index:2;">
    <div class="hero-content">
      <span class="hero-badge"><?= htmlspecialchars(__('hero_badge', get_setting('hero_badge_text', 'SACRED SANCTUARY'))) ?></span>
      <h1 class="hero-title"><?= htmlspecialchars(__('hero_title', get_setting('hero_title', 'Love, Care & Seva for Gau Mata'))) ?></h1>
      <p class="hero-desc">
        <?= htmlspecialchars(__('hero_desc', get_setting('hero_description', 'Join our holy endeavor to protect, shelter, nurse, and feed indigenous Indian cows in Vrindavan Dham. Every small contribution supports lifelong medical care and green fodder.'))) ?>
      </p>
      <div class="hero-buttons">
        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-lg"><?= htmlspecialchars(__('btn_donate_now', get_setting('hero_primary_btn_text', 'Donate Now 💖'))) ?></a>
        <a href="<?= $baseUrl ?>pages/cows.php" class="btn btn-outline btn-lg" style="color:var(--bg-white); border-color:var(--bg-white);"><?= htmlspecialchars(__('btn_meet_cows', get_setting('hero_secondary_btn_text', 'Meet Our Cows 🐄'))) ?></a>
      </div>
    </div>
  </div>
</section>

<!-- 3. About Section & Statistics -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="grid-responsive-2" style="align-items: center;">
      <div>
        <span class="section-subtitle"><?= __('about_subtitle', 'OUR SACRED MISSION') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(__('about_title', get_setting('about_section_title', 'Nurturing Indigenous Desi Cows With Devotion'))) ?></h2>
        <p style="margin-bottom: 25px; color: var(--text-dark); line-height:1.7;">
          <?= nl2br(htmlspecialchars(__('about_text', get_setting('about_section_text', 'Kamadhenu Goushala was established with the sole mission of serving non-lactating, old, injured, and street-abandoned cows. We provide a peaceful, natural environment where cows roam freely in open green pastures.')))) ?>
        </p>
        <a href="<?= $baseUrl ?>pages/about.php" class="btn btn-secondary"><?= __('btn_know_more', 'Know More About Us') ?></a>
      </div>
      <div>
        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid var(--bg-light-green); background:#fdfbf7; display:flex; align-items:center; justify-content:center;">
          <?php $customAboutImg = get_setting('about_section_image', ''); ?>
          <img src="<?= !empty($customAboutImg) ? ($baseUrl . htmlspecialchars($customAboutImg)) : ($baseUrl . 'images/hero/about-preview.jpg') ?>" alt="Kamadhenu Goushala Sanctuary" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:400px; object-fit:contain; padding:8px;">
        </div>
      </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('total_cows_count', '450')) ?>+</div>
        <div class="stat-label"><?= __('stat_cows', 'Cows Under Our Care') ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('rescued_cows_count', '310')) ?>+</div>
        <div class="stat-label"><?= __('stat_rescued', 'Rescued & Medical Care') ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('volunteers_count', '120')) ?>+</div>
        <div class="stat-label"><?= __('stat_volunteers', 'Dedicated Volunteers') ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('years_of_service', '15')) ?>+</div>
        <div class="stat-label"><?= __('stat_years', 'Years of Pure Seva') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- 3.5 Video Presentation Section -->
<section class="section-padding" style="background-color: var(--bg-cream);">
  <div class="container">
    <div class="grid-responsive-2" style="align-items: center;">
      <div>
        <span class="section-subtitle"><?= __('journey_subtitle', 'OUR JOURNEY') ?></span>
        <h2 class="section-title"><?= __('journey_title', 'Experience Our Goushala') ?></h2>
        <p style="margin-bottom: 15px; color: var(--text-dark);">
          <?= __('journey_desc_1', 'Watch this beautiful presentation that captures the essence of our daily Seva. See firsthand how your contributions help us rescue, feed, and provide lifelong medical care to our beloved cows in Vrindavan Dham.') ?>
        </p>
        <p style="margin-bottom: 25px; color: var(--text-muted);">
          <?= __('journey_desc_2', 'Every moment spent serving Gau Mata is a blessing. We invite you to witness the peace, devotion, and joy that fills our sanctuary every single day.') ?>
        </p>
        <a href="<?= $baseUrl ?>pages/cows.php" class="btn btn-primary"><?= __('btn_meet_residents', 'Meet Our Residents') ?></a>
      </div>
      
      <div>
        <!-- Main Presentation Video Screen -->
        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid var(--border-light); position: relative; padding-bottom: 56.25%; height: 0; background: #000;">
          <?php 
          $rawUrls = get_setting('homepage_youtube_urls', '');
          $videoUrls = json_decode($rawUrls, true);
          if (!is_array($videoUrls) || empty($videoUrls)) {
              $oldUrl = get_setting('homepage_youtube_url', 'https://www.youtube.com/watch?v=pRsrn9THN8Q');
              $videoUrls = [$oldUrl];
          }
          $activeUrl = $videoUrls[0];
          $activeEmbed = get_youtube_embed_url($activeUrl);
          ?>
          <iframe id="main-goushala-video" src="<?= $activeEmbed ?>" title="Goushala Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
        </div>

        <!-- Video Selection Thumbnails (Show only if multiple videos exist) -->
        <?php if (count($videoUrls) > 1): ?>
          <div class="video-thumbnails-grid">
            <?php foreach ($videoUrls as $idx => $url): 
              $vidId = get_youtube_video_id($url);
              $thumbUrl = "https://img.youtube.com/vi/" . $vidId . "/mqdefault.jpg";
              $embedUrl = get_youtube_embed_url($url);
            ?>
              <div class="video-thumbnail-card <?= $idx === 0 ? 'active' : '' ?>" data-video-url="<?= htmlspecialchars($embedUrl) ?>">
                <img src="<?= $thumbUrl ?>" alt="Video <?= $idx + 1 ?> Thumbnail">
                <!-- Play Icon Overlay -->
                <div class="thumb-play-overlay">
                  <span>▶</span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <script>
          document.addEventListener('DOMContentLoaded', () => {
            const mainVideo = document.getElementById('main-goushala-video');
            const thumbCards = document.querySelectorAll('.video-thumbnail-card');

            thumbCards.forEach(card => {
              card.addEventListener('click', () => {
                thumbCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                const targetUrl = card.getAttribute('data-video-url');
                mainVideo.src = targetUrl;
              });
            });
          });
          </script>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- 4. Our Cows Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle"><?= htmlspecialchars(__('cows_subtitle', get_setting('cows_section_subtitle', 'MEET OUR RESIDENTS'))) ?></span>
      <h2 class="section-title center"><?= htmlspecialchars(__('cows_title', get_setting('cows_section_title', 'Our Beloved Cows'))) ?></h2>
      <p><?= __('cows_desc', 'Each cow in our Goushala has a unique story. You can adopt, sponsor feeding, or visit them.') ?></p>
    </div>

    <div class="card-grid">
      <?php foreach ($featuredCows as $cow): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($cow['main_image']) ?>" alt="<?= htmlspecialchars(__($cow['name'], $cow['name'])) ?>" onerror="this.src='https://images.unsplash.com/photo-1570042707223-2cb2ed999557?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge status-<?= strtolower(str_replace(' ', '-', $cow['health_status'])) ?>">
              <?= htmlspecialchars(__($cow['health_status'], $cow['health_status'])) ?>
            </span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars(__($cow['name'], $cow['name'])) ?></h3>
            <p class="card-subtitle"><?= __('breed_label', 'Breed') ?>: <?= htmlspecialchars(__($cow['breed'], $cow['breed'])) ?> | <?= __('age_label', 'Age') ?>: <?= $cow['age_years'] ?> <?= __('years_label', 'Yrs') ?> (<?= htmlspecialchars(__($cow['gender'], $cow['gender'])) ?>)</p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth(__($cow['bio'], $cow['bio']), 0, 100, '...')) ?></p>
            <div class="card-meta">
              <span><?= __('tag_label', 'Tag:') ?> <strong><?= htmlspecialchars($cow['tag_number']) ?></strong></span>
              <span style="color:var(--accent-orange); font-weight:bold;"><?= format_currency($cow['monthly_adoption_fee']) ?><?= __('mo_label', '/mo') ?></span>
            </div>
            <?php 
              $cowMode = get_item_checkout_mode($cow, 'cow'); 
              $waUrl = get_whatsapp_cow_url($cow);
              $siteUrl = get_base_url() . 'pages/adopt.php?cow_id=' . $cow['id'];
            ?>
            <div class="card-actions" style="display:flex; flex-direction:column; gap:6px;">
              <?php if ($cowMode === 'both'): ?>
                <div style="display:flex; gap:6px;">
                  <a href="<?= $siteUrl ?>" class="btn btn-primary btn-sm" style="flex:1; display:inline-flex; align-items:center; justify-content:center;">🌐 <?= __('btn_adopt_online', 'Adopt Online') ?></a>
                  <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:2px;"><?= get_whatsapp_icon_svg() ?> WhatsApp</a>
                </div>
                <a href="<?= $baseUrl ?>pages/cow-details.php?id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm text-center"><?= __('btn_view_profile', 'View Profile') ?></a>
              <?php elseif ($cowMode === 'whatsapp'): ?>
                <div style="display:flex; gap:6px;">
                  <a href="<?= $baseUrl ?>pages/cow-details.php?id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;"><?= __('btn_details', 'Details') ?></a>
                  <a href="<?= $waUrl ?>" target="_blank" class="btn btn-primary btn-sm" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:2px;"><?= get_whatsapp_icon_svg() ?> WhatsApp</a>
                </div>
              <?php else: ?>
                <div style="display:flex; gap:6px;">
                  <a href="<?= $baseUrl ?>pages/cow-details.php?id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;"><?= __('btn_details', 'Details') ?></a>
                  <a href="<?= $siteUrl ?>" class="btn btn-primary btn-sm" style="flex:1;"><?= __('btn_adopt_online', 'Adopt Online') ?></a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 40px;">
      <a href="<?= $baseUrl ?>pages/cows.php" class="btn btn-secondary btn-lg"><?= __('btn_view_all_cows', 'View All Cows') ?> (<?= count($featuredCows) ?>+)</a>
    </div>
  </div>
</section>

<!-- 5. Seva Opportunities Section -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle"><?= htmlspecialchars(__('seva_subtitle', get_setting('seva_section_subtitle', 'HOLY SERVICE'))) ?></span>
      <h2 class="section-title center"><?= htmlspecialchars(__('seva_title', get_setting('seva_section_title', 'Ways To Offer Seva'))) ?></h2>
      <p><?= __('seva_desc', 'Sponsor essential grass, medicines, shelter construction, or daily care for sacred cows.') ?></p>
    </div>

    <div class="card-grid">
      <?php foreach ($sevaItems as $seva): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($seva['image']) ?>" alt="<?= htmlspecialchars(__($seva['title'], $seva['title'])) ?>" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge"><?= htmlspecialchars(__($seva['category'], $seva['category'])) ?></span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars(__($seva['title'], $seva['title'])) ?></h3>
            <p class="card-subtitle"><?= htmlspecialchars(__($seva['subtitle'], $seva['subtitle'])) ?></p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth(__($seva['description'], $seva['description']), 0, 110, '...')) ?></p>
            <div class="card-meta">
              <span><?= __('suggested_seva', 'Suggested Seva:') ?></span>
              <span class="card-price"><?= format_currency($seva['suggested_amount']) ?></span>
            </div>
            <div class="card-actions">
              <a href="<?= $baseUrl ?>pages/seva-details.php?id=<?= $seva['id'] ?>" class="btn btn-outline btn-sm" style="width:100%;"><?= __('btn_view_details_sponsor', 'View Details & Sponsor') ?></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 6. Organic Products Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle"><?= htmlspecialchars(__('products_subtitle', get_setting('products_section_subtitle', 'GOUSHALA STORE'))) ?></span>
      <h2 class="section-title center"><?= htmlspecialchars(__('products_title', get_setting('products_section_title', 'Pure Organic Panchagavya Products'))) ?></h2>
      <p><?= __('products_desc', 'Support Goushala maintenance by purchasing authentic A2 Bilona Ghee, herbal soaps, and organic compost.') ?></p>
    </div>

    <div class="card-grid">
      <?php foreach ($featuredProducts as $prod): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars(__($prod['name'], $prod['name'])) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge"><?= htmlspecialchars(__($prod['category_name'], $prod['category_name'])) ?></span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars(__($prod['name'], $prod['name'])) ?></h3>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth(__($prod['description'], $prod['description']), 0, 90, '...')) ?></p>
            <div class="card-meta">
              <div class="card-price">
                <?= format_currency($prod['sale_price'] ?: $prod['price']) ?>
                <?php if ($prod['sale_price']): ?>
                  <span class="original-price"><?= format_currency($prod['price']) ?></span>
                <?php endif; ?>
              </div>
              <span style="font-size:0.85rem; color:#2E7D32;"><?= __('in_stock', 'In Stock') ?> (<?= $prod['stock_quantity'] ?>)</span>
            </div>
            <?php 
              $prodMode = get_item_checkout_mode($prod, 'product'); 
              $waUrl = get_whatsapp_product_url($prod);
            ?>
            <div class="card-actions" style="display:flex; flex-direction:column; gap:6px;">
              <?php if ($prodMode === 'both'): ?>
                <div style="display:flex; gap:6px;">
                  <button class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" style="flex:1;"><?= __('btn_add_to_cart', '🛒 Add to Cart') ?></button>
                  <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:2px;"><?= get_whatsapp_icon_svg() ?> WhatsApp</a>
                </div>
                <a href="<?= $baseUrl ?>pages/product-details.php?id=<?= $prod['id'] ?>" class="btn btn-outline btn-sm text-center"><?= __('btn_view_product', 'View Product') ?></a>
              <?php elseif ($prodMode === 'whatsapp'): ?>
                <div style="display:flex; gap:6px;">
                  <a href="<?= $baseUrl ?>pages/product-details.php?id=<?= $prod['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;"><?= __('btn_details', 'Details') ?></a>
                  <a href="<?= $waUrl ?>" target="_blank" class="btn btn-primary btn-sm" style="flex:1; background:#25D366; border-color:#25D366; color:white; display:inline-flex; align-items:center; justify-content:center; gap:2px;"><?= get_whatsapp_icon_svg() ?> WhatsApp</a>
                </div>
              <?php else: ?>
                <div style="display:flex; gap:6px;">
                  <a href="<?= $baseUrl ?>pages/product-details.php?id=<?= $prod['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;"><?= __('btn_details', 'Details') ?></a>
                  <button class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" style="flex:1;"><?= __('btn_add_to_cart', 'Add to Cart') ?></button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 40px;">
      <a href="<?= $baseUrl ?>pages/products.php" class="btn btn-secondary btn-lg"><?= __('btn_browse_store', 'Browse Organic Store') ?></a>
    </div>
  </div>
</section>

<!-- 7. Gallery Preview Section -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle"><?= htmlspecialchars(__('gallery_subtitle', get_setting('gallery_section_subtitle', 'VISUAL MEMORIES'))) ?></span>
      <h2 class="section-title center"><?= htmlspecialchars(__('gallery_title', get_setting('gallery_section_title', 'Sanctuary Gallery'))) ?></h2>
      <p><?= __('gallery_desc', 'Glance into the serene daily life, feeding rituals, and festivals at Kamadhenu Goushala.') ?></p>
    </div>

    <div class="gallery-grid">
      <?php foreach ($galleryItems as $g): ?>
        <div class="gallery-item" data-image="<?= $baseUrl . htmlspecialchars($g['image_path']) ?>" data-title="<?= htmlspecialchars(__($g['title'], $g['title'])) ?>">
          <img src="<?= $baseUrl . htmlspecialchars($g['image_path']) ?>" alt="<?= htmlspecialchars(__($g['title'], $g['title'])) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
          <div class="gallery-overlay">
            <h4><?= htmlspecialchars(__($g['title'], $g['title'])) ?></h4>
            <p style="font-size:0.85rem; opacity:0.9;"><?= htmlspecialchars(__($g['description'], $g['description'])) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 35px;">
      <a href="<?= $baseUrl ?>pages/gallery.php" class="btn btn-outline btn-lg"><?= __('btn_view_gallery', 'Explore Full Gallery') ?></a>
    </div>
  </div>
</section>

<!-- Upcoming Events Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle"><?= __('events_subtitle', 'FESTIVALS & CELEBRATIONS') ?></span>
      <h2 class="section-title center"><?= __('events_title', 'Upcoming Sanctuary Events') ?></h2>
      <p><?= __('events_desc', 'Participate in sacred cow pooja rituals, veterinary medical camps, and festival celebrations.') ?></p>
    </div>

    <div class="card-grid">
      <?php foreach ($upcomingEvents as $ev): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($ev['image']) ?>" alt="<?= htmlspecialchars(__($ev['title'], $ev['title'])) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge" style="background-color: var(--accent-orange);"><?= htmlspecialchars(__($ev['status'], $ev['status'])) ?></span>
          </div>
          <div class="card-body">
            <div style="font-size:0.85rem; color:var(--accent-orange); font-weight:bold; margin-bottom:6px;">
              📅 <?= date('l, d M Y', strtotime($ev['event_date'])) ?>
            </div>
            <h3 class="card-title"><?= htmlspecialchars(__($ev['title'], $ev['title'])) ?></h3>
            <p class="card-subtitle">📍 <?= htmlspecialchars(__($ev['location'], $ev['location'])) ?></p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth(__($ev['description'], $ev['description']), 0, 95, '...')) ?></p>
            <div class="card-actions" style="margin-top:auto;">
              <a href="<?= $baseUrl ?>pages/event-details.php?id=<?= $ev['id'] ?>" class="btn btn-primary btn-sm btn-block"><?= __('btn_view_event', 'View Event & Attend 🗓') ?></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 35px;">
      <a href="<?= $baseUrl ?>pages/events.php" class="btn btn-secondary btn-lg"><?= __('btn_view_all_events', 'View All Events') ?></a>
    </div>
  </div>
</section>

<!-- 8. Testimonials Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle"><?= __('testimonials_subtitle', 'DEVOTEE FEEDBACK') ?></span>
      <h2 class="section-title center"><?= __('testimonials_title', 'What Donors & Sevaks Say') ?></h2>
    </div>

    <div class="card-grid">
      <?php foreach ($testimonials as $t): ?>
        <div class="card" style="padding: 25px;">
          <div style="font-size: 1.2rem; color: var(--accent-orange); margin-bottom: 10px;">
            <?= str_repeat('★', $t['rating']) ?>
          </div>
          <p style="font-style: italic; color: var(--text-dark); margin-bottom: 20px; flex:1;">
            "<?= htmlspecialchars(__($t['message'], $t['message'])) ?>"
          </p>
          <div style="display: flex; align-items: center; gap: 12px; border-top: 1px solid var(--border-light); padding-top: 15px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-green); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
              <?= strtoupper(substr($t['author_name'], 0, 1)) ?>
            </div>
            <div>
              <h4 style="font-size: 1rem; margin-bottom: 0;"><?= htmlspecialchars(__($t['author_name'], $t['author_name'])) ?></h4>
              <span style="font-size: 0.82rem; color: var(--text-muted);"><?= htmlspecialchars(__($t['role_location'], $t['role_location'])) ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 9. Donation CTA Banner -->
<section class="cta-banner-section" style="background: linear-gradient(135deg, #244526, #388E3C); color: #FFFFFF; padding: 70px 0; text-align: center;">
  <div class="container" style="max-width: 800px;">
    <h2 style="color: #FFFFFF !important; font-size: 2.5rem; margin-bottom: 15px;"><?= __('cta_title', "Transform a Cow's Life Today") ?></h2>
    <p style="font-size: 1.15rem; color: #E0E7DE !important; margin-bottom: 30px;">
      <?= __('cta_desc', 'Gau Seva brings peace, prosperity, and spiritual blessings to your family. Sponsor feeding or medical treatment with 100% tax exemption.') ?>
    </p>
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
      <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-lg"><?= __('btn_make_donation', 'Make a Donation 💖') ?></a>
      <a href="<?= $baseUrl ?>pages/adopt.php" class="btn btn-outline btn-lg" style="color: #FFFFFF !important; border-color: #FFFFFF !important;"><?= __('btn_adopt_cow_btn', 'Adopt a Cow 🐄') ?></a>
    </div>
  </div>
</section>

<!-- Lightbox Modal Container -->
<div class="lightbox-modal">
  <div class="lightbox-content">
    <button class="lightbox-close">&times;</button>
    <button class="lightbox-prev">&#10094;</button>
    <button class="lightbox-next">&#10095;</button>
    <img src="" class="lightbox-img" alt="Gallery Image">
    <div class="lightbox-caption"></div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
