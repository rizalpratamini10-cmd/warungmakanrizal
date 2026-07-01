<?php
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer = $koneksi->query("SELECT * FROM users WHERE id = $customer_id")->fetch_assoc();

// Ambil menu aktif
$menu = isset($_GET['menu']) ? $_GET['menu'] : 'profil';
$success = '';
$error = '';

// Update Profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    
    $koneksi->query("UPDATE users SET nama_lengkap = '$nama', no_hp = '$no_hp', alamat = '$alamat', jenis_kelamin = '$jenis_kelamin', tanggal_lahir = '$tanggal_lahir' WHERE id = $customer_id");
    
    $_SESSION['customer_nama'] = $nama;
    $success = "Profil berhasil diperbarui!";
    $customer = $koneksi->query("SELECT * FROM users WHERE id = $customer_id")->fetch_assoc();
}

// Update Password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];
    
    if (password_verify($password_lama, $customer['password'])) {
        if ($password_baru == $konfirmasi && strlen($password_baru) >= 6) {
            $new_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $koneksi->query("UPDATE users SET password = '$new_hash' WHERE id = $customer_id");
            $success = "Password berhasil diubah!";
        } else {
            $error = "Password baru tidak cocok atau kurang dari 6 karakter!";
        }
    } else {
        $error = "Password lama salah!";
    }
}

// Tambah Alamat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_alamat'])) {
    $label = mysqli_real_escape_string($koneksi, $_POST['label']);
    $alamat_baru = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $koneksi->query("INSERT INTO customer_addresses (customer_id, label, alamat) VALUES ($customer_id, '$label', '$alamat_baru')");
    $success = "Alamat berhasil ditambahkan!";
}

// Hapus Alamat
if (isset($_GET['hapus_alamat'])) {
    $id = intval($_GET['hapus_alamat']);
    $koneksi->query("DELETE FROM customer_addresses WHERE id = $id AND customer_id = $customer_id");
    header("Location: akun_saya.php?menu=alamat");
    exit();
}

