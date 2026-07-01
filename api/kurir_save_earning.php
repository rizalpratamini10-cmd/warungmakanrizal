<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$kurir_id = intval($data['kurir_id']);
$order_id = intval($data['order_id']);
$jarak_km = floatval($data['jarak_km']);
$upah = intval($data['upah']);

$query = "INSERT INTO kurir_earnings (kurir_id, order_id, jarak_km, upah) VALUES ($kurir_id, $order_id, $jarak_km, $upah)";

if ($koneksi->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Pendapatan disimpan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan']);
}
?>