-- Kamadhenu Goushala Database Schema & Initial Data
-- Database Engine: InnoDB | Character Set: utf8mb4_unicode_ci

CREATE DATABASE IF NOT EXISTS `kamadhenu_goushala` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kamadhenu_goushala`;

-- --------------------------------------------------------
-- Table: roles
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator with full system access'),
(2, 'user', 'Regular registered user/donor');

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT NOT NULL DEFAULT 2,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `city` VARCHAR(50) DEFAULT NULL,
  `state` VARCHAR(50) DEFAULT NULL,
  `pincode` VARCHAR(10) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User (Password: Admin@12345)
-- Hash generated using password_hash('Admin@12345', PASSWORD_BCRYPT)
INSERT INTO `users` (`id`, `role_id`, `full_name`, `email`, `phone`, `password_hash`, `status`) VALUES
(1, 1, 'Goushala Administrator', 'admin@kamadhenugoushala.org', '+91 9876543210', '$2y$10$CCuvcmxZSPTVn72hZHFE9.rPE7.G41hLVfZAEfbKzDGm2FAEkbOqW', 'active');

-- --------------------------------------------------------
-- Table: cows
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cows` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tag_number` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `breed` VARCHAR(100) NOT NULL,
  `age_years` INT NOT NULL DEFAULT 1,
  `gender` ENUM('Female', 'Male', 'Calf') NOT NULL DEFAULT 'Female',
  `health_status` ENUM('Healthy', 'Under Treatment', 'Rescued - Recovering', 'Special Care') DEFAULT 'Healthy',
  `adoption_status` ENUM('Available', 'Adopted', 'Pending') DEFAULT 'Available',
  `monthly_adoption_fee` DECIMAL(10,2) NOT NULL DEFAULT 1500.00,
  `bio` TEXT DEFAULT NULL,
  `main_image` VARCHAR(255) DEFAULT 'images/cows/cow-default.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cows` (`id`, `tag_number`, `name`, `breed`, `age_years`, `gender`, `health_status`, `adoption_status`, `monthly_adoption_fee`, `bio`, `main_image`) VALUES
(1, 'KG-0101', 'Kamadhenu (Gauri)', 'Gir', 5, 'Female', 'Healthy', 'Available', 2100.00, 'Gauri is a gentle Gir cow with iconic curved horns and a loving nature. She yields pure A2 milk and loves to be petted.', 'images/cows/cow1.jpg'),
(2, 'KG-0102', 'Nandi', 'Sahiwal', 4, 'Male', 'Healthy', 'Available', 1800.00, 'Nandi is a majestic Sahiwal bull rescued from urban stray hazards. He is strong, calm, and leads the herd with grace.', 'images/cows/cow2.jpg'),
(3, 'KG-0103', 'Surabhi', 'Tharparkar', 3, 'Female', 'Healthy', 'Adopted', 1500.00, 'Surabhi is a resilient white Tharparkar cow known for her high adaptability and peaceful eyes. She is currently supported by a loving donor.', 'images/cows/cow3.jpg'),
(4, 'KG-0104', 'Gopala', 'Rathi', 2, 'Male', 'Rescued - Recovering', 'Available', 1500.00, 'Gopala was rescued from a road accident and has been nursed back to vigor by our veterinary team.', 'images/cows/cow4.jpg'),
(5, 'KG-0105', 'Radha', 'Kankrej', 6, 'Female', 'Healthy', 'Available', 2500.00, 'Radha is a stunning Kankrej cow with grand crescent horns and an affectionate personality.', 'images/cows/cow5.jpg'),
(6, 'KG-0106', 'Little Krishna', 'Gir Calf', 1, 'Calf', 'Healthy', 'Available', 1100.00, 'A joyful 8-month-old calf who loves running across the green pastures of Kamadhenu Goushala.', 'images/cows/cow6.jpg');

-- --------------------------------------------------------
-- Table: cow_images
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cow_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cow_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cow_id`) REFERENCES `cows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: seva
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `seva` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `category` ENUM('Daily', 'Medical', 'Feeding', 'Shelter', 'Special') DEFAULT 'Daily',
  `suggested_amount` DECIMAL(10,2) NOT NULL DEFAULT 500.00,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'images/seva/seva-default.jpg',
  `is_featured` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `seva` (`id`, `title`, `subtitle`, `category`, `suggested_amount`, `description`, `image`, `is_featured`) VALUES
(1, 'Gau Grass & Fodder Seva', 'Provide fresh green fodder, jaggery, and nutrition', 'Feeding', 501.00, 'Provide nutritious green grass, dry fodder, jaggery, and mineral mixture for one cow for a week.', 'images/seva/seva1.jpg', 1),
(2, 'Medical Treatment & Healthcare', 'Emergency surgeries, medicine & doctor checkups', 'Medical', 1501.00, 'Support critical veterinary medicine, surgeries, bandages, and daily health checkups for sick & injured rescued cows.', 'images/seva/seva2.jpg', 1),
(3, 'Nitya Gau Seva (Daily Care)', 'Sponsor daily care, bath, and shed hygiene', 'Daily', 251.00, 'Sponsor daily grooming, clean water supply, shed cleaning, and love for our sacred cows.', 'images/seva/seva3.jpg', 1),
(4, 'Goushala Shelter Construction', 'Build clean, airy, rain-proof cow sheds', 'Shelter', 5001.00, 'Help construct durable solar-roofed shelters, water troughs, and padded resting areas for senior cows.', 'images/seva/seva4.jpg', 1);

