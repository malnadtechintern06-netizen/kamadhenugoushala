<?php
require_once __DIR__ . '/../includes/db.php';

try {
    // Add whatsapp_number column to cows table
    $pdo->exec("ALTER TABLE cows ADD COLUMN whatsapp_number VARCHAR(30) NULL DEFAULT NULL AFTER checkout_mode");
    echo "Added whatsapp_number to cows table.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "whatsapp_number already exists in cows table.<br>";
    } else {
        echo "Cows error: " . $e->getMessage() . "<br>";
    }
}

try {
    // Add whatsapp_number column to products table
    $pdo->exec("ALTER TABLE products ADD COLUMN whatsapp_number VARCHAR(30) NULL DEFAULT NULL AFTER checkout_mode");
    echo "Added whatsapp_number to products table.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "whatsapp_number already exists in products table.<br>";
    } else {
        echo "Products error: " . $e->getMessage() . "<br>";
    }
}

try {
    // Add whatsapp_number column to seva table
    $pdo->exec("ALTER TABLE seva ADD COLUMN whatsapp_number VARCHAR(30) NULL DEFAULT NULL AFTER checkout_mode");
    echo "Added whatsapp_number to seva table.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "whatsapp_number already exists in seva table.<br>";
    } else {
        echo "Seva error: " . $e->getMessage() . "<br>";
    }
}

echo "Database migration complete!";
