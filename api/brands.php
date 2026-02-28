<?php
// ========================================
// API: Brands JSON Endpoint
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

// Get all brands
$stmt = $pdo->query("SELECT id, name, description FROM brands ORDER BY name ASC");
$brands = $stmt->fetchAll();

// Ensure proper types
foreach ($brands as &$b) {
    $b['id'] = (int) $b['id'];
}

echo json_encode($brands, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
