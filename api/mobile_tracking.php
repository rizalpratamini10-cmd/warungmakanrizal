<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$order = $koneksi->query("SELECT * FROM orders WHERE id = $order_id")->fetch_assoc();

$kurir_location = null;
if ($order['kurir_id']) {
    $loc = $koneksi->query("SELECT latitude, longitude, last_update FROM kurir_locations WHERE kurir_id = {$order['kurir_id']} ORDER BY last_update DESC LIMIT 1")->fetch_assoc();
    if ($loc) {
        $kurir_location = $loc;
    }
}

$kurir = null;
if ($order['kurir_id']) {
    $kur = $koneksi->query("SELECT nama_lengkap, no_hp FROM users WHERE id = {$order['kurir_id']}")->fetch_assoc();
    $kurir = $kur;
}

echo json_encode([
    'success' => true,
    'order_number' => $order['order_number'],
    'status' => $order['status'],
    'kurir_location' => $kurir_location,
    'kurir_name' => $kurir['nama_lengkap'] ?? null,
    'kurir_phone' => $kurir['no_hp'] ?? null
]);
?>