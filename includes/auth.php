<?php
// includes/auth.php - Session Authentication & Guard System

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && !empty($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login($customMessage = 'Please log in or create an account to continue.') {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        set_flash('error', $customMessage);
        header('Location: ' . get_base_url() . 'pages/login.php');
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        set_flash('error', 'Access denied. Administrator privileges required.');
        header('Location: ' . get_base_url() . 'admin/login.php');
        exit;
    }
}

function get_current_user_data() {
    global $pdo;
    if (!is_logged_in()) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id, role_id, full_name, email, phone, address, city, state, pincode, status, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Base URL helper to generate clean site links dynamically
 */
function get_base_url() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    // Detect folder path relative to server root
    if (strpos($scriptName, '/kamadhenugoushala/') !== false) {
        return '/kamadhenugoushala/';
    }
    return '/';
}
