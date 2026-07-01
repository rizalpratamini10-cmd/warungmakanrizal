<?php
include '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Update status pesanan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $koneksi->query("UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = $order_id");
    
    // Kirim notifikasi ke customer
    $order = $koneksi->query("SELECT customer_id FROM orders WHERE id = $order_id")->fetch_assoc();
    send_notification($order['customer_id'], "Status Pesanan Diperbarui", "Pesanan Anda sekarang: " . ucfirst($status), 'order');
    
    $success = "Status pesanan berhasil diperbarui";
}

// Filter status
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT o.*, u.nama_lengkap as customer_name, u.no_hp as customer_phone 
          FROM orders o 
          JOIN users u ON o.customer_id = u.id";
if ($status_filter) {
    $query .= " WHERE o.status = '$status_filter'";
}
$query .= " ORDER BY o.created_at DESC";

$orders = $koneksi->query($query);

// Ambil detail pesanan jika ada
$detail_id = isset($_GET['detail']) ? $_GET['detail'] : 0;
$order_detail = null;
$order_items = null;
if ($detail_id) {
    $order_detail = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, u.no_hp, u.alamat 
                                      FROM orders o 
                                      JOIN users u ON o.customer_id = u.id 
                                      WHERE o.id = $detail_id")->fetch_assoc();
    $order_items = $koneksi->query("SELECT od.*, p.nama_produk 
                                     FROM order_details od 
                                     JOIN products p ON od.product_id = p.id 
                                     WHERE od.order_id = $detail_id");
}

// Ambil daftar kurir untuk assign
$kurir_list = $koneksi->query("SELECT id, nama_lengkap FROM users WHERE role = 'kurir' AND is_active = 1");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan - Admin Warung Makan Rizal</title>
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
        .navbar-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
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
                    <a class="nav-link active" href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan</a>
                    <a class="nav-link" href="produk.php"><i class="fas fa-utensils"></i> Produk</a>
                    <a class="nav-link" href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
                    <a class="nav-link" href="customer.php"><i class="fas fa-users"></i> Customer</a>
                    <a class="nav-link" href="kurir.php"><i class="fas fa-motorcycle"></i> Kurir</a>
                    <a class="nav-link" href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user-cog"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Manajemen Pesanan</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x me-2"></i>
                        <span><?php echo $_SESSION['admin_nama']; ?></span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filter Status -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="btn-group flex-wrap">
                                <a href="?status=" class="btn btn-outline-secondary <?php echo !$status_filter ? 'active' : ''; ?>">Semua</a>
                                <a href="?status=pending" class="btn btn-outline-secondary <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                                <a href="?status=diproses" class="btn btn-outline-secondary <?php echo $status_filter == 'diproses' ? 'active' : ''; ?>">Diproses</a>
                                <a href="?status=dikirim" class="btn btn-outline-secondary <?php echo $status_filter == 'dikirim' ? 'active' : ''; ?>">Dikirim</a>
                                <a href="?status=selesai" class="btn btn-outline-secondary <?php echo $status_filter == 'selesai' ? 'active' : ''; ?>">Selesai</a>
                                <a href="?status=dibatalkan" class="btn btn-outline-secondary <?php echo $status_filter == 'dibatalkan' ? 'active' : ''; ?>">Dibatalkan</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daftar Pesanan -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Daftar Pesanan</h5>
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
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                            <td>
                                                <?php echo $order['customer_name']; ?><br>
                                                <small class="text-muted"><?php echo $order['customer_phone']; ?></small>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo format_rupiah($order['total_harga']); ?></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                                        <option value="diproses" <?php echo $order['status'] == 'diproses' ? 'selected' : ''; ?>>👨‍🍳 Diproses</option>
                                                        <option value="dikirim" <?php echo $order['status'] == 'dikirim' ? 'selected' : ''; ?>>🚚 Dikirim</option>
                                                        <option value="selesai" <?php echo $order['status'] == 'selesai' ? 'selected' : ''; ?>>✅ Selesai</option>
                                                        <option value="dibatalkan" <?php echo $order['status'] == 'dibatalkan' ? 'selected' : ''; ?>>❌ Dibatalkan</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td>
                                                <a href="?detail=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Detail Pesanan -->
                    <?php if($detail_id && $order_detail): ?>
                    <div class="modal fade show" id="detailModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header" style="background: linear-gradient(135deg, #6F4E37, #A0522D); color: white;">
                                    <h5 class="modal-title">Detail Pesanan #<?php echo $order_detail['order_number']; ?></h5>
                                    <a href="pesanan.php" class="btn-close btn-close-white"></a>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Customer:</strong> <?php echo $order_detail['customer_name']; ?><br>
                                            <strong>No. HP:</strong> <?php echo $order_detail['no_hp']; ?><br>
                                            <strong>Alamat:</strong> <?php echo $order_detail['alamat']; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tanggal:</strong> <?php echo date('d/m/Y H:i', strtotime($order_detail['created_at'])); ?><br>
                                            <strong>Status:</strong> 
                                            <span class="status-badge status-<?php echo $order_detail['status']; ?>">
                                                <?php echo ucfirst($order_detail['status']); ?>
                                            </span><br>
                                            <strong>Metode Bayar:</strong> <?php echo $order_detail['payment_method']; ?>
                                        </div>
                                    </div>
                                    
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php while($item = $order_items->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $item['nama_produk']; ?></td>
                                                <td><?php echo $item['quantity']; ?> x</td>
                                                <td><?php echo format_rupiah($item['harga_per_item']); ?></td>
                                                <td><?php echo format_rupiah($item['subtotal']); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <tr class="table-active">
                                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                                <td><strong><?php echo format_rupiah($order_detail['total_harga']); ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <?php if($order_detail['status'] == 'diproses'): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Untuk mengirim pesanan, assign kurir terlebih dahulu.
                                    </div>
                                    <form method="POST" class="mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Assign Kurir</label>
                                                <select name="kurir_id" class="form-select" required>
                                                    <option value="">Pilih Kurir</option>
                                                    <?php 
                                                    $kurirs = $koneksi->query("SELECT id, nama_lengkap FROM users WHERE role = 'kurir' AND is_active = 1");
                                                    while($kurir = $kurirs->fetch_assoc()): ?>
                                                        <option value="<?php echo $kurir['id']; ?>"><?php echo $kurir['nama_lengkap']; ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 align-self-end">
                                                <button type="submit" name="assign_kurir" class="btn btn-primary w-100">
                                                    <i class="fas fa-motorcycle"></i> Assign Kurir & Kirim
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="order_id" value="<?php echo $order_detail['id']; ?>">
                                    </form>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="pesanan.php" class="btn btn-secondary">Tutup</a>
                                    <?php if($order_detail['status'] != 'selesai' && $order_detail['status'] != 'dibatalkan'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?php echo $order_detail['id']; ?>">
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" name="update_status" class="btn btn-success" onclick="return confirm('Tandai pesanan selesai?')">
                                            <i class="fas fa-check"></i> Tandai Selesai
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    // Proses assign kurir
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_kurir'])) {
        $order_id = $_POST['order_id'];
        $kurir_id = $_POST['kurir_id'];
        if ($kurir_id) {
            $koneksi->query("UPDATE orders SET kurir_id = $kurir_id, status = 'dikirim', updated_at = NOW() WHERE id = $order_id");
            send_notification($kurir_id, "Pesanan Baru", "Ada pesanan baru yang perlu diantar", 'delivery');
            echo "<script>window.location.href='pesanan.php?detail=$order_id';</script>";
        }
    }
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>