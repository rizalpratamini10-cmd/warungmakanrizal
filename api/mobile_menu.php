<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

$query = "SELECT p.*, c.nama_kategori FROM products p LEFT JOIN categories c ON p.kategori_id = c.id WHERE p.is_available = 1";
if ($kategori > 0) {
    $query .= " AND p.kategori_id = $kategori";
}
$query .= " ORDER BY p.id DESC";

$products = $koneksi->query($query);
$list = [];

while ($p = $products->fetch_assoc()) {
    $list[] = [
        'id' => $p['id'],
        'nama_produk' => $p['nama_produk'],
        'kategori_id' => $p['kategori_id'],
        'nama_kategori' => $p['nama_kategori'],
        'deskripsi' => $p['deskripsi'],
        'harga' => (int)$p['harga'],
        'gambar' => $p['gambar']
    ];
}

echo json_encode(['success' => true, 'menu' => $list]);
?>