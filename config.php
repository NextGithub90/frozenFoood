<?php
// ========================================
// CONFIG: Nada Agen Sosis
// ========================================
// PANDUAN cPanel Rumahweb:
// 1. Buat database di cPanel → MySQL Databases
// 2. Buat user database di cPanel → MySQL Databases
// 3. Assign user ke database (ALL PRIVILEGES)
// 4. Isi kredensial di bawah sesuai yang dibuat di cPanel
// ========================================

session_start();

// ===== DATABASE CONFIG =====
// Format cPanel: namaakuncpanel_namadb
// Contoh: nadafood_frozen_db
// GANTI NILAI DI BAWAH SESUAI cPANEL ANDA:
define('DB_HOST', 'localhost');
define('DB_NAME', 'movh6621_agen');
define('DB_USER', 'movh6621_agen');
define('DB_PASS', 'agen123#');

// ===== APP CONFIG =====
// Jika website di domain utama (public_html), gunakan '/'
// Saat ini: agen.movaindonesia.com (langsung di root domain)
define('BASE_URL', '/');
define('UPLOAD_DIR', __DIR__ . '/uploads/products/');
define('UPLOAD_URL', 'uploads/products/');

// ===== PDO CONNECTION =====
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal. Pastikan kredensial di config.php sudah benar.");
}

// Helper: Sanitize output
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper: Redirect
function redirect($url)
{
    header("Location: $url");
    exit;
}

// Helper: Flash message
function setFlash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
