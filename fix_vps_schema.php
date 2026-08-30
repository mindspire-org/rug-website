<?php
/**
 * VPS Schema Fix Script
 * Upload to public_html/ and visit: https://costikyan.mindspire.org/fix_vps_schema.php
 * Deletes itself after running.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');
echo "=== VPS SCHEMA FIX ===\n\n";

$errors = [];
$fixes = [];

try {
    // 1. Fix product_colors.name -> make nullable
    if (Schema::hasColumn('product_colors', 'name')) {
        DB::statement('ALTER TABLE product_colors MODIFY name VARCHAR(255) NULL DEFAULT NULL');
        $fixes[] = "product_colors.name is now nullable";
    } else {
        $fixes[] = "product_colors.name column does not exist (already fixed or never existed)";
    }

    // 2. Add color_name if missing
    if (!Schema::hasColumn('product_colors', 'color_name')) {
        DB::statement('ALTER TABLE product_colors ADD COLUMN color_name VARCHAR(255) NULL AFTER product_id');
        $fixes[] = "Added product_colors.color_name";
    } else {
        $fixes[] = "product_colors.color_name already exists";
    }

    // 3. Add color_hex if missing
    if (!Schema::hasColumn('product_colors', 'color_hex')) {
        DB::statement('ALTER TABLE product_colors ADD COLUMN color_hex VARCHAR(10) NULL AFTER color_name');
        $fixes[] = "Added product_colors.color_hex";
    } else {
        $fixes[] = "product_colors.color_hex already exists";
    }

    // 4. Create product_dimension_prices
    if (!Schema::hasTable('product_dimension_prices')) {
        DB::statement("CREATE TABLE product_dimension_prices (
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

    // 5. Create product_filter_attributes
    if (!Schema::hasTable('product_filter_attributes')) {
        DB::statement("CREATE TABLE product_filter_attributes (
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

    // 6. Create product_filter_values
    if (!Schema::hasTable('product_filter_values')) {
        DB::statement("CREATE TABLE product_filter_values (
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

    // 7. Create pivot table
    if (!Schema::hasTable('product_filter_value_product')) {
        DB::statement("CREATE TABLE product_filter_value_product (
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

    // 8. Seed default filter attributes
    $defaultAttrs = [
        ['color', 'Color', 'select', 1, 1],
        ['material', 'Material', 'select', 1, 2],
        ['construction', 'Construction', 'select', 1, 3],
        ['pattern', 'Pattern', 'select', 1, 4],
        ['weave', 'Weave', 'select', 1, 5],
    ];
    foreach ($defaultAttrs as $attr) {
        $exists = DB::table('product_filter_attributes')->where('name', $attr[0])->exists();
        if (!$exists) {
            DB::table('product_filter_attributes')->insert([
                'name' => $attr[0],
                'display_name' => $attr[1],
                'type' => $attr[2],
                'is_active' => $attr[3],
                'sort_order' => $attr[4],
            ]);
            $fixes[] = "Inserted filter attribute: {$attr[0]}";
        }
    }

    // 9. Seed sample filter values
    $sampleValues = [
        ['material', 'wool', 'Wool', 1],
        ['material', 'silk', 'Silk', 2],
        ['material', 'cotton', 'Cotton', 3],
        ['construction', 'hand-knotted', 'Hand Knotted', 1],
        ['construction', 'hand-tufted', 'Hand Tufted', 2],
        ['pattern', 'traditional', 'Traditional', 1],
        ['pattern', 'modern', 'Modern', 2],
    ];
    foreach ($sampleValues as $val) {
        $attr = DB::table('product_filter_attributes')->where('name', $val[0])->first();
        if ($attr) {
            $exists = DB::table('product_filter_values')
                ->where('product_filter_attribute_id', $attr->id)
                ->where('value', $val[1])
                ->exists();
            if (!$exists) {
                DB::table('product_filter_values')->insert([
                    'product_filter_attribute_id' => $attr->id,
                    'value' => $val[1],
                    'display_value' => $val[2],
                    'sort_order' => $val[3],
                ]);
                $fixes[] = "Inserted filter value: {$val[1]} ({$val[0]})";
            }
        }
    }

} catch (\Throwable $e) {
    $errors[] = $e->getMessage();
}

// Output results
echo "FIXES APPLIED:\n";
foreach ($fixes as $f) {
    echo "  [OK] $f\n";
}

if (!empty($errors)) {
    echo "\nERRORS:\n";
    foreach ($errors as $e) {
        echo "  [FAIL] $e\n";
    }
}

echo "\n=== DONE ===\n";

// Self-delete
@unlink(__FILE__);
