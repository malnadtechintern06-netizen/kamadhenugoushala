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
          <?= __('tax_exemption_note', '* All Donations are Eligible for 80G Tax Exemption.') ?>
        </p>
        <div class="footer-social" style="display: flex; gap: 15px; margin-top: 15px;">
          <a href="<?= htmlspecialchars(get_setting('facebook_url', 'https://facebook.com')) ?>" class="social-fb" target="_blank" title="Facebook" style="color: rgba(255,255,255,0.8); font-size: 1.3rem; transition: color 0.2s; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h137.9V327.7h-63v-72.2h63v-55c0-62.2 38-96.4 93.6-96.4 26.6 0 54.7 4.7 54.7 4.7v60h-30.8c-30.8 0-40.4 19.1-40.4 38.7v46.4h67.8l-10.8 72.2h-57V480H400c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48z"></path></svg></a>
          <a href="<?= htmlspecialchars(get_setting('instagram_url', 'https://instagram.com')) ?>" class="social-ig" target="_blank" title="Instagram" style="color: rgba(255,255,255,0.8); font-size: 1.3rem; transition: color 0.2s; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.8 9.9 67.6 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg></a>
          <a href="<?= htmlspecialchars(get_setting('youtube_url', 'https://youtube.com')) ?>" class="social-yt" target="_blank" title="YouTube" style="color: rgba(255,255,255,0.8); font-size: 1.3rem; transition: color 0.2s; display: flex; align-items: center;"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-12.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"></path></svg></a>
        </div>
      </div>

      <!-- Col 2: Quick Links -->
      <div class="footer-col">
        <h4><?= __('footer_quick_links', 'Quick Links') ?></h4>
        <ul class="footer-links">
          <li><a href="<?= $baseUrl ?>index.php"><?= __('nav_home', 'Home') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/about.php"><?= __('footer_about_us', 'About Us & Mission') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/cows.php"><?= __('footer_meet_cows', 'Meet Our Cows') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/products.php"><?= __('footer_organic_products', 'Organic A2 Products') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/events.php"><?= __('footer_events', 'Upcoming Events & Festivals') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/gallery.php"><?= __('footer_gallery', 'Photo & Video Gallery') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/contact.php"><?= __('footer_contact', 'Contact & Location') ?></a></li>
        </ul>
      </div>

      <!-- Col 3: Seva Programs -->
      <div class="footer-col">
        <h4><?= __('footer_seva_opp', 'Seva Opportunities') ?></h4>
        <ul class="footer-links">
          <li><a href="<?= $baseUrl ?>pages/seva.php"><?= __('footer_gau_grass', 'Gau Grass & Fodder Seva') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/seva.php"><?= __('footer_medical_treatment', 'Medical & Veterinary Treatment') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/adopt.php"><?= __('footer_adopt_cow_option', 'Adopt a Cow (Monthly/Yearly)') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/seva.php"><?= __('footer_shelter_construction', 'Shelter Shed Construction') ?></a></li>
          <li><a href="<?= $baseUrl ?>pages/donate.php"><?= __('footer_general_donation', 'One-Time General Donation') ?></a></li>
        </ul>
      </div>

      <!-- Col 4: Contact & Bank Details -->
      <div class="footer-col">
        <h4><?= __('footer_contact_support', 'Contact & Support') ?></h4>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 8px;">
          📍 <?= htmlspecialchars(get_setting('contact_address', 'Vrinda Dham, Mathura, UP - 281001')) ?>
        </p>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 8px;">
          📞 <?= htmlspecialchars(get_setting('contact_phone', '+91 98765 43210')) ?>
        </p>
        <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 15px;">
          📧 <?= htmlspecialchars(get_setting('contact_email', 'info@kamadhenugoushala.org')) ?>
        </p>
        <a href="<?= $baseUrl ?>pages/donate.php" class="btn btn-primary btn-sm"><?= __('btn_support_gau_seva', 'Support Gau Seva') ?></a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_setting('footer_copyright_text', get_setting('site_name', 'Kamadhenu Goushala') . ' Sanctuary. All Rights Reserved.')) ?></p>
      <p><?= __('footer_built_with_devotion', 'Built with Devotion & Care for Gau Mata') ?></p>
    </div>
  </div>
</footer>

<!-- Master JavaScript -->
<script src="<?= $baseUrl ?>js/script.js?v=<?= time() ?>"></script>

