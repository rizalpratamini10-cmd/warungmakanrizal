<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

// Ambil pesanan baru (last 5 minutes)
$last_check = isset($_GET['last_check']) ? $_GET['last_check'] : date('Y-m-d H:i:s', strtotime('-5 minutes'));

$orders = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name 
                           FROM orders o 
                           JOIN users u ON o.customer_id = u.id 
                           WHERE o.created_at > '$last_check' 
                           ORDER BY o.created_at DESC");

$new_orders = [];
while ($o = $orders->fetch_assoc()) {
    $new_orders[] = [
        'id' => $o['id'],
        'order_number' => $o['order_number'],
        'customer_name' => $o['customer_name'],
        'total_harga' => (int)$o['total_harga'],
        'status' => $o['status'],
        'created_at' => $o['created_at']
    ];
}

echo json_encode([
    'success' => true,
    'new_orders' => $new_orders,
    'count' => count($new_orders),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>