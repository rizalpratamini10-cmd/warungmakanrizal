<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$products = $koneksi->query("SELECT p.*, c.nama_kategori 
                             FROM products p 
                             LEFT JOIN categories c ON p.kategori_id = c.id 
                             ORDER BY p.id DESC");

$list = [];
while ($p = $products->fetch_assoc()) {
    $list[] = [
        'id' => $p['id'],
        'nama_produk' => $p['nama_produk'],
        'kategori_id' => $p['kategori_id'],
        'nama_kategori' => $p['nama_kategori'],
        'deskripsi' => $p['deskripsi'],
        'harga' => (int)$p['harga'],
        'stok' => (int)$p['stok'],
        'gambar' => $p['gambar'],
        'is_available' => (bool)$p['is_available']
    ];
}

echo json_encode(['success' => true, 'products' => $list]);
?>