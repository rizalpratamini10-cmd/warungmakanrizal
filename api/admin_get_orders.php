<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$status = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT o.*, u.nama_lengkap as customer_name, u.no_hp as customer_phone, 
          k.nama_lengkap as kurir_name
          FROM orders o 
          JOIN users u ON o.customer_id = u.id 
          LEFT JOIN users k ON o.kurir_id = k.id";

if ($status) {
    $query .= " WHERE o.status = '$status'";
}

$query .= " ORDER BY o.created_at DESC";

$result = $koneksi->query($query);
$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'id' => $row['id'],
        'order_number' => $row['order_number'],
        'customer_name' => $row['customer_name'],
        'customer_phone' => $row['customer_phone'],
        'total_harga' => (int)$row['total_harga'],
        'status' => $row['status'],
        'payment_method' => $row['payment_method'],
        'kurir_name' => $row['kurir_name'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'orders' => $orders]);
?>