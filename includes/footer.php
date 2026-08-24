<?php
// includes/footer.php - Global Footer Layout Component
?>
</main> <!-- End Main Content -->

<footer>
  <div class="container">
    <div class="footer-grid">
      <!-- Col 1: About -->
      <div class="footer-col">
        <h4><?= htmlspecialchars(get_setting('site_name', 'Kamadhenu Goushala')) ?></h4>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.92rem; margin-bottom: 20px;">
          <?= htmlspecialchars(get_setting('footer_about_text', 'Dedicated to the protection, rescue, medical care, and lifelong service of indigenous Indian cows. Operating with pure devotion in sacred Vrindavan Dham.')) ?>
        </p>
        <p style="color: var(--accent-orange); font-size: 0.88rem; font-weight: 600; margin-bottom: 15px;">
          * All Donations are Eligible for 80G Tax Exemption.
        </p>
        <div class="footer-social" style="display: flex; gap: 15px; margin-top: 15px;">
          <a href="<?= htmlspecialchars(get_setting('facebook_url', 'https://facebook.com')) ?>" class="social-fb" target="_blank" title="Facebook" style="color: rgba(255,255,255,0.8); font-size: 1.3rem; transition: color 0.2s; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h137.9V327.7h-63v-72.2h63v-55c0-62.2 38-96.4 93.6-96.4 26.6 0 54.7 4.7 54.7 4.7v60h-30.8c-30.8 0-40.4 19.1-40.4 38.7v46.4h67.8l-10.8 72.2h-57V480H400c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48z"></path></svg></a>
          <a href="<?= htmlspecialchars(get_setting('instagram_url', 'https://instagram.com')) ?>" class="social-ig" target="_blank" title="Instagram" style="color: rgba(255,255,255,0.8); font-size: 1.3rem; transition: color 0.2s; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.8 9.9 67.6 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg></a>
          <a href="<?= htmlspecialchars(get_setting('youtube_url', 'https://youtube.com')) ?>" class="social-yt" target="_blank" title="YouTube" style="color: rgba(255,255,255,0.8); font-size: 1.3rem; transition: color 0.2s; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-12.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"></path></svg></a>
        </div>
      </div>

      <!-- Col 2: Quick Links -->
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul class="footer-links">
          <li><a href="<?= $baseUrl ?>index.php">Home</a></li>
          <li><a href="<?= $baseUrl ?>pages/about.php">About Us & Mission</a></li>
          <li><a href="<?= $baseUrl ?>pages/cows.php">Meet Our Cows</a></li>
          <li><a href="<?= $baseUrl ?>pages/products.php">Organic A2 Products</a></li>
          <li><a href="<?= $baseUrl ?>pages/events.php">Upcoming Events & Festivals</a></li>
          <li><a href="<?= $baseUrl ?>pages/gallery.php">Photo & Video Gallery</a></li>
          <li><a href="<?= $baseUrl ?>pages/contact.php">Contact & Location</a></li>
        </ul>
      </div>

      <!-- Col 3: Seva Programs -->
      <div class="footer-col">
        <h4>Seva Opportunities</h4>
        <ul class="footer-links">
          <li><a href="<?= $baseUrl ?>pages/seva.php">Gau Grass & Fodder Seva</a></li>
          <li><a href="<?= $baseUrl ?>pages/seva.php">Medical & Veterinary Treatment</a></li>
          <li><a href="<?= $baseUrl ?>pages/adopt.php">Adopt a Cow (Monthly/Yearly)</a></li>
          <li><a href="<?= $baseUrl ?>pages/seva.php">Shelter Shed Construction</a></li>
          <li><a href="<?= $baseUrl ?>pages/donate.php">One-Time General Donation</a></li>
        </ul>
      </div>

      <!-- Col 4: Contact & Bank Details -->
      <div class="footer-col">
        <h4>Contact & Support</h4>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 8px;">
          📍 <?= htmlspecialchars(get_setting('contact_address', 'Vrinda Dham, Mathura, UP - 281001')) ?>
        </p>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 8px;">
          📞 <?= htmlspecialchars(get_setting('contact_phone', '+91 98765 43210')) ?>
        </p>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 15px;">
          📧 <?= htmlspecialchars(get_setting('contact_email', 'info@kamadhenugoushala.org')) ?>
        </p>
        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-sm">Support Gau Seva</a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_setting('footer_copyright_text', get_setting('site_name', 'Kamadhenu Goushala') . ' Sanctuary. All Rights Reserved.')) ?></p>
      <p>Built with Devotion & Care for Gau Mata</p>
    </div>
  </div>
</footer>

<!-- Master JavaScript -->
<script src="<?= $baseUrl ?>js/script.js?v=<?= time() ?>"></script>
</body>
</html>
