<?php
// ========================================
// API: Categories JSON Endpoint
// ========================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Cache-Control: no-cache, must-revalidate');

try {
    require_once __DIR__ . '/../config.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$stmt = $pdo->query("
    SELECT 
        c.id,
        c.name,
        c.slug,
        c.icon,
        c.description,
        (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
    FROM categories c 
    ORDER BY c.id ASC
");

$categories = $stmt->fetchAll();

foreach ($categories as &$c) {
    $c['id'] = (int) $c['id'];
    $c['product_count'] = (int) $c['product_count'];
}

echo json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
