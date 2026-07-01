<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$kurir_id = isset($_GET['kurir_id']) ? intval($_GET['kurir_id']) : 0;

$history = $koneksi->query("SELECT o.id, o.order_number, o.total_harga, o.updated_at, 
                                   u.nama_lengkap as customer_name, e.jarak_km, e.upah
                            FROM orders o 
                            JOIN users u ON o.customer_id = u.id 
                            LEFT JOIN kurir_earnings e ON o.id = e.order_id
                            WHERE o.kurir_id = $kurir_id AND o.status = 'selesai'
                            ORDER BY o.updated_at DESC");

$list = [];
while ($h = $history->fetch_assoc()) {
    $list[] = [
        'id' => $h['id'],
        'order_number' => $h['order_number'],
        'customer_name' => $h['customer_name'],
        'total_harga' => (int)$h['total_harga'],
        'jarak_km' => (float)($h['jarak_km'] ?? 0),
        'upah' => (int)($h['upah'] ?? 0),
        'completed_at' => $h['updated_at']
    ];
}

$total_earning = $koneksi->query("SELECT SUM(upah) as total FROM kurir_earnings WHERE kurir_id = $kurir_id")->fetch_assoc()['total'] ?? 0;

echo json_encode([
    'success' => true,
    'history' => $list,
    'total_earning' => (int)$total_earning,
    'total_earning_formatted' => "Rp " . number_format($total_earning, 0, ',', '.'),
    'total_deliveries' => count($list)
]);
?>