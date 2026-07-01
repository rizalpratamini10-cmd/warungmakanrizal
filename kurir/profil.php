<?php
include '../config/database.php';

if (!isset($_SESSION['kurir_id'])) {
    header("Location: login.php");
    exit();
}

$kurir_id = $_SESSION['kurir_id'];
$kurir = $koneksi->query("SELECT * FROM users WHERE id = $kurir_id")->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $koneksi->query("UPDATE users SET nama_lengkap = '$nama', no_hp = '$no_hp', alamat = '$alamat', password = '$password' WHERE id = $kurir_id");
    } else {
        $koneksi->query("UPDATE users SET nama_lengkap = '$nama', no_hp = '$no_hp', alamat = '$alamat' WHERE id = $kurir_id");
    }
    
    $_SESSION['kurir_nama'] = $nama;
    $success = "Profil berhasil diperbarui!";
    $kurir = $koneksi->query("SELECT * FROM users WHERE id = $kurir_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Kurir - Warung Makan Rizal</title>
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
                    <a class="nav-link" href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    <a class="nav-link active" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profil Kurir</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card shadow-sm text-center">
                                <div class="card-body">
                                    <i class="fas fa-user-circle fa-5x mb-3" style="color: #6F4E37;"></i>
                                    <h5><?php echo $kurir['nama_lengkap']; ?></h5>
                                    <p class="text-muted mb-0"><?php echo $kurir['email']; ?></p>
                                    <p class="text-muted">Kurir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Profil</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $kurir['nama_lengkap']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Username (tidak bisa diubah)</label>
                                            <input type="text" class="form-control" value="<?php echo $kurir['username']; ?>" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" value="<?php echo $kurir['email']; ?>" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">No. HP</label>
                                            <input type="text" name="no_hp" class="form-control" value="<?php echo $kurir['no_hp']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Alamat</label>
                                            <textarea name="alamat" class="form-control" rows="3"><?php echo $kurir['alamat']; ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                                            <input type="password" name="password" class="form-control">
                                            <small class="text-muted">Minimal 6 karakter</small>
                                        </div>
                                        <button type="submit" class="btn" style="background: #6F4E37; color: white;">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                    </form>
                                </div>
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