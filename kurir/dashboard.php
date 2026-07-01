<?php
include '../config/database.php';

if (!isset($_SESSION['kurir_id'])) {
    header("Location: login.php");
    exit();
}

$kurir_id = $_SESSION['kurir_id'];

// Statistik
$total_delivered = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE kurir_id = $kurir_id AND status = 'selesai'")->fetch_assoc()['total'];
$total_pending = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE kurir_id = $kurir_id AND status = 'dikirim'")->fetch_assoc()['total'];
$total_earning = $koneksi->query("SELECT SUM(upah) as total FROM kurir_earnings WHERE kurir_id = $kurir_id")->fetch_assoc()['total'] ?? 0;

// Pesanan yang sedang dikirim
$current_orders = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, u.alamat, u.no_hp, u.latitude, u.longitude 
                                   FROM orders o 
                                   JOIN users u ON o.customer_id = u.id 
                                   WHERE o.kurir_id = $kurir_id AND o.status = 'dikirim'
                                   ORDER BY o.created_at ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kurir - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body { background: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
        
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
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: #D4AF37;
        }
        .sidebar .nav-link i { width: 25px; margin-right: 8px; }
        
        .navbar-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
        }
        
        .card-stats {
            border: none;
            border-radius: 15px;
            background: white;
            transition: all 0.3s;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        #map {
            height: 300px;
            border-radius: 12px;
            margin-bottom: 15px;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-dikirim { background: #007bff; color: white; }
        
        .online-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            background: #28a745;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center py-4 border-bottom border-secondary">
                    <i class="fas fa-motorcycle fa-3x" style="color: #D4AF37;"></i>
                    <h5 class="mt-2"><?php echo $_SESSION['kurir_nama']; ?></h5>
                    <small>Kurir</small>
                    <div class="mt-2">
                        <span class="online-dot"></span> <small>Online</small>
                    </div>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="ambil_pesanan.php"><i class="fas fa-clipboard-list"></i> Ambil Pesanan</a>
                    <a class="nav-link" href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Kurir</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Welcome Card -->
                    <div class="alert alert-light border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #6F4E37, #A0522D); color: white;">
                        <h4 class="mb-1">Selamat datang, <?php echo $_SESSION['kurir_nama']; ?>! 🏍️</h4>
                        <p class="mb-0">Status: Online - Siap mengantar pesanan</p>
                    </div>
                    
                    <!-- Statistik -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Pesanan Selesai</h6>
                                            <h2 class="mb-0 text-success"><?php echo $total_delivered; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-success bg-opacity-25 p-3">
                                            <i class="fas fa-check-circle fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Sedang Dikirim</h6>
                                            <h2 class="mb-0 text-primary"><?php echo $total_pending; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-primary bg-opacity-25 p-3">
                                            <i class="fas fa-truck fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Pendapatan</h6>
                                            <h5 class="mb-0"><?php echo format_rupiah($total_earning); ?></h5>
                                        </div>
                                        <div class="rounded-circle bg-warning bg-opacity-25 p-3">
                                            <i class="fas fa-money-bill fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Peta Tracking -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt text-danger"></i> Lokasi Real-time</h5>
                        </div>
                        <div class="card-body">
                            <div id="map"></div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <button id="startTracking" class="btn btn-success w-100">
                                        <i class="fas fa-play"></i> Mulai Tracking
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button id="stopTracking" class="btn btn-danger w-100">
                                        <i class="fas fa-stop"></i> Stop Tracking
                                    </button>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3" id="trackingStatus">
                                <i class="fas fa-info-circle"></i> Klik "Mulai Tracking" untuk mengirim lokasi Anda
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pesanan Sedang Dikirim -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-truck"></i> Pesanan Sedang Dikirim</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if($current_orders->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No. Pesanan</th>
                                                <th>Customer</th>
                                                <th>Alamat</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($order = $current_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                                <td>
                                                    <?php echo $order['customer_name']; ?><br>
                                                    <small class="text-muted"><?php echo $order['no_hp']; ?></small>
                                                </td>
                                                <td><?php echo substr($order['alamat'], 0, 50); ?>...</td>
                                                <td><span class="status-badge status-dikirim">Dikirim</span></td>
                                                <td>
                                                    <a href="tracking.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-map"></i> Tracking
                                                    </a>
                                                    <button class="btn btn-sm btn-success" onclick="completeOrder(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-check"></i> Selesai
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-4x text-muted mb-3 d-block"></i>
                                    <h5>Tidak ada pesanan yang sedang dikirim</h5>
                                    <p class="text-muted">Ambil pesanan baru di menu "Ambil Pesanan"</p>
                                    <a href="ambil_pesanan.php" class="btn" style="background: #6F4E37; color: white;">
                                        <i class="fas fa-clipboard-list"></i> Ambil Pesanan
                                    </a>
                                </div>
                            <?php endif; ?>
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
        var marker;
        var watchId;
        var isTracking = false;
        
        function initMap(lat = 1.0456, lng = 104.030) {
            map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            marker = L.marker([lat, lng]).addTo(map);
        }
        
        function startTracking() {
            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(updateLocation, showError, {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 5000
                });
                isTracking = true;
                document.getElementById('trackingStatus').innerHTML = '<i class="fas fa-check-circle text-success"></i> Tracking aktif - Lokasi Anda sedang dikirim';
                document.getElementById('trackingStatus').className = 'alert alert-success mt-3';
            } else {
                alert('Browser tidak support geolocation');
            }
        }
        
        function stopTracking() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
                isTracking = false;
                document.getElementById('trackingStatus').innerHTML = '<i class="fas fa-info-circle"></i> Tracking dihentikan. Klik "Mulai Tracking" untuk mengirim lokasi.';
                document.getElementById('trackingStatus').className = 'alert alert-info mt-3';
            }
        }
        
        function updateLocation(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            
            if (marker) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 16);
            } else {
                initMap(lat, lng);
            }
            
            $.ajax({
                url: '../api/track_location.php',
                method: 'POST',
                data: { kurir_id: <?php echo $kurir_id; ?>, latitude: lat, longitude: lng }
            });
        }
        
        function showError(error) {
            console.log('Geolocation error:', error);
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
                            location.reload();
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    }
                });
            }
        }
        
        document.getElementById('startTracking')?.addEventListener('click', startTracking);
        document.getElementById('stopTracking')?.addEventListener('click', stopTracking);
        initMap();
    </script>
</body>
</html>