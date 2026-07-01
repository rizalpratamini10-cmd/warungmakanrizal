<?php
include '../config/database.php';

if (!isset($_SESSION['kurir_id'])) {
    header("Location: login.php");
    exit();
}

$kurir_id = $_SESSION['kurir_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header("Location: dashboard.php");
    exit();
}

$order = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, u.alamat, u.no_hp, u.latitude, u.longitude 
                          FROM orders o 
                          JOIN users u ON o.customer_id = u.id 
                          WHERE o.id = $order_id AND o.kurir_id = $kurir_id")->fetch_assoc();

if (!$order) {
    header("Location: dashboard.php");
    exit();
}

$items = $koneksi->query("SELECT od.*, p.nama_produk 
                          FROM order_details od 
                          JOIN products p ON od.product_id = p.id 
                          WHERE od.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pesanan - Kurir Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        #map {
            height: 400px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .btn-navigate {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 50px;
            width: 100%;
        }
        .btn-navigate:hover {
            background: linear-gradient(135deg, #A0522D, #6F4E37);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center py-4 border-bottom border-secondary">
                    <i class="fas fa-motorcycle fa-3x" style="color: #D4AF37;"></i>
                    <h5 class="mt-2"><?php echo $_SESSION['kurir_nama']; ?></h5>
                    <small>Kurir</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="ambil_pesanan.php"><i class="fas fa-clipboard-list"></i> Ambil Pesanan</a>
                    <a class="nav-link active" href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Tracking Pesanan #<?php echo $order['order_number']; ?></h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-7">
                            <div id="map"></div>
                            <div class="info-card">
                                <h6><i class="fas fa-user"></i> Informasi Customer</h6>
                                <p><strong>Nama:</strong> <?php echo $order['customer_name']; ?></p>
                                <p><strong>No. HP:</strong> <?php echo $order['no_hp']; ?></p>
                                <p><strong>Alamat:</strong> <?php echo $order['alamat']; ?></p>
                                <?php if($order['latitude'] && $order['longitude']): ?>
                                    <button class="btn-navigate" onclick="openNavigation(<?php echo $order['latitude']; ?>, <?php echo $order['longitude']; ?>)">
                                        <i class="fas fa-directions"></i> Buka Navigasi (Google Maps)
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="info-card">
                                <h6><i class="fas fa-receipt"></i> Detail Pesanan</h6>
                                <table class="table table-sm">
                                    <thead><tr><th>Produk</th><th>Jml</th><th>Harga</th></tr></thead>
                                    <tbody>
                                        <?php while($item = $items->fetch_assoc()): ?>
                                        <tr><td><?php echo $item['nama_produk']; ?></td><td><?php echo $item['quantity']; ?>x</td><td><?php echo format_rupiah($item['harga_per_item']); ?></td></tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot><tr class="table-active"><td colspan="2"><strong>Total</strong></td><td><strong><?php echo format_rupiah($order['total_harga']); ?></strong></td></tr></tfoot>
                                </table>
                            </div>
                            <div class="info-card">
                                <h6><i class="fas fa-check-circle"></i> Aksi</h6>
                                <button class="btn-navigate" onclick="completeOrder(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-check"></i> Tandai Selesai
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var map;
        var kurirMarker;
        var customerMarker;
        var watchId;
        
        var customerLat = <?php echo $order['latitude'] ?: '1.0456'; ?>;
        var customerLng = <?php echo $order['longitude'] ?: '104.030'; ?>;
        
        function initMap() {
            map = L.map('map').setView([customerLat, customerLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            customerMarker = L.marker([customerLat, customerLng]).addTo(map)
                .bindPopup('📍 Lokasi Customer')
                .openPopup();
        }
        
        function startTracking() {
            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(updateLocation, showError, {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 5000
                });
            }
        }
        
        function updateLocation(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            
            if (kurirMarker) {
                kurirMarker.setLatLng([lat, lng]);
                map.setView([lat, lng], 14);
            } else {
                kurirMarker = L.marker([lat, lng]).addTo(map)
                    .bindPopup('🏍️ Lokasi Saya')
                    .openPopup();
                map.setView([lat, lng], 14);
            }
            
            $.ajax({
                url: '../api/track_location.php',
                method: 'POST',
                data: { kurir_id: <?php echo $kurir_id; ?>, latitude: lat, longitude: lng, order_id: <?php echo $order_id; ?> }
            });
        }
        
        function showError(error) {
            console.log('Error:', error);
        }
        
        function openNavigation(lat, lng) {
            window.open('https://www.google.com/maps/dir/current/' + lat + ',' + lng);
        }
        
        function completeOrder(orderId) {
            if (confirm('Sudah sampai tujuan? Tandai pesanan selesai?')) {
                $.ajax({
                    url: '../api/update_status.php',
                    method: 'POST',
                    data: { order_id: orderId, status: 'selesai' },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            alert('Pesanan selesai! Terima kasih.');
                            window.location.href = 'dashboard.php';
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    }
                });
            }
        }
        
        initMap();
        startTracking();
    </script>
</body>
</html>