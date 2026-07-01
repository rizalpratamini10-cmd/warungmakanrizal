<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$cart_id = intval($data['cart_id']);

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit();
}

$customer_id = $_SESSION['customer_id'];

$query = "DELETE FROM cart WHERE id = $cart_id AND customer_id = $customer_id";

if ($koneksi->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Item berhasil dihapus dari keranjang']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus item']);
}
?>