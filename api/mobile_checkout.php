<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$customer_id = 1; // dari token
$alamat = mysqli_real_escape_string($koneksi, $data['alamat']);
$payment_method = mysqli_real_escape_string($koneksi, $data['payment_method']);
$catatan = mysqli_real_escape_string($koneksi, $data['catatan'] ?? '');

// Ambil cart
$cart = $koneksi->query("SELECT c.*, p.harga FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id = $customer_id");
$total = 0;
$items = [];
while ($item = $cart->fetch_assoc()) {
    $subtotal = $item['quantity'] * $item['harga'];
    $total += $subtotal;
    $items[] = $item;
}

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Keranjang kosong']);
    exit();
}

$order_number = 'ORD' . date('ymd') . rand(1000, 9999);
$query = "INSERT INTO orders (order_number, customer_id, total_harga, alamat_pengiriman, payment_method, catatan) 
          VALUES ('$order_number', $customer_id, $total, '$alamat', '$payment_method', '$catatan')";

if ($koneksi->query($query)) {
    $order_id = $koneksi->insert_id;
    foreach ($items as $item) {
        $subtotal = $item['quantity'] * $item['harga'];
        $koneksi->query("INSERT INTO order_details (order_id, product_id, quantity, harga_per_item, subtotal) 
                         VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['harga']}, $subtotal)");
    }
    $koneksi->query("DELETE FROM cart WHERE customer_id = $customer_id");
    
    echo json_encode(['success' => true, 'order_id' => $order_id, 'order_number' => $order_number]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal checkout']);
}
?>