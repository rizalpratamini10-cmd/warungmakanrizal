<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/database.php';

// Ambil data untuk ditampilkan
$produk_unggulan = $koneksi->query("SELECT * FROM products WHERE is_available = 1 ORDER BY id DESC LIMIT 6");
$testimoni = $koneksi->query("SELECT t.*, u.nama_lengkap FROM testimonials t JOIN users u ON t.customer_id = u.id WHERE t.is_active = 1 ORDER BY t.created_at DESC LIMIT 3");
$galeri = $koneksi->query("SELECT * FROM gallery ORDER BY id LIMIT 6");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Warung Makan Rizal - Masakan Rumahan Lezat, Harga Bersahabat</title>
    <meta name="description" content="Warung Makan Rizal menyediakan berbagai masakan rumahan khas Indonesia dengan cita rasa lezat dan harga terjangkau di Batam, Kepulauan Riau.">
    <meta name="keywords" content="warung makan, masakan rumahan, kuliner batam, makanan lezat, harga bersahabat">
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" />
    
    <!-- IcoFont -->
    <link rel="stylesheet" href="assets/css/icofont.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- Animate CSS -->
    <link href="assets/css/animate.min.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="assets/css/swiper.min.css">
    
    <!-- Theme Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/typography/poppins-quciksland.css">
    
    <!-- Custom Theme -->
    <link rel="stylesheet" href="assets/css/colors/theme-custom.css">

    <style>
        /* ======================================================
           CUSTOM WARNA WARUNG MAKAN RIZAL - PREMIUM BROWN THEME
        ====================================================== */
        
        :root {
            --primary: #6F4E37;
            --primary-dark: #3E2723;
            --secondary: #A0522D;
            --accent: #D4AF37;
            --bg-light: #FDFBF7;
            --white: #FFFFFF;
        }
        
        /* Override gradient background */
        .gredient-bg, a.comment-reply-link:hover, .single-widget ul.tags-cloud li a:hover, 
        .app-pagenation .page-item.active .page-link, .blog-tag a, #page-header, 
        .social-network .social-icon:hover, .read-more, .featured-2, 
        .pricing-head .wave:nth-of-type(3), .pricing-head .wave, .single-pricing:before, 
        .swiper-pagination-bullet-active, .screenshot-swiper .swiper-button-prev:hover,
        .screenshot-swiper .swiper-button-next:hover, .testimonials, .filled-circle, 
        .bordered-box, .bordered-circle, .bordered-circle2, .hero-area, .loader:before, 
        .circle, .btn-default:hover, .btn-filled {
            background: linear-gradient(135deg, #6F4E37, #A0522D, #D4AF37) !important;
            background-color: #6F4E37 !important;
        }
        
        /* Override gradient color */
        .gredient-color, .single-feature:hover h4, .how-it-box i, .address-box i, 
        .how_works_arrow, .service-box:hover i, .single-feature:hover i, 
        .how-it-box:hover i, .address-box:hover i {
            background: linear-gradient(135deg, #6F4E37, #A0522D, #D4AF37) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }
        
        /* Dark color */
        .dark-purple-color, .single-widget ul a:hover, .post-meta ul li a:hover, 
        .single-blog h2:hover, .page-content h2:hover, .post-comments h2:hover, 
        #testimonials .rotate-heading h2 {
            color: #6F4E37 !important;
        }
        
        /* Primary border color */
        .primary-border-color, .single-widget .form-control:focus, .single-widget h3, 
        .contact-form input:focus, .contact-form textarea:focus, 
        .screenshot-swiper .swiper-button-prev, .screenshot-swiper .swiper-button-next {
            border-color: #6F4E37 !important;
        }
        
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #6F4E37, #A0522D, #D4AF37) !important;
        }
        
        .navbar-brand span {
            background: linear-gradient(135deg, #FFD700, #FFF8DC);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Buttons */
        .btn-default {
            background: transparent;
            border-color: #D4AF37;
            color: white;
        }
        
        .btn-default:hover {
            background: linear-gradient(135deg, #C68E17, #FFD700) !important;
            border-color: transparent;
        }
        
        .btn-filled {
            background: linear-gradient(135deg, #C68E17, #FFD700) !important;
            color: #3E2723 !important;
            font-weight: bold;
        }
        
        /* Product Card */
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .product-img {
            height: 200px;
            overflow: hidden;
            background: #F5F2EB;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-img i {
            font-size: 4rem;
            color: #6F4E37;
        }
        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-info {
            padding: 20px;
        }
        .product-info h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #6F4E37;
        }
        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #C68E17;
            margin-bottom: 15px;
        }
        .btn-add-cart {
            background: linear-gradient(135deg, #C68E17, #FFD700);
            border: none;
            padding: 10px;
            border-radius: 30px;
            color: #3E2723;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        .btn-add-cart:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
        
        /* Hero Stats */
        .hero-stats {
            display: flex;
            gap: 30px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #FFD700;
            display: block;
        }
        .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        /* Cart Badge */
        .cart-badge {
            position: relative;
            color: white !important;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* User Dropdown */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        .user-btn {
            background: rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 8px 16px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            min-width: 200px;
            padding: 8px 0;
            margin-top: 10px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .user-dropdown:hover .dropdown-menu-custom {
            opacity: 1;
            visibility: visible;
        }
        .dropdown-menu-custom a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
        }
        .dropdown-menu-custom a:hover {
            background: #f0f0f0;
        }
        
        /* Food Banner */
        .food-banner {
            background: linear-gradient(135deg, #3E2723, #4E342E);
            padding: 60px 0;
            text-align: center;
            color: #FFD700;
        }
        
        /* Footer */
        .footer {
            background: #2D1F1A !important;
            color: #FDFBF7;
        }
        .footer a {
            color: #D4AF37;
        }
        .footer a:hover {
            color: #FFD700;
        }
        
        body {
            background-color: #FDFBF7;
            color: #3E2723;
        }
        section {
            background-color: #FDFBF7;
        }
        .gray-bg {
            background-color: #F5F2EB !important;
        }
        
        @media (max-width: 768px) {
            .hero-area h2 {
                font-size: 32px;
            }
            .hero-stats {
                justify-content: center;
            }
        }
    </style>
</head>
<body data-spy="scroll" data-target="#navbarCodeply" data-offset="70">

<!-- Preloader -->
<div class="loader-wrapper">
    <div class="loader"></div>
</div>

<!-- Header -->
<header id="home">
    <nav class="navbar navbar-inverse navbar-expand-lg header-nav fixed-top light-header">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/img/logo.png" alt="logo" style="height: 40px;">
                <span>Warung Makan Rizal</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCodeply">
                <i class="icofont-navigation-menu"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarCodeply">
                <ul class="nav navbar-nav ml-auto">
                    <li><a class="nav-link" href="#home">Beranda</a></li>
                    <li><a class="nav-link" href="#about">Tentang</a></li>
                    <li><a class="nav-link" href="#feature">Menu</a></li>
                    <li><a class="nav-link" href="#pricing">Paket</a></li>
                    <li><a class="nav-link" href="#testimonials">Testimoni</a></li>
                    <li><a class="nav-link" href="#contact">Kontak</a></li>
                    <li><a class="nav-link" href="customer/menu.php">Pesan Online</a></li>
                </ul>
                
                <!-- Cart & User -->
                <div class="ml-3 d-flex align-items-center">
                    <a href="customer/keranjang.php" class="cart-badge">
                        <i class="icofont-shopping-cart"></i>
                        <?php if(isset($_SESSION['customer_id'])): 
                            $cart_count = get_cart_count($_SESSION['customer_id']);
                            if($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; endif; ?>
                    </a>
                    
                    <?php if(isset($_SESSION['customer_id'])): ?>
                    <div class="user-dropdown">
                        <div class="user-btn">
                            <i class="icofont-user"></i> <?php echo $_SESSION['customer_nama']; ?>
                            <i class="icofont-rounded-down"></i>
                        </div>
                        <div class="dropdown-menu-custom">
                            <a href="customer/dashboard.php"><i class="icofont-dashboard"></i> Dashboard</a>
                            <a href="customer/profil.php"><i class="icofont-id-card"></i> Profil</a>
                            <a href="customer/pesanan_saya.php"><i class="icofont-history"></i> Pesanan</a>
                            <a href="customer/keranjang.php"><i class="icofont-shopping-cart"></i> Keranjang</a>
                            <hr>
                            <a href="customer/logout.php"><i class="icofont-logout"></i> Logout</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="user-dropdown">
                        <div class="user-btn">
                            <i class="icofont-user"></i> Akun
                            <i class="icofont-rounded-down"></i>
                        </div>
                        <div class="dropdown-menu-custom">
                            <!-- INI SUDAH DIUBAH KE pilih_login.php -->
                            <a href="pilih_login.php"><i class="icofont-login"></i> Login</a>
                            <a href="customer/register.php"><i class="icofont-user-alt-5"></i> Daftar</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- Hero Section -->
<section class="hero-area circle-wrap">
    <div class="circle x1"></div>
    <div class="circle x2"></div>
    <div class="circle x3"></div>
    <div class="circle x4"></div>
    <div class="circle x5"></div>
    <div class="circle x6"></div>
    <div class="circle x7"></div>
    <div class="circle x8"></div>
    <div class="circle x9"></div>
    <div class="circle x10"></div>
    
    <div class="container">
        <div class="row full-height align-items-center">
            <div class="col-md-6 p-100px-t p-50px-b md-p-10px-b">
                <h2 class="text-capitalize m-25px-b">
                    MASAKAN RUMAHAN<br>
                    <span class="gredient-color">LEZAT & BERGIZI</span>
                </h2>
                <p class="m-25px-b">Nikmati berbagai masakan rumahan khas Indonesia dengan cita rasa yang lezat dan harga yang terjangkau. Kami siap melayani Anda setiap hari!</p>
                <div class="hero-btn-wrapper">
                    <a href="customer/menu.php" class="btn btn-default animated-btn">Pesan Sekarang</a>
                    <a href="#feature" class="btn btn-default btn-default-outline animated-btn">Lihat Menu</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">5+</span>
                        <span class="stat-label">Tahun Berdiri</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">1000+</span>
                        <span class="stat-label">Pelanggan Puas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">30+</span>
                        <span class="stat-label">Menu Variasi</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 p-100px-t p-50px-b md-p-10px-t">
                <img class="hero-mock" src="assets/img/hero-mock.png" alt="Hero mockup"/>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="p-100px-tb sm-p-50px-b">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 offset-sm-2">
                <div class="section-title text-center m-50px-b">
                    <h2>Tentang <span class="gredient-color">Warung Makan Rizal</span></h2>
                </div>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="position-relative md-m-50px-b">
                    <div class="bordered-circle"></div>
                    <img class="moveUpDown" src="assets/img/preface.png" alt="Tentang Kami">
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="m-15px-b">Masakan Rumahan Lezat, Harga Bersahabat</h3>
                <p class="m-25px-b">Warung Makan Rizal merupakan usaha kuliner yang menyediakan berbagai masakan rumahan khas Indonesia dengan cita rasa yang lezat dan harga yang terjangkau. Usaha ini telah berdiri sejak Januari 2020 dan berlokasi di samping Kantor Camat Lubuk Baja, Kota Batam, Kepulauan Riau.</p>
                <p>Kami berkomitmen untuk menyediakan makanan yang sehat dan berkualitas dengan mengutamakan kepuasan pelanggan serta menjaga kebersihan dan kenyamanan tempat makan.</p>
                <div class="apps-buttons mt-4">
                    <a href="https://wa.me/6281234567890" class="btn btn-default btn-filled animated-btn">
                        <i class="icofont-brand-whatsapp"></i> WhatsApp<br>Pesan Sekarang
                    </a>
                    <a href="https://instagram.com/warungrizal" class="btn btn-default btn-filled animated-btn">
                        <i class="icofont-brand-instagram"></i> Instagram<br>@warungrizal
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Unggulan Section -->
<section id="feature" class="p-80px-tb position-relative">
    <div class="filled-circle"></div>
    <div class="container">
        <div class="row">
            <div class="col-sm-8 offset-sm-2">
                <div class="section-title text-center m-50px-b">
                    <h2>Menu <span class="gredient-color">Unggulan</span></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if($produk_unggulan && $produk_unggulan->num_rows > 0): ?>
                <?php while($produk = $produk_unggulan->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-img">
                            <?php if(!empty($produk['gambar']) && file_exists('uploads/produk/'.$produk['gambar'])): ?>
                                <img src="uploads/produk/<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama_produk']; ?>">
                            <?php else: ?>
                                <i class="icofont-restaurant"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h4><?php echo htmlspecialchars($produk['nama_produk']); ?></h4>
                            <p class="text-muted small"><?php echo substr(htmlspecialchars($produk['deskripsi'] ?? ''), 0, 70); ?>...</p>
                            <div class="product-price"><?php echo format_rupiah($produk['harga']); ?></div>
                            <button class="btn-add-cart" onclick="addToCart(<?php echo $produk['id']; ?>)">
                                <i class="icofont-cart-alt"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Belum ada produk. Silakan cek lagi nanti.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="customer/menu.php" class="btn btn-default animated-btn">Lihat Semua Menu</a>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- PAKET PEMBELIAN SECTION (TAMBAHAN BARU)   -->
<!-- ========================================== -->
<section id="pricing" class="p-80px-tb parallax bg-overlay opacity-5" style="background-image:url(assets/img/pricing-bg.jpg)">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 offset-sm-2">
                <div class="section-title text-center m-50px-b">
                    <h2>Paket <span class="gredient-color">Hemat</span></h2>
                    <p>Hemat lebih dengan paket spesial dari kami</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-md-center">
            <!-- Paket Ayam -->
            <div class="col-lg-4 col-md-6">
                <div class="single-pricing text-center m-10px-tb">
                    <div class="pricing-head p-60px-lr lg-p-30px-lr">
                        <div class="pricing-head-text">
                            <div class="package-price">
                                <span class="price">🍗</span>
                            </div>
                            <div class="package-name">
                                <h5>Paket Ayam</h5>
                                <p class="small text-white">Nasi + Ayam + Sambal + Es Teh</p>
                            </div>
                        </div>
                        <span class="wave"></span>
                        <span class="wave"></span>
                        <span class="wave"></span>
                    </div>
                    <div class="pricing-body p-60px-lr lg-p-30px-lr">
                        <ul>
                            <li>✅ Nasi Putih</li>
                            <li>✅ Ayam Goreng / Ayam Bakar</li>
                            <li>✅ Sambal & Lalapan</li>
                            <li>✅ Es Teh Manis</li>
                        </ul>
                        <h4 class="mt-3" style="color: #D4AF37;">Rp 20.000</h4>
                    </div>
                    <div class="pricing-footer p-60px-lr lg-p-30px-lr">
                        <a href="customer/add_paket_to_cart.php?paket=ayam" class="btn btn-default btn-filled animated-btn">
                            <i class="icofont-cart-alt"></i> Pesan
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Paket Ikan -->
            <div class="col-lg-4 col-md-6">
                <div class="single-pricing text-center featured-pricing m-10px-tb">
                    <div class="pricing-head p-60px-lr lg-p-30px-lr">
                        <div class="pricing-head-text">
                            <div class="package-price">
                                <span class="price">🐟</span>
                            </div>
                            <div class="package-name">
                                <h5>Paket Ikan</h5>
                                <p class="small text-white">Nasi + Ikan + Sambal + Es Jeruk</p>
                            </div>
                        </div>
                        <span class="wave"></span>
                        <span class="wave"></span>
                        <span class="wave"></span>
                    </div>
                    <div class="pricing-body p-60px-lr lg-p-30px-lr">
                        <ul>
                            <li>✅ Nasi Putih</li>
                            <li>✅ Ikan Bakar / Ikan Goreng</li>
                            <li>✅ Sambal & Lalapan</li>
                            <li>✅ Es Jeruk</li>
                        </ul>
                        <h4 class="mt-3" style="color: #D4AF37;">Rp 22.000</h4>
                    </div>
                    <div class="pricing-footer p-60px-lr lg-p-30px-lr">
                        <a href="customer/add_paket_to_cart.php?paket=ikan" class="btn btn-default btn-filled animated-btn">
                            <i class="icofont-cart-alt"></i> Pesan
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Paket Nasi Goreng -->
            <div class="col-lg-4 col-md-6">
                <div class="single-pricing text-center m-10px-tb">
                    <div class="pricing-head p-60px-lr lg-p-30px-lr">
                        <div class="pricing-head-text">
                            <div class="package-price">
                                <span class="price">🍛</span>
                            </div>
                            <div class="package-name">
                                <h5>Paket Nasi Goreng</h5>
                                <p class="small text-white">Nasi Goreng + Telur + Es Teh</p>
                            </div>
                        </div>
                        <span class="wave"></span>
                        <span class="wave"></span>
                        <span class="wave"></span>
                    </div>
                    <div class="pricing-body p-60px-lr lg-p-30px-lr">
                        <ul>
                            <li>✅ Nasi Goreng Kampung</li>
                            <li>✅ Telur Mata Sapi</li>
                            <li>✅ Kerupuk</li>
                            <li>✅ Es Teh Manis</li>
                        </ul>
                        <h4 class="mt-3" style="color: #D4AF37;">Rp 18.000</h4>
                    </div>
                    <div class="pricing-footer p-60px-lr lg-p-30px-lr">
                        <a href="customer/add_paket_to_cart.php?paket=nasgor" class="btn btn-default btn-filled animated-btn">
                            <i class="icofont-cart-alt"></i> Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-md-center mt-3">
            <!-- Paket Soto -->
            <div class="col-lg-4 col-md-6">
                <div class="single-pricing text-center m-10px-tb">
                    <div class="pricing-head p-60px-lr lg-p-30px-lr">
                        <div class="pricing-head-text">
                            <div class="package-price">
                                <span class="price">🍜</span>
                            </div>
                            <div class="package-name">
                                <h5>Paket Soto</h5>
                                <p class="small text-white">Soto Ayam + Nasi + Es Teh</p>
                            </div>
                        </div>
                        <span class="wave"></span>
                        <span class="wave"></span>
                        <span class="wave"></span>
                    </div>
                    <div class="pricing-body p-60px-lr lg-p-30px-lr">
                        <ul>
                            <li>✅ Soto Ayam</li>
                            <li>✅ Nasi Putih</li>
                            <li>✅ Perkedel & Telur</li>
                            <li>✅ Es Teh Manis</li>
                        </ul>
                        <h4 class="mt-3" style="color: #D4AF37;">Rp 20.000</h4>
                    </div>
                    <div class="pricing-footer p-60px-lr lg-p-30px-lr">
                        <a href="customer/add_paket_to_cart.php?paket=soto" class="btn btn-default btn-filled animated-btn">
                            <i class="icofont-cart-alt"></i> Pesan
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Paket Keluarga -->
            <div class="col-lg-4 col-md-6">
                <div class="single-pricing text-center featured-pricing m-10px-tb">
                    <div class="pricing-head p-60px-lr lg-p-30px-lr">
                        <div class="pricing-head-text">
                            <div class="package-price">
                                <span class="price">👨‍👩‍👧‍👦</span>
                            </div>
                            <div class="package-name">
                                <h5>Paket Keluarga</h5>
                                <p class="small text-white">Untuk 4 Orang</p>
                            </div>
                        </div>
                        <span class="wave"></span>
                        <span class="wave"></span>
                        <span class="wave"></span>
                    </div>
                    <div class="pricing-body p-60px-lr lg-p-30px-lr">
                        <ul>
                            <li>✅ Nasi Putih (4 porsi)</li>
                            <li>✅ Ayam Goreng (2 potong)</li>
                            <li>✅ Ikan Bakar (1 ekor)</li>
                            <li>✅ Tahu + Tempe Goreng</li>
                            <li>✅ Sayur Asem</li>
                            <li>✅ Es Teh (4 gelas)</li>
                        </ul>
                        <h4 class="mt-3" style="color: #D4AF37;">Rp 70.000</h4>
                    </div>
                    <div class="pricing-footer p-60px-lr lg-p-30px-lr">
                        <a href="customer/add_paket_to_cart.php?paket=keluarga" class="btn btn-default btn-filled animated-btn">
                            <i class="icofont-cart-alt"></i> Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Food Banner -->
<section class="food-banner">
    <div class="container">
        <h2 class="m-15px-b">🍚 Makanan Rumahan Lezat</h2>
        <p class="m-25px-b">Kualitas terbaik, harga bersahabat, siap memanjakan lidah Anda</p>
    </div>
</section>

<!-- Testimoni Section -->
<section id="testimonials" class="p-175px-tb md-p-80px-tb position-relative testimonials">
    <div class="container">
        <div class="row">
            <div class="rotate-heading">
                <h2>Testimoni<br>Pelanggan</h2>
            </div>
            <div class="col-lg-8 offset-lg-2 swiper-container testimonialSwiper p-50px-b">
                <div class="swiper-wrapper">
                    <?php if($testimoni && $testimoni->num_rows > 0): ?>
                        <?php while($t = $testimoni->fetch_assoc()): ?>
                        <div class="single-testimonial swiper-slide">
                            <div class="row">
                                <div class="col-lg-2 col-md-3">
                                    <img class="rounded-circle" src="assets/img/avater1.jpeg" alt="">
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <p>“ <?php echo htmlspecialchars($t['komentar']); ?> ”</p>
                                    <h5><?php echo htmlspecialchars($t['nama_lengkap']); ?></h5>
                                    <p class="ratings">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="icofont-ui-rating"></i>
                                        <?php endfor; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="single-testimonial swiper-slide">
                            <div class="row">
                                <div class="col-lg-2 col-md-3">
                                    <img class="rounded-circle" src="assets/img/avater1.jpeg" alt="">
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <p>“ Makanannya enak banget, rasanya seperti masakan rumahan. Harga juga terjangkau, recommended! ”</p>
                                    <h5>Budi Setiawan</h5>
                                    <p class="ratings"><i class="icofont-ui-rating"></i><i class="icofont-ui-rating"></i><i class="icofont-ui-rating"></i><i class="icofont-ui-rating"></i><i class="icofont-ui-rating"></i></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="p-80px-tb">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 offset-sm-2">
                <div class="section-title text-center m-50px-b">
                    <h2>Hubungi <span class="gredient-color">Kami</span></h2>
                </div>
            </div>
        </div>
        <div class="row row-eq-height">
            <div class="col-lg-4 col-md-6 contact-address p-30px">
                <div class="address-box text-center p-15px m-15px-b">
                    <i class="icofont-google-map"></i>
                    <h5>Alamat</h5>
                    <p>Samping Kantor Camat Lubuk Baja, Kota Batam, Kepulauan Riau</p>
                </div>
                <div class="address-box text-center p-15px m-15px-b">
                    <i class="icofont-whatsapp"></i>
                    <h5>WhatsApp</h5>
                    <p>+62 812 3456 7890</p>
                </div>
                <div class="address-box text-center p-15px">
                    <i class="icofont-instagram"></i>
                    <h5>Instagram</h5>
                    <p>@warungrizal</p>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 contact-form p-30px">
                <h3 class="m-25px-b">Kirim Pesan</h3>
                <p class="m-25px-b">Ada pertanyaan? Hubungi kami melalui form di bawah.</p>
                <form id="contact-form" method="post" action="proses/kirim_pesan.php">
                    <div class="mb13">
                        <input name="name" class="contact-name" type="text" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="mb13">
                        <input name="email" class="contact-email" type="email" placeholder="Email" required>
                    </div>
                    <div class="mb13">
                        <input name="subject" class="contact-subject" type="text" placeholder="Subject" required>
                    </div>
                    <div class="mb30">
                        <textarea name="message" class="contact-message" placeholder="Pesan Anda" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-default btn-filled animated-btn">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Download App Section -->
<section class="food-banner" style="background: linear-gradient(135deg, #3E2723, #2D1F1A);">
    <div class="container">
        <h2 class="m-15px-b">📱 Download Aplikasi</h2>
        <p class="m-25px-b">Pesan lebih mudah melalui aplikasi mobile kami</p>
        <a href="download.php" class="btn btn-default animated-btn">
            <i class="icofont-android"></i> Download APK
        </a>
    </div>
</section>

<!-- Footer -->
<footer id="footer" class="p-70px-t p-30px-b footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="footer-top text-center p-30px-tb">
                    <a class="footer-logo" href="#"><img src="assets/img/logo.png" alt="Warung Makan Rizal"></a>
                    <p>"Masakan Rumahan Lezat, Harga Bersahabat"</p>
                    <div class="social-network">
                        <a href="#"><i class="social-icon icofont-facebook"></i></a>
                        <a href="https://instagram.com/warungrizal"><i class="social-icon icofont-instagram"></i></a>
                        <a href="https://wa.me/6281234567890"><i class="social-icon icofont-whatsapp"></i></a>
                        <a href="#"><i class="social-icon icofont-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-copyright p-30px-tb text-center">
                    <p>Copyright &copy; <?php echo date('Y'); ?> Warung Makan Rizal | Design by <a href="#">ThemeAtelier</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>

<div id="toastNotif" class="toast-notif"></div>

<!-- Scripts -->
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/jquery-migrate-3.0.0.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.textillate.js"></script>
<script src="assets/js/jquery.lettering.js"></script>
<script src="assets/js/jquery.fittext.js"></script>
<script src="assets/js/jquery.ajaxchimp.min.js"></script>
<script src="assets/js/swiper.min.js"></script>
<script src="assets/js/custom.js"></script>

<script>
function showToast(message) {
    var toast = document.getElementById('toastNotif');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(function() { toast.classList.remove('show'); }, 3000);
}

function addToCart(produkId) {
    <?php if(!isset($_SESSION['customer_id'])): ?>
        if(confirm('Silakan login terlebih dahulu untuk berbelanja.')) {
            window.location.href = 'pilih_login.php?redirect=' + encodeURIComponent(window.location.href);
        }
        return;
    <?php endif; ?>
    
    fetch('api/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + produkId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast('Produk ditambahkan ke keranjang!');
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            showToast(data.message || 'Gagal menambahkan');
        }
    });
}

document.getElementById('contact-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Terima kasih! Pesan Anda akan segera kami balas.');
    this.reset();
});
</script>
</body>
</html>