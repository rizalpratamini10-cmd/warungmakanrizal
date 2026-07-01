<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id']);
$kurir_id = intval($data['kurir_id']);

$update = $koneksi->query("UPDATE orders SET kurir_id = $kurir_id, status = 'dikirim', updated_at = NOW() WHERE id = $order_id AND status = 'diproses'");

if ($update) {
    $order = $koneksi->query("SELECT customer_id FROM orders WHERE id = $order_id")->fetch_assoc();
    send_notification($order['customer_id'], "Pesanan Dikirim", "Kurir sedang menuju ke lokasi Anda", 'delivery');
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil diambil']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil pesanan']);
}
?>