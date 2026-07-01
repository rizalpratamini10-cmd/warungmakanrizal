<?php
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header("Location: pesanan_saya.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Ambil data pesanan
$order = $koneksi->query("SELECT * FROM orders WHERE id = $order_id AND customer_id = $customer_id")->fetch_assoc();

if (!$order) {
    header("Location: pesanan_saya.php");
    exit();
}

// Ambil detail produk
$items = $koneksi->query("SELECT od.*, p.nama_produk 
                          FROM order_details od 
                          JOIN products p ON od.product_id = p.id 
                          WHERE od.order_id = $order_id");

// Ambil tracking kurir jika status dikirim
$kurir_info = null;
if ($order['status'] == 'dikirim' && $order['kurir_id']) {
    $kurir_info = $koneksi->query("SELECT nama_lengkap, no_hp FROM users WHERE id = {$order['kurir_id']}")->fetch_assoc();
    
    // Ambil lokasi kurir terakhir
    $lokasi = $koneksi->query("SELECT latitude, longitude, last_update FROM kurir_locations WHERE kurir_id = {$order['kurir_id']} AND is_online = 1 ORDER BY last_update DESC LIMIT 1")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body { background: #f5f5f5; }
        .sidebar {
            background: #2D1F1A;
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 5px 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: #D4AF37;
        }
        .sidebar .nav-link i { width: 25px; margin-right: 8px; }
        .navbar-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-pending { background: #ffc107; color: #856404; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-dikirim { background: #007bff; color: white; }
        .status-selesai { background: #28a745; color: white; }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -20px;
            top: 0;
            width: 2px;
            height: 100%;
            background: #e0e0e0;
        }
        .timeline-item:last-child:before {
            height: 0;
        }
        .timeline-dot {
            position: absolute;
            left: -26px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #ccc;
            border: 2px solid white;
        }
        .timeline-dot.active {
            background: #28a745;
        }
        .timeline-dot.current {
            background: #007bff;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0,123,255,0.4); }
            70% { box-shadow: 0 0 0 10px rgba(0,123,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(0,123,255,0); }
        }
        .timeline-content {
            background: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center py-4 border-bottom border-secondary">
                    <i class="fas fa-utensils fa-3x" style="color: #D4AF37;"></i>
                    <h5 class="mt-2"><?php echo $_SESSION['customer_nama']; ?></h5>
                    <small>Customer</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="menu.php"><i class="fas fa-utensils"></i> Menu Makanan</a>
                    <a class="nav-link" href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
                    <a class="nav-link active" href="pesanan_saya.php"><i class="fas fa-history"></i> Pesanan Saya</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Detail Pesanan</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-7">
                            <!-- Info Pesanan -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Pesanan #<?php echo $order['order_number']; ?></h5>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Alamat Pengiriman:</strong> <?php echo $order['alamat_pengiriman']; ?></p>
                                    <p><strong>Metode Pembayaran:</strong> <?php echo $order['payment_method']; ?></p>
                                    <p><strong>Catatan:</strong> <?php echo $order['catatan'] ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <!-- Detail Produk -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Detail Produk</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php while($item = $items->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $item['nama_produk']; ?></td>
                                                    <td><?php echo $item['quantity']; ?>x</td>
                                                    <td><?php echo format_rupiah($item['harga_per_item']); ?></td>
                                                    <td><?php echo format_rupiah($item['subtotal']); ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr><td colspan="3" class="text-end"><strong>Total</strong></td><td><strong><?php echo format_rupiah($order['total_harga']); ?></strong></td></tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <!-- Status Timeline -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-hourglass-half"></i> Status Pesanan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo in_array($order['status'], ['pending','diproses','dikirim','selesai']) ? 'active' : ''; ?>"></div>
                                            <div class="timeline-content">
                                                <strong>Pesanan Diterima</strong>
                                                <p class="mb-0 text-muted small"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo in_array($order['status'], ['diproses','dikirim','selesai']) ? 'active' : ''; ?>"></div>
                                            <div class="timeline-content">
                                                <strong>Sedang Dimasak</strong>
                                                <p class="mb-0 text-muted small">Restoran sedang menyiapkan pesanan Anda</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo in_array($order['status'], ['dikirim','selesai']) ? 'active' : ''; ?> <?php echo $order['status'] == 'dikirim' ? 'current' : ''; ?>"></div>
                                            <div class="timeline-content">
                                                <strong>Sedang Dikirim</strong>
                                                <p class="mb-0 text-muted small">Pesanan sedang dalam perjalanan</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo $order['status'] == 'selesai' ? 'active' : ''; ?>"></div>
                                            <div class="timeline-content">
                                                <strong>Pesanan Selesai</strong>
                                                <p class="mb-0 text-muted small">Terima kasih telah berbelanja!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Info Kurir -->
                            <?php if($kurir_info): ?>
                            <div class="card shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-motorcycle"></i> Informasi Kurir</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nama Kurir:</strong> <?php echo $kurir_info['nama_lengkap']; ?></p>
                                    <p><strong>No. HP:</strong> <?php echo $kurir_info['no_hp']; ?></p>
                                    <?php if(isset($lokasi)): ?>
                                    <p><strong>Lokasi terakhir:</strong> <span id="lastLocation">Memuat...</span></p>
                                    <p><strong>Terakhir update:</strong> <?php echo date('d/m/Y H:i:s', strtotime($lokasi['last_update'])); ?></p>
                                    <div class="d-grid">
                                        <button class="btn btn-primary" onclick="trackKurir(<?php echo $lokasi['latitude']; ?>, <?php echo $lokasi['longitude']; ?>)">
                                            <i class="fas fa-map-marker-alt"></i> Lihat Lokasi Kurir
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Tombol Aksi -->
                            <div class="mt-3">
                                <a href="pesanan_saya.php" class="btn btn-secondary w-100">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Pesanan Saya
                                </a>
                                <?php if($order['status'] == 'pending'): ?>
                                <a href="javascript:void(0)" class="btn btn-danger w-100 mt-2" onclick="confirmCancel(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-times"></i> Batalkan Pesanan
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmCancel(orderId) {
            if(confirm('Yakin ingin membatalkan pesanan ini?')) {
                window.location.href = 'proses/batal_pesanan.php?id=' + orderId;
            }
        }
        
        function trackKurir(lat, lng) {
            // Buka Google Maps
            window.open('https://www.google.com/maps?q=' + lat + ',' + lng);
        }
        
        <?php if(isset($lokasi)): ?>
        document.getElementById('lastLocation').innerHTML = 'Lat: <?php echo $lokasi['latitude']; ?>, Lng: <?php echo $lokasi['longitude']; ?>';
        <?php else: ?>
        document.getElementById('lastLocation').innerHTML = 'Belum tersedia';
        <?php endif; ?>
    </script>
</body>
</html>