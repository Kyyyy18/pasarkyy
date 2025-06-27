<?php
include 'koneksi.php';

echo "<h2>Database Check - MarketVision</h2>";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<p>✅ Database connection successful</p>";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'harga_sembako'");
if ($result->num_rows > 0) {
    echo "<p>✅ Table 'harga_sembako' exists</p>";
} else {
    echo "<p>❌ Table 'harga_sembako' does not exist</p>";
    exit;
}

// Get all data
$sql = "SELECT * FROM harga_sembako ORDER BY tanggal_update DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<p>✅ Found " . $result->num_rows . " records in database</p>";
    echo "<h3>Sample Data:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nama Produk</th><th>Nama Pasar</th><th>Harga</th><th>Satuan</th><th>Tanggal Update</th></tr>";
    
    $count = 0;
    while ($row = $result->fetch_assoc() && $count < 10) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_pasar']) . "</td>";
        echo "<td>" . number_format($row['harga'], 0, ',', '.') . "</td>";
        echo "<td>" . htmlspecialchars($row['satuan']) . "</td>";
        echo "<td>" . $row['tanggal_update'] . "</td>";
        echo "</tr>";
        $count++;
    }
    echo "</table>";
    
    if ($result->num_rows > 10) {
        echo "<p>... and " . ($result->num_rows - 10) . " more records</p>";
    }
} else {
    echo "<p>❌ No data found in table</p>";
    echo "<p>You need to add some product data through the admin panel.</p>";
}

// Check grouped data structure
echo "<h3>Grouped Data Structure (for modal):</h3>";
$sql = "SELECT * FROM harga_sembako ORDER BY tanggal_update DESC";
$result = $conn->query($sql);
$all_products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_products[] = $row;
    }
}

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

echo "<p>Found " . count($grouped_products) . " unique products:</p>";
foreach ($grouped_products as $product_name => $prices) {
    echo "<h4>" . htmlspecialchars($product_name) . "</h4>";
    echo "<p>Available in " . count($prices) . " markets:</p>";
    echo "<ul>";
    foreach ($prices as $price) {
        echo "<li>" . htmlspecialchars($price['nama_pasar']) . ": Rp " . number_format($price['harga'], 0, ',', '.') . " / " . htmlspecialchars($price['satuan']) . "</li>";
    }
    echo "</ul>";
    
    // Show JSON structure
    echo "<p><strong>JSON for modal:</strong></p>";
    echo "<pre>" . htmlspecialchars(json_encode($prices, JSON_PRETTY_PRINT)) . "</pre>";
    break; // Only show first product
}

$conn->close();
?> 