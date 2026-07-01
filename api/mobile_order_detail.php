<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$order = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, u.alamat, u.no_hp 
                          FROM orders o 
                          JOIN users u ON o.customer_id = u.id 
                          WHERE o.id = $order_id")->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
    exit();
}

$items = $koneksi->query("SELECT od.*, p.nama_produk 
                          FROM order_details od 
                          JOIN products p ON od.product_id = p.id 
                          WHERE od.order_id = $order_id");

$order_items = [];
while ($item = $items->fetch_assoc()) {
    $order_items[] = [
        'id' => $item['id'],
        'product_id' => $item['product_id'],
        'product_name' => $item['nama_produk'],
        'quantity' => (int)$item['quantity'],
        'harga_per_item' => (int)$item['harga_per_item'],
        'subtotal' => (int)$item['subtotal']
    ];
}

// Ambil tracking jika ada
$tracking = null;
if ($order['kurir_id']) {
    $loc = $koneksi->query("SELECT latitude, longitude, last_update FROM kurir_locations WHERE kurir_id = {$order['kurir_id']} ORDER BY last_update DESC LIMIT 1")->fetch_assoc();
    $kurir = $koneksi->query("SELECT nama_lengkap, no_hp FROM users WHERE id = {$order['kurir_id']}")->fetch_assoc();
    $tracking = [
        'kurir_name' => $kurir['nama_lengkap'] ?? null,
        'kurir_phone' => $kurir['no_hp'] ?? null,
        'latitude' => $loc['latitude'] ?? null,
        'longitude' => $loc['longitude'] ?? null,
        'last_update' => $loc['last_update'] ?? null
    ];
}

echo json_encode([
    'success' => true,
    'order' => [
        'id' => $order['id'],
        'order_number' => $order['order_number'],
        'customer_name' => $order['customer_name'],
        'alamat' => $order['alamat'],
        'customer_phone' => $order['no_hp'],
        'total_harga' => (int)$order['total_harga'],
        'status' => $order['status'],
        'payment_method' => $order['payment_method'],
        'catatan' => $order['catatan'],
        'created_at' => $order['created_at']
    ],
    'items' => $order_items,
    'tracking' => $tracking
]);
?>