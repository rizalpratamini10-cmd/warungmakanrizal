<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => true, 'count' => 0]);
    exit();
}

$customer_id = $_SESSION['customer_id'];
$result = $koneksi->query("SELECT SUM(quantity) as total FROM cart WHERE customer_id = $customer_id");
$data = $result->fetch_assoc();

echo json_encode(['success' => true, 'count' => (int)($data['total'] ?? 0)]);
?>