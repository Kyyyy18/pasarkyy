<?php
session_start();
include 'koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle search functionality
$search_results = [];
$search_query = '';
$total_results = 0;

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = trim($_GET['q']);
    $search_term = '%' . $search_query . '%';
    
    // Search in both product name and market name
    $sql = "SELECT * FROM harga_sembako WHERE nama_produk LIKE ? OR nama_pasar LIKE ? ORDER BY tanggal_update DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
    $total_results = count($search_results);
    $stmt->close();
}

// Get unique markets for filter
$markets = ['Pasar Cepiring', 'Pasar Kendal', 'Pasar Kaliwungu', 'Pasar Weleri'];

// Group products by name for display
$grouped_products = [];
if (!empty($search_results)) {
    foreach ($search_results as $product) {
        $product_name = $product['nama_produk'];
        if (!isset($grouped_products[$product_name])) {
            $grouped_products[$product_name] = [];
        }
        $grouped_products[$product_name][] = $product;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - MarketVision</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .search-page {
            padding-top: 100px;
            min-height: 100vh;
            background-color: var(--light-bg);
        }
        
        .search-header {
            background: var(--white);
            padding: 2rem 0;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .search-header .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .search-form {
            display: flex;
            max-width: 500px;
            width: 100%;
        }
        
        .search-form input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid var(--neutral-color);
            border-radius: 25px 0 0 25px;
            outline: none;
            font-size: 16px;
        }
        
        .search-form button {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .search-form button:hover {
            background: var(--secondary-color);
        }
        
        .search-stats {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .back-btn {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        
        .back-btn:hover {
            color: var(--secondary-color);
        }
        
        .no-results {
            text-align: center;
            padding: 4rem 0;
            color: var(--text-light);
        }
        
        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--neutral-color);
        }
        
        .no-results h3 {
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        
        .no-results p {
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .search-suggestions {
            background: var(--white);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }
        
        .search-suggestions h4 {
            color: var(--text-dark);
            margin-bottom: 1rem;
        }
        
        .suggestion-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .suggestion-tag {
            background: var(--light-bg);
            color: var(--text-dark);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .suggestion-tag:hover {
            background: var(--primary-color);
            color: var(--white);
        }
        
        /* Market Links in Search Results */
        .price .market-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .price .market-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo" style="display: flex; align-items: center;">
                <img src="admin/kendal logo.png" alt="Logo Kabupaten Kendal" style="height:48px; margin-right: 16px;">
                <h2><i class="fas fa-chart-line"></i> MarketVision</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Beranda</a>
                <a href="dashboard.php#harga" class="nav-link">Monitor Harga</a>
                <a href="dashboard.php#pasar" class="nav-link">Daftar Pasar</a>
                <a href="dashboard.php#tentang" class="nav-link">Tentang</a>
                <div class="user-menu">
                    <span class="user-name">Selamat datang, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Pengguna'); ?></span>
                    <a href="logout.php" class="nav-link logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <div class="search-page">
        <!-- Search Header -->
        <div class="search-header">
            <div class="container">
                <a href="dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Beranda
                </a>
                
                <form method="GET" action="cari.php" class="search-form">
                    <input type="text" name="q" placeholder="Cari produk sembako atau pasar..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <?php if (!empty($search_query)): ?>
                    <div class="search-stats">
                        <?php echo $total_results; ?> hasil ditemukan
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="container">
            <?php if (!empty($search_query)): ?>
                <?php if (empty($search_results)): ?>
                    <!-- No Results -->
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>Tidak ada hasil ditemukan</h3>
                        <p>Kami tidak dapat menemukan hasil untuk pencarian "<?php echo htmlspecialchars($search_query); ?>"</p>
                        <a href="dashboard.php" class="btn btn-primary">Lihat Semua Produk</a>
                    </div>
                    
                    <!-- Search Suggestions -->
                    <div class="search-suggestions">
                        <h4>Mungkin yang Anda maksud:</h4>
                        <div class="suggestion-tags">
                            <a href="cari.php?q=beras" class="suggestion-tag">Beras</a>
                            <a href="cari.php?q=gula" class="suggestion-tag">Gula</a>
                            <a href="cari.php?q=minyak" class="suggestion-tag">Minyak</a>
                            <a href="cari.php?q=telur" class="suggestion-tag">Telur</a>
                            <a href="cari.php?q=Kendal" class="suggestion-tag">Pasar Kendal</a>
                            <a href="cari.php?q=Cepiring" class="suggestion-tag">Pasar Cepiring</a>
                            <a href="cari.php?q=Weleri" class="suggestion-tag">Pasar Weleri</a>
                            <a href="cari.php?q=Kaliwungu" class="suggestion-tag">Pasar Kaliwungu</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Search Results -->
                    <h2 class="section-title">Hasil Pencarian: "<?php echo htmlspecialchars($search_query); ?>"</h2>
                    
                    <div class="products-grid">
                        <?php foreach ($grouped_products as $product_name => $prices): ?>
                            <div class="product-card">
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
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Default Search Page -->
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>Cari Harga Sembako</h3>
                    <p>Masukkan kata kunci untuk mencari produk sembako atau pasar</p>
                </div>
                
                <!-- Popular Searches -->
                <div class="search-suggestions">
                    <h4>Pencarian Populer:</h4>
                    <div class="suggestion-tags">
                        <a href="cari.php?q=beras" class="suggestion-tag">Beras</a>
                        <a href="cari.php?q=gula" class="suggestion-tag">Gula</a>
                        <a href="cari.php?q=minyak" class="suggestion-tag">Minyak</a>
                        <a href="cari.php?q=telur" class="suggestion-tag">Telur</a>
                        <a href="cari.php?q=Kendal" class="suggestion-tag">Pasar Kendal</a>
                        <a href="cari.php?q=Cepiring" class="suggestion-tag">Pasar Cepiring</a>
                        <a href="cari.php?q=Weleri" class="suggestion-tag">Pasar Weleri</a>
                        <a href="cari.php?q=Kaliwungu" class="suggestion-tag">Pasar Kaliwungu</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="price-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-product-name">Rincian Harga Produk</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body" id="modal-price-details">
                <!-- Price details will be injected here -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    
    <!-- Inline Modal Script for Testing -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Search page modal script loaded');
            
            const modal = document.getElementById('price-modal');
            if (!modal) {
                console.error('Modal tidak ditemukan di search page!');
                return;
            }

            const detailButtons = document.querySelectorAll('.btn-details');
            const closeModalBtn = modal.querySelector('.close-btn');
            const modalProductName = document.getElementById('modal-product-name');
            const modalPriceDetails = document.getElementById('modal-price-details');
            const allMarkets = ['Pasar Cepiring', 'Pasar Kendal', 'Pasar Kaliwungu', 'Pasar Weleri'];

            console.log('Found', detailButtons.length, 'detail buttons in search page');

            detailButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Search page button clicked!');
                    
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
                        console.log('Search page modal should be visible now');

                    } catch (e) {
                        console.error("Search page: Gagal mem-parsing detail harga:", e);
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
</body>
</html> 