$addresses = $koneksi->query("SELECT * FROM customer_addresses WHERE customer_id = $customer_id ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body { background: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
        
        /* Navbar Atas */
        .navbar-top {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            padding: 12px 0;
        }
        .navbar-top .navbar-brand {
            color: white;
            font-weight: 600;
            font-size: 20px;
        }
        .navbar-top .nav-link {
            color: white;
        }
        
        /* Account Sidebar - Seperti Shopee */
        .account-sidebar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        /* Profile Card */
        .profile-card {
            text-align: center;
            padding: 25px 20px;
            border-bottom: 1px solid #f0f0f0;
        }
        .profile-card .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #D4AF37;
            margin-bottom: 12px;
        }
        .profile-card .username {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .profile-card .ubah-profil {
            font-size: 13px;
            color: #D4AF37;
            text-decoration: none;
        }
        .profile-card .ubah-profil:hover {
            text-decoration: underline;
        }
        
        /* Navigation Menu */
        .nav-menu-account {
            padding: 10px 0;
        }
        .nav-group-title {
            font-size: 13px;
            font-weight: 600;
            color: #999;
            padding: 12px 20px 5px 20px;
            letter-spacing: 0.5px;
        }
        .nav-menu-account a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }
        .nav-menu-account a:hover {
            background: #f5f5f5;
            color: #D4AF37;
        }
        .nav-menu-account a.active {
            background: #FFF8F0;
            color: #D4AF37;
            border-right: 3px solid #D4AF37;
        }
        .nav-menu-account a i {
            width: 22px;
            color: #888;
        }
        .nav-menu-account a:hover i,
        .nav-menu-account a.active i {
            color: #D4AF37;
        }
        
        /* Content Area */
        .content-area {
            background: white;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            padding: 25px;
            min-height: 550px;
        }
        .content-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        /* Info Row untuk tampilan profil */
        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
        }
        .info-label {
            width: 150px;
            color: #666;
        }
        .info-value {
            flex: 1;
            color: #333;
            font-weight: 500;
        }
        .verified-badge {
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            display: inline-block;
        }
        
        /* Address Card */
        .address-card {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #eee;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }
        .form-control:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
        .btn-save {
            background: #6F4E37;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-save:hover {
            background: #A0522D;
            color: white;
        }
        
        /* Setting Item untuk switch */
        .setting-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .setting-item:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar Atas -->
<nav class="navbar navbar-expand-lg navbar-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-utensils"></i> Warung Makan Rizal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="keranjang.php">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row">
        <!-- Account Sidebar (Seperti Shopee) -->
        <div class="col-md-3">
            <div class="account-sidebar">
                <!-- Profile Card -->
                <div class="profile-card">
                    <img src="https://ui-avatars.com/api/?background=6F4E37&color=fff&name=<?php echo urlencode($customer['nama_lengkap']); ?>" class="avatar" alt="Avatar">
                    <div class="username"><?php echo $customer['nama_lengkap']; ?></div>
                    <a href="?menu=profil" class="ubah-profil">Ubah Profil</a>
                </div>
                
                <!-- Navigation Menu -->
                <div class="nav-menu-account">
                    <!-- AKUN SAYA Group -->
                    <div class="nav-group-title">AKUN SAYA</div>
                    <a href="?menu=profil" class="<?php echo ($menu == 'profil') ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i> Profil
                    </a>
                    <a href="?menu=bank" class="<?php echo ($menu == 'bank') ? 'active' : ''; ?>">
                        <i class="fas fa-credit-card"></i> Bank & Kartu
                    </a>
                    <a href="?menu=alamat" class="<?php echo ($menu == 'alamat') ? 'active' : ''; ?>">
                        <i class="fas fa-map-marker-alt"></i> Alamat
                    </a>
                    <a href="?menu=password" class="<?php echo ($menu == 'password') ? 'active' : ''; ?>">
                        <i class="fas fa-key"></i> Ubah Password
                    </a>
                    <a href="?menu=notifikasi" class="<?php echo ($menu == 'notifikasi') ? 'active' : ''; ?>">
                        <i class="fas fa-bell"></i> Pengaturan Notifikasi
                    </a>
                    <a href="?menu=privasi" class="<?php echo ($menu == 'privasi') ? 'active' : ''; ?>">
                        <i class="fas fa-shield-alt"></i> Pengaturan Privasi
                    </a>
                    
                    <!-- PESANAN SAYA Group -->
                    <div class="nav-group-title">PESANAN SAYA</div>
                    <a href="pesanan_saya.php">
                        <i class="fas fa-receipt"></i> Pesanan Saya
                    </a>
                    <a href="?menu=notifikasi">
                        <i class="fas fa-bell"></i> Notifikasi
                    </a>
                    <a href="#">
                        <i class="fas fa-ticket-alt"></i> Voucher Saya
                    </a>
                    <a href="#">
                        <i class="fas fa-coins"></i> Koin Saya
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Content Area (Kanan) -->
        <div class="col-md-9">
            <div class="content-area">
                
                <!-- Alert Success/Error -->
                <?php if($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- ==================== MENU PROFIL ==================== -->
                <?php if($menu == 'profil'): ?>
                <div class="content-title">
                    <i class="fas fa-user me-2"></i> Profil
                </div>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" value="<?php echo $customer['username']; ?>" disabled>
                                <small class="text-muted">Username tidak dapat diubah</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $customer['nama_lengkap']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" value="<?php echo $customer['email']; ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="no_hp" class="form-control" value="<?php echo $customer['no_hp']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="Laki-laki" <?php echo ($customer['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo ($customer['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    <option value="Lainnya" <?php echo ($customer['jenis_kelamin'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $customer['tanggal_lahir']; ?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3"><?php echo $customer['alamat']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Status Verifikasi</label>
                                <div>
                                    <span class="verified-badge"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="update_profil" class="btn-save">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
                
                <!-- ==================== MENU BANK & KARTU ==================== -->
                <?php if($menu == 'bank'): ?>
                <div class="content-title">
                    <i class="fas fa-credit-card me-2"></i> Bank & Kartu
                </div>
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                    <p>Belum ada kartu yang ditambahkan</p>
                    <button class="btn btn-outline-secondary" disabled>+ Tambah Kartu (Coming Soon)</button>
                </div>
                <?php endif; ?>
                
                <!-- ==================== MENU ALAMAT ==================== -->
                <?php if($menu == 'alamat'): ?>
                <div class="content-title">
                    <i class="fas fa-map-marker-alt me-2"></i> Alamat Saya
                </div>
                
                <button class="btn btn-sm mb-3" style="background: #6F4E37; color: white;" data-bs-toggle="modal" data-bs-target="#modalAlamat">
                    <i class="fas fa-plus"></i> Tambah Alamat Baru
                </button>
                
                <?php if($addresses->num_rows > 0): ?>
                    <?php while($addr = $addresses->fetch_assoc()): ?>
                    <div class="address-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><i class="fas fa-tag me-1"></i> <?php echo $addr['label']; ?></strong>
                                <div class="mt-1"><?php echo $addr['alamat']; ?></div>
                            </div>
                            <div>
                                <a href="?menu=alamat&hapus_alamat=<?php echo $addr['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus alamat ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                        <p>Belum ada alamat tersimpan</p>
                    </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <!-- ==================== MENU UBAH PASSWORD ==================== -->
                <?php if($menu == 'password'): ?>
                <div class="content-title">
                    <i class="fas fa-key me-2"></i> Ubah Password
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label>Password Lama</label>
                        <input type="password" name="password_lama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" class="form-control" required>
                    </div>
                    <button type="submit" name="update_password" class="btn-save">
                        <i class="fas fa-key"></i> Ubah Password
                    </button>
                </form>
                <?php endif; ?>
                
                <!-- ==================== MENU PENGATURAN NOTIFIKASI ==================== -->
                <?php if($menu == 'notifikasi'): ?>
                <div class="content-title">
                    <i class="fas fa-bell me-2"></i> Pengaturan Notifikasi
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Pilih notifikasi yang ingin Anda terima
                </div>
                <div class="setting-item">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notif_order" checked>
                        <label class="form-check-label" for="notif_order">
                            <strong>Notifikasi Pesanan</strong><br>
                            <small class="text-muted">Status pesanan, pengiriman, dan selesai</small>
                        </label>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notif_promosi" checked>
                        <label class="form-check-label" for="notif_promosi">
                            <strong>Notifikasi Promosi</strong><br>
                            <small class="text-muted">Info promo, diskon, dan penawaran menarik</small>
                        </label>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notif_payment" checked>
                        <label class="form-check-label" for="notif_payment">
                            <strong>Notifikasi Pembayaran</strong><br>
                            <small class="text-muted">Konfirmasi pembayaran berhasil/gagal</small>
                        </label>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn-save" onclick="alert('Pengaturan notifikasi disimpan!')">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
                <?php endif; ?>
                
                <!-- ==================== MENU PENGATURAN PRIVASI ==================== -->
                <?php if($menu == 'privasi'): ?>
                <div class="content-title">
                    <i class="fas fa-shield-alt me-2"></i> Pengaturan Privasi
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Atur siapa yang dapat melihat informasi Anda
                </div>
                <div class="setting-item">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="privacy_profile" checked>
                        <label class="form-check-label" for="privacy_profile">
                            <strong>Tampilkan Profil ke Publik</strong><br>
                            <small class="text-muted">Izinkan orang lain melihat profil Anda</small>
                        </label>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="privacy_phone" checked>
                        <label class="form-check-label" for="privacy_phone">
                            <strong>Tampilkan Nomor Telepon</strong><br>
                            <small class="text-muted">Nomor telepon hanya untuk keperluan pengiriman</small>
                        </label>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="privacy_address" checked>
                        <label class="form-check-label" for="privacy_address">
                            <strong>Tampilkan Alamat</strong><br>
                            <small class="text-muted">Alamat hanya untuk keperluan pengiriman</small>
                        </label>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn-save" onclick="alert('Pengaturan privasi disimpan!')">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Alamat -->
<div class="modal fade" id="modalAlamat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #6F4E37; color: white;">
                <h5 class="modal-title">Tambah Alamat Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Label Alamat</label>
                        <input type="text" name="label" class="form-control" placeholder="Contoh: Rumah, Kantor, Kost" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_alamat" class="btn" style="background: #6F4E37; color: white;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>