-- ============================================================
-- VPS Schema Fix - Run each block one at a time in phpMyAdmin SQL tab
-- If a line says "Duplicate column name" or "Table already exists", skip it and continue
-- ============================================================

-- ----------------------------------------------------------
-- STEP 1: Fix product_colors.name (make it nullable)
-- ----------------------------------------------------------
ALTER TABLE product_colors MODIFY name VARCHAR(255) NULL DEFAULT NULL;

-- ----------------------------------------------------------
-- STEP 2: Add color_name if missing
-- If error "Duplicate column name", skip this line
-- ----------------------------------------------------------
ALTER TABLE product_colors ADD COLUMN color_name VARCHAR(255) NULL AFTER product_id;

-- ----------------------------------------------------------
-- STEP 3: Add color_hex if missing
-- If error "Duplicate column name", skip this line
-- ----------------------------------------------------------
ALTER TABLE product_colors ADD COLUMN color_hex VARCHAR(10) NULL AFTER color_name;

-- ----------------------------------------------------------
-- STEP 4: Create product_dimension_prices if missing
-- If error "Table already exists", skip this block
-- ----------------------------------------------------------
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

-- ----------------------------------------------------------
-- STEP 5: Create product_filter_attributes if missing
-- ----------------------------------------------------------
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

-- ----------------------------------------------------------
-- STEP 6: Create product_filter_values if missing
-- ----------------------------------------------------------
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

-- ----------------------------------------------------------
-- STEP 7: Create pivot table if missing
-- ----------------------------------------------------------
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

-- ----------------------------------------------------------
-- STEP 7b: Add sku to products if missing
-- If error "Duplicate column name", skip this line
-- ----------------------------------------------------------
ALTER TABLE products ADD COLUMN sku VARCHAR(255) NULL UNIQUE AFTER slug;

-- ----------------------------------------------------------
-- STEP 8: Seed default filter attributes (safe - uses INSERT IGNORE)
-- ----------------------------------------------------------
INSERT IGNORE INTO product_filter_attributes (name, display_name, type, is_active, sort_order) VALUES
('color', 'Color', 'select', 1, 1),
('material', 'Material', 'select', 1, 2),
('construction', 'Construction', 'select', 1, 3),
('pattern', 'Pattern', 'select', 1, 4),
('weave', 'Weave', 'select', 1, 5);

-- ----------------------------------------------------------
-- STEP 9: Seed sample filter values (safe - uses INSERT IGNORE)
-- ----------------------------------------------------------
-- These will only insert if the attribute IDs match; run this AFTER Step 8
INSERT IGNORE INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT id, 'wool', 'Wool', 1 FROM product_filter_attributes WHERE name = 'material';

INSERT IGNORE INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT id, 'silk', 'Silk', 2 FROM product_filter_attributes WHERE name = 'material';

INSERT IGNORE INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT id, 'cotton', 'Cotton', 3 FROM product_filter_attributes WHERE name = 'material';

INSERT IGNORE INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT id, 'hand-knotted', 'Hand Knotted', 1 FROM product_filter_attributes WHERE name = 'construction';

INSERT IGNORE INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT id, 'hand-tufted', 'Hand Tufted', 2 FROM product_filter_attributes WHERE name = 'construction';

INSERT IGNORE INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT id, 'traditional', 'Traditional', 1 FROM product_filter_attributes WHERE name = 'pattern';

INSERT IGNORE INTO product_filter_values (product_filter_attribute_id, value, display_value, sort_order)
SELECT id, 'modern', 'Modern', 2 FROM product_filter_attributes WHERE name = 'pattern';
