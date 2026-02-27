-- ========================================
-- DATABASE: Nada Agen Sosis Admin Panel
-- ========================================
-- PANDUAN IMPORT DI cPANEL RUMAHWEB:
-- 1. Buat database di cPanel → MySQL Databases (contoh: nadafood_frozen_db)
-- 2. Buat user dan assign ke database (ALL PRIVILEGES)
-- 3. Buka phpMyAdmin dari cPanel
-- 4. Pilih database yang sudah dibuat
-- 5. Klik tab "Import" → pilih file ini → klik "Go"
-- CATATAN: JANGAN jalankan CREATE DATABASE di cPanel, database sudah dibuat manual!
-- ========================================

-- ========== CATEGORIES ==========
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  icon VARCHAR(10) DEFAULT '',
  description VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (name, slug, icon, description) VALUES
('Sosis', 'sosis', '🌭', 'Beragam varian sosis premium'),
('Nugget', 'nugget', '🍗', 'Nugget ayam renyah & lezat'),
('Bakso', 'bakso', '🥩', 'Bakso daging segar & kenyal'),
('Frozen Lainnya', 'lainnya', '❄️', 'Lumpia, dimsum, & lainnya');

-- ========== PRODUCTS ==========
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category_id INT NOT NULL,
  brand VARCHAR(100) NOT NULL DEFAULT '',
  description TEXT,
  weight VARCHAR(50) DEFAULT '',
  storage VARCHAR(100) DEFAULT 'Disimpan beku (-18°C)',
  price VARCHAR(50) DEFAULT '',
  img VARCHAR(255) DEFAULT 'img/product-default.png',
  halal TINYINT(1) DEFAULT 1,
  badge VARCHAR(50) DEFAULT '',
  is_new TINYINT(1) DEFAULT 0,
  is_best_seller TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========== SEED: EXISTING PRODUCTS ==========
