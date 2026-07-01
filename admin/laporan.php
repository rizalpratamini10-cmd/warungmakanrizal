<?php
include '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$query = "SELECT o.*, u.nama_lengkap as customer_name 
          FROM orders o 
          JOIN users u ON o.customer_id = u.id 
          WHERE DATE(o.created_at) BETWEEN '$start_date' AND '$end_date' 
          ORDER BY o.created_at DESC";
$orders = $koneksi->query($query);

$total_revenue = $koneksi->query("SELECT SUM(total_harga) as total FROM orders WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date' AND status='selesai'")->fetch_assoc()['total'] ?? 0;
$total_orders = $orders->num_rows;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin Warung Makan Rizal</title>
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
        @media print {
            .sidebar, .navbar-custom, .btn, .no-print { display: none; }
            .col-md-9 { width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center py-4 border-bottom border-secondary">
                    <i class="fas fa-store fa-3x" style="color: #D4AF37;"></i>
                    <h5 class="mt-2">Warung Rizal</h5>
                    <small>Admin Panel</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan</a>
                    <a class="nav-link" href="produk.php"><i class="fas fa-utensils"></i> Produk</a>
                    <a class="nav-link" href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
                    <a class="nav-link" href="customer.php"><i class="fas fa-users"></i> Customer</a>
                    <a class="nav-link" href="kurir.php"><i class="fas fa-motorcycle"></i> Kurir</a>
                    <a class="nav-link active" href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user-cog"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white no-print">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Laporan Penjualan</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x me-2"></i>
                        <span><?php echo $_SESSION['admin_nama']; ?></span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Filter Tanggal -->
                    <div class="card shadow-sm mb-4 no-print">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="form-control">
                                </div>
                                <div class="col-md-4 align-self-end">
                                    <button type="submit" class="btn" style="background: #6F4E37; color: white;">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="window.print();">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Ringkasan -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Pendapatan</h6>
                                    <h3 class="text-success"><?php echo format_rupiah($total_revenue); ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Pesanan</h6>
                                    <h3><?php echo $total_orders; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Rata-rata per Pesanan</h6>
                                    <h3><?php echo $total_orders > 0 ? format_rupiah($total_revenue / $total_orders) : format_rupiah(0); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabel Laporan -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Detail Laporan Periode <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?></h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Pesanan</th>
                                            <th>Customer</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Metode Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($o = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $o['order_number']; ?></strong></td>
                                            <td><?php echo $o['customer_name']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                                            <td><?php echo format_rupiah($o['total_harga']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $o['status'] == 'selesai' ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($o['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $o['payment_method']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                                            <td colspan="3"><strong><?php echo format_rupiah($total_revenue); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>