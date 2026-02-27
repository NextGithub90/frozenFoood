<?php
// ========================================
// API: Products JSON Endpoint
// ========================================

// CORS headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Cache-Control: no-cache, must-revalidate');

// Connect to DB
try {
    require_once __DIR__ . '/../config.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get all products with category names
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.name,
        c.name as category,
        p.brand,
        p.description as `desc`,
        p.weight,
        p.storage,
        p.price,
        p.img,
        p.halal,
        p.badge,
        p.is_new as isNew,
        p.is_best_seller as isBestSeller
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id ASC
");

$products = $stmt->fetchAll();

// Ensure boolean-like fields are proper types
foreach ($products as &$p) {
    $p['id'] = (int) $p['id'];
    $p['halal'] = (bool) $p['halal'];
    $p['isNew'] = (bool) $p['isNew'];
    $p['isBestSeller'] = (bool) $p['isBestSeller'];
}

echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
