<?php
include '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// ============================================
// PROSES TAMBAH PRODUK
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori = intval($_POST['kategori_id']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    
    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/produk/";
        
        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate nama file unik
        $ekstensi = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '_' . uniqid() . '.' . $ekstensi;
        $target_file = $target_dir . $gambar;
        
        // Upload file
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // Upload berhasil
        } else {
            $error = "Gagal upload gambar";
        }
    }
    
    $query = "INSERT INTO products (nama_produk, kategori_id, deskripsi, harga, stok, gambar, is_available) 
              VALUES ('$nama', '$kategori', '$deskripsi', '$harga', '$stok', '$gambar', 1)";
    
    if ($koneksi->query($query)) {
        $success = "Produk berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan produk: " . $koneksi->error;
    }
}

// ============================================
// PROSES EDIT PRODUK
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori = intval($_POST['kategori_id']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Cek apakah upload gambar baru
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/produk/";
        
        // Hapus gambar lama
        $old = $koneksi->query("SELECT gambar FROM products WHERE id = $id")->fetch_assoc();
        if ($old['gambar'] && file_exists($target_dir . $old['gambar'])) {
            unlink($target_dir . $old['gambar']);
        }
        
        // Upload gambar baru
        $ekstensi = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '_' . uniqid() . '.' . $ekstensi;
        $target_file = $target_dir . $gambar;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file);
        
        // Update dengan gambar baru
        $query = "UPDATE products SET 
                  nama_produk = '$nama', 
                  kategori_id = '$kategori', 
                  deskripsi = '$deskripsi', 
                  harga = '$harga', 
                  stok = '$stok', 
                  is_available = '$is_available',
                  gambar = '$gambar'
                  WHERE id = $id";
    } else {
        // Update tanpa mengubah gambar
        $query = "UPDATE products SET 
                  nama_produk = '$nama', 
                  kategori_id = '$kategori', 
                  deskripsi = '$deskripsi', 
                  harga = '$harga', 
                  stok = '$stok', 
                  is_available = '$is_available'
                  WHERE id = $id";
    }
    
    if ($koneksi->query($query)) {
        $success = "Produk berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate produk: " . $koneksi->error;
    }
}

// ============================================
// PROSES HAPUS PRODUK
// ============================================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    // Hapus gambar dari folder
    $gambar = $koneksi->query("SELECT gambar FROM products WHERE id = $id")->fetch_assoc();
    if ($gambar['gambar'] && file_exists("../uploads/produk/" . $gambar['gambar'])) {
        unlink("../uploads/produk/" . $gambar['gambar']);
    }
    
    // Hapus dari database
    if ($koneksi->query("DELETE FROM products WHERE id = $id")) {
        header("Location: produk.php?msg=deleted");
        exit();
    }
}

// ============================================
// AMBIL DATA PRODUK
// ============================================
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kategori_filter = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

$query = "SELECT p.*, c.nama_kategori 
          FROM products p 
          LEFT JOIN categories c ON p.kategori_id = c.id 
          WHERE 1=1";

if ($search) {
    $query .= " AND p.nama_produk LIKE '%$search%'";
}
if ($kategori_filter > 0) {
    $query .= " AND p.kategori_id = $kategori_filter";
}
$query .= " ORDER BY p.id DESC";

$products = $koneksi->query($query);
$categories = $koneksi->query("SELECT * FROM categories ORDER BY nama_kategori");

