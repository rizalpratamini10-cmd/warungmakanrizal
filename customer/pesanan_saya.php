<?php
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT * FROM orders WHERE customer_id = $customer_id";
if ($status_filter) {
    $query .= " AND status = '$status_filter'";
}
$query .= " ORDER BY created_at DESC";

$orders = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Warung Makan Rizal</title>
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
        .status-dibatalkan { background: #dc3545; color: white; }
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
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Pesanan Saya</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Filter Status -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="btn-group flex-wrap">
                                <a href="?status=" class="btn btn-outline-secondary <?php echo !$status_filter ? 'active' : ''; ?>">Semua</a>
                                <a href="?status=pending" class="btn btn-outline-secondary <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                                <a href="?status=diproses" class="btn btn-outline-secondary <?php echo $status_filter == 'diproses' ? 'active' : ''; ?>">Diproses</a>
                                <a href="?status=dikirim" class="btn btn-outline-secondary <?php echo $status_filter == 'dikirim' ? 'active' : ''; ?>">Dikirim</a>
                                <a href="?status=selesai" class="btn btn-outline-secondary <?php echo $status_filter == 'selesai' ? 'active' : ''; ?>">Selesai</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daftar Pesanan -->
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Pesanan</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($orders->num_rows > 0): ?>
                                            <?php while($order = $orders->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                <td><?php echo format_rupiah($order['total_harga']); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="detail_pesanan.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    <?php if($order['status'] == 'pending'): ?>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="confirmCancel(<?php echo $order['id']; ?>)">
                                                            <i class="fas fa-times"></i> Batal
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                                    <h5>Belum ada pesanan</h5>
                                                    <p class="text-muted">Mulai pesan makanan favorit Anda</p>
                                                    <a href="menu.php" class="btn" style="background: #6F4E37; color: white;">
                                                        <i class="fas fa-utensils"></i> Pesan Sekarang
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
    </script>
</body>
</html>