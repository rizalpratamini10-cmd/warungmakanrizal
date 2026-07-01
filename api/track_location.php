<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = $_POST;
$kurir_id = intval($data['kurir_id']);
$latitude = floatval($data['latitude']);
$longitude = floatval($data['longitude']);

$query = "INSERT INTO kurir_locations (kurir_id, latitude, longitude, last_update, is_online) 
          VALUES ($kurir_id, $latitude, $longitude, NOW(), 1)
          ON DUPLICATE KEY UPDATE latitude = $latitude, longitude = $longitude, last_update = NOW(), is_online = 1";

if ($koneksi->query($query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>