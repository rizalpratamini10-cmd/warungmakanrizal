<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$total_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pending_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];
$total_revenue = $koneksi->query("SELECT SUM(total_harga) as total FROM orders WHERE status = 'selesai'")->fetch_assoc()['total'] ?? 0;
$total_customers = $koneksi->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'")->fetch_assoc()['total'];
$total_products = $koneksi->query("SELECT COUNT(*) as total FROM products WHERE is_available = 1")->fetch_assoc()['total'];
$online_kurir = $koneksi->query("SELECT COUNT(DISTINCT kurir_id) as online FROM kurir_locations WHERE is_online = 1 AND TIMESTAMPDIFF(MINUTE, last_update, NOW()) < 5")->fetch_assoc()['online'] ?? 0;

echo json_encode([
    'success' => true,
    'stats' => [
        'total_orders' => (int)$total_orders,
        'pending_orders' => (int)$pending_orders,
        'total_revenue' => (int)$total_revenue,
        'total_customers' => (int)$total_customers,
        'total_products' => (int)$total_products,
        'online_kurir' => (int)$online_kurir
    ]
]);
?>