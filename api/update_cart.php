<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$cart_id = intval($data['cart_id']);
$quantity = intval($data['quantity']);

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit();
}

if ($quantity <= 0) {
    $koneksi->query("DELETE FROM cart WHERE id = $cart_id");
    echo json_encode(['success' => true, 'message' => 'Item dihapus dari keranjang']);
} else {
    $query = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id";
    if ($koneksi->query($query)) {
        echo json_encode(['success' => true, 'message' => 'Jumlah berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui jumlah']);
    }
}
?>