-- --------------------------------------------------------
-- Table: product_categories
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'A2 Ghee & Dairy', 'a2-ghee-dairy', 'Pure Bilona A2 Desi Cow Ghee and traditional dairy items'),
(2, 'Ayurvedic & Wellness', 'ayurvedic-wellness', 'Panchagavya wellness products, soaps, and oils'),
(3, 'Pooja & Spiritual', 'pooja-spiritual', 'Natural Sambrani, organic Dhoop sticks, and cow dung diyas'),
(4, 'Organic Farming', 'organic-farming', 'Vedic Vermicompost, Jeevamrut, and natural fertilizers');

-- --------------------------------------------------------
-- Table: products
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `sale_price` DECIMAL(10,2) DEFAULT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 50,
  `image` VARCHAR(255) DEFAULT 'images/products/product-default.jpg',
  `is_featured` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock_quantity`, `image`, `is_featured`) VALUES
(1, 1, 'Pure A2 Vedic Gir Cow Ghee (Bilona Method 500ml)', 'pure-a2-vedic-gir-cow-ghee-500ml', 'Handcrafted using traditional Vedic Bilona method from free-grazing Gir cows. Rich golden aroma and high medicinal value.', 1499.00, 1299.00, 45, 'images/products/ghee.jpg', 1),
(2, 2, 'Panchagavya Herbal Bathing Soap (Pack of 3)', 'panchagavya-herbal-bathing-soap', 'Infused with cow ghee, milk, curd, neem, and camphor. 100% natural, chemical-free skin nourishment.', 299.00, 249.00, 120, 'images/products/soap.jpg', 1),
(3, 3, 'Organic Gau Maya Dhoop Batti (100g Stick Pack)', 'organic-gau-maya-dhoop-batti', 'Pure cow dung and aromatic herbs like Guggal, Havan Samagri, and Camphor. Purification and spiritual serenity.', 199.00, 149.00, 85, 'images/products/dhoop.jpg', 1),
(4, 4, 'Enriched Bio-Organic Vermicompost (5kg Bag)', 'enriched-bio-organic-vermicompost-5kg', 'Nutrient-rich earthworm-processed cow dung manure for home gardens and organic farming.', 399.00, 349.00, 60, 'images/products/compost.jpg', 1),
(5, 3, 'Handmade Eco Cow Dung Diyas (Pack of 12)', 'handmade-eco-cow-dung-diyas', 'Biodegradable, sacred cow-dung lamps for puja, festivals, and home rituals.', 180.00, 150.00, 100, 'images/products/diya.jpg', 0),
(6, 2, 'Herbal Panchagavya Hair Oil (200ml)', 'herbal-panchagavya-hair-oil-200ml', 'Enriched with A2 ghee, bhringraj, and amla for deep root nourishment.', 450.00, 399.00, 40, 'images/products/hair-oil.jpg', 0);

-- --------------------------------------------------------
-- Table: product_images
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: cart & cart_items
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `session_id` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cart_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: orders & order_items
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `city` VARCHAR(50) NOT NULL,
  `state` VARCHAR(50) NOT NULL,
  `pincode` VARCHAR(10) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Online Simulation',
  `payment_status` ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Paid',
  `order_status` ENUM('Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Confirmed',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT DEFAULT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: payments
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reference_type` ENUM('order', 'donation', 'adoption') NOT NULL,
  `reference_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'Online Simulation',
  `transaction_id` VARCHAR(100) NOT NULL UNIQUE,
  `status` ENUM('Success', 'Pending', 'Failed') DEFAULT 'Success',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: donations
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `donations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `donation_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `donor_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `purpose` VARCHAR(100) DEFAULT 'General Gau Seva',
  `message` TEXT DEFAULT NULL,
  `payment_status` ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Completed',
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: adoptions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `adoptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `adoption_number` VARCHAR(50) NOT NULL UNIQUE,
  `cow_id` INT NOT NULL,
  `user_id` INT DEFAULT NULL,
  `adopter_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `duration_months` INT NOT NULL DEFAULT 1,
  `monthly_amount` DECIMAL(10,2) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `message` TEXT DEFAULT NULL,
  `payment_status` ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Completed',
  `status` ENUM('Active', 'Expired', 'Cancelled') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cow_id`) REFERENCES `cows` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: gallery_categories & gallery
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `slug` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gallery_categories` (`id`, `name`, `slug`) VALUES
(1, 'All', 'all'),
(2, 'Cows', 'cows'),
(3, 'Goushala', 'goushala'),
(4, 'Seva', 'seva'),
(5, 'Events', 'events'),
(6, 'Visitors', 'visitors');

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gallery` (`id`, `category_id`, `title`, `image_path`, `description`) VALUES
(1, 2, 'Gir Cows Grazing in Morning Pasture', 'images/gallery/gallery1.jpg', 'Our indigenous cows enjoying early morning green pastures under solar sheds.'),
(2, 3, 'Clean & Airy Goushala Sheds', 'images/gallery/gallery2.jpg', 'Spacious shelter equipped with fresh water, rubber mats, and natural ventilation.'),
(3, 4, 'Volunteers Serving Fresh Jaggery & Fodder', 'images/gallery/gallery3.jpg', 'Devotees offering fresh green fodder and organic jaggery to Gau Mata.'),
(4, 5, 'Gopashtami Mahotsav Celebration', 'images/gallery/gallery4.jpg', 'Annual Gopashtami festival with Vedic chants, cow pooja, and community feast.'),
(5, 6, 'School Children Learning Cow Protection', 'images/gallery/gallery5.jpg', 'Young visitors connecting with gentle calves during an educational tour.'),
(6, 2, 'Mother Cow & Newborn Calf', 'images/gallery/gallery6.jpg', 'A peaceful moment between mother Gir cow and her healthy newborn calf.');

-- --------------------------------------------------------
-- Table: testimonials
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `author_name` VARCHAR(100) NOT NULL,
  `role_location` VARCHAR(100) DEFAULT 'Devotee & Donor',
  `message` TEXT NOT NULL,
  `rating` INT NOT NULL DEFAULT 5,
  `avatar` VARCHAR(255) DEFAULT 'images/testimonials/user1.jpg',
  `is_approved` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `testimonials` (`id`, `author_name`, `role_location`, `message`, `rating`) VALUES
(1, 'Ramesh Sharma', 'Jaipur, Rajasthan', 'Visiting Kamadhenu Goushala was a deeply spiritual experience. The cows are taken care of like family members, and the A2 Ghee is 100% authentic!', 5),
(2, 'Priya Kulkarni', 'Mumbai, Maharashtra', 'I adopted Gauri for 1 year. Getting monthly health updates and video clips of her grazing gives immense bliss. Har Har Mahadev!', 5),
(3, 'Dr. Anand Verma', 'New Delhi', 'The veterinary medical care facilities here are state of the art. Proud to support such a dedicated team of Gau Sevaks.', 5);

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: site_settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Kamadhenu Goushala'),
('site_tagline', 'Love, Care & Seva for Gau Mata'),
('total_cows_count', '450'),
('rescued_cows_count', '310'),
('volunteers_count', '120'),
('years_of_service', '15'),
('contact_email', 'info@kamadhenugoushala.org'),
('contact_phone', '+91 98765 43210'),
('contact_address', 'Kamadhenu Dham, Vrindavan Highway, Mathura, Uttar Pradesh - 281001');

-- --------------------------------------------------------
-- Table: events
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `event_date` DATE NOT NULL,
  `event_time` VARCHAR(50) NOT NULL DEFAULT '09:00 AM - 01:00 PM',
  `location` VARCHAR(200) NOT NULL DEFAULT 'Kamadhenu Dham, Vrindavan',
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'images/events/event-default.jpg',
  `is_featured` TINYINT(1) DEFAULT 1,
  `status` ENUM('Upcoming', 'Ongoing', 'Completed') DEFAULT 'Upcoming',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `events` (`id`, `title`, `slug`, `event_date`, `event_time`, `location`, `description`, `image`, `is_featured`, `status`) VALUES
(1, 'Grand Gopashtami Mahotsav & Cow Pooja', 'gopashtami-mahotsav-cow-pooja', '2026-11-18', '08:00 AM - 02:00 PM', 'Kamadhenu Dham, Vrindavan', 'Join us for Vedic Gopashtami Mahotsav featuring 108 cow pooja rituals, special jaggery & fodder feast, cultural bhajans, and prasadam.', 'images/events/event1.jpg', 1, 'Upcoming'),
(2, 'Free Emergency Veterinary Medical Camp', 'free-emergency-veterinary-medical-camp', '2026-09-10', '09:00 AM - 05:00 PM', 'Sanctuary Medical ICU, Mathura', 'Free health checkup camp for stray and rural cows. Expert doctors providing vaccinations, wound dressing, and medicine kits.', 'images/events/event2.jpg', 1, 'Upcoming'),
(3, 'Kartik Purnima 1008 Cow Dung Diya Deepotsav', 'kartik-purnima-1008-cow-dung-diya-deepotsav', '2026-11-24', '05:30 PM - 09:00 PM', 'Sanctuary Open Pastures, Vrindavan', 'Lighting 1008 eco-friendly organic cow dung diyas on the auspicious eve of Kartik Purnima accompanied by evening Aarti.', 'images/events/event3.jpg', 1, 'Upcoming');

