<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$orders = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name 
                           FROM orders o 
                           JOIN users u ON o.customer_id = u.id 
                           ORDER BY o.created_at DESC LIMIT 20");

$list = [];
while ($o = $orders->fetch_assoc()) {
    $list[] = [
        'id' => $o['id'],
        'order_number' => $o['order_number'],
        'customer_name' => $o['customer_name'],
        'total_harga' => (int)$o['total_harga'],
        'status' => $o['status'],
        'created_at' => $o['created_at']
    ];
}

echo json_encode(['success' => true, 'orders' => $list]);
?>