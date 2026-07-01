<?php
include '../config/database.php';

if (!isset($_SESSION['kurir_id'])) {
    header("Location: login.php");
    exit();
}

$kurir_id = $_SESSION['kurir_id'];

// Ambil pesanan yang tersedia (status diproses, belum ada kurir)
$available_orders = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, u.alamat, u.no_hp, u.latitude, u.longitude 
                                     FROM orders o 
                                     JOIN users u ON o.customer_id = u.id 
                                     WHERE o.status = 'diproses' AND (o.kurir_id IS NULL OR o.kurir_id = 0)
                                     ORDER BY o.created_at ASC");

// Proses ambil pesanan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ambil_pesanan'])) {
    $order_id = intval($_POST['order_id']);
    $jarak_km = floatval($_POST['jarak_km']);
    $upah = intval($_POST['upah']);
    
    $update = $koneksi->query("UPDATE orders SET kurir_id = $kurir_id, status = 'dikirim', updated_at = NOW() WHERE id = $order_id");
    
    if ($update) {
        $koneksi->query("INSERT INTO kurir_earnings (kurir_id, order_id, jarak_km, upah) VALUES ($kurir_id, $order_id, $jarak_km, $upah)");
        $order = $koneksi->query("SELECT customer_id FROM orders WHERE id = $order_id")->fetch_assoc();
        send_notification($order['customer_id'], "Pesanan Dikirim", "Kurir sedang menuju ke lokasi Anda", 'delivery');
        echo "<script>alert('Pesanan berhasil diambil!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        $error = "Gagal mengambil pesanan";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Pesanan - Kurir Warung Makan Rizal</title>
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
        
        .order-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            transition: all 0.3s;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .order-header {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: white;
            padding: 12px 20px;
        }
        .order-body {
            padding: 20px;
        }
        .status-available {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .distance-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px;
            margin: 10px 0;
        }
        .earning-amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .btn-take {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border: none;
            color: white;
            padding: 10px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
        }
        .btn-take:hover {
            background: linear-gradient(135deg, #A0522D, #6F4E37);
        }
        .btn-calculate {
            background: #D4AF37;
            border: none;
            color: #3E2723;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
        }
        .btn-calculate:hover {
            background: #C68E17;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
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
                    <a class="nav-link active" href="ambil_pesanan.php"><i class="fas fa-clipboard-list"></i> Ambil Pesanan</a>
                    <a class="nav-link" href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Ambil Pesanan</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if($available_orders->num_rows > 0): ?>
                        <?php while($order = $available_orders->fetch_assoc()): ?>
                        <div class="order-card" id="order-<?php echo $order['id']; ?>">
                            <div class="order-header d-flex justify-content-between align-items-center">
                                <span><strong>#<?php echo $order['order_number']; ?></strong></span>
                                <span class="status-available">Tersedia</span>
                            </div>
                            <div class="order-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><i class="fas fa-user"></i> <strong><?php echo $order['customer_name']; ?></strong></p>
                                        <p><i class="fas fa-phone"></i> <?php echo $order['no_hp']; ?></p>
                                        <p><i class="fas fa-map-marker-alt"></i> <?php echo substr($order['alamat'], 0, 60); ?>...</p>
                                        <p><i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                        <p><strong>Total: <?php echo format_rupiah($order['total_harga']); ?></strong></p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="distance-card">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><i class="fas fa-route"></i> Jarak & Upah</span>
                                                <button class="btn-calculate" onclick="calculateDistance(<?php echo $order['id']; ?>, '<?php echo $order['latitude']; ?>', '<?php echo $order['longitude']; ?>')">
                                                    <i class="fas fa-calculator"></i> Hitung
                                                </button>
                                            </div>
                                            <div id="distance-result-<?php echo $order['id']; ?>">
                                                <div class="text-center text-muted py-2">
                                                    <i class="fas fa-info-circle"></i> Klik "Hitung" untuk melihat jarak dan upah
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="form-<?php echo $order['id']; ?>" style="display: none;">
                                            <form method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="jarak_km" id="jarak_km_<?php echo $order['id']; ?>">
                                                <input type="hidden" name="upah" id="upah_<?php echo $order['id']; ?>">
                                                <div class="alert alert-success mt-2" id="info-<?php echo $order['id']; ?>"></div>
                                                <button type="submit" name="ambil_pesanan" class="btn-take mt-2">
                                                    <i class="fas fa-hand-holding-heart"></i> Ambil Pesanan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="card shadow-sm text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                <h5>Tidak ada pesanan tersedia</h5>
                                <p class="text-muted">Semua pesanan sudah diambil atau belum ada pesanan baru</p>
                                <a href="dashboard.php" class="btn" style="background: #6F4E37; color: white;">
                                    <i class="fas fa-tachometer-alt"></i> Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div id="loadingOverlay" class="loading-overlay">
        <div class="bg-white p-4 rounded text-center">
            <div class="spinner-border text-primary mb-2"></div>
            <p>Menghitung jarak...</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Koordinat restoran Warung Makan Rizal (Batam)
        const restoranLat = 1.0456;
        const restoranLng = 104.030;
        
        // Fungsi hitung upah berdasarkan jarak (tarif progresif)
        function hitungUpah(jarak_km) {
            if (jarak_km <= 1) return 5000;
            if (jarak_km <= 2) return 8000;
            if (jarak_km <= 3) return 10000;
            if (jarak_km <= 4) return 12000;
            if (jarak_km <= 5) return 15000;
            if (jarak_km <= 7) return 20000;
            if (jarak_km <= 10) return 25000;
            return Math.round(jarak_km * 5000);
        }
        
        // Fungsi dapatkan rincian tarif
        function getRincianTarif(jarak_km) {
            if (jarak_km <= 1) return "Tarif minimal (0-1 km)";
            if (jarak_km <= 2) return "Tarif jarak 1-2 km";
            if (jarak_km <= 3) return "Tarif jarak 2-3 km";
            if (jarak_km <= 4) return "Tarif jarak 3-4 km";
            if (jarak_km <= 5) return "Tarif jarak 4-5 km";
            if (jarak_km <= 7) return "Tarif jarak 5-7 km";
            if (jarak_km <= 10) return "Tarif jarak 7-10 km";
            return "Tarif jarak >10 km (" + jarak_km.toFixed(1) + " km × Rp 5.000)";
        }
        
        function calculateDistance(orderId, latCustomer, lngCustomer) {
            $('#loadingOverlay').fadeIn();
            
            // Jika koordinat tidak ada, gunakan estimasi manual
            if (!latCustomer || !lngCustomer || latCustomer == 0 || lngCustomer == 0) {
                setTimeout(function() {
                    $('#loadingOverlay').fadeOut();
                    showManualEstimation(orderId);
                }, 500);
                return;
            }
            
            $.ajax({
                url: '../api/kurir_calculate_distance.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    origin_lat: restoranLat,
                    origin_lng: restoranLng,
                    dest_lat: parseFloat(latCustomer),
                    dest_lng: parseFloat(lngCustomer)
                }),
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    var data = response;
                    
                    if (data.success) {
                        displayDistanceResult(orderId, data.jarak_km, data.distance_text, data.upah, data.upah_formatted, data.rincian);
                    } else {
                        showManualEstimation(orderId);
                    }
                },
                error: function() {
                    $('#loadingOverlay').fadeOut();
                    showManualEstimation(orderId);
                }
            });
        }
        
        // Estimasi manual (fallback jika API gagal)
        function showManualEstimation(orderId) {
            // Estimasi jarak berdasarkan alamat atau random (1-10 km)
            // Di sini kita bisa hitung manual atau pakai random
            var randomJarak = (Math.random() * 9 + 1).toFixed(1);
            var jarak = parseFloat(randomJarak);
            var upah = hitungUpah(jarak);
            var rincian = getRincianTarif(jarak);
            
            displayDistanceResult(orderId, jarak, jarak + ' km', upah, 'Rp ' + upah.toLocaleString('id-ID'), rincian + ' (Estimasi)');
        }
        
        function displayDistanceResult(orderId, jarakKm, distanceText, upah, upahFormatted, rincian) {
            $('#distance-result-' + orderId).html(`
                <div class="distance-card" style="background: #e8f5e9;">
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Jarak Tempuh</small>
                            <div class="fw-bold">${distanceText}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Upah Pengiriman</small>
                            <div class="earning-amount">${upahFormatted}</div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">Rincian: ${rincian}</small>
                    </div>
                </div>
            `);
            
            $('#jarak_km_' + orderId).val(jarakKm);
            $('#upah_' + orderId).val(upah);
            $('#info-' + orderId).html(`<i class="fas fa-check-circle"></i> Jarak: ${distanceText} | Upah: ${upahFormatted}`);
            $('#form-' + orderId).fadeIn();
        }
    </script>
</body>
</html>