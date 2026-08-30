<?php
/**
 * Standalone VPS Schema Fix - No Laravel dependencies
 * Upload to public_html/ and visit: https://costikyan.mindspire.org/fix_db.php
 * Deletes itself after running.
 */

// ---- CONFIGURE YOUR DATABASE CREDENTIALS ----
// Get these from Hostinger hPanel → Databases → MySQL
$host = '127.0.0.1';
$db   = 'u714104226_rug';
$user = 'u714104226_rug';
$pass = ''; // PUT YOUR PASSWORD HERE before uploading
// ---------------------------------------------

header('Content-Type: text/plain');

if (empty($pass)) {
    die("ERROR: Please edit this file and set \$pass to your database password.\n");
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage() . "\n");
}

echo "=== VPS SCHEMA FIX ===\n\n";
$fixes = [];
$errors = [];

// Helper function
function columnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT 1 FROM information_schema.COLUMNS WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() !== false;
}

function tableExists($pdo, $table) {
    $stmt = $pdo->prepare("SELECT 1 FROM information_schema.TABLES WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() !== false;
}

// 1. Fix product_colors.name (make nullable)
try {
    if (columnExists($pdo, 'product_colors', 'name')) {
        $pdo->exec("ALTER TABLE product_colors MODIFY name VARCHAR(255) NULL DEFAULT NULL");
        $fixes[] = "product_colors.name is now nullable";
    } else {
        $fixes[] = "product_colors.name does not exist (OK)";
    }
} catch (PDOException $e) {
    $errors[] = "Step 1: " . $e->getMessage();
}

// 2. Add color_name if missing
try {
    if (!columnExists($pdo, 'product_colors', 'color_name')) {
        $pdo->exec("ALTER TABLE product_colors ADD COLUMN color_name VARCHAR(255) NULL AFTER product_id");
        $fixes[] = "Added product_colors.color_name";
    } else {
        $fixes[] = "product_colors.color_name already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Step 2: " . $e->getMessage();
}

// 3. Add color_hex if missing
try {
    if (!columnExists($pdo, 'product_colors', 'color_hex')) {
        $pdo->exec("ALTER TABLE product_colors ADD COLUMN color_hex VARCHAR(10) NULL AFTER color_name");
        $fixes[] = "Added product_colors.color_hex";
    } else {
        $fixes[] = "product_colors.color_hex already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Step 3: " . $e->getMessage();
}

// 3b. Add sku to products if missing
try {
    if (!columnExists($pdo, 'products', 'sku')) {
        $pdo->exec("ALTER TABLE products ADD COLUMN sku VARCHAR(255) NULL UNIQUE AFTER slug");
        $fixes[] = "Added products.sku";
    } else {
        $fixes[] = "products.sku already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Step 3b: " . $e->getMessage();
}

// 4. Create product_dimension_prices
try {
    if (!tableExists($pdo, 'product_dimension_prices')) {
        $pdo->exec("CREATE TABLE product_dimension_prices (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id BIGINT UNSIGNED NOT NULL,
            label VARCHAR(255) NULL,
            width DECIMAL(10,2) NULL,
            length DECIMAL(10,2) NULL,
            shape VARCHAR(50) DEFAULT 'rectangular',
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            sale_price DECIMAL(10,2) NULL,
            stock INT DEFAULT 0,
            is_default TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $fixes[] = "Created product_dimension_prices table";
    } else {
        $fixes[] = "product_dimension_prices already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Step 4: " . $e->getMessage();
}

// 5. Create product_filter_attributes
try {
    if (!tableExists($pdo, 'product_filter_attributes')) {
        $pdo->exec("CREATE TABLE product_filter_attributes (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            display_name VARCHAR(255) NULL,
            type VARCHAR(50) DEFAULT 'select',
            is_active TINYINT(1) DEFAULT 1,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $fixes[] = "Created product_filter_attributes table";
    } else {
        $fixes[] = "product_filter_attributes already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Step 5: " . $e->getMessage();
}

// 6. Create product_filter_values
try {
    if (!tableExists($pdo, 'product_filter_values')) {
        $pdo->exec("CREATE TABLE product_filter_values (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_filter_attribute_id BIGINT UNSIGNED NOT NULL,
            value VARCHAR(255) NOT NULL,
            display_value VARCHAR(255) NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (product_filter_attribute_id) REFERENCES product_filter_attributes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $fixes[] = "Created product_filter_values table";
    } else {
        $fixes[] = "product_filter_values already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Step 6: " . $e->getMessage();
}

// 7. Create pivot table
try {
    if (!tableExists($pdo, 'product_filter_value_product')) {
        $pdo->exec("CREATE TABLE product_filter_value_product (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id BIGINT UNSIGNED NOT NULL,
            product_filter_value_id BIGINT UNSIGNED NOT NULL,
            product_filter_attribute_id BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (product_filter_value_id) REFERENCES product_filter_values(id) ON DELETE CASCADE,
            FOREIGN KEY (product_filter_attribute_id) REFERENCES product_filter_attributes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $fixes[] = "Created product_filter_value_product pivot table";
    } else {
        $fixes[] = "product_filter_value_product already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Step 7: " . $e->getMessage();
}

// 8. Seed default filter attributes
$attrs = [
    ['color', 'Color', 'select', 1, 1],
    ['material', 'Material', 'select', 1, 2],
    ['construction', 'Construction', 'select', 1, 3],
    ['pattern', 'Pattern', 'select', 1, 4],
    ['weave', 'Weave', 'select', 1, 5],
];
foreach ($attrs as $a) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM product_filter_attributes WHERE name = ?");
        $stmt->execute([$a[0]]);
        if (!$stmt->fetch()) {
            $ins = $pdo->prepare("INSERT INTO product_filter_attributes (name, display_name, type, is_active, sort_order) VALUES (?,?,?,?,?)");
            $ins->execute($a);
            $fixes[] = "Inserted attribute: {$a[0]}";
        }
    } catch (PDOException $e) {
        $errors[] = "Attribute {$a[0]}: " . $e->getMessage();
    }
}

// 9. Seed sample filter values
$values = [
    ['material', 'wool', 'Wool', 1],
    ['material', 'silk', 'Silk', 2],
    ['material', 'cotton', 'Cotton', 3],
    ['construction', 'hand-knotted', 'Hand Knotted', 1],
    ['construction', 'hand-tufted', 'Hand Tufted', 2],
    ['pattern', 'traditional', 'Traditional', 1],
    ['pattern', 'modern', 'Modern', 2],
];
foreach ($values as $v) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM product_filter_attributes WHERE name = ?");
        $stmt->execute([$v[0]]);
        $attrId = $stmt->fetchColumn();
        if ($attrId) {
            $stmt2 = $pdo->prepare("SELECT 1 FROM product_filter_values WHERE product_filter_attribute_id = ? AND value = ?");
            $stmt2->execute([$attrId, $v[1]]);
            if (!$stmt2->fetch()) {
                $ins = $pdo->prepare("INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order) VALUES (?,?,?,?)");
                $ins->execute([$attrId, $v[1], $v[2], $v[3]]);
                $fixes[] = "Inserted value: {$v[1]} ({$v[0]})";
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Value {$v[1]}: " . $e->getMessage();
    }
}

// Results
echo "FIXES APPLIED (" . count($fixes) . "):\n";
foreach ($fixes as $f) {
    echo "  [OK] $f\n";
}

if (!empty($errors)) {
    echo "\nERRORS (" . count($errors) . "):\n";
    foreach ($errors as $e) {
        echo "  [FAIL] $e\n";
    }
}

echo "\n=== DONE ===\n";

// Self-delete
@unlink(__FILE__);
