<?php
// ========================================
// CONFIG: Nada Agen Sosis
// ========================================
// Otomatis support: XAMPP (lokal) + Rumahweb (hosting)
// Tidak perlu ubah apapun, tinggal upload!
// ========================================

session_start();

// ===== ENVIRONMENT DETECTION =====
$httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = (
    $httpHost === 'localhost' ||
    $httpHost === '127.0.0.1' ||
    strpos($httpHost, 'localhost:') === 0 ||
    strpos($httpHost, '127.0.0.1:') === 0
);

// ===== DATABASE CONFIG =====
if ($isLocal) {
    // LOCAL (XAMPP / PHP built-in server)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'nada_frozen_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // HOSTING (Hostinger)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u251231747_agen');
    define('DB_USER', 'u251231747_agen');
    define('DB_PASS', 'Agen12345#');
}

// ===== APP CONFIG =====
if ($isLocal) {
    // Auto-detect BASE_URL untuk XAMPP (subdirectory di htdocs)
    // Contoh: jika folder di htdocs/frozenFoood/, BASE_URL = '/frozenFoood/'
    // Jika pakai php -S localhost:8080, BASE_URL = '/'
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // Cari root project (folder yang berisi config.php)
    $configDir = str_replace('\\', '/', __DIR__);
    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    if ($docRoot && strpos($configDir, $docRoot) === 0) {
        $basePath = substr($configDir, strlen($docRoot));
        define('BASE_URL', rtrim($basePath, '/') . '/');
    } else {
        define('BASE_URL', '/');
    }
} else {
    // Hosting: langsung di root domain
    define('BASE_URL', '/');
}

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
