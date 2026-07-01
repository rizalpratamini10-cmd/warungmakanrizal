<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$customer_id = 1; // dari token

$orders = $koneksi->query("SELECT * FROM orders WHERE customer_id = $customer_id ORDER BY created_at DESC");

$list = [];
while ($o = $orders->fetch_assoc()) {
    $list[] = [
        'id' => $o['id'],
        'order_number' => $o['order_number'],
        'total_harga' => (int)$o['total_harga'],
        'status' => $o['status'],
        'created_at' => $o['created_at']
    ];
}

echo json_encode(['success' => true, 'orders' => $list]);
?>