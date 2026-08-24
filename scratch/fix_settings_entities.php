<?php
require_once __DIR__ . '/../includes/db.php';

$rows = $pdo->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll();

$stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");

foreach ($rows as $row) {
    $val = $row['setting_value'];
    // Recursively decode entities until stable
    while ($val !== html_entity_decode($val, ENT_QUOTES, 'UTF-8')) {
        $val = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
    }
    $stmt->execute([$val, $row['setting_key']]);
}

echo "Cleaned up all settings table entities successfully!";
