<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$origin_lat = floatval($data['origin_lat']);
$origin_lng = floatval($data['origin_lng']);
$dest_lat = floatval($data['dest_lat']);
$dest_lng = floatval($data['dest_lng']);

// Fungsi Haversine
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    return round($earthRadius * $c, 2);
}

$jarak_km = haversineDistance($origin_lat, $origin_lng, $dest_lat, $dest_lng);

// Hitung tarif
if ($jarak_km <= 1) $tarif = 5000;
elseif ($jarak_km <= 2) $tarif = 7000;
elseif ($jarak_km <= 3) $tarif = 10000;
elseif ($jarak_km <= 4) $tarif = 12000;
elseif ($jarak_km <= 5) $tarif = 15000;
elseif ($jarak_km <= 7) $tarif = 20000;
elseif ($jarak_km <= 10) $tarif = 25000;
else $tarif = round($jarak_km * 5000);

echo json_encode([
    'success' => true,
    'jarak_km' => $jarak_km,
    'jarak_text' => $jarak_km . ' km',
    'tarif' => $tarif,
    'tarif_formatted' => "Rp " . number_format($tarif, 0, ',', '.')
]);
?>