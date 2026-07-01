<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id']);
$kurir_id = intval($data['kurir_id']);

$update = $koneksi->query("UPDATE orders SET status = 'selesai', updated_at = NOW() WHERE id = $order_id AND kurir_id = $kurir_id");

if ($update) {
    $order = $koneksi->query("SELECT customer_id FROM orders WHERE id = $order_id")->fetch_assoc();
    send_notification($order['customer_id'], "Pesanan Selesai", "Pesanan Anda telah selesai. Terima kasih!", 'delivery');
    echo json_encode(['success' => true, 'message' => 'Pesanan selesai']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyelesaikan pesanan']);
}
?>