// Data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_data = $koneksi->query("SELECT * FROM products WHERE id = $edit_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body { background: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar Styles */
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
        
        /* Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
        }
        
        /* Product Image */
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background: #f0f0f0;
        }
        
        /* Modal */
        .modal-header-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: white;
        }
        
        .btn-primary-custom {
            background: #6F4E37;
            border: none;
            color: white;
        }
        .btn-primary-custom:hover {
            background: #A0522D;
            color: white;
        }
        
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        
        .table-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
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
                    <a class="nav-link active" href="produk.php"><i class="fas fa-utensils"></i> Produk</a>
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
                <!-- Navbar -->
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-utensils me-2"></i>Manajemen Produk</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x me-2"></i>
                        <span><?php echo $_SESSION['admin_nama']; ?></span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Alert Success/Error -->
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
                    
                    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> Produk berhasil dihapus!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Header dengan Tombol Tambah -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Daftar Produk</h4>
                        <button class="btn" style="background: #6F4E37; color: white;" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fas fa-plus"></i> Tambah Produk
                        </button>
                    </div>
                    
                    <!-- Search dan Filter -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?php echo $search; ?>">
                                        <button type="submit" class="btn" style="background: #6F4E37; color: white;">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                                        <option value="0">Semua Kategori</option>
                                        <?php 
                                        $cats = $koneksi->query("SELECT * FROM categories ORDER BY nama_kategori");
                                        while($cat = $cats->fetch_assoc()): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo $kategori_filter == $cat['id'] ? 'selected' : ''; ?>>
                                                <?php echo $cat['nama_kategori']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <?php if($search || $kategori_filter): ?>
                                <div class="col-md-2">
                                    <a href="produk.php" class="btn btn-secondary w-100">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabel Produk -->
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">ID</th>
                                            <th width="80">Gambar</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th>Status</th>
                                            <th width="120">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($products->num_rows > 0): ?>
                                            <?php while($p = $products->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $p['id']; ?></td>
                                                <td>
                                                    <?php if($p['gambar'] && file_exists("../uploads/produk/".$p['gambar'])): ?>
                                                        <img src="../uploads/produk/<?php echo $p['gambar']; ?>" class="table-img" alt="<?php echo $p['nama_produk']; ?>">
                                                    <?php else: ?>
                                                        <div class="table-img bg-light d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-image fa-2x text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo $p['nama_produk']; ?></strong><br>
                                                    <small class="text-muted"><?php echo substr($p['deskripsi'], 0, 50); ?>...</small>
                                                </td>
                                                <td><?php echo $p['nama_kategori']; ?></td>
                                                <td><?php echo format_rupiah($p['harga']); ?></td>
                                                <td>
                                                    <?php if($p['stok'] <= 0): ?>
                                                        <span class="text-danger">Habis</span>
                                                    <?php elseif($p['stok'] <= 5): ?>
                                                        <span class="text-warning"><?php echo $p['stok']; ?> (Segera Habis)</span>
                                                    <?php else: ?>
                                                        <?php echo $p['stok']; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($p['is_available']): ?>
                                                        <span class="badge bg-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?hapus=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus produk <?php echo $p['nama_produk']; ?>?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <i class="fas fa-box-open fa-4x text-muted mb-3 d-block"></i>
                                                    <h5>Belum ada produk</h5>
                                                    <p class="text-muted">Silakan tambah produk baru dengan klik tombol "Tambah Produk"</p>
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
    
    <!-- ============================================ -->
    <!-- MODAL TAMBAH PRODUK -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Produk Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_produk" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori_id" class="form-select" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php 
                                        $kats = $koneksi->query("SELECT * FROM categories ORDER BY nama_kategori");
                                        while($kat = $kats->fetch_assoc()): ?>
                                            <option value="<?php echo $kat['id']; ?>"><?php echo $kat['nama_kategori']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi produk..."></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                                            <input type="number" name="harga" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                                            <input type="number" name="stok" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Gambar Produk</label>
                                    <div class="border rounded p-3 text-center" style="min-height: 200px;">
                                        <i class="fas fa-image fa-4x text-muted mb-2 d-block"></i>
                                        <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImage(this)">
                                        <small class="text-muted">Format: JPG, PNG, JPEG<br>Max: 2MB</small>
                                        <div id="imagePreview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary-custom">
                            <i class="fas fa-save"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ============================================ -->
    <!-- MODAL EDIT PRODUK -->
    <!-- ============================================ -->
    <?php if($edit_data): ?>
    <div class="modal fade show" id="modalEdit" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Produk</h5>
                    <a href="produk.php" class="btn-close btn-close-white"></a>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_produk" class="form-control" value="<?php echo $edit_data['nama_produk']; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori_id" class="form-select" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php 
                                        $kats = $koneksi->query("SELECT * FROM categories ORDER BY nama_kategori");
                                        while($kat = $kats->fetch_assoc()): ?>
                                            <option value="<?php echo $kat['id']; ?>" <?php echo $edit_data['kategori_id'] == $kat['id'] ? 'selected' : ''; ?>>
                                                <?php echo $kat['nama_kategori']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="3"><?php echo $edit_data['deskripsi']; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                                            <input type="number" name="harga" class="form-control" value="<?php echo $edit_data['harga']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                                            <input type="number" name="stok" class="form-control" value="<?php echo $edit_data['stok']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="is_available" class="form-check-input" id="is_available" <?php echo $edit_data['is_available'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_available">Produk Tersedia (Aktif)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Gambar Saat Ini</label>
                                    <div class="border rounded p-3 text-center">
                                        <?php if($edit_data['gambar'] && file_exists("../uploads/produk/".$edit_data['gambar'])): ?>
                                            <img src="../uploads/produk/<?php echo $edit_data['gambar']; ?>" class="img-fluid rounded" style="max-height: 150px;">
                                            <p class="text-muted small mt-2"><?php echo $edit_data['gambar']; ?></p>
                                        <?php else: ?>
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                            <p class="text-muted mt-2">Tidak ada gambar</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ganti Gambar (opsional)</label>
                                    <div class="border rounded p-3 text-center">
                                        <i class="fas fa-upload fa-3x text-muted mb-2 d-block"></i>
                                        <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImageEdit(this)">
                                        <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar</small>
                                        <div id="imagePreviewEdit" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="produk.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" name="edit" class="btn btn-primary-custom">
                            <i class="fas fa-save"></i> Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview gambar sebelum upload (Tambah)
        function previewImage(input) {
            var preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 120px;">';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '';
            }
        }
        
        // Preview gambar sebelum upload (Edit)
        function previewImageEdit(input) {
            var preview = document.getElementById('imagePreviewEdit');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded mt-2" style="max-height: 100px;"><small class="text-success d-block">Gambar baru akan mengganti yang lama</small>';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '';
            }
        }
    </script>
</body>
</html>