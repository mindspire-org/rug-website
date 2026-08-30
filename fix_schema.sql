-- ============================================================
-- COSTIKYAN RUG WEBSITE - MySQL Schema Fix Script
-- Run this in phpMyAdmin SQL tab or: mysql -u USER -p DBNAME < fix_schema.sql
-- ============================================================

-- 1. Fix product_colors: make old 'name' column nullable (or drop it)
-- This fixes: General error: 1364 Field 'name' doesn't have a default value
SET @dbname = DATABASE();
SET @tbl = 'product_colors';
SET @col = 'name';

SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE table_schema = @dbname
  AND table_name = @tbl
  AND column_name = @col;

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE product_colors MODIFY name VARCHAR(255) NULL DEFAULT NULL;',
    'SELECT "Column name already fixed or does not exist" AS message;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Ensure color_name and color_hex exist in product_colors
SET @col2 = 'color_name';
SELECT COUNT(*) INTO @col2_exists
FROM information_schema.COLUMNS
WHERE table_schema = @dbname
  AND table_name = @tbl
  AND column_name = @col2;

SET @sql2 = IF(@col2_exists = 0,
    'ALTER TABLE product_colors ADD COLUMN color_name VARCHAR(255) NULL AFTER product_id;',
    'SELECT "color_name already exists" AS message;'
);
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

SET @col3 = 'color_hex';
SELECT COUNT(*) INTO @col3_exists
FROM information_schema.COLUMNS
WHERE table_schema = @dbname
  AND table_name = @tbl
  AND column_name = @col3;

SET @sql3 = IF(@col3_exists = 0,
    'ALTER TABLE product_colors ADD COLUMN color_hex VARCHAR(10) NULL AFTER color_name;',
    'SELECT "color_hex already exists" AS message;'
);
PREPARE stmt3 FROM @sql3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- 3. Create product_dimension_prices if missing
CREATE TABLE IF NOT EXISTS product_dimension_prices (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create product_filter_attributes if missing
CREATE TABLE IF NOT EXISTS product_filter_attributes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) NULL,
    type VARCHAR(50) DEFAULT 'select',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create product_filter_values if missing
CREATE TABLE IF NOT EXISTS product_filter_values (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_filter_attribute_id BIGINT UNSIGNED NOT NULL,
    value VARCHAR(255) NOT NULL,
    display_value VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_filter_attribute_id) REFERENCES product_filter_attributes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create pivot table product_filter_value_product if missing
CREATE TABLE IF NOT EXISTS product_filter_value_product (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    product_filter_value_id BIGINT UNSIGNED NOT NULL,
    product_filter_attribute_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (product_filter_value_id) REFERENCES product_filter_values(id) ON DELETE CASCADE,
    FOREIGN KEY (product_filter_attribute_id) REFERENCES product_filter_attributes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Insert default filter attributes (if table is empty)
INSERT INTO product_filter_attributes (name, display_name, type, is_active, sort_order)
SELECT * FROM (SELECT 'color', 'Color', 'select', 1, 1) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM product_filter_attributes WHERE name = 'color') LIMIT 1;

INSERT INTO product_filter_attributes (name, display_name, type, is_active, sort_order)
SELECT * FROM (SELECT 'material', 'Material', 'select', 1, 2) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM product_filter_attributes WHERE name = 'material') LIMIT 1;

INSERT INTO product_filter_attributes (name, display_name, type, is_active, sort_order)
SELECT * FROM (SELECT 'construction', 'Construction', 'select', 1, 3) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM product_filter_attributes WHERE name = 'construction') LIMIT 1;

INSERT INTO product_filter_attributes (name, display_name, type, is_active, sort_order)
SELECT * FROM (SELECT 'pattern', 'Pattern', 'select', 1, 4) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM product_filter_attributes WHERE name = 'pattern') LIMIT 1;

INSERT INTO product_filter_attributes (name, display_name, type, is_active, sort_order)
SELECT * FROM (SELECT 'weave', 'Weave', 'select', 1, 5) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM product_filter_attributes WHERE name = 'weave') LIMIT 1;

-- 8. Seed some common filter values
INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT a.id, 'wool', 'Wool', 1 FROM product_filter_attributes a WHERE a.name = 'material'
AND NOT EXISTS (SELECT 1 FROM product_filter_values v WHERE v.value = 'wool' AND v.product_filter_attribute_id = a.id);

INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT a.id, 'silk', 'Silk', 2 FROM product_filter_attributes a WHERE a.name = 'material'
AND NOT EXISTS (SELECT 1 FROM product_filter_values v WHERE v.value = 'silk' AND v.product_filter_attribute_id = a.id);

INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT a.id, 'cotton', 'Cotton', 3 FROM product_filter_attributes a WHERE a.name = 'material'
AND NOT EXISTS (SELECT 1 FROM product_filter_values v WHERE v.value = 'cotton' AND v.product_filter_attribute_id = a.id);

INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT a.id, 'hand-knotted', 'Hand Knotted', 1 FROM product_filter_attributes a WHERE a.name = 'construction'
AND NOT EXISTS (SELECT 1 FROM product_filter_values v WHERE v.value = 'hand-knotted' AND v.product_filter_attribute_id = a.id);

INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT a.id, 'hand-tufted', 'Hand Tufted', 2 FROM product_filter_attributes a WHERE a.name = 'construction'
AND NOT EXISTS (SELECT 1 FROM product_filter_values v WHERE v.value = 'hand-tufted' AND v.product_filter_attribute_id = a.id);

INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT a.id, 'traditional', 'Traditional', 1 FROM product_filter_attributes a WHERE a.name = 'pattern'
AND NOT EXISTS (SELECT 1 FROM product_filter_values v WHERE v.value = 'traditional' AND v.product_filter_attribute_id = a.id);

INSERT INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT a.id, 'modern', 'Modern', 2 FROM product_filter_attributes a WHERE a.name = 'pattern'
AND NOT EXISTS (SELECT 1 FROM product_filter_values v WHERE v.value = 'modern' AND v.product_filter_attribute_id = a.id);

-- 9. Verify: show current product_colors columns
SELECT column_name, data_type, is_nullable, column_default
FROM information_schema.COLUMNS
WHERE table_schema = @dbname AND table_name = 'product_colors'
ORDER BY ordinal_position;

SELECT 'Schema fix complete!' AS status;
