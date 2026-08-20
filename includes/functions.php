<?php
// includes/functions.php - Global Utility Functions & Helpers

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
