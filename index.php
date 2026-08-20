<?php
// index.php - Home Page

$pageTitle = 'Kamadhenu Goushala - Love, Care & Seva for Gau Mata';
require_once __DIR__ . '/includes/header.php';

// Fetch Featured Cows from MySQL
$stmtCows = $pdo->query("SELECT * FROM cows ORDER BY id ASC LIMIT 3");
$featuredCows = $stmtCows->fetchAll();

// Fetch Featured Seva Opportunities from MySQL
$stmtSeva = $pdo->query("SELECT * FROM seva WHERE is_featured = 1 ORDER BY id ASC LIMIT 4");
$sevaItems = $stmtSeva->fetchAll();

// Fetch Featured Products from MySQL
$stmtProducts = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN product_categories c ON p.category_id = c.id 
    WHERE p.is_featured = 1 
    LIMIT 4
");
$featuredProducts = $stmtProducts->fetchAll();

// Fetch Gallery Preview
$stmtGallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC LIMIT 6");
$galleryItems = $stmtGallery->fetchAll();

// Fetch Testimonials
$stmtTestimonials = $pdo->query("SELECT * FROM testimonials WHERE is_approved = 1 LIMIT 3");
$testimonials = $stmtTestimonials->fetchAll();

// Fetch Upcoming Events from MySQL
$stmtEvents = $pdo->query("SELECT * FROM events WHERE is_featured = 1 ORDER BY event_date ASC LIMIT 3");
$upcomingEvents = $stmtEvents->fetchAll();
?>

<!-- 2. Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="hero-content">
      <span class="hero-badge"> sacred sanctuary</span>
      <h1 class="hero-title">Love, Care & Seva for Gau Mata</h1>
      <p class="hero-desc">
        Join our holy endeavor to protect, shelter, nurse, and feed indigenous Indian cows in Vrindavan Dham. Every small contribution supports lifelong medical care and green fodder.
      </p>
      <div class="hero-buttons">
        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-lg">Donate Now 💖</a>
        <a href="<?= $baseUrl ?>pages/cows.php" class="btn btn-outline btn-lg" style="color:var(--bg-white); border-color:var(--bg-white);">Meet Our Cows 🐄</a>
      </div>
    </div>
  </div>
</section>

<!-- 3. About Section & Statistics -->
<section class="section-padding bg-white">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; align-items: center;">
      <div>
        <span class="section-subtitle">OUR SACRED MISSION</span>
        <h2 class="section-title">Nurturing Indigenous Desi Cows With Devotion</h2>
        <p style="margin-bottom: 15px; color: var(--text-dark);">
          Kamadhenu Goushala was established with the sole mission of serving non-lactating, old, injured, and street-abandoned cows. We provide a peaceful, natural environment where cows roam freely in open green pastures.
        </p>
        <p style="margin-bottom: 25px; color: var(--text-muted);">
          Our Vedic organic farming initiatives produce 100% pure A2 Gir Cow Ghee, herbal Panchagavya soaps, and bio-vermicompost, making our sanctuary self-sustainable.
        </p>
        <a href="<?= $baseUrl ?>pages/about.php" class="btn btn-secondary">Know More About Us</a>
      </div>
      <div>
        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid var(--bg-light-green);">
          <img src="<?= $baseUrl ?>images/hero/about-preview.jpg" alt="Kamadhenu Goushala Sanctuary" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=800&q=80'" style="width:100%; height:400px; object-fit:cover;">
        </div>
      </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('total_cows_count', '450')) ?>+</div>
        <div class="stat-label">Cows Under Our Care</div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('rescued_cows_count', '310')) ?>+</div>
        <div class="stat-label">Rescued & Medical Care</div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('volunteers_count', '120')) ?>+</div>
        <div class="stat-label">Dedicated Volunteers</div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= htmlspecialchars(get_setting('years_of_service', '15')) ?>+</div>
        <div class="stat-label">Years of Pure Seva</div>
      </div>
    </div>
  </div>
</section>