<!-- Custom i18n Language Switcher & Full Page Translation Script -->
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
  const langSelects = document.querySelectorAll('#custom-lang-select, .custom-mobile-lang-select');
  langSelects.forEach(select => {
    select.addEventListener('change', function() {
      const selected = this.value;
      const url = new URL(window.location.href);
      url.searchParams.set('lang', selected);
      window.location.href = url.toString();
    });
  });

  const currentLang = "<?= get_current_lang() ?>";
  if (currentLang !== 'en') {
    const dictionary = {
      hi: {
        "Kamadhenu Goushala": "कामधेनु गौशाला",
        "Vrindavan Dham": "वृंदावन धाम",
        "Mathura": "मथुरा",
        "Gau Mata": "गौमाता",
        "Gau Seva": "गौ सेवा",
        "Love, Care & Seva for Gau Mata": "गौमाता के लिए प्रेम, सेवा एवं संरक्षण",
        "SACRED SANCTUARY": "पवित्र गौशाला धाम",
        "OUR SACRED MISSION": "हमारा पवित्र उद्देश्य",
        "Nurturing Indigenous Desi Cows With Devotion": "भक्ति और श्रद्धा से देशी गौवंश का संवर्धन एवं सेवा",
        "Cows Under Our Care": "संरक्षित गौवंश",
        "Rescued & Medical Care": "उपचारित एवं रक्षित गौमाता",
        "Dedicated Volunteers": "समर्पित गौसेवक",
        "Years of Pure Seva": "वर्षों की निरंतर सेवा",
        "OUR JOURNEY": "हमारी विकास यात्रा",
        "Experience Our Goushala": "हमारी गौशाला का अनुभव करें",
        "Meet Our Residents": "गौमाता दर्शन करें",
        "MEET OUR RESIDENTS": "गौमाता दर्शन",
        "Our Beloved Cows": "हमारी प्यारी गौमाता",
        "Adopt Online": "ऑनलाइन गोद लें",
        "View Profile": "प्रोफ़ाइल देखें",
        "Details": "विवरण",
        "View All Cows": "सभी गौमाता देखें",
        "HOLY SERVICE": "पवित्र सेवा",
        "Ways To Offer Seva": "गौ सेवा के अवसर",
        "Suggested Contribution:": "अनुशंसित सेवा राशि:",
        "View Details & Sponsor": "विवरण देखें और सेवा करें",
        "Donate Online": "ऑनलाइन दान करें",
        "Offer Seva on WhatsApp": "व्हाट्सएप पर सेवा करें",
        "GOUSHALA STORE": "गौशाला स्टोर",
        "Pure Organic Panchagavya Products": "शुद्ध जैविक पंचगव्य उत्पाद",
        "Add to Cart": "कार्ट में जोड़ें",
        "View Product": "उत्पाद देखें",
        "Browse Organic Store": "जैविक स्टोर देखें",
        "In Stock": "उपलब्ध",
        "VISUAL MEMORIES": "दृश्य स्मृतियाँ",
        "Sanctuary Gallery": "गौशाला गैलरी",
        "Explore Photo & Video Gallery": "फोटो और वीडियो गैलरी देखें",
        "FESTIVALS & CELEBRATIONS": "उत्सव एवं समारोह",
        "Upcoming Sanctuary Events": "आगामी गौशाला कार्यक्रम",
        "View Event & Attend 🗓": "कार्यक्रम देखें और भाग लें 🗓",
        "View All Events": "सभी कार्यक्रम देखें",
        "DEVOTEE FEEDBACK": "भक्तों के अनुभव",
        "What Donors & Sevaks Say": "भक्तों एवं दानदाताओं के विचार",
        "EVERY GAU SEVA BRINGS BLESSINGS": "प्रत्येक गौ सेवा लाती है सुख-समृद्धि",
        "Support Our Cows Today": "आज ही गौमाता की सेवा में सहयोग करें",
        "Make a Donation 💖": "दान करें 💖",
        "Adopt a Cow 🐄": "गौमाता गोद लें 🐄",
        "Home": "मुख्य पृष्ठ",
        "About": "हमारे बारे में",
        "Our Cows": "हमारी गौमाता",
        "Seva": "गौ सेवा",
        "Products": "पंचगव्य उत्पाद",
        "Events": "कार्यक्रम",
        "Gallery": "गैलरी",
        "Contact": "संपर्क",
        "Login": "लॉग इन",
        "My Account": "मेरा खाता",
        "Donate Now": "दान करें",
        "Breed": "नस्ल",
        "Age": "आयु",
        "Yrs": "वर्ष",
        "Tag": "टैग",
        "Filter": "फिल्टर करें",
        "Reset": "पुनः सेट करें",
        "Search": "खोजें",
        "Search by Cow Name, Tag #, or Breed...": "गौमाता का नाम, टैग न० या नस्ल खोजें...",
        "Search products...": "जैविक उत्पाद खोजें...",
        "Search events...": "कार्यक्रम खोजें...",
        "All Breeds": "सभी नस्लें",
        "All Health Statuses": "सभी स्वास्थ्य स्थितियां",
        "All Products": "सभी उत्पाद",
        "All Events": "सभी कार्यक्रम",
        "All Photos": "सभी फोटो",
        "Upcoming": "आगामी",
        "Ongoing": "जारी है",
        "Completed": "संपन्न",
        "Healthy": "स्वस्थ",
        "Under Care": "संरक्षित",
        "Rescued": "रक्षित",
        "Gir": "गिर",
        "Sahiwal": "साहीवाल",
        "Tharparkar": "थारपारकर",
        "Kankrej": "कांकरेज",
        "Rathi": "राठी",
        "Quick Links": "त्वरित लिंक",
        "About Us & Mission": "हमारे बारे में एवं उद्देश्य",
        "Meet Our Cows": "गौमाता दर्शन",
        "Organic A2 Products": "जैविक A2 उत्पाद",
        "Upcoming Events & Festivals": "आगामी कार्यक्रम एवं उत्सव",
        "Photo & Video Gallery": "फोटो एवं वीडियो गैलरी",
        "Contact & Location": "संपर्क एवं स्थान",
        "Seva Opportunities": "सेवा के अवसर",
        "Gau Grass & Fodder Seva": "हरा चारा एवं भूसा सेवा",
        "Medical & Veterinary Treatment": "चिकित्सा एवं उपचार सेवा",
        "Adopt a Cow (Monthly/Yearly)": "गौमाता गोद लें (मासिक/वार्षिक)",
        "Shelter Shed Construction": "गौशाला शेड निर्माण",
        "One-Time General Donation": "सामान्य दान",
        "Contact & Support": "संपर्क एवं सहायता",
        "Built with Devotion & Care for Gau Mata": "गौमाता के प्रति परम भक्ति एवं श्रद्धा से निर्मित",
        "All Donations are Eligible for 80G Tax Exemption.": "सभी दान 80G आयकर छूट के तहत योग्य हैं।",
        "Gauri": "गौरी",
        "Nandi": "नंदी",
        "Lakshmi": "लक्ष्मी",
        "Saraswati": "सरस्वती",
        "Ganga": "गंगा",
        "Yamuna": "यमुना",
        "Radha": "राधा",
        "Krishna": "कृष्णा",
        "Surabhi": "सुरभि",
        "Kamadhenu": "कामधेनु",
        "Pure A2 Vedic Gir Cow Ghee (Bilona Method 500ml)": "शुद्ध A2 वैदिक गिर गाय का घी (बिलोना विधि 500 मि.ली.)",
        "Handcrafted using traditional Vedic Bilona method from free-grazing Gir cows. Rich golden aroma and high medicinal value.": "पारंपरिक वैदिक बिलोना विधि द्वारा स्वतंत्र विचरण करने वाली गिर गायों के दूध से निर्मित। समृद्ध सुगंध और उच्च औषधीय गुण।",
        "Panchagavya Herbal Bathing Soap (Pack of 3)": "पंचगव्य हर्बल स्नान साबुन (3 का पैक)",
        "Infused with cow ghee, milk, curd, neem, and camphor. 100% natural, chemical-free skin nourishment.": "गौ घी, दूध, दही, नीम और कपूर से युक्त। 100% प्राकृतिक और रसायन मुक्त त्वचा पोषण।",
        "Organic Gau Maya Dhoop Batti (100g Stick Pack)": "जैविक गोमय धूप बत्ती (100 ग्राम स्टिक पैक)",
        "Pure cow dung and aromatic herbs like Guggal, Havan Samagri, and Camphor. Purification and spiritual serenity.": "शुद्ध गोमय और गूगल, हवन सामग्री तथा कपूर जैसे सुगंधित जड़ी-बूटियों से निर्मित। वातावरण शुद्धि एवं आध्यात्मिक शांति।",
        "Enriched Bio-Organic Vermicompost (5kg Bag)": "समृद्ध बायो-ऑर्गेनिक वर्मीकंपोस्ट (5 किग्रा बैग)",
        "Nutrient-rich earthworm-processed cow dung manure for home gardens and organic farming.": "गृह वाटिका और जैविक खेती के लिए केचुओं द्वारा संसाधित पोषक तत्वों से भरपूर गोबर खाद।",
        "Handmade Eco Cow Dung Diyas (Pack of 12)": "हस्तनिर्मित ईको गोमय दीपक (12 का पैक)",
        "Biodegradable, sacred cow-dung lamps for puja, festivals, and home rituals.": "पूजा, त्योहारों और धार्मिक अनुष्ठानों के लिए पर्यावरण-अनुकूल, पवित्र गोमय दीये।",
        "Herbal Panchagavya Hair Oil (200ml)": "हर्बल पंचगव्य केश तेल (200 मि.ली.)",
        "Enriched with A2 ghee, bhringraj, and amla for deep root nourishment.": "A2 घी, भृंगराज और आंवला से समृद्ध, बालों की जड़ों के गहरे पोषण के लिए।",
        "Gomaya Diya": "गोमय दीया",
        "Handmade traditional diyas made from cow dung, suitable for puja, festivals and other cultural occasions.": "गौमय से निर्मित पारंपरिक हस्तनिर्मित दीये, पूजा और त्योहारों के लिए उत्तम।",
        "Cow Dung Cakes": "गोबर के उपले / कंडे",
        "Sun-dried cow dung cakes traditionally used in rural households and certain puja and cultural practices.": "धूप में सुखाए गए गोबर के कंडे, यज्ञ, पूजा एवं धार्मिक अनुष्ठानों के लिए प्रयुक्त।",
        "Organic Cow Manure": "जैविक गोबर खाद",
        "Natural cow manure that can be used as an organic soil amendment and source of nutrients for gardening and agriculture.": "कृषि और बागवानी के लिए मिट्टी की उर्वरता बढ़ाने वाली प्राकृतिक गोबर खाद।",
        "Vermicompost": "वर्मीकंपोस्ट (केंचुआ खाद)",
        "Nutrient-rich organic compost produced with the help of earthworms, suitable for enriching soil and supporting healthy plant growth.": "केंचुआ द्वारा निर्मित पोषक तत्वों से भरपूर जैविक खाद, पौधों की वृद्धि के लिए आदर्श।",
        "Sacred Gau Ark Distillate (Immunity Tonic 500ml)": "पवित्र गो अर्क (रोग प्रतिरोधक टॉनिक 500 मि.ली.)",
        "Pure distilled A2 cow urine distillate processed with medicinal Tulsi and Ajwain. Supports natural immunity and digestion.": "तुलसी और अजवाइन के साथ प्रसंस्कृत शुद्ध A2 गोमूत्र अर्क। प्राकृतिक प्रतिरक्षा और पाचन में सहायक।",
        "Herbal Gau Dant Manjan / Tooth Powder (100g)": "हर्बल गौ दंत मंजन (100 ग्राम)",
        "Traditional ayurvedic tooth powder prepared with cow dung ash, clove oil, camphor, and rock salt for strong gums & teeth.": "गौमय भस्म, लौंग तेल, कपूर और सेंधा नमक से निर्मित पारंपरिक आयुर्वेदिक दंत मंजन। मसूड़ों और दांतों को मजबूत बनाता है।",
        "Grand Gopashtami Mahotsav & Cow Pooja": "भव्य गोपाष्टमी महोत्सव एवं गौ पूजन",
        "Grand Gopashtami Mahotsav &amp; Cow Pooja": "भव्य गोपाष्टमी महोत्सव एवं गौ पूजन",
        "Kamadhenu Dham, Vrindavan": "कामधेनु धाम, वृंदावन",
        "Join us for Vedic Gopashtami Mahotsav featuring 108 cow pooja rituals, special jaggery & fodder feast, cultural bhajans, and prasadam.": "वैदिक गोपाष्टमी महोत्सव में भाग लें जिसमें 108 गौ पूजन अनुष्ठान, विशेष गुड़ व चारा भोजन, सांस्कृतिक भजन एवं प्रसाद शामिल हैं।",
        "Free Emergency Veterinary Medical Camp": "निःशुल्क आपातकालीन पशु चिकित्सा शिविर",
        "Sanctuary Medical ICU, Mathura": "गौशाला मेडिकल आईसीयू, मथुरा",
        "Free health checkup camp for stray and rural cows. Expert doctors providing vaccinations, wound dressing, and medicine kits.": "असाहायिक और ग्रामीण गौवंश के लिए निःशुल्क स्वास्थ्य जाँच शिविर। विशेषज्ञ डॉक्टरों द्वारा टीकाकरण, घाव पट्टी और औषधि किट।",
        "Kartik Purnima 1008 Cow Dung Diya Deepotsav": "कार्तिक पूर्णिमा 1008 गोमय दीप उत्सव",
        "Sanctuary Open Pastures, Vrindavan": "गौशाला खुले मैदान, वृंदावन",
        "Lighting 1008 eco-friendly organic cow dung diyas on the auspicious eve of Kartik Purnima accompanied by evening Aarti.": "कार्तिक पूर्णिमा के शुभ अवसर पर संध्या आरती के साथ 1008 पर्यावरण-अनुकूल गोमय दीपों का प्रज्वलन।",
        "Desi Cow Breed Awareness Program": "देशी गौवंश नस्ल जागरूकता कार्यक्रम",
        "An educational program introducing participants to India's indigenous cow breeds, their unique characteristics, traditional importance, conservation, and role in sustainable agriculture. The program encourages awareness and responsible care of native cattle breeds.": "भारत की देशी गौवंश नस्लों, उनकी विशेषताओं, पारंपरिक महत्व और जैविक कृषि में उनकी भूमिका से परिचित कराने वाला जागरूकता कार्यक्रम।",
        "Gau-Gram Sustainable Farming Camp": "गौ-ग्राम सतत जैविक कृषि शिविर",
        "A practical awareness and learning camp focused on sustainable farming, organic manure, vermicomposting, water conservation, natural farming methods, and the responsible use of cow-based resources to support healthy soil and rural communities.": "सतत जैविक कृषि, गोबर खाद, वर्मीकंपोस्टिंग, जल संरक्षण और प्राकृतिक खेती पद्धतियों पर आधारित व्यावहारिक शिक्षा शिविर।",
        "Mother Cow & Newborn Calf": "गौमाता एवं नवजात बछड़ा",
        "Mother Cow &amp; Newborn Calf": "गौमाता एवं नवजात बछड़ा",
        "A heartwarming moment of a mother cow caring for her newborn calf, representing the love, protection and nurturing care found within our Goushala": "अपनी नवजात संतान की देखभाल करती वात्सल्यमयी गौमाता, जो हमारी गौशाला के प्रेम और संरक्षण को दर्शाती है।",
        "School Children Learning Cow Protection": "गौ संरक्षण की शिक्षा लेते स्कूली बच्चे",
        "An educational experience where school children learn about compassion, responsible animal care, cow protection, and the importance of treating animals with kindness": "एक शैक्षणिक अनुभव जहाँ स्कूली बच्चे दयाभाव, पशु देखभाल, गौ रक्षा और पशुओं के प्रति करुणा सीखते हैं।",
        "Gopashtami Mahotsav Celebration": "गोपाष्टमी महोत्सव समारोह",
        "A joyful celebration dedicated to Gau Mata, featuring traditional cow pooja, beautiful decorations, prayers, devotional activities, and community participation at our Goushala": "गौमाता को समर्पित आनंदमय उत्सव, जिसमें पारंपरिक गौ पूजन, सुंदर सजावट, प्रार्थना और सामुदायिक भागीदारी शामिल है।",
        "Volunteers Serving Fresh Jaggery & Fodder": "ताजा चारा एवं गुड़ सेवा करते स्वयंसेवक",
        "Volunteers Serving Fresh Jaggery &amp; Fodder": "ताजा चारा एवं गुड़ सेवा करते स्वयंसेवक",
        "Volunteers come together to serve fresh fodder and suitable treats such as jaggery to the cows, expressing compassion and devotion through hands-on Gau Seva.": "स्वयंसेवक गौमाता को ताजा हरा चारा और गुड़ सेवा अर्पित करते हैं, जो प्रत्यक्ष गौ सेवा द्वारा भक्ति व्यक्त करते हैं।",
        "Clean & Airy Goushala Sheds": "स्वच्छ एवं हवादार गौशाला शेड",
        "Clean &amp; Airy Goushala Sheds": "स्वच्छ एवं हवादार गौशाला शेड",
        "Our clean and well-ventilated Goushala sheds provide cows with a safe, comfortable and hygienic environment, with proper airflow, shade, space and regular cleaning.": "हमारे स्वच्छ और हवादार गौशाला शेड गौमाता को एक सुरक्षित, आरामदायक और स्वच्छ वातावरण प्रदान करते हैं।",
        "Gir Cows Grazing in Morning Pasture": "प्रातःकालीन मैदान में चरती गिर गायें",
        "A peaceful morning scene of Gir cows grazing freely in green pastures, enjoying fresh grass, open space and the natural surroundings of our Goushala.": "हरे-भरे मैदानों में स्वतंत्र रूप से चरती गिर गायों का शांत प्रातःकालीन दृश्य।",
        "Green Grazing": "हरे मैदानों में विचरण",
        "cows in open grass areas": "खुले हरे मैदानों में विचरण करती गौमाता",
        "Daily Feeding": "दैनिक चारा सेवा",
        "volunteers/caretakers providing fodder": "गौसेवकों द्वारा प्रतिदिन हरा चारा सेवा",
        "Visitors": "दर्शनार्थी एवं श्रद्धालु",
        "Goushala": "गौशाला परिसर",
        "Cows": "गौमाता",
        "OUR JOURNEY": "हमारी यात्रा",
        "Experience Our Goushala": "हमारी गौशाला का अनुभव करें",
        "Watch this beautiful presentation that captures the essence of our daily Seva. See firsthand how your contributions help us rescue, feed, and provide lifelong medical care to our beloved cows in Vrindavan Dham.": "हमारी दैनिक गौ सेवा के इस सुंदर प्रस्तुतिकरण को देखें। देखें कि कैसे आपका सहयोग वृंदावन धाम में असहाय गौवंश के पुनर्वास और उपचार में सहायक है।",
        "Every moment spent serving Gau Mata is a blessing. We invite you to witness the peace, devotion, and joy that fills our sanctuary every single day.": "गौमाता की सेवा में बिताया हर क्षण आशीर्वाद लाता है। हम आपको हमारी गौशाला की शांति और भक्ति का अनुभव करने के लिए आमंत्रित करते हैं।",
        "Meet Our Residents": "गौमाता दर्शन 🐄",
        "DEVOTEE FEEDBACK": "भक्तों के अनुभव",
        "What Donors & Sevaks Say": "भक्तों एवं दानदाताओं के विचार",
        "Ramesh Sharma": "रमेश शर्मा",
        "Jaipur, Rajasthan": "जयपुर, राजस्थान",
        "Visiting Kamadhenu Goushala was a deeply spiritual experience. The cows are taken care of like family members, and the A2 Ghee is 100% authentic!": "कामधेनु गौशाला का दर्शन एक अत्यंत आध्यात्मिक अनुभव था। यहाँ गौमाता की देखभाल परिवार के सदस्यों की तरह की जाती है और A2 घी 100% शुद्ध एवं प्रमाणिक है!",
        "Priya Kulkarni": "प्रिया कुलकर्णी",
        "Mumbai, Maharashtra": "मुंबई, महाराष्ट्र",
        "I adopted Gauri for 1 year. Getting monthly health updates and video clips of her grazing gives immense bliss. Har Har Mahadev!": "मैंने 1 वर्ष के लिए गौरी को गोद लिया। उसकी मासिक स्वास्थ्य अपडेट और हरे मैदानों में चरते हुए वीडियो देखकर अत्यंत आनंद मिलता है। हर हर महादेव!",
        "Dr. Anand Verma": "डॉ० आनंद वर्मा",
        "New Delhi": "नई दिल्ली",
        "The veterinary medical care facilities here are state of the art. Proud to support such a dedicated team of Gau Sevaks.": "यहाँ पशु चिकित्सा की सुविधाएं अत्यंत आधुनिक हैं। गौसेवकों की ऐसी समर्पित टीम का समर्थन करने पर गर्व है।"
      },
      kn: {
        "Kamadhenu Goushala": "ಕಾಮಧೇನು ಗೋಶಾಲೆ",
        "Vrindavan Dham": "ವೃಂದಾವನ ಧಾಮ",
        "Mathura": "ಮಥುರಾ",
        "Gau Mata": "ಗೋಮಾತೆ",
        "Gau Seva": "ಗೋ ಸೇವೆ",
        "Love, Care & Seva for Gau Mata": "ಗೋಮಾತೆಗೆ ಪ್ರೀತಿ, ಕಾಳಜಿ ಮತ್ತು ಸೇವೆ",
        "SACRED SANCTUARY": "ಪವಿತ್ರ ಗೋಶಾಲೆ ಧಾಮ",
        "OUR SACRED MISSION": "ನಮ್ಮ ಪವಿತ್ರ ಧ್ಯೇಯ",
        "Nurturing Indigenous Desi Cows With Devotion": "ಶ್ರದ್ಧೆ ಮತ್ತು ಭಕ್ತಿಯಿಂದ ದೇಶಿ ಗೋವುಗಳ ಪಾಲನೆ",
        "Cows Under Our Care": "ಆರೈಕೆಯಲ್ಲಿರುವ ಗೋವುಗಳು",
        "Rescued & Medical Care": "ರಕ್ಷಿಸಲ್ಪಟ್ಟ ಗೋವುಗಳು",
        "Dedicated Volunteers": "ಸಮರ್ಪಿತ ಗೋಸೇವಕರು",
        "Years of Pure Seva": "ವರ್ಷಗಳ ನಿರಂತರ ಸೇವೆ",
        "OUR JOURNEY": "ನಮ್ಮ ಪ್ರಯಾಣ",
        "Experience Our Goushala": "ನಮ್ಮ ಗೋಶಾಲೆಯ ಅನುಭವ ಪಡೆಯಿರಿ",
        "Meet Our Residents": "ನಮ್ಮ ಗೋವುಗಳನ್ನು ಭೇಟಿ ಮಾಡಿ",
        "MEET OUR RESIDENTS": "ನಮ್ಮ ಗೋವುಗಳನ್ನು ಭೇಟಿ ಮಾಡಿ",
        "Our Beloved Cows": "ನಮ್ಮ ಪ್ರೀತಿಯ ಗೋವುಗಳು",
        "Adopt Online": "ಆನ್‌ಲೈನ್ ದತ್ತು ಪಡೆಯಿರಿ",
        "View Profile": "ಪ್ರೊಫೈಲ್ ವೀಕ್ಷಿಸಿ",
        "Details": "ವಿವರಗಳು",
        "View All Cows": "ಎಲ್ಲಾ ಗೋವುಗಳನ್ನು ವೀಕ್ಷಿಸಿ",
        "HOLY SERVICE": "ಪವಿತ್ರ ಸೇವೆ",
        "Ways To Offer Seva": "ಗೋ ಸೇವೆ ಸಲ್ಲಿಸುವ ಮಾರ್ಗಗಳು",
        "Suggested Contribution:": "ಸೂಚಿಸಿದ ಸೇವಾ ಮೊತ್ತ:",
        "View Details & Sponsor": "ವಿವರ ವೀಕ್ಷಿಸಿ ಮತ್ತು ಪ್ರಾಯೋಜಿಸಿ",
        "Donate Online": "ಆನ್‌ಲೈನ್ ದೇಣಿಗೆ ನೀಡಿ",
        "Offer Seva on WhatsApp": "ವಾಟ್ಸಾಪ್‌ನಲ್ಲಿ ಸೇವೆ ಸಲ್ಲಿಸಿ",
        "GOUSHALA STORE": "ಗೋಶಾಲೆ ಮಳಿಗೆ",
        "Pure Organic Panchagavya Products": "ಶುದ್ಧ ಸಾವಯವ ಪಂಚಗವ್ಯ ಉತ್ಪನ್ನಗಳು",
        "Add to Cart": "ಕಾರ್ಟ್‌ಗೆ ಸೇರಿಸಿ",
        "View Product": "ಉತ್ಪನ್ನ ವೀಕ್ಷಿಸಿ",
        "Browse Organic Store": "ಸಾವಯವ ಮಳಿಗೆ ವೀಕ್ಷಿಸಿ",
        "In Stock": "ಸ್ಟಾಕ್‌ನಲ್ಲಿದೆ",
        "VISUAL MEMORIES": "ದೃಶ್ಯ ನೆನಪುಗಳು",
        "Sanctuary Gallery": "ಗೋಶಾಲೆ ಗ್ಯಾಲರಿ",
        "Explore Photo & Video Gallery": "ಫೋಟೋ ಮತ್ತು ವೀಡಿಯೊ ಗ್ಯಾಲರಿ ವೀಕ್ಷಿಸಿ",
        "FESTIVALS & CELEBRATIONS": "ಉತ್ಸವಗಳು ಮತ್ತು ಆಚರಣೆಗಳು",
        "Upcoming Sanctuary Events": "ಮುಂಬರುವ ಗೋಶಾಲೆ ಕಾರ್ಯಕ್ರಮಗಳು",
        "View Event & Attend 🗓": "ಕಾರ್ಯಕ್ರಮ ವೀಕ್ಷಿಸಿ ಮತ್ತು ಭಾಗವಹಿಸಿ 🗓",
        "View All Events": "ಎಲ್ಲಾ ಕಾರ್ಯಕ್ರಮಗಳನ್ನು ವೀಕ್ಷಿಸಿ",
        "DEVOTEE FEEDBACK": "ಭಕ್ತರ ಅನಿಸಿಕೆಗಳು",
        "What Donors & Sevaks Say": "ಭಕ್ತರು ಮತ್ತು ದೇಣಿಗೆದಾರರ ಮಾತುಗಳು",
        "EVERY GAU SEVA BRINGS BLESSINGS": "ಪ್ರತಿಯೊಂದು ಗೋ ಸೇವೆಯೂ ಆಶೀರ್ವಾದ ತರುತ್ತದೆ",
        "Support Our Cows Today": "ಇಂದೇ ಗೋ ಸೇವೆಗೆ ಬೆಂಬಲಿಸಿ",
        "Make a Donation 💖": "ದೇಣಿಗೆ ನೀಡಿ 💖",
        "Adopt a Cow 🐄": "ಹಸುವನ್ನು ದತ್ತು ගන්න 🐄",
        "Home": "ಮುಖ್ಯ ಪುಟ",
        "About": "ನಮ್ಮ ಬಗ್ಗೆ",
        "Our Cows": "ನಮ್ಮ ಗೋವುಗಳು",
        "Seva": "ಗೋ ಸೇವೆ",
        "Products": "ಉತ್ಪನ್ನಗಳು",
        "Events": "ಕಾರ್ಯಕ್ರಮಗಳು",
        "Gallery": "ಗ್ಯಾಲರಿ",
        "Contact": "ಸಂಪರ್ಕಿಸಿ",
        "Login": "ಲಾಗಿನ್",
        "My Account": "ನನ್ನ ಖಾತೆ",
        "Donate Now": "ದೇಣಿಗೆ ನೀಡಿ",
        "Breed": "ತಳಿ",
        "Age": "ವಯಸ್ಸು",
        "Yrs": "ವರ್ಷ",
        "Tag": "ಟ್ಯಾಗ್",
        "Filter": "ಫಿಲ್ಟರ್ ಮಾಡಿ",
        "Reset": "ಮರುಹೊಂದಿಸಿ",
        "Search": "ಹುಡುಕಿ",
        "Search by Cow Name, Tag #, or Breed...": "ಹಸುವಿನ ಹೆಸರು, ಟ್ಯಾಗ್ ಸಂ., ಅಥವಾ ತಳಿಯ ಮೂಲಕ ಹುಡುಕಿ...",
        "Search products...": "ಸಾವಯವ ಉತ್ಪನ್ನಗಳನ್ನು ಹುಡುಕಿ...",
        "Search events...": "ಕಾರ್ಯಕ್ರಮಗಳನ್ನು ಹುಡುಕಿ...",
        "All Breeds": "ಎಲ್ಲಾ ತಳಿಗಳು",
        "All Health Statuses": "ಎಲ್ಲಾ ಆರೋಗ್ಯ ಸ್ಥಿತಿಗಳು",
        "All Products": "ಎಲ್ಲಾ ಉತ್ಪನ್ನಗಳು",
        "All Events": "ಎಲ್ಲಾ ಕಾರ್ಯಕ್ರಮಗಳು",
        "All Photos": "ಎಲ್ಲಾ ಫೋಟೋಗಳು",
        "Upcoming": "ಮುಂಬರುವ",
        "Ongoing": "ನಡೆಯುತ್ತಿರುವ",
        "Completed": "ಪೂರ್ಣಗೊಂಡಿದೆ",
        "Healthy": "ಆರೋಗ್ಯಕರ",
        "Under Care": "ಆರೈಕೆಯಲ್ಲಿದೆ",
        "Rescued": "ರಕ್ಷಿಸಲ್ಪಟ್ಟಿದೆ",
        "Gir": "ಗಿರ್",
        "Sahiwal": "ಸಾಹಿವಾಲ್",
        "Tharparkar": "ಥಾರ್‌ಪಾರ್ಕರ್",
        "Kankrej": "ಕಾಂಕ್ರೇಜ್",
        "Rathi": "ರಾಠಿ",
        "Quick Links": "ತ್ವರಿತ ಲಿಂಕ್‌ಗಳು",
        "About Us & Mission": "ನಮ್ಮ ಬಗ್ಗೆ ಮತ್ತು ಧ್ಯೇಯ",
        "Meet Our Cows": "ನಮ್ಮ ಗೋವುಗಳನ್ನು ಭೇಟಿ ಮಾಡಿ",
        "Organic A2 Products": "ಸಾವಯವ A2 ಉತ್ಪನ್ನಗಳು",
        "Upcoming Events & Festivals": "ಮುಂಬರುವ ಕಾರ್ಯಕ್ರಮಗಳು",
        "Photo & Video Gallery": "ಫೋಟೋ ಮತ್ತು ವೀಡಿಯೊ ಗ್ಯಾಲರಿ",
        "Contact & Location": "ಸಂಪರ್ಕ ಮತ್ತು ಸ್ಥಳ",
        "Seva Opportunities": "ಸೇವೆಯ ಅವಕಾಶಗಳು",
        "Gau Grass & Fodder Seva": "ಹಸಿರು ಹುಲ್ಲು ಮತ್ತು ಮೇವಿನ ಸೇವೆ",
        "Medical & Veterinary Treatment": "ವೈದ್ಯಕೀಯ ಮತ್ತು ಚಿಕಿತ್ಸಾ ಸೇವೆ",
        "Adopt a Cow (Monthly/Yearly)": "ಗೋ ದತ್ತು (ಮಾಸಿಕ/ವಾರ್ಷಿಕ)",
        "Shelter Shed Construction": "ಗೋಶಾಲೆ ಶೆಡ್ ನಿರ್ಮಾಣ",
        "One-Time General Donation": "ಸಾಮಾನ್ಯ ದೇಣಿಗೆ",
        "Contact & Support": "ಸಂಪರ್ಕ ಮತ್ತು ಬೆಂಬಲ",
        "Built with Devotion & Care for Gau Mata": "ಗೋಮಾತೆಯ ಪರಮ ಭಕ್ತಿಯಿಂದ ನಿರ್ಮಿಸಲಾಗಿದೆ",
        "All Donations are Eligible for 80G Tax Exemption.": "ಎಲ್ಲಾ ದೇಣಿಗೆಗಳು 80G ತೆರಿಗೆ ವಿನಾಯಿತಿಗೆ ಅರ್ಹವಾಗಿವೆ.",
        "Gauri": "ಗೌರಿ",
        "Nandi": "ನಂದಿ",
        "Lakshmi": "ಲಕ್ಷ್ಮಿ",
        "Saraswati": "ಸರಸ್ವತಿ",
        "Ganga": "ಗಂಗಾ",
        "Yamuna": "ಯಮುನಾ",
        "Radha": "ರಾಧಾ",
        "Krishna": "ಕೃಷ್ಣ",
        "Surabhi": "ಸುರಭಿ",
        "Kamadhenu": "ಕಾಮಧೇನು",
        "Pure A2 Vedic Gir Cow Ghee (Bilona Method 500ml)": "ಶುದ್ಧ A2 ವೈದಿಕ ಗಿರ್ ಹಸುವಿನ ತುಪ್ಪ (ಬಿಲೋನಾ ವಿಧಾನ 500 ಮಿ.ಲೀ.)",
        "Handcrafted using traditional Vedic Bilona method from free-grazing Gir cows. Rich golden aroma and high medicinal value.": "ಮುಕ್ತವಾಗಿ ಮೇಯುವ ಗಿರ್ ಹಸುಗಳ ಹಾಲಿನಿಂದ ಸಾಂಪ್ರದಾಯಿಕ ವೈದಿಕ ಬಿಲೋನಾ ವಿಧಾನದಲ್ಲಿ ತಯಾರಿಸಲಾಗಿದೆ. ಅತ್ಯುನ್ನತ ಔಷಧೀಯ ಗುಣ ಹೊಂದಿದೆ.",
        "Panchagavya Herbal Bathing Soap (Pack of 3)": "ಪಂಚಗವ್ಯ ಹರ್ಬಲ್ ಸ್ನಾನದ ಸೋಪು (3 ರ ಪ್ಯಾಕ್)",
        "Infused with cow ghee, milk, curd, neem, and camphor. 100% natural, chemical-free skin nourishment.": "ಹಸುವಿನ ತುಪ್ಪ, ಹಾಲು, ಮೊಸರು, ಬೇವಿನಿಂದ ಸಮೃದ್ಧವಾಗಿದೆ. 100% ನೈಸರ್ಗಿಕ, ರಾಸಾಯನಿಕ ರಹಿತ ತ್ವಚೆಯ ಆರೈಕೆ.",
        "Organic Gau Maya Dhoop Batti (100g Stick Pack)": "ಸಾವಯವ ಗೋಮಯ ಧೂಪದ ಬತ್ತಿ (100 ಗ್ರಾಂ ಸ್ಟಿಕ್ ಪ್ಯಾಕ್)",
        "Pure cow dung and aromatic herbs like Guggal, Havan Samagri, and Camphor. Purification and spiritual serenity.": "ಶುದ್ಧ ಸಗಣಿ ಮತ್ತು ಗೂಗಲ್, ಹವನ ಸಾಮಗ್ರಿಗಳೊಂದಿಗೆ ತಯಾರಿಸಲಾಗಿದೆ. ವಾತಾವರಣದ ಶುದ್ಧತೆ ಮತ್ತು ಆಧ್ಯಾತ್ಮಿಕ ಶಾಂತಿಗೆ ಶ್ರೇಷ್ಠ.",
        "Enriched Bio-Organic Vermicompost (5kg Bag)": "ಸಮೃದ್ಧ ಬಯೋ-ಸಾವಯವ ವರ್ಮಿಕಂಪೋಸ್ಟ್ (5 ಕೆಜಿ ಬ್ಯಾಗ್)",
        "Nutrient-rich earthworm-processed cow dung manure for home gardens and organic farming.": "ಮನೆ ತೋಟ ಮತ್ತು ಸಾವಯವ ಕೃಷಿಗೆ ಉಪಯುಕ್ತವಾದ ಎರೆಹುಳು ಸಂಸ್ಕರಿಸಿದ ಸಗಣಿ ಗೊಬ್ಬರ.",
        "Handmade Eco Cow Dung Diyas (Pack of 12)": "ಹಸ್ತನಿರ್ಮಿತ ಇಕೋ ಗೋಮಯ ದೀಪಗಳು (12 ರ ಪ್ಯಾಕ್)",
        "Biodegradable, sacred cow-dung lamps for puja, festivals, and home rituals.": "ಪೂಜೆ, ಹಬ್ಬಗಳು ಮತ್ತು ಧಾರ್ಮಿಕ ಕಾರ್ಯಕ್ರಮಗಳಿಗೆ ಸೂಕ್ತವಾದ ನೈಸರ್ಗಿಕ ಗೋಮಯ ದೀಪಗಳು.",
        "Herbal Panchagavya Hair Oil (200ml)": "ಹರ್ಬಲ್ ಪಂಚಗವ್ಯ ಕೂದಲು ಎಣ್ಣೆ (200 ಮಿ.ಲೀ.)",
        "Enriched with A2 ghee, bhringraj, and amla for deep root nourishment.": "A2 ತುಪ್ಪ, ಭೃಂಗರಾಜ ಮತ್ತು ಆಮ್ಲಾದಿಂದ ಸಮೃದ್ಧವಾಗಿದೆ, ಕೂದಲಿನ ಬೇರುಗಳಿಗೆ ಪೋಷಣೆ ನೀಡುತ್ತದೆ.",
        "Gomaya Diya": "ಗೋಮಯ ದೀಪ",
        "Handmade traditional diyas made from cow dung, suitable for puja, festivals and other cultural occasions.": "ಸಗಣಿಯಿಂದ ತಯಾರಿಸಿದ ಸಾಂಪ್ರದಾಯಿಕ ದೀಪಗಳು, ಪೂಜೆ ಮತ್ತು ಹಬ್ಬಗಳಿಗೆ ಸೂಕ್ತ.",
        "Cow Dung Cakes": "ಸಗಣಿ ಬೆರಣಿಗಳು",
        "Sun-dried cow dung cakes traditionally used in rural households and certain puja and cultural practices.": "ಬಿಸಿಲಿನಲ್ಲಿ ಒಣಗಿಸಿದ ಸಗಣಿ ಬೆರಣಿಗಳು, ಯಜ್ಞ ಮತ್ತು ಪೂಜಾ ಕಾರ್ಯಕ್ರಮಗಳಿಗೆ ಸೂಕ್ತ.",
        "Organic Cow Manure": "ಸಾವಯವ ಸಗಣಿ ಗೊಬ್ಬರ",
        "Natural cow manure that can be used as an organic soil amendment and source of nutrients for gardening and agriculture.": "ಕೃಷಿ ಮತ್ತು ತೋಟಗಾರಿಕೆಗೆ ಮಣ್ಣಿನ ಫಲವತ್ತತೆ ಹೆಚ್ಚಿಸುವ ನೈಸರ್ಗಿಕ ಸಗಣಿ ಗೊಬ್ಬರ.",
        "Vermicompost": "ವರ್ಮಿಕಂಪೋಸ್ಟ್ (ಎರೆಹುಳು ಗೊಬ್ಬರ)",
        "Nutrient-rich organic compost produced with the help of earthworms, suitable for enriching soil and supporting healthy plant growth.": "ಎರೆಹುಳುಗಳಿಂದ ತಯಾರಿಸಿದ ನೈಸರ್ಗಿಕ ಗೊಬ್ಬರ, ಗಿಡಗಳ ಉತ್ತಮ ಬೆಳವಣಿಗೆಗೆ ಶ್ರೇಷ್ಠ.",
        "Sacred Gau Ark Distillate (Immunity Tonic 500ml)": "ಪವಿತ್ರ ಗೋ ಅರ್ಕ (ರೋಗನಿರೋಧಕ ಟಾನಿಕ್ 500 ಮಿ.ಲೀ.)",
        "Pure distilled A2 cow urine distillate processed with medicinal Tulsi and Ajwain. Supports natural immunity and digestion.": "ತುಳಸಿ ಮತ್ತು ಓಂಕಾಳಿನೊಂದಿಗೆ ಸಂಸ್ಕರಿಸಿದ ಶುದ್ಧ A2 ಗೋಮೂತ್ರ ಅರ್ಕ. ರೋಗನಿರೋಧಕ ಶಕ್ತಿ ಹೆಚ್ಚಿಸುತ್ತದೆ.",
        "Herbal Gau Dant Manjan / Tooth Powder (100g)": "ಹರ್ಬಲ್ ಗೋ ದಂತ ಮಂಜನ (100 ಗ್ರಾಂ)",
        "Traditional ayurvedic tooth powder prepared with cow dung ash, clove oil, camphor, and rock salt for strong gums & teeth.": "ಗೋಮಯ ಭಸ್ಮ, ಲವಂಗದ ಎಣ್ಣೆ, ಕರ್ಪೂರ ಮತ್ತು ಕಲ್ಲುಪ್ಪಿನಿಂದ ತಯಾರಿಸಿದ ಸಾಂಪ್ರದಾಯಿಕ ದಂತ ಮಂಜನ.",
        "Grand Gopashtami Mahotsav & Cow Pooja": "ಭವ್ಯ ಗೋಪಾಷ್ಟಮಿ ಮಹೋತ್ಸವ ಮತ್ತು ಗೋ ಪೂಜೆ",
        "Grand Gopashtami Mahotsav &amp; Cow Pooja": "ಭವ್ಯ ಗೋಪಾಷ್ಟಮಿ ಮಹೋತ್ಸವ ಮತ್ತು ಗೋ ಪೂಜೆ",
        "Kamadhenu Dham, Vrindavan": "ಕಾಮಧೇನು ಧಾಮ, ವೃಂದಾವನ",
        "Join us for Vedic Gopashtami Mahotsav featuring 108 cow pooja rituals, special jaggery & fodder feast, cultural bhajans, and prasadam.": "108 ಗೋ ಪೂಜೆ, ವಿಶೇಷ ಬೆಲ್ಲ ಮತ್ತು ಮೇವಿನ ಹಬ್ಬ, ಸಾಂಸ್ಕೃತಿಕ ಭಜನೆ ಮತ್ತು ಪ್ರಸಾದದೊಂದಿಗೆ ವೈದಿಕ ಗೋಪಾಷ್ಟಮಿ ಮಹೋತ್ಸವದಲ್ಲಿ ಪಾಲ್ಗೊಳ್ಳಿ.",
        "Free Emergency Veterinary Medical Camp": "ಉಚಿತ ತುರ್ತು ಪಶು ವೈದ್ಯಕೀಯ ಶಿಬಿರ",
        "Sanctuary Medical ICU, Mathura": "ಗೋಶಾಲೆ ವೈದ್ಯಕೀಯ ಐಸಿಯು, ಮಥುರಾ",
        "Free health checkup camp for stray and rural cows. Expert doctors providing vaccinations, wound dressing, and medicine kits.": "ಅನಾಥ ಮತ್ತು ಗ್ರಾಮೀಣ ಗೋವುಗಳಿಗೆ ಉಚಿತ ಆರೋಗ್ಯ ತಪಾಸಣಾ ಶಿಬಿರ. ತಜ್ಞ ವೈದ್ಯರಿಂದ ಲಸಿಕೆ, ಗಾಯದ ಚಿಕಿತ್ಸೆ ಮತ್ತು ಔಷಧ ಕಿಟ್‌ಗಳು.",
        "Kartik Purnima 1008 Cow Dung Diya Deepotsav": "ಕಾರ್ತಿಕ ಪೂರ್ಣಿಮಾ 1008 ಗೋಮಯ ದೀಪೋತ್ಸವ",
        "Sanctuary Open Pastures, Vrindavan": "ಗೋಶಾಲೆ ಮೈದಾನ, ವೃಂದಾವನ",
        "Lighting 1008 eco-friendly organic cow dung diyas on the auspicious eve of Kartik Purnima accompanied by evening Aarti.": "ಕಾರ್ತಿಕ ಪೂರ್ಣಿಮೆಯ ಪವಿತ್ರ ದಿನದಂದು ಸಂಜೆ ಆರತಿಯೊಂದಿಗೆ 1008 ನೈಸರ್ಗಿಕ ಗೋಮಯ ದೀಪಗಳನ್ನು ಬೆಳಗಿಸುವ ಕಾರ್ಯಕ್ರಮ.",
        "Desi Cow Breed Awareness Program": "ದೇಶಿ ಗೋವಿನ ತಳಿ ಜಾಗೃತಿ ಕಾರ್ಯಕ್ರಮ",
        "An educational program introducing participants to India's indigenous cow breeds, their unique characteristics, traditional importance, conservation, and role in sustainable agriculture. The program encourages awareness and responsible care of native cattle breeds.": "ಭಾರತದ ತಳಿಗಳು, ಅವುಗಳ ಪ್ರಾಮುಖ್ಯತೆ ಮತ್ತು ಸಾವಯವ ಕೃಷಿಯಲ್ಲಿ ಅವುಗಳ ಪಾತ್ರದ ಕುರಿತು ಜಾಗೃತಿ ಮೂಡಿಸುವ ಕಾರ್ಯಕ್ರಮ.",
        "Gau-Gram Sustainable Farming Camp": "ಗೋ-ಗ್ರಾಮ ಸುಸ್ಥಿರ ಸಾವಯವ ಕೃಷಿ ಶಿಬಿರ",
        "A practical awareness and learning camp focused on sustainable farming, organic manure, vermicomposting, water conservation, natural farming methods, and the responsible use of cow-based resources to support healthy soil and rural communities.": "ಸುಸ್ಥಿರ ಕೃಷಿ, ಸಾವಯವ ಗೊಬ್ಬರ, ವರ್ಮಿಕಂಪೋಸ್ಟಿಂಗ್ ಮತ್ತು ನೈಸರ್ಗಿಕ ಕೃಷಿ ಪದ್ಧತಿಗಳ ಕುರಿತು ಪ್ರಾಯೋಗಿಕ ತರಬೇತಿ ಶಿಬಿರ.",
        "Mother Cow & Newborn Calf": "ತಾಯಿ ಹಸು ಮತ್ತು ನವಜಾತ ಕರು",
        "Mother Cow &amp; Newborn Calf": "ತಾಯಿ ಹಸು ಮತ್ತು ನವಜಾತ ಕರು",
        "A heartwarming moment of a mother cow caring for her newborn calf, representing the love, protection and nurturing care found within our Goushala": "ತನ್ನ ನವಜಾತ ಕರುವನ್ನು ಪ್ರೀತಿಯಿಂದ ಆರೈಕೆ ಮಾಡುತ್ತಿರುವ ತಾಯಿ ಹಸುವಿನ ಸುಂದರ ಕ್ಷಣ.",
        "School Children Learning Cow Protection": "ಗೋ ರಕ್ಷಣೆಯ ಬಗ್ಗೆ ಕಲಿಯುತ್ತಿರುವ ಶಾಲಾ ಮಕ್ಕಳು",
        "An educational experience where school children learn about compassion, responsible animal care, cow protection, and the importance of treating animals with kindness": "ಶಾಲಾ ಮಕ್ಕಳು ಪ್ರಾಣಿಗಳ ಮೇಲಿನ ದಯೆ, ಗೋ ರಕ್ಷಣೆ ಮತ್ತು ಕರುಣೆಯ ಬಗ್ಗೆ ಕಲಿಯುವ ಶೈಕ್ಷಣಿಕ ಕಾರ್ಯಕ್ರಮ.",
        "Gopashtami Mahotsav Celebration": "ಗೋಪಾಷ್ಟಮಿ ಮಹೋತ್ಸವ ಆಚರಣೆ",
        "A joyful celebration dedicated to Gau Mata, featuring traditional cow pooja, beautiful decorations, prayers, devotional activities, and community participation at our Goushala": "ಗೋಮಾತೆಗೆ ಅರ್ಪಿತವಾದ ಭಕ್ತಿಪೂರ್ವಕ ಉತ್ಸವ, ಗೋ ಪೂಜೆ ಮತ್ತು ಸಮುದಾಯದ ಭಾಗಿತ್ವದೊಂದಿಗೆ.",
        "Volunteers Serving Fresh Jaggery & Fodder": "ತಾಜಾ ಹುಲ್ಲು ಮತ್ತು ಬೆಲ್ಲದ ಸೇವೆ ಮಾಡುತ್ತಿರುವ ಸ್ವಯಂಸೇವಕರು",
        "Volunteers Serving Fresh Jaggery &amp; Fodder": "ತಾಜಾ ಹುಲ್ಲು ಮತ್ತು ಬೆಲ್ಲದ ಸೇವೆ ಮಾಡುತ್ತಿರುವ ಸ್ವಯಂಸೇವಕರು",
        "Volunteers come together to serve fresh fodder and suitable treats such as jaggery to the cows, expressing compassion and devotion through hands-on Gau Seva.": "ಗೋವುಗಳಿಗೆ ತಾಜಾ ಹಸಿರು ಹುಲ್ಲು ಮತ್ತು ಬೆಲ್ಲವನ್ನು ನೀಡುವ ಮೂಲಕ ಗೋ ಸೇವೆ ಸಲ್ಲಿಸುತ್ತಿರುವ ಸ್ವಯಂಸೇವಕರು.",
        "Clean & Airy Goushala Sheds": "ಶುಚಿಯಾದ ಮತ್ತು ಗಾಳಿಯಾಡುವ ಗೋಶಾಲೆ ಶೆಡ್‌ಗಳು",
        "Clean &amp; Airy Goushala Sheds": "ಶುಚಿಯಾದ ಮತ್ತು ಗಾಳಿಯಾಡುವ ಗೋಶಾಲೆ ಶೆಡ್‌ಗಳು",
        "Our clean and well-ventilated Goushala sheds provide cows with a safe, comfortable and hygienic environment, with proper airflow, shade, space and regular cleaning.": "ನಮ್ಮ ನೈರ್ಮಲ್ಯ ಭರಿತ ಮತ್ತು ಸುಸಜ್ಜಿತ ಗೋಶಾಲೆ ಶೆಡ್‌ಗಳು ಗೋವುಗಳಿಗೆ ಸುರಕ್ಷಿತ ಮತ್ತು ಆರಾಮದಾಯಕ ವಾತಾವರಣವನ್ನು ನೀಡುತ್ತವೆ.",
        "Gir Cows Grazing in Morning Pasture": "ಬೆಳಗಿನ ಹುಲ್ಲುಗಾವಲಿನಲ್ಲಿ ಮೇಯುತ್ತಿರುವ ಗಿರ್ ಹಸುಗಳು",
        "A peaceful morning scene of Gir cows grazing freely in green pastures, enjoying fresh grass, open space and the natural surroundings of our Goushala.": "ಹಸಿರು ಹುಲ್ಲುಗಾವಲುಗಳಲ್ಲಿ ಮುಕ್ತವಾಗಿ ಮೇಯುತ್ತಿರುವ ಗಿರ್ ಹಸುಗಳ ಶಾಂತಿಯುತ ಬೆಳಗಿನ ನೋಟ.",
        "Green Grazing": "ಹಸಿರು ಹುಲ್ಲುಗಾವಲು ಮೇಯುವಿಕೆ",
        "cows in open grass areas": "ಮುಕ್ತ ಹಸಿರು ಹುಲ್ಲುಗಾವಲಿನಲ್ಲಿರುವ ಗೋವುಗಳು",
        "Daily Feeding": "ದೈನಂದಿನ ಮೇವಿನ ಸೇವೆ",
        "volunteers/caretakers providing fodder": "ಗೋಸೇವಕರಿಂದ ದಿನನಿತ್ಯದ ಮೇವಿನ ಸೇವೆ",
        "Visitors": "ಸಂದರ್ಶಕರು",
        "Goushala": "ಗೋಶಾಲೆ ಆವರಣ",
        "Cows": "ಗೋವುಗಳು",
        "OUR JOURNEY": "ನಮ್ಮ ಪ್ರಯಾಣ",
        "Experience Our Goushala": "ನಮ್ಮ ಗೋಶಾಲೆಯನ್ನು ಅನುಭವಿಸಿ",
        "Watch this beautiful presentation that captures the essence of our daily Seva. See firsthand how your contributions help us rescue, feed, and provide lifelong medical care to our beloved cows in Vrindavan Dham.": "ನಮ್ಮ ದೈನಂದಿನ ಗೋ ಸೇವೆಯ ಸುಂದರ ನೋಟವನ್ನು ವೀಕ್ಷಿಸಿ. ನಿಮ್ಮ ಬೆಂಬಲವು ಗೋವುಗಳ ಚಿಕಿತ್ಸೆ ಮತ್ತು ಮೇವಿಗೆ ಹೇಗೆ ನೆರವಾಗುತ್ತದೆ ಎಂಬುದನ್ನು ನೇರವಾಗಿ ವೀಕ್ಷಿಸಿ.",
        "Every moment spent serving Gau Mata is a blessing. We invite you to witness the peace, devotion, and joy that fills our sanctuary every single day.": "ಗೋಮಾತೆಯ ಸೇವೆಯಲ್ಲಿ ಕಳೆದ ಪ್ರತಿಯೊಂದು ಕ್ಷಣವೂ ಆಶೀರ್ವಾದ ನೀಡುತ್ತದೆ. ನಮ್ಮ ಗೋಶಾಲೆಯ ಶಾಂತಿ ಮತ್ತು ಭಕ್ತಿಯನ್ನು ಅನುಭವಿಸಲು ನಾವು ನಿಮ್ಮನ್ನು ಆಹ್ವಾನಿಸುತ್ತೇವೆ.",
        "Meet Our Residents": "ನಮ್ಮ ಗೋವುಗಳನ್ನು ಭೇಟಿ ಮಾಡಿ 🐄",
        "DEVOTEE FEEDBACK": "ಭಕ್ತರ ಅನಿಸಿಕೆಗಳು",
        "What Donors & Sevaks Say": "ಭಕ್ತರು ಮತ್ತು ದೇಣಿಗೆದಾರರ ಮಾತುಗಳು",
        "Ramesh Sharma": "ರಮೇಶ್ ಶರ್ಮಾ",
        "Jaipur, Rajasthan": "ಜೈಪುರ, ರಾಜಸ್ಥಾನ",
        "Visiting Kamadhenu Goushala was a deeply spiritual experience. The cows are taken care of like family members, and the A2 Ghee is 100% authentic!": "ಕಾಮಧೇನು ಗೋಶಾಲೆಗೆ ಭೇಟಿ ನೀಡಿದ್ದು ಅತ್ಯಂತ ಆಧ್ಯಾತ್ಮಿಕ ಅನುಭವವಾಗಿತ್ತು. ಗೋವುಗಳನ್ನು ಕುಟುಂಬದ ಸದಸ್ಯರಂತೆ ಆರೈಕೆ ಮಾಡಲಾಗುತ್ತದೆ ಮತ್ತು A2 ತುಪ್ಪವು 100% ಶುದ್ಧವಾಗಿದೆ!",
        "Priya Kulkarni": "ಪ್ರಿಯಾ ಕುಲಕರ್ಣಿ",
        "Mumbai, Maharashtra": "ಮುಂಬೈ, ಮಹಾರಾಷ್ಟ್ರ",
        "I adopted Gauri for 1 year. Getting monthly health updates and video clips of her grazing gives immense bliss. Har Har Mahadev!": "ನಾನು 1 ವರ್ಷದ ಅವಧಿಗೆ ಗೌರಿಯನ್ನು ದತ್ತು ಪಡೆದೆ. ಮಾಸಿಕ ಆರೋಗ್ಯ ವರದಿ ಮತ್ತು ವೀಡಿಯೊ ತುಣುಕುಗಳನ್ನು ಪಡೆಯುವುದು ಅಪಾರ ಆನಂದವನ್ನು ನೀಡುತ್ತದೆ.",
        "Dr. Anand Verma": "ಡಾ. ಆನಂದ್ ವರ್ಮಾ",
        "New Delhi": "ನವದೆಹಲಿ",
        "The veterinary medical care facilities here are state of the art. Proud to support such a dedicated team of Gau Sevaks.": "ಇಲ್ಲಿನ ಪಶು ವೈದ್ಯಕೀಯ ಚಿಕಿತ್ಸಾ ಸೌಲಭ್ಯಗಳು ಅತ್ಯುತ್ತಮವಾಗಿವೆ. ಇಂತಹ ಸಮರ್ಪಿತ ಗೋಸೇವಕರ ತಂಡಕ್ಕೆ ಬೆಂಬಲ ನೀಡಲು ಹೆಮ್ಮೆಯೆನಿಸುತ್ತದೆ."
      }
    };

    const activeDict = dictionary[currentLang] || {};

    function translateNodeText(node) {
      if (node.nodeType === Node.TEXT_NODE) {
        let val = node.nodeValue;
        if (!val || !val.trim()) return;

        const trimmed = val.trim();
        if (activeDict[trimmed]) {
          node.nodeValue = val.replace(trimmed, activeDict[trimmed]);
          return;
        }

        for (const [key, replacement] of Object.entries(activeDict)) {
          if (val.includes(key)) {
            val = val.replaceAll(key, replacement);
          }
        }
        node.nodeValue = val;
      } else if (node.nodeType === Node.ELEMENT_NODE) {
        const tag = node.tagName.toLowerCase();
        if (tag === 'script' || tag === 'style' || tag === 'textarea' || tag === 'code' || node.classList.contains('notranslate')) {
          return;
        }

        if (tag === 'input' && node.placeholder) {
          let ph = node.placeholder.trim();
          if (activeDict[ph]) {
            node.placeholder = activeDict[ph];
          }
        }

        for (let child of node.childNodes) {
          translateNodeText(child);
        }
      }
    }

    translateNodeText(document.body);
  }
});
</script>
</body>
</html>
