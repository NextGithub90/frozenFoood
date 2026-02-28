-- ========================================
-- MIGRATION: Tambah fitur Brand Management
-- ========================================
-- FILE INI YANG HARUS DI-IMPORT DI PHPMYADMIN RUMAHWEB
-- (BUKAN database.sql!)
-- 
-- Cara:
-- 1. Buka phpMyAdmin di cPanel Rumahweb
-- 2. Pilih database movh6621_agen
-- 3. Klik tab "Import"
-- 4. Pilih file ini (migration_brands.sql)
-- 5. Klik "Go" / "Kirim"
-- 
-- CATATAN: Aman dijalankan ulang, tidak akan merusak data
-- ========================================

-- STEP 1: Buat tabel brands (aman, skip jika sudah ada)
CREATE TABLE IF NOT EXISTS brands (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  logo VARCHAR(255) DEFAULT '',
  description VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- STEP 2: Insert data brand (IGNORE = skip jika sudah ada)
INSERT IGNORE INTO brands (name, description) VALUES
('Kanzler', 'Brand sosis premium asal Jerman'),
('Fiesta', 'Produk frozen food populer Indonesia'),
('So Nice', 'Sosis dan olahan ayam praktis'),
('Champ', 'Frozen food untuk anak-anak'),
('Bernardi', 'Produk daging olahan premium'),
('Cedea', 'Produk seafood frozen berkualitas'),
('So Good', 'Olahan ayam dan daging terpercaya'),
('Aviko', 'Kentang goreng impor premium');

-- STEP 3: Tambah kolom brand_id ke products
-- Jika muncul error "Duplicate column name", abaikan, artinya sudah ada
ALTER TABLE products ADD COLUMN brand_id INT DEFAULT NULL AFTER category_id;

-- STEP 4: Update brand_id di produk yang sudah ada berdasarkan nama brand
UPDATE products p
  JOIN brands b ON p.brand = b.name
  SET p.brand_id = b.id
  WHERE p.brand_id IS NULL;
