<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
// Parse token untuk dapat customer_id (sederhana, bisa pakai JWT nanti)

$customer_id = 1; // Sementara, ganti dengan logic token

$cart = $koneksi->query("SELECT c.*, p.nama_produk, p.harga 
                         FROM cart c 
                         JOIN products p ON c.product_id = p.id 
                         WHERE c.customer_id = $customer_id");

$items = [];
$total = 0;
while ($item = $cart->fetch_assoc()) {
    $subtotal = $item['quantity'] * $item['harga'];
    $total += $subtotal;
    $items[] = [
        'id' => $item['id'],
        'product_id' => $item['product_id'],
        'product_name' => $item['nama_produk'],
        'quantity' => (int)$item['quantity'],
        'harga' => (int)$item['harga'],
        'subtotal' => $subtotal
    ];
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'total_items' => count($items),
    'total_harga' => $total
]);
?>