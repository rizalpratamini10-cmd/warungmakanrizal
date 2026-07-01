<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$quantity = $data['quantity'] ?? 1;

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Cek apakah produk sudah ada di keranjang
$check = $koneksi->query("SELECT id, quantity FROM cart WHERE customer_id = $customer_id AND product_id = $product_id");

if ($check->num_rows > 0) {
    $cart = $check->fetch_assoc();
    $new_qty = $cart['quantity'] + $quantity;
    $koneksi->query("UPDATE cart SET quantity = $new_qty WHERE id = {$cart['id']}");
    $message = "Jumlah produk diperbarui";
} else {
    $koneksi->query("INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, $quantity)");
    $message = "Produk ditambahkan ke keranjang";
}

echo json_encode(['success' => true, 'message' => $message]);
?>