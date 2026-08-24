<?php
// includes/functions.php - Global Utility Functions & Helpers

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!ob_get_level()) {
    ob_start();
}

/**
 * Sanitize string input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Render CSRF Field
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Flash Notification System
 */
function set_flash($type, $message) {
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (!empty($_SESSION['flash_messages'])) {
        $messages = $_SESSION['flash_messages'];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
    return [];
}

/**
 * Format Indian Currency (INR)
 */
function format_currency($amount) {
    return '₹' . number_format((float)$amount, 2);
}

/**
 * Get Site Setting from Database
 */
function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $res = $stmt->fetchColumn();
        return $res !== false ? $res : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Convert any standard YouTube link to its embed version
 */
function get_youtube_embed_url($url) {
    if (strpos($url, 'youtube.com/embed/') !== false) {
        return $url;
    }
    $videoId = '';
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|[^/]+/*\?v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
        $videoId = $match[1];
    }
    if ($videoId) {
        return "https://www.youtube.com/embed/" . $videoId;
    }
    return "https://www.youtube.com/embed/pRsrn9THN8Q";
}


/**
 * Secure File Upload Handler
 */
function upload_file($file, $targetDir = 'uploads/', $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid parameters.'];
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'No file sent.'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'Exceeded filesize limit (Max 5MB).'];
        default:
            return ['success' => false, 'error' => 'Unknown upload error.'];
    }

    if ($file['size'] > 5242880) { // 5MB limit
        return ['success' => false, 'error' => 'File size exceeds 5MB limit.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime, $allowedMimes)) {
        return ['success' => false, 'error' => 'Invalid file format. Only JPG, PNG and WEBP allowed.'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = sprintf('%s_%s.%s', sha1_file($file['tmp_name']), uniqid(), strtolower($ext));
    
    $fullTargetDir = __DIR__ . '/../' . trim($targetDir, '/') . '/';
    if (!is_dir($fullTargetDir)) {
        mkdir($fullTargetDir, 0755, true);
    }

    $destination = $fullTargetDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file.'];
    }

    return ['success' => true, 'filepath' => trim($targetDir, '/') . '/' . $filename];
}

/**
 * Session ID for Cart Management
 */
function get_session_id() {
    if (empty($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = session_id() ?: bin2hex(random_bytes(16));
    }
    return $_SESSION['cart_session_id'];
}

/**
 * Get Cart Record ID
 */
function get_or_create_cart_id() {
    global $pdo;
    $sessionId = get_session_id();
    $userId = $_SESSION['user_id'] ?? null;

    $stmt = $pdo->prepare("SELECT id, user_id FROM cart WHERE session_id = ? OR (user_id IS NOT NULL AND user_id = ?)");
    $stmt->execute([$sessionId, $userId]);
    $cart = $stmt->fetch();

    if ($cart) {
        // Update user_id if newly logged in
        if ($userId && empty($cart['user_id'])) {
            $update = $pdo->prepare("UPDATE cart SET user_id = ? WHERE id = ?");
            $update->execute([$userId, $cart['id']]);
        }
        return $cart['id'];
    }

    $insert = $pdo->prepare("INSERT INTO cart (user_id, session_id) VALUES (?, ?)");
    $insert->execute([$userId, $sessionId]);
    return $pdo->lastInsertId();
}

/**
 * Get Cart Items Count
 */
function get_cart_count() {
    global $pdo;
    $cartId = get_or_create_cart_id();
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cartId]);
    return (int)($stmt->fetchColumn() ?: 0);
}

/**
 * Get Detailed Cart Items
 */
function get_cart_details() {
    global $pdo;
    $cartId = get_or_create_cart_id();
    $stmt = $pdo->prepare("
        SELECT ci.id AS item_id, ci.quantity, p.id AS product_id, p.name, p.price, p.sale_price, p.image, p.stock_quantity, p.slug
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_id = ?
        ORDER BY ci.created_at DESC
    ");
    $stmt->execute([$cartId]);
    $items = $stmt->fetchAll();

    $subtotal = 0;
    foreach ($items as &$item) {
        $effectivePrice = ($item['sale_price'] > 0 && $item['sale_price'] < $item['price']) ? $item['sale_price'] : $item['price'];
        $item['unit_price'] = $effectivePrice;
        $item['total_price'] = $effectivePrice * $item['quantity'];
        $subtotal += $item['total_price'];
    }

    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'tax' => 0.00, // Exempt/Inclusive for non-profit products
        'total' => $subtotal
    ];
}

/**
 * Get All Configured Admin WhatsApp Helplines Directory
 */
function get_whatsapp_numbers_list() {
    $list = [];
    $contact = get_setting('contact_phone', '+91 98765 43210');
    
    for ($i = 1; $i <= 4; $i++) {
        $p = get_setting('whatsapp_phone_' . $i, ($i === 1 ? $contact : ''));
        if (!empty($p)) {
            $list['phone_' . $i] = [
                'label' => 'WhatsApp Number ' . $i . ' (' . $p . ')',
                'number' => $p
            ];
        }
    }
    return $list;
}

/**
 * Get Clean Sanitized WhatsApp Contact Number (Supports Per-Item Overrides)
 */
function get_whatsapp_phone($item = null) {
    $rawPhone = '';
    // 1. Per-item custom chosen number
    if (is_array($item) && !empty($item['whatsapp_number'])) {
        $rawPhone = $item['whatsapp_number'];
    }
    
    // 2. Active Primary Default WhatsApp setting number
    if (empty($rawPhone)) {
        $activeKey = get_setting('active_default_whatsapp', 'phone_1');
        $rawPhone = get_setting('whatsapp_' . $activeKey, '');
    }

    // 3. Fallback to general helpline setting or default
    if (empty($rawPhone)) {
        $rawPhone = get_setting('whatsapp_phone', get_setting('contact_phone', '919876543210'));
    }

    $phone = preg_replace('/[^0-9]/', '', $rawPhone);
    if (strlen($phone) === 10) {
        $phone = '91' . $phone;
    }
    if (empty($phone)) {
        $phone = '919876543210';
    }
    return $phone;
}

/**
 * Generate Direct WhatsApp Link for Cow Adoption
 */
function get_whatsapp_cow_url($cow) {
    $phone = get_whatsapp_phone($cow);

    $name = is_array($cow) ? ($cow['name'] ?? 'Gau Mata') : (string)$cow;
    $breed = is_array($cow) ? ($cow['breed'] ?? '') : '';
    $fee = is_array($cow) ? ($cow['monthly_adoption_fee'] ?? '') : '';

    $msg = "Jai Shree Krishna! 🙏 I am interested in adopting Cow: " . $name;
    if ($breed) $msg .= " Breed: " . $breed;
    if ($fee) $msg .= " (Monthly Fee: ₹" . number_format($fee, 2) . ")";
    $msg .= ". Please provide adoption details and guide me on how to proceed with Gau Seva.";

    return "https://wa.me/" . $phone . "?text=" . urlencode($msg);
}

/**
 * Generate Crisp SVG WhatsApp Logo Icon
 */
function get_whatsapp_icon_svg($size = '1.15em') {
    return '<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="' . $size . '" width="' . $size . '" xmlns="http://www.w3.org/2000/svg" style="vertical-align: -0.18em; margin-right: 5px; display: inline-block;"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3 18.6-68.1-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path></svg>';
}

/**
 * Get Section Checkout Mode ('website', 'whatsapp', or 'both')
 */
function get_checkout_mode($section = 'cow') {
    $defaults = [
        'cow' => 'both',
        'product' => 'both',
        'donation' => 'both'
    ];
    $default = $defaults[$section] ?? 'both';
    return get_setting($section . '_checkout_mode', $default);
}

/**
 * Resolve Item-level Checkout Mode ('website', 'whatsapp', or 'both')
 * Honors item-specific checkout_mode override if set to 'website', 'whatsapp', or 'both',
 * otherwise falls back to the global section setting.
 */
function get_item_checkout_mode($item, $section = 'cow') {
    $itemMode = is_array($item) ? ($item['checkout_mode'] ?? 'default') : 'default';
    if ($itemMode === 'whatsapp' || $itemMode === 'website' || $itemMode === 'both') {
        return $itemMode;
    }
    return get_checkout_mode($section);
}

/**
 * Get Cow Action URL based on Item & Global Admin Checkout Mode setting
 */
function get_cow_action_url($cow) {
    if (get_item_checkout_mode($cow, 'cow') === 'whatsapp') {
        return get_whatsapp_cow_url($cow);
    }
    $cowId = is_array($cow) ? ($cow['id'] ?? 0) : (int)$cow;
    return get_base_url() . 'pages/adopt.php?cow_id=' . $cowId;
}

/**
 * Generate Direct WhatsApp Link for Product Order
 */
function get_whatsapp_product_url($product = null) {
    $phone = get_whatsapp_phone($product);

    $name = is_array($product) ? ($product['name'] ?? 'Organic Product') : (string)$product;
    $price = is_array($product) ? ($product['sale_price'] ?: $product['price'] ?? 0) : 0;

    $msg = "Jai Shree Krishna! 🛍️ I want to order organic product: " . $name;
    if ($price > 0) $msg .= " (Price: ₹" . number_format($price, 2) . ")";
    $msg .= ". Please confirm availability and delivery details.";

    return "https://wa.me/" . $phone . "?text=" . urlencode($msg);
}

/**
 * Get Product Action URL based on Item & Global Admin Checkout Mode setting
 */
function get_product_action_url($product) {
    if (get_item_checkout_mode($product, 'product') === 'whatsapp') {
        return get_whatsapp_product_url($product);
    }
    $prodId = is_array($product) ? ($product['id'] ?? 0) : (int)$product;
    return get_base_url() . 'pages/product-details.php?id=' . $prodId;
}

/**
 * Generate Direct WhatsApp Link for Donation
 */
function get_whatsapp_donation_url($purpose = 'General Gau Seva', $seva = null) {
    $phone = get_whatsapp_phone($seva);

    $msg = "Jai Shree Krishna! 💖 I wish to offer a donation for " . $purpose . " at Kamadhenu Goushala. Please share payment and 80G receipt details.";
    return "https://wa.me/" . $phone . "?text=" . urlencode($msg);
}

/**
 * Get Seva / Donation Action URL based on Item & Global Admin Checkout Mode setting
 */
function get_seva_action_url($seva) {
    if (get_item_checkout_mode($seva, 'donation') === 'whatsapp') {
        $title = is_array($seva) ? ($seva['title'] ?? 'Seva Program') : (string)$seva;
        return get_whatsapp_donation_url($title, $seva);
    }
    $sevaId = is_array($seva) ? ($seva['id'] ?? 0) : (int)$seva;
    return get_base_url() . 'pages/seva-details.php?id=' . $sevaId;
}

/**
 * Get Donation Action URL based on Admin Checkout Mode setting
 */
function get_donation_action_url($purpose = 'General Gau Seva') {
    if (get_checkout_mode('donation') === 'whatsapp') {
        return get_whatsapp_donation_url($purpose);
    }
    return get_base_url() . 'pages/donate.php';
}




