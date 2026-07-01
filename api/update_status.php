<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = $_POST;
$order_id = intval($data['order_id']);
$status = mysqli_real_escape_string($koneksi, $data['status']);

$update = $koneksi->query("UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = $order_id");

if ($update) {
    $order = $koneksi->query("SELECT customer_id FROM orders WHERE id = $order_id")->fetch_assoc();
    send_notification($order['customer_id'], "Status Pesanan Diperbarui", "Pesanan Anda: " . ucfirst($status), 'order');
    echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
}
?>