<!-- 4. Our Cows Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle">MEET OUR RESIDENTS</span>
      <h2 class="section-title center">Our Beloved Cows</h2>
      <p>Each cow in our Goushala has a unique story. You can adopt, sponsor feeding, or visit them.</p>
    </div>

    <div class="card-grid">
      <?php foreach ($featuredCows as $cow): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($cow['main_image']) ?>" alt="<?= htmlspecialchars($cow['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1570042707223-2cb2ed999557?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge status-<?= strtolower(str_replace(' ', '-', $cow['health_status'])) ?>">
              <?= htmlspecialchars($cow['health_status']) ?>
            </span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($cow['name']) ?></h3>
            <p class="card-subtitle">Breed: <?= htmlspecialchars($cow['breed']) ?> | Age: <?= $cow['age_years'] ?> Yrs (<?= htmlspecialchars($cow['gender']) ?>)</p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($cow['bio'], 0, 100, '...')) ?></p>
            <div class="card-meta">
              <span>Tag: <strong><?= htmlspecialchars($cow['tag_number']) ?></strong></span>
              <span style="color:var(--accent-orange); font-weight:bold;"><?= format_currency($cow['monthly_adoption_fee']) ?>/mo</span>
            </div>
            <div class="card-actions">
              <a href="<?= $baseUrl ?>pages/cow-details.php?id=<?= $cow['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;">View Details</a>
              <a href="<?= $baseUrl ?>pages/adopt.php?cow_id=<?= $cow['id'] ?>" class="btn btn-primary btn-sm" style="flex:1;">Adopt Cow</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 40px;">
      <a href="<?= $baseUrl ?>pages/cows.php" class="btn btn-secondary btn-lg">View All Cows (<?= count($featuredCows) ?>+)</a>
    </div>
  </div>
</section>

<!-- 5. Seva Opportunities Section -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle">HOLY SERVICE</span>
      <h2 class="section-title center">Ways To Offer Seva</h2>
      <p>Sponsor essential grass, medicines, shelter construction, or daily care for sacred cows.</p>
    </div>

    <div class="card-grid">
      <?php foreach ($sevaItems as $seva): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($seva['image']) ?>" alt="<?= htmlspecialchars($seva['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge"><?= htmlspecialchars($seva['category']) ?></span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($seva['title']) ?></h3>
            <p class="card-subtitle"><?= htmlspecialchars($seva['subtitle']) ?></p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($seva['description'], 0, 110, '...')) ?></p>
            <div class="card-meta">
              <span>Suggested Seva:</span>
              <span class="card-price"><?= format_currency($seva['suggested_amount']) ?></span>
            </div>
            <div class="card-actions">
              <a href="<?= $baseUrl ?>pages/seva-details.php?id=<?= $seva['id'] ?>" class="btn btn-outline btn-sm" style="width:100%;">View Details & Sponsor</a>
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
      <span class="section-subtitle">GOUSHALA STORE</span>
      <h2 class="section-title center">Pure Organic Panchagavya Products</h2>
      <p>Support Goushala maintenance by purchasing authentic A2 Bilona Ghee, herbal soaps, and organic compost.</p>
    </div>

    <div class="card-grid">
      <?php foreach ($featuredProducts as $prod): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1589927986089-35812388d1f4?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge"><?= htmlspecialchars($prod['category_name']) ?></span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($prod['name']) ?></h3>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($prod['description'], 0, 90, '...')) ?></p>
            <div class="card-meta">
              <div class="card-price">
                <?= format_currency($prod['sale_price'] ?: $prod['price']) ?>
                <?php if ($prod['sale_price']): ?>
                  <span class="original-price"><?= format_currency($prod['price']) ?></span>
                <?php endif; ?>
              </div>
              <span style="font-size:0.85rem; color:#2E7D32;">In Stock (<?= $prod['stock_quantity'] ?>)</span>
            </div>
            <div class="card-actions">
              <a href="<?= $baseUrl ?>pages/product-details.php?id=<?= $prod['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;">View Product</a>
              <button class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" style="flex:1;">Add to Cart</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 40px;">
      <a href="<?= $baseUrl ?>pages/products.php" class="btn btn-secondary btn-lg">Browse Organic Store</a>
    </div>
  </div>
</section>

<!-- 7. Gallery Preview Section -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle">VISUAL MEMORIES</span>
      <h2 class="section-title center">Sanctuary Gallery</h2>
      <p>Glance into the serene daily life, feeding rituals, and festivals at Kamadhenu Goushala.</p>
    </div>

    <div class="gallery-grid">
      <?php foreach ($galleryItems as $g): ?>
        <div class="gallery-item" data-image="<?= $baseUrl . htmlspecialchars($g['image_path']) ?>" data-title="<?= htmlspecialchars($g['title']) ?>">
          <img src="<?= $baseUrl . htmlspecialchars($g['image_path']) ?>" alt="<?= htmlspecialchars($g['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
          <div class="gallery-overlay">
            <h4><?= htmlspecialchars($g['title']) ?></h4>
            <p style="font-size:0.85rem; opacity:0.9;"><?= htmlspecialchars($g['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 35px;">
      <a href="<?= $baseUrl ?>pages/gallery.php" class="btn btn-outline btn-lg">Explore Full Gallery</a>
    </div>
  </div>
</section>

<!-- Upcoming Events Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle">FESTIVALS & CELEBRATIONS</span>
      <h2 class="section-title center">Upcoming Sanctuary Events</h2>
      <p>Participate in sacred cow pooja rituals, veterinary medical camps, and festival celebrations.</p>
    </div>

    <div class="card-grid">
      <?php foreach ($upcomingEvents as $ev): ?>
        <div class="card">
          <div class="card-img-wrapper">
            <img src="<?= $baseUrl . htmlspecialchars($ev['image']) ?>" alt="<?= htmlspecialchars($ev['title']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'">
            <span class="card-badge" style="background-color: var(--accent-orange);"><?= htmlspecialchars($ev['status']) ?></span>
          </div>
          <div class="card-body">
            <div style="font-size:0.85rem; color:var(--accent-orange); font-weight:bold; margin-bottom:6px;">
              📅 <?= date('l, d M Y', strtotime($ev['event_date'])) ?>
            </div>
            <h3 class="card-title"><?= htmlspecialchars($ev['title']) ?></h3>
            <p class="card-subtitle">📍 <?= htmlspecialchars($ev['location']) ?></p>
            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($ev['description'], 0, 95, '...')) ?></p>
            <div class="card-actions" style="margin-top:auto;">
              <a href="<?= $baseUrl ?>pages/event-details.php?id=<?= $ev['id'] ?>" class="btn btn-primary btn-sm btn-block">View Event & Attend 🗓</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 35px;">
      <a href="<?= $baseUrl ?>pages/events.php" class="btn btn-secondary btn-lg">View All Events</a>
    </div>
  </div>
</section>

<!-- 8. Testimonials Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
      <span class="section-subtitle">DEVOTEE FEEDBACK</span>
      <h2 class="section-title center">What Donors & Sevaks Say</h2>
    </div>

    <div class="card-grid">
      <?php foreach ($testimonials as $t): ?>
        <div class="card" style="padding: 25px;">
          <div style="font-size: 1.2rem; color: var(--accent-orange); margin-bottom: 10px;">
            <?= str_repeat('★', $t['rating']) ?>
          </div>
          <p style="font-style: italic; color: var(--text-dark); margin-bottom: 20px; flex:1;">
            "<?= htmlspecialchars($t['message']) ?>"
          </p>
          <div style="display: flex; align-items: center; gap: 12px; border-top: 1px solid var(--border-light); padding-top: 15px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-green); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
              <?= strtoupper(substr($t['author_name'], 0, 1)) ?>
            </div>
            <div>
              <h4 style="font-size: 1rem; margin-bottom: 0;"><?= htmlspecialchars($t['author_name']) ?></h4>
              <span style="font-size: 0.82rem; color: var(--text-muted);"><?= htmlspecialchars($t['role_location']) ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 9. Donation CTA Banner -->
<section style="background: linear-gradient(135deg, var(--primary-dark), var(--primary-green)); color: var(--bg-white); padding: 70px 0; text-align: center;">
  <div class="container" style="max-width: 800px;">
    <h2 style="color: var(--bg-white); font-size: 2.5rem; margin-bottom: 15px;">Transform a Cow's Life Today</h2>
    <p style="font-size: 1.15rem; color: var(--bg-light-green); margin-bottom: 30px;">
      Gau Seva brings peace, prosperity, and spiritual blessings to your family. Sponsor feeding or medical treatment with 100% tax exemption.
    </p>
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
      <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-lg">Make a Donation 💖</a>
      <a href="<?= $baseUrl ?>pages/adopt.php" class="btn btn-outline btn-lg" style="color: var(--bg-white); border-color: var(--bg-white);">Adopt a Cow 🐄</a>
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
