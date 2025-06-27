<?php
session_start();

// If user is already logged in as admin, redirect to admin panel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin/dashboard.php');
    exit();
}

// If user is already logged in as regular user, redirect to dashboard
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Jangan redirect ke login.php di sini!
// Biarkan HTML di bawah tetap tampil untuk pengunjung umum
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketVision - Monitor Harga Sembako Real-time Kendal</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><i class="fas fa-chart-line"></i> MarketVision</h2>
            </div>
            
            <div class="nav-search">
                <div class="search-container">
                    <input type="text" placeholder="Cari produk sembako atau pasar..." class="search-input">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="nav-menu">
                <a href="#beranda" class="nav-link">Beranda</a>
                <a href="#harga" class="nav-link">Monitor Harga</a>
                <a href="#pasar" class="nav-link">Daftar Pasar</a>
                <a href="#tentang" class="nav-link">Tentang</a>
                <a href="login.php" class="nav-link login-btn">
                    <i class="fas fa-user"></i> Login
                </a>
                <a href="#" id="toggle-dark" class="btn btn-secondary" style="margin-left: 1rem; font-size: 1.2rem; display: flex; align-items: center; gap: 6px;"><span id="dark-icon">🌙</span> <span id="dark-label">Dark Mode</span></a>
            </div>
            
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="hero-container">
            <div class="hero-content">
                <h1 class="hero-title">Monitor Harga <span class="highlight">Sembako Real-time</span></h1>
                <p class="hero-subtitle">Sistem monitoring harga sembako (beras, gula, minyak, telur, dll.) di sejumlah pasar tradisional Kendal secara real-time. Bandingkan harga antar pasar dan rencanakan belanja dengan lebih efisien.</p>
                <div class="hero-buttons">
                    <a href="#harga" class="btn btn-primary">Lihat Harga Terkini</a>
                    <a href="#pasar" class="btn btn-secondary">Daftar Pasar</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Harga Real-time</h3>
                    <p>Update harga setiap hari</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">apa itu MarketVision?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Update Real-time</h3>
                    <p>Harga sembako diupdate setiap hari dari berbagai pasar tradisional di Kendal untuk informasi yang akurat dan terkini.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3>Bandingkan Harga</h3>
                    <p>Bandingkan harga sembako antar pasar dengan mudah untuk mendapatkan harga terbaik dan menghemat pengeluaran.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Lokasi Pasar</h3>
                    <p>Informasi lengkap lokasi pasar tradisional di Kendal beserta jam operasional dan fasilitas yang tersedia.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Akses Mudah</h3>
                    <p>Akses informasi harga sembako kapan saja dan di mana saja melalui website yang responsif dan mudah digunakan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Price Monitoring Section -->
    <section class="products" id="harga">
        <div class="container">
            <h2 class="section-title">Monitor Harga Sembako</h2>
            <div class="price-filters">
                <button class="filter-btn active" data-category="all">Semua</button>
                <button class="filter-btn" data-category="beras">Beras</button>
                <button class="filter-btn" data-category="gula">Gula</button>
                <button class="filter-btn" data-category="minyak">Minyak</button>
                <button class="filter-btn" data-category="telur">Telur</button>
            </div>
            <div class="products-grid">
                <div class="product-card" data-category="beras">
                    <div class="product-image">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="product-info">
                        <h3>Beras Premium</h3>
                        <p>Beras putih berkualitas premium</p>
                        <div class="price-comparison">
                            <span class="price">Pasar Baru: Rp 12.500/kg</span>
                            <span class="price">Pasar Lama: Rp 12.000/kg</span>
                        </div>
                    </div>
                </div>
                <div class="product-card" data-category="gula">
                    <div class="product-image">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="product-info">
                        <h3>Gula Pasir</h3>
                        <p>Gula pasir putih berkualitas</p>
                        <div class="price-comparison">
                            <span class="price">Pasar Baru: Rp 13.500/kg</span>
                            <span class="price">Pasar Lama: Rp 13.000/kg</span>
                        </div>
                    </div>
                </div>
                <div class="product-card" data-category="minyak">
                    <div class="product-image">
                        <i class="fas fa-oil-can"></i>
                    </div>
                    <div class="product-info">
                        <h3>Minyak Goreng</h3>
                        <p>Minyak goreng curah segar</p>
                        <div class="price-comparison">
                            <span class="price">Pasar Baru: Rp 14.000/liter</span>
                            <span class="price">Pasar Lama: Rp 13.500/liter</span>
                        </div>
                    </div>
                </div>
                <div class="product-card" data-category="telur">
                    <div class="product-image">
                        <i class="fas fa-egg"></i>
                    </div>
                    <div class="product-info">
                        <h3>Telur Ayam</h3>
                        <p>Telur ayam negeri segar</p>
                        <div class="price-comparison">
                            <span class="price">Pasar Baru: Rp 28.000/kg</span>
                            <span class="price">Pasar Lama: Rp 27.500/kg</span>
                        </div>
                    </div>
                </div>
                <div class="product-card" data-category="beras">
                    <div class="product-image">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="product-info">
                        <h3>Beras Medium</h3>
                        <p>Beras putih kualitas medium</p>
                        <div class="price-comparison">
                            <span class="price">Pasar Baru: Rp 11.000/kg</span>
                            <span class="price">Pasar Lama: Rp 10.500/kg</span>
                        </div>
                    </div>
                </div>
                <div class="product-card" data-category="gula">
                    <div class="product-image">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="product-info">
                        <h3>Gula Merah</h3>
                        <p>Gula merah/gula jawa asli</p>
                        <div class="price-comparison">
                            <span class="price">Pasar Baru: Rp 18.000/kg</span>
                            <span class="price">Pasar Lama: Rp 17.500/kg</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <a href="#harga" class="btn btn-primary">Lihat Harga Terkini</a>
            </div>
        </div>
    </section>

    <!-- Markets Section -->
    <section class="markets" id="pasar">
        <div class="container">
            <h2 class="section-title">Pasar Tradisional Kendal</h2>
            <div class="markets-grid">
                <div class="market-card">
                    <div class="market-image">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="market-info">
                        <h3>Pasar Baru Kendal</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Pasar Baru No. 123, Kendal</p>
                        <p><i class="fas fa-clock"></i> 06:00 - 17:00 WIB</p>
                        <p><i class="fas fa-phone"></i> +62 294 123456</p>
                        <div class="market-stats">
                            <span class="stat">150+ Pedagang</span>
                            <span class="stat">50+ Produk</span>
                        </div>
                    </div>
                </div>
                <div class="market-card">
                    <div class="market-image">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="market-info">
                        <h3>Pasar Lama Kendal</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Pasar Lama No. 45, Kendal</p>
                        <p><i class="fas fa-clock"></i> 05:00 - 18:00 WIB</p>
                        <p><i class="fas fa-phone"></i> +62 294 123457</p>
                        <div class="market-stats">
                            <span class="stat">200+ Pedagang</span>
                            <span class="stat">75+ Produk</span>
                        </div>
                    </div>
                </div>
                <div class="market-card">
                    <div class="market-image">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="market-info">
                        <h3>Pasar Weleri</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Weleri No. 67, Weleri</p>
                        <p><i class="fas fa-clock"></i> 06:00 - 16:00 WIB</p>
                        <p><i class="fas fa-phone"></i> +62 294 123458</p>
                        <div class="market-stats">
                            <span class="stat">100+ Pedagang</span>
                            <span class="stat">40+ Produk</span>
                        </div>
                    </div>
                </div>
                <div class="market-card">
                    <div class="market-image">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="market-info">
                        <h3>Pasar Kaliwungu</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Pasar Kaliwungu No. 89, Kaliwungu</p>
                        <p><i class="fas fa-clock"></i> 05:30 - 17:30 WIB</p>
                        <p><i class="fas fa-phone"></i> +62 294 123459</p>
                        <div class="market-stats">
                            <span class="stat">120+ Pedagang</span>
                            <span class="stat">55+ Produk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="tentang">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Tentang MarketVision</h2>
                    <p>MarketVision adalah sistem monitoring harga sembako real-time yang membantu masyarakat Kendal dalam memantau harga sembako (beras, gula, minyak, telur, dll.) di sejumlah pasar tradisional secara real-time.</p>
                    <p>Dengan adanya sistem ini, pengguna bisa membandingkan harga antar pasar dan merencanakan belanja dengan lebih efisien, sehingga dapat menghemat pengeluaran dan mendapatkan harga terbaik.</p>
                    <div class="stats">
                        <div class="stat">
                            <h3>4</h3>
                            <p>Pasar Terdaftar</p>
                        </div>
                        <div class="stat">
                            <h3>50+</h3>
                            <p>Produk Sembako</p>
                        </div>
                        <div class="stat">
                            <h3>Real-time</h3>
                            <p>Update Harga</p>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <div class="about-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Monitor Harga</h3>
                        <p>Informasi harga terkini setiap hari</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="kontak">
        <div class="container">
            <h2 class="section-title">Hubungi Kami</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Alamat</h3>
                            <p>Jl. Dahlia No 95 Botomulyo Cepiring Kendal</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Telepon</h3>
                            <p>+6285951491116</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>info@marketvision.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Jam Operasional</h3>
                            <p>Senin - Minggu: 06:00 - 18:00 WIB</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <form action="process_contact.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="nama" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <textarea name="pesan" placeholder="Pesan Anda" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-chart-line"></i> MarketVision</h3>
                    <p>Sistem monitoring harga sembako real-time untuk pasar tradisional Kendal.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="#">Monitor Harga</a></li>
                        <li><a href="#">Bandingkan Harga</a></li>
                        <li><a href="#">Info Pasar</a></li>
                        <li><a href="#">Update Real-time</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Produk Sembako</h4>
                    <ul>
                        <li><a href="#">Beras</a></li>
                        <li><a href="#">Gula</a></li>
                        <li><a href="#">Minyak Goreng</a></li>
                        <li><a href="#">Telur</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Download App</h4>
                    <p>Dapatkan notifikasi harga terbaru dengan aplikasi mobile kami.</p>
                    <div class="app-buttons">
                        <a href="#" class="app-btn">
                            <i class="fab fa-google-play"></i>
                            <span>Google Play</span>
                        </a>
                        <a href="#" class="app-btn">
                            <i class="fab fa-apple"></i>
                            <span>App Store</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 MarketVision. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <button id="toggle-dark" class="btn btn-secondary" style="margin-left: 1rem;">🌙/☀️</button>
    <script>
    document.getElementById('toggle-dark').onclick = function() {
        document.body.classList.toggle('dark-mode');
        if(document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
        } else {
            localStorage.setItem('theme', 'light');
        }
    };
    if(localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
    </script>
    <script src="script.js"></script>
</body>
</html> 