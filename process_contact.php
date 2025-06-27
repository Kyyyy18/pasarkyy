<?php
// Include database connection
include 'koneksi.php';

// Set header for JSON response
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get form data
$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$pesan = $_POST['pesan'] ?? '';

// Validate required fields
if (empty($nama) || empty($email) || empty($pesan)) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit();
}

try {
    // Prepare SQL statement to prevent SQL injection
    $sql = "INSERT INTO contact_messages (nama, email, pesan, tanggal_kirim) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sss", $nama, $email, $pesan);
        
        if ($stmt->execute()) {
            // Success
            echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim! Kami akan segera menghubungi Anda.']);
            
            // Optional: Send email notification to admin
            sendEmailNotification($nama, $email, $pesan);
            
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesan. Silakan coba lagi.']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error. Silakan coba lagi.']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.']);
}

$conn->close();

// Function to send email notification (optional)
function sendEmailNotification($nama, $email, $pesan) {
    $to = "admin@marketvision.com"; // Replace with your admin email
    $subject = "Pesan Baru dari MarketVision - " . $nama;
    
    $message = "
    <html>
    <head>
        <title>Pesan Baru dari Website</title>
    </head>
    <body>
        <h2>Pesan Baru dari MarketVision</h2>
        <p><strong>Nama:</strong> " . htmlspecialchars($nama) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Pesan:</strong></p>
        <p>" . nl2br(htmlspecialchars($pesan)) . "</p>
        <p><strong>Tanggal:</strong> " . date('Y-m-d H:i:s') . "</p>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    
    // Send email (uncomment if you want to enable email notifications)
    // mail($to, $subject, $message, $headers);
}

// SQL to create the contact_messages table (run this in your database):
/*
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal_kirim DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread'
);
*/
?> 