INSERT INTO products (name, category_id, brand, description, weight, storage, price, img, halal, badge, is_new, is_best_seller) VALUES
-- Sosis (category_id = 1)
('Sosis Kanzler Bratwurst 500g', 1, 'Kanzler', 'Sosis bratwurst premium dari Kanzler dengan cita rasa autentik Jerman, cocok untuk BBQ dan masakan sehari-hari.', '500g', 'Disimpan beku (-18°C)', 'Rp 65.000', 'img/product-sosis.png', 1, 'Laris', 0, 1),
('Sosis Fiesta 1kg', 1, 'Fiesta', 'Sosis ayam Fiesta ukuran ekonomis, ideal untuk keluarga dan usaha kuliner.', '1kg (isi ±15 pcs)', 'Disimpan beku (-18°C)', 'Rp 55.000', 'img/product-sosis.png', 1, 'Laris', 0, 1),
('Sosis So Nice 700g', 1, 'So Nice', 'Sosis ayam So Nice yang digemari anak-anak, praktis dimasak dan bergizi.', '700g', 'Disimpan beku (-18°C)', 'Rp 42.000', 'img/product-sosis.png', 1, '', 1, 0),
('Sosis Champ 500g', 1, 'Champ', 'Sosis ayam Champ rasa original, pas untuk camilan dan bekal sekolah.', '500g', 'Disimpan beku (-18°C)', 'Rp 38.000', 'img/product-sosis.png', 1, '', 0, 0),
('Sosis Kanzler Frankfurter 360g', 1, 'Kanzler', 'Sosis frankfurter classic dari Kanzler, lembut dan juicy, cocok untuk hotdog.', '360g', 'Disimpan beku (-18°C)', 'Rp 58.000', 'img/product-sosis.png', 1, '', 1, 0),
('Sosis Bernardi 1kg', 1, 'Bernardi', 'Sosis sapi premium Bernardi, tekstur lembut dan rasa gurih khas daging sapi.', '1kg', 'Disimpan beku (-18°C)', 'Rp 72.000', 'img/product-sosis.png', 1, '', 0, 0),
-- Nugget (category_id = 2)
('Nugget Fiesta 500g', 2, 'Fiesta', 'Nugget ayam Fiesta renyah di luar, lembut di dalam. Favorit keluarga Indonesia.', '500g (isi ±20 pcs)', 'Disimpan beku (-18°C)', 'Rp 48.000', 'img/product-nugget.png', 1, 'Laris', 0, 1),
('Nugget Champ 250g', 2, 'Champ', 'Nugget ayam Champ bentuk lucu, disukai anak-anak, praktis digoreng.', '250g', 'Disimpan beku (-18°C)', 'Rp 25.000', 'img/product-nugget.png', 1, '', 0, 0),
('Nugget So Good Spicy 400g', 2, 'So Good', 'Nugget ayam pedas So Good, sensasi renyah dengan bumbu pedas yang pas.', '400g', 'Disimpan beku (-18°C)', 'Rp 40.000', 'img/product-nugget.png', 1, 'Baru', 1, 0),
('Nugget Golden Fiesta 1kg', 2, 'Fiesta', 'Nugget ayam premium ukuran besar, cocok untuk usaha dan keluarga besar.', '1kg (isi ±40 pcs)', 'Disimpan beku (-18°C)', 'Rp 85.000', 'img/product-nugget.png', 1, 'Laris', 0, 1),
-- Bakso (category_id = 3)
('Bakso Sapi Bernardi 500g', 3, 'Bernardi', 'Bakso sapi asli dari Bernardi, kenyal dan gurih, siap untuk kuah atau goreng.', '500g (isi ±25 pcs)', 'Disimpan beku (-18°C)', 'Rp 52.000', 'img/product-bakso.png', 1, 'Laris', 0, 1),
('Bakso Ikan 300g', 3, 'Cedea', 'Bakso ikan premium, lembut dan bergizi, cocok untuk sup dan hotpot.', '300g', 'Disimpan beku (-18°C)', 'Rp 35.000', 'img/product-bakso.png', 1, '', 1, 0),
('Bakso Keju So Good 400g', 3, 'So Good', 'Bakso sapi isi keju leleh dari So Good, sensasi unik saat digigit.', '400g', 'Disimpan beku (-18°C)', 'Rp 45.000', 'img/product-bakso.png', 1, 'Baru', 1, 0),
-- Frozen Lainnya (category_id = 4)
('Lumpia Udang 300g', 4, 'Cedea', 'Lumpia isi udang renyah, cocok sebagai camilan atau lauk pendamping.', '300g (isi ±10 pcs)', 'Disimpan beku (-18°C)', 'Rp 38.000', 'img/product-frozen.png', 1, 'Baru', 1, 0),
('Dimsum Ayam 500g', 4, 'Fiesta', 'Dimsum ayam kukus premium, lembut dan juicy, sajikan dengan saus pedas.', '500g (isi ±15 pcs)', 'Disimpan beku (-18°C)', 'Rp 46.000', 'img/product-frozen.png', 1, '', 0, 1),
('Kentang Goreng Aviko 1kg', 4, 'Aviko', 'Kentang goreng impor premium, renyah sempurna untuk pendamping makan.', '1kg', 'Disimpan beku (-18°C)', 'Rp 62.000', 'img/product-frozen.png', 1, '', 0, 0),
('Otak-Otak Bandeng 250g', 4, 'Cedea', 'Otak-otak ikan bandeng khas, gurih dan nikmat dibakar atau dikukus.', '250g (isi ±8 pcs)', 'Disimpan beku (-18°C)', 'Rp 30.000', 'img/product-frozen.png', 1, '', 1, 0),
('Siomay Ikan 400g', 4, 'Bernardi', 'Siomay ikan lembut dan gurih, tinggal kukus dan sajikan dengan saus kacang.', '400g (isi ±20 pcs)', 'Disimpan beku (-18°C)', 'Rp 42.000', 'img/product-frozen.png', 1, '', 0, 0);
