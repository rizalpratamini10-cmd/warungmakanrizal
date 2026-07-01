<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$jarak_km = floatval($data['jarak_km']);

// Cek di database
$query = "SELECT tarif FROM kurir_tarif WHERE jarak_min_km <= $jarak_km AND jarak_max_km > $jarak_km LIMIT 1";
$result = $koneksi->query($query);

if ($result && $result->num_rows > 0) {
    $tarif = $result->fetch_assoc()['tarif'];
} else {
    if ($jarak_km <= 1) $tarif = 5000;
    elseif ($jarak_km <= 2) $tarif = 7000;
    elseif ($jarak_km <= 3) $tarif = 10000;
    elseif ($jarak_km <= 4) $tarif = 12000;
    elseif ($jarak_km <= 5) $tarif = 15000;
    elseif ($jarak_km <= 7) $tarif = 20000;
    elseif ($jarak_km <= 10) $tarif = 25000;
    else $tarif = round($jarak_km * 5000);
}

echo json_encode([
    'success' => true,
    'jarak_km' => $jarak_km,
    'tarif' => $tarif,
    'tarif_formatted' => "Rp " . number_format($tarif, 0, ',', '.')
]);
?>