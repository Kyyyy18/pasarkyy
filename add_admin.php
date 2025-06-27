<?php
// Script untuk menambahkan admin user ke database
// HAPUS FILE INI SETELAH DIGUNAKAN UNTUK KEAMANAN

include 'koneksi.php';

// Data admin yang akan ditambahkan
$admin_nama = 'admin123';
$admin_password = 'admin';

// Cek apakah user sudah ada
$check_sql = "SELECT * FROM user WHERE nama = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $admin_nama);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo "User admin123 sudah ada di database!";
} else {
    // Tambah user admin baru
    $insert_sql = "INSERT INTO user (nama, password) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ss", $admin_nama, $admin_password);
    
    if ($insert_stmt->execute()) {
        echo "User admin berhasil ditambahkan!<br>";
        echo "Nama: admin123<br>";
        echo "Password: admin<br><br>";
        echo "Silakan hapus file add_admin.php ini untuk keamanan!";
    } else {
        echo "Gagal menambahkan user admin: " . $conn->error;
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
?> 