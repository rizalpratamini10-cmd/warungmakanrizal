<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$kurir_id = isset($_GET['kurir_id']) ? intval($_GET['kurir_id']) : 0;

$total = $koneksi->query("SELECT SUM(upah) as total FROM kurir_earnings WHERE kurir_id = $kurir_id")->fetch_assoc()['total'] ?? 0;
$today = $koneksi->query("SELECT SUM(upah) as today FROM kurir_earnings WHERE kurir_id = $kurir_id AND DATE(created_at) = CURDATE()")->fetch_assoc()['today'] ?? 0;

$history = $koneksi->query("SELECT e.*, o.order_number FROM kurir_earnings e JOIN orders o ON e.order_id = o.id WHERE e.kurir_id = $kurir_id ORDER BY e.created_at DESC LIMIT 20");

$list = [];
while ($h = $history->fetch_assoc()) {
    $list[] = [
        'id' => $h['id'],
        'order_number' => $h['order_number'],
        'jarak_km' => (float)$h['jarak_km'],
        'upah' => (int)$h['upah'],
        'status' => $h['status'],
        'created_at' => $h['created_at']
    ];
}

echo json_encode([
    'success' => true,
    'total' => (int)$total,
    'today' => (int)$today,
    'total_formatted' => "Rp " . number_format($total, 0, ',', '.'),
    'today_formatted' => "Rp " . number_format($today, 0, ',', '.'),
    'history' => $list
]);
?>