<?php
include '../config/database.php';

if (!isset($_SESSION['kurir_id'])) {
    header("Location: login.php");
    exit();
}

$kurir_id = $_SESSION['kurir_id'];

$history = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, e.jarak_km, e.upah
                            FROM orders o 
                            JOIN users u ON o.customer_id = u.id 
                            LEFT JOIN kurir_earnings e ON o.id = e.order_id
                            WHERE o.kurir_id = $kurir_id AND o.status = 'selesai'
                            ORDER BY o.updated_at DESC");

$total_earning = $koneksi->query("SELECT SUM(upah) as total FROM kurir_earnings WHERE kurir_id = $kurir_id")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - Kurir Warung Makan Rizal</title>
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
        .card-stats {
            border: none;
            border-radius: 15px;
            background: white;
        }
        .status-selesai {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
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
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pengiriman</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Pendapatan</h6>
                                            <h3 class="mb-0 text-success"><?php echo format_rupiah($total_earning); ?></h3>
                                        </div>
                                        <div class="rounded-circle bg-success bg-opacity-25 p-3">
                                            <i class="fas fa-money-bill fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Pengiriman</h6>
                                            <h3 class="mb-0"><?php echo $history->num_rows; ?></h3>
                                        </div>
                                        <div class="rounded-circle bg-primary bg-opacity-25 p-3">
                                            <i class="fas fa-truck fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Daftar Pengiriman Selesai</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if($history->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No. Pesanan</th>
                                                <th>Customer</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Jarak</th>
                                                <th>Upah</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = $history->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong>#<?php echo $row['order_number']; ?></strong></td>
                                                <td><?php echo $row['customer_name']; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($row['updated_at'])); ?></td>
                                                <td><?php echo $row['jarak_km'] ? $row['jarak_km'] . ' km' : '-'; ?></td>
                                                <td><?php echo $row['upah'] ? format_rupiah($row['upah']) : '-'; ?></td>
                                                <td><span class="status-selesai">Selesai</span></td>
                                                <td>
                                                    <a href="tracking.php?order_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                    <h5>Belum ada riwayat pengiriman</h5>
                                    <p class="text-muted">Ambil pesanan untuk memulai</p>
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
</body>
</html>