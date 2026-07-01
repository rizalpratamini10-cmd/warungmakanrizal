<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$orders = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, u.no_hp, u.alamat 
                           FROM orders o 
                           JOIN users u ON o.customer_id = u.id 
                           WHERE o.status = 'diproses' AND (o.kurir_id IS NULL OR o.kurir_id = 0)
                           ORDER BY o.created_at ASC");

$list = [];
while ($o = $orders->fetch_assoc()) {
    $list[] = [
        'id' => $o['id'],
        'order_number' => $o['order_number'],
        'customer_name' => $o['customer_name'],
        'customer_phone' => $o['no_hp'],
        'customer_address' => $o['alamat'],
        'total_harga' => (int)$o['total_harga'],
        'created_at' => $o['created_at']
    ];
}

echo json_encode(['success' => true, 'orders' => $list]);
?>