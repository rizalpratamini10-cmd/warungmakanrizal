<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$user_id = $_SESSION['customer_id'] ?? $_SESSION['kurir_id'] ?? $_SESSION['admin_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => true, 'data' => [], 'count' => 0]);
    exit();
}

$notif = $koneksi->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 20");
$notifications = [];

while ($n = $notif->fetch_assoc()) {
    $notifications[] = [
        'id' => $n['id'],
        'title' => $n['title'],
        'message' => $n['message'],
        'type' => $n['type'],
        'is_read' => (bool)$n['is_read'],
        'created_at' => $n['created_at']
    ];
}

echo json_encode(['success' => true, 'data' => $notifications, 'count' => count($notifications)]);
?>