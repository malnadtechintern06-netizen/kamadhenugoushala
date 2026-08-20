<?php
// includes/footer.php - Global Footer Layout Component
?>
</main> <!-- End Main Content -->

<footer>
  <div class="container">
    <div class="footer-grid">
      <!-- Col 1: About -->
      <div class="footer-col">
        <h4>Kamadhenu Goushala</h4>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.92rem; margin-bottom: 20px;">
          Dedicated to the protection, rescue, medical care, and lifelong service of indigenous Indian cows. Operating with pure devotion in sacred Vrindavan Dham.
        </p>
        <p style="color: var(--accent-orange); font-size: 0.88rem; font-weight: 600;">
          * All Donations are Eligible for 80G Tax Exemption.
        </p>
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
      <p>&copy; <?= date('Y') ?> Kamadhenu Goushala Sanctuary. All Rights Reserved.</p>
      <p>Built with Devotion & Care for Gau Mata</p>
    </div>
  </div>
</footer>

<!-- Master JavaScript -->
<script src="<?= $baseUrl ?>js/script.js"></script>
</body>
</html>
