<?php
session_start();
include 'koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Get all products for display
$sql = "SELECT * FROM harga_sembako ORDER BY tanggal_update DESC";
$result = $conn->query($sql);
$all_products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_products[] = $row;
    }
}

// Group products by name for display
$grouped_products = [];
if (!empty($all_products)) {
    foreach ($all_products as $product) {
        $product_name = $product['nama_produk'];
        if (!isset($grouped_products[$product_name])) {
            $grouped_products[$product_name] = [];
        }
        $grouped_products[$product_name][] = $product;
    }
}

// Get unique markets for filter
$markets = ['Pasar Cepiring', 'Pasar Kendal', 'Pasar Kaliwungu', 'Pasar Weleri'];
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
            <div class="nav-logo" style="display: flex; align-items: center;">
                <img src="admin/kendal logo.png" alt="Logo Kabupaten Kendal" style="height:48px; margin-right: 16px;">
                <h2><i class="fas fa-chart-line"></i> MarketVision</h2>
            </div>
            
            <div class="nav-search">
                <form method="GET" action="cari.php" class="search-container">
                    <input type="text" name="q" placeholder="Cari produk sembako atau pasar..." class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <div class="nav-menu">
                <a href="#beranda" class="nav-link">Beranda</a>
                <a href="#harga" class="nav-link">Monitor Harga</a>
                <a href="#pasar" class="nav-link">Daftar Pasar</a>
                <a href="#tentang" class="nav-link">Tentang</a>
                <div class="user-menu">
                    <span class="user-name">Selamat datang, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Pengguna'); ?></span>
                    <a href="logout.php" class="nav-link logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
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
                <?php if (!empty($grouped_products)): ?>
                    <?php foreach ($grouped_products as $product_name => $prices): ?>
                        <div class="product-card" data-category="<?php echo strtolower(explode(' ', $product_name)[0]); ?>">
                            <div class="product-image">
                                <?php 
                                $icon = 'fas fa-box';
                                if (stripos($product_name, 'beras') !== false) $icon = 'fas fa-seedling';
                                elseif (stripos($product_name, 'gula') !== false) $icon = 'fas fa-cubes';
                                elseif (stripos($product_name, 'minyak') !== false) $icon = 'fas fa-oil-can';
                                elseif (stripos($product_name, 'telur') !== false) $icon = 'fas fa-egg';
                                ?>
                                <i class="<?php echo $icon; ?>"></i>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product_name); ?></h3>
                                <?php
                                $available_prices = [];
                                foreach ($prices as $price) {
                                    $available_prices[] = $price['harga'];
                                }
                                $lowest_price = !empty($available_prices) ? min($available_prices) : 0;
                                ?>
                                <p>Mulai dari: <span class="lowest-price">Rp <?php echo number_format($lowest_price, 0, ',', '.'); ?></span></p>
                                <button class="btn-details" data-details='<?php echo htmlspecialchars(json_encode($prices), ENT_QUOTES, 'UTF-8'); ?>' data-product-name="<?php echo htmlspecialchars($product_name); ?>">
                                    Lihat Rincian Harga
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-database"></i>
                        <p>Belum ada data harga sembako tersedia.</p>
                        <p>Silakan hubungi admin untuk menambahkan data harga.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Markets Section -->
    <section class="markets" id="pasar">
        <div class="container">
            <h2 class="section-title">Daftar Pasar Tradisional</h2>
            <div class="markets-grid">
                <div class="market-card">
                    <div class="market-image">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="market-info">
                        <h3>
                            <a href="https://maps.google.com/?q=35F4+95V,+Jl.+Raya+Soekarno-Hatta,+Cepiring+Tengah,+Cepiring,+Kec.+Cepiring,+Kabupaten+Kendal,+Jawa+Tengah+51352" target="_blank" class="market-link">
                                Pasar Cepiring <i class="fas fa-external-link-alt"></i>
                            </a>
                        </h3>
                        <p>
                            <a href="https://maps.google.com/?q=35F4+95V,+Jl.+Raya+Soekarno-Hatta,+Cepiring+Tengah,+Cepiring,+Kec.+Cepiring,+Kabupaten+Kendal,+Jawa+Tengah+51352" target="_blank" class="market-link">
                                <i class="fas fa-map-marker-alt"></i> Jl. Raya Soekarno-Hatta, Cepiring Tengah, Cepiring, Kendal
                            </a>
                        </p>
                        <p><i class="fas fa-clock"></i> 06:00 - 17:00 WIB</p>
                        <p><i class="fas fa-phone"></i> +62 294 111111</p>
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
                        <h3>
                            <a href="https://maps.google.com/?q=35JX+8PV,+Jl.+Raya+Soekarno-Hatta,+Pekauman,+Pakauman,+Kec.+Kendal,+Kabupaten+Kendal,+Jawa+Tengah+51318" target="_blank" class="market-link">
                                Pasar Kendal <i class="fas fa-external-link-alt"></i>
                            </a>
                        </h3>
                        <p>
                            <a href="https://maps.google.com/?q=35JX+8PV,+Jl.+Raya+Soekarno-Hatta,+Pekauman,+Pakauman,+Kec.+Kendal,+Kabupaten+Kendal,+Jawa+Tengah+51318" target="_blank" class="market-link">
                                <i class="fas fa-map-marker-alt"></i> Jl. Raya Soekarno-Hatta, Pekauman, Kendal
                            </a>
                        </p>
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
                        <h3>
                            <a href="https://maps.google.com/?q=GJQC+65X,+Canggal,+Jetis,+Kec.+Kaliwungu,+Kabupaten+Semarang,+Jawa+Tengah+50229" target="_blank" class="market-link">
                                Pasar Kaliwungu <i class="fas fa-external-link-alt"></i>
                            </a>
                        </h3>
                        <p>
                            <a href="https://maps.google.com/?q=GJQC+65X,+Canggal,+Jetis,+Kec.+Kaliwungu,+Kabupaten+Semarang,+Jawa+Tengah+50229" target="_blank" class="market-link">
                                <i class="fas fa-map-marker-alt"></i> Canggal, Jetis, Kec. Kaliwungu, Semarang
                            </a>
                        </p>
                        <p><i class="fas fa-clock"></i> 05:30 - 17:30 WIB</p>
                        <p><i class="fas fa-phone"></i> +62 294 123459</p>
                        <div class="market-stats">
                            <span class="stat">120+ Pedagang</span>
                            <span class="stat">55+ Produk</span>
                        </div>
                    </div>
                </div>
                <div class="market-card">
                    <div class="market-image">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="market-info">
                        <h3>
                            <a href="https://maps.google.com/?q=23GC+599,+Krajan,+Penyangkringan,+Kec.+Weleri,+Kabupaten+Kendal,+Jawa+Tengah+51355" target="_blank" class="market-link">
                                Pasar Weleri <i class="fas fa-external-link-alt"></i>
                            </a>
                        </h3>
                        <p>
                            <a href="https://maps.google.com/?q=23GC+599,+Krajan,+Penyangkringan,+Kec.+Weleri,+Kabupaten+Kendal,+Jawa+Tengah+51355" target="_blank" class="market-link">
                                <i class="fas fa-map-marker-alt"></i> Krajan, Penyangkringan, Kec. Weleri, Kendal
                            </a>
                        </p>
                        <p><i class="fas fa-clock"></i> 06:00 - 16:00 WIB</p>
                        <p><i class="fas fa-phone"></i> +62 294 123458</p>
                        <div class="market-stats">
                            <span class="stat">100+ Pedagang</span>
                            <span class="stat">40+ Produk</span>
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
                            <p>+6285951491116 </p>
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

    <div id="price-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-product-name">Rincian Harga Produk</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body" id="modal-price-details">
                <!-- Price details will be injected here by JavaScript -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    
    <!-- Inline Modal Script for Testing -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard modal script loaded');
            
            const modal = document.getElementById('price-modal');
            if (!modal) {
                console.error('Modal tidak ditemukan di dashboard!');
                return;
            }

            const detailButtons = document.querySelectorAll('.btn-details');
            const closeModalBtn = modal.querySelector('.close-btn');
            const modalProductName = document.getElementById('modal-product-name');
            const modalPriceDetails = document.getElementById('modal-price-details');
            const allMarkets = ['Pasar Cepiring', 'Pasar Kendal', 'Pasar Kaliwungu', 'Pasar Weleri'];

            console.log('Found', detailButtons.length, 'detail buttons in dashboard');

            detailButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Dashboard button clicked!');
                    
                    try {
                        const details = JSON.parse(this.getAttribute('data-details'));
                        const productName = this.getAttribute('data-product-name');
                        
                        console.log('Product:', productName);
                        console.log('Details:', details);
                        
                        modalProductName.textContent = `Rincian Harga: ${productName}`;
                        
                        let detailsHtml = '';
                        
                        const marketPrices = {};
                        details.forEach(detail => {
                            marketPrices[detail.nama_pasar] = detail;
                        });

                        allMarkets.forEach(market => {
                            detailsHtml += '<div class="price-item">';
                            detailsHtml += `<span class="market-name">${market}</span>`;

                            if (marketPrices[market]) {
                                const priceData = marketPrices[market];
                                const formattedPrice = new Intl.NumberFormat('id-ID').format(priceData.harga);
                                detailsHtml += `<span class="price-value">Rp ${formattedPrice} / ${priceData.satuan}</span>`;
                            } else {
                                detailsHtml += '<span class="price-unavailable">Harga tidak tersedia</span>';
                            }
                            detailsHtml += '</div>';
                        });
                        
                        modalPriceDetails.innerHTML = detailsHtml;
                        modal.style.display = 'block';
                        console.log('Dashboard modal should be visible now');

                    } catch (e) {
                        console.error("Dashboard: Gagal mem-parsing detail harga:", e);
                        modalPriceDetails.innerHTML = '<p>Maaf, terjadi kesalahan saat memuat rincian harga.</p>';
                        modal.style.display = 'block';
                    }
                });
            });

            function closeModal() {
                modal.style.display = 'none';
            }

            if(closeModalBtn) {
                closeModalBtn.addEventListener('click', closeModal);
            }

            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            });

            window.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.style.display === 'block') {
                    closeModal();
                }
            });
        });
    </script>

    <script>
    function updateDarkButton() {
        const isDark = document.body.classList.contains('dark-mode');
        document.getElementById('dark-icon').textContent = isDark ? '☀️' : '🌙';
        document.getElementById('dark-label').textContent = isDark ? 'Light Mode' : 'Dark Mode';
    }
    document.getElementById('toggle-dark').onclick = function(e) {
        e.preventDefault();
        document.body.classList.toggle('dark-mode');
        if(document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
        } else {
            localStorage.setItem('theme', 'light');
        }
        updateDarkButton();
    };
    if(localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
    updateDarkButton();
    </script>
</body>
</html> 