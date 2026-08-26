<?php
// includes/lang.php - Custom Language / i18n System (EN, HI, KN)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle language switching via GET parameter or Cookie/Session
if (isset($_GET['lang'])) {
    $requestedLang = strtolower(trim($_GET['lang']));
    if (in_array($requestedLang, ['en', 'hi', 'kn'])) {
        $_SESSION['lang'] = $requestedLang;
        setcookie('user_lang', $requestedLang, time() + (86400 * 365), '/');
    }
} elseif (empty($_SESSION['lang'])) {
    if (isset($_COOKIE['user_lang']) && in_array($_COOKIE['user_lang'], ['en', 'hi', 'kn'])) {
        $_SESSION['lang'] = $_COOKIE['user_lang'];
    } else {
        $_SESSION['lang'] = 'en';
    }
}

/**
 * Get current active language code
 */
function get_current_lang() {
    return $_SESSION['lang'] ?? 'en';
}

/**
 * Load Modular Language Files from /language directory
 */
$langDir = __DIR__ . '/../language';

$translations = [
    'en' => file_exists("$langDir/english.php") ? require("$langDir/english.php") : [],
    'hi' => file_exists("$langDir/hindi.php") ? require("$langDir/hindi.php") : [],
    'kn' => file_exists("$langDir/kannada.php") ? require("$langDir/kannada.php") : [],
];

/**
 * Get translated text helper function with HTML entity decoding fallback
 */
function __($key, $default = '') {
    global $translations;
    $lang = get_current_lang();
    
    // Normalize key and default by decoding any HTML entities (&amp;, &amp;amp;, etc.)
    $cleanKey = html_entity_decode(html_entity_decode((string)$key, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    $cleanDefault = $default !== '' ? html_entity_decode(html_entity_decode((string)$default, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') : '';

    // If English, return default if provided, else key/cleanKey
    if ($lang === 'en') {
        if ($default !== '') return $default;
        if (isset($translations['en'][$cleanKey])) return $translations['en'][$cleanKey];
        if (isset($translations['en'][$key])) return $translations['en'][$key];
        return $cleanKey;
    }

    // Direct dictionary key lookup
    if (isset($translations[$lang][$cleanKey])) {
        return $translations[$lang][$cleanKey];
    }
    if (isset($translations[$lang][$key])) {
        return $translations[$lang][$key];
    }
    
    // Lookup by default text if key was not found
    if ($cleanDefault !== '' && isset($translations[$lang][$cleanDefault])) {
        return $translations[$lang][$cleanDefault];
    }
    if ($default !== '' && isset($translations[$lang][$default])) {
        return $translations[$lang][$default];
    }

    // Lowercase key lookup fallback
    $lowerKey = strtolower(trim($cleanKey));
    if (isset($translations[$lang][$lowerKey])) {
        return $translations[$lang][$lowerKey];
    }

    return $default !== '' ? $default : $key;
}
