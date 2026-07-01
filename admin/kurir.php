<?php
include '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Tambah kurir
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $check = $koneksi->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Username atau Email sudah terdaftar!";
    } else {
        $koneksi->query("INSERT INTO users (username, password, nama_lengkap, email, no_hp, alamat, role) 
                         VALUES ('$username', '$password', '$nama', '$email', '$no_hp', '$alamat', 'kurir')");
        $success = "Kurir berhasil ditambahkan";
    }
}

// Hapus kurir
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM users WHERE id = $id AND role = 'kurir'");
    header("Location: kurir.php");
    exit();
}

// Toggle status
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $current = $koneksi->query("SELECT is_active FROM users WHERE id = $id")->fetch_assoc();
    $new = $current['is_active'] ? 0 : 1;
    $koneksi->query("UPDATE users SET is_active = $new WHERE id = $id");
    header("Location: kurir.php");
    exit();
}

$kurir_list = $koneksi->query("SELECT * FROM users WHERE role = 'kurir' ORDER BY created_at DESC");
$online_kurir = $koneksi->query("SELECT COUNT(DISTINCT kurir_id) as online FROM kurir_locations WHERE is_online = 1 AND TIMESTAMPDIFF(MINUTE, last_update, NOW()) < 5")->fetch_assoc()['online'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurir - Admin Warung Makan Rizal</title>
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
        .online-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .online { background: #28a745; }
        .offline { background: #dc3545; }
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
                    <a class="nav-link active" href="kurir.php"><i class="fas fa-motorcycle"></i> Kurir</a>
                    <a class="nav-link" href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user-cog"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <div class="d-flex justify-content-between w-100">
                        <h5 class="mb-0"><i class="fas fa-motorcycle me-2"></i>Manajemen Kurir</h5>
                        <div>
                            <span class="me-3">
                                <span class="online-dot online"></span> Online: <?php echo $online_kurir; ?>
                            </span>
                            <button class="btn btn-sm" style="background: #D4AF37; color: #3E2723;" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus"></i> Tambah Kurir
                            </button>
                        </div>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Daftar Kurir</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>No. HP</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($k = $kurir_list->fetch_assoc()): 
                                            $last_loc = $koneksi->query("SELECT is_online FROM kurir_locations WHERE kurir_id = {$k['id']} AND TIMESTAMPDIFF(MINUTE, last_update, NOW()) < 5")->num_rows > 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $k['id']; ?></td>
                                            <td><?php echo $k['nama_lengkap']; ?></td>
                                            <td><?php echo $k['username']; ?></td>
                                            <td><?php echo $k['email']; ?></td>
                                            <td><?php echo $k['no_hp']; ?></td>
                                            <td>
                                                <?php if($k['is_active']): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                <?php endif; ?>
                                                <br>
                                                <small class="<?php echo $last_loc ? 'text-success' : 'text-secondary'; ?>">
                                                    <span class="online-dot <?php echo $last_loc ? 'online' : 'offline'; ?>"></span>
                                                    <?php echo $last_loc ? 'Online' : 'Offline'; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="?toggle=<?php echo $k['id']; ?>" class="btn btn-sm <?php echo $k['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                                        <i class="fas <?php echo $k['is_active'] ? 'fa-ban' : 'fa-check'; ?>"></i>
                                                    </a>
                                                    <a href="?hapus=<?php echo $k['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kurir ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Kurir -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #6F4E37, #A0522D); color: white;">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Tambah Kurir</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn" style="background: #6F4E37; color: white;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>