<?php
session_start();
// Mengaktifkan kembali koneksi ke database
include 'koneksi.php';

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_login'])) {
    $nama = $_POST['nama'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Cek kredensial ke database
    // Menggunakan prepared statement untuk keamanan
    $sql = "SELECT * FROM user WHERE nama = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password (plain text, sesuai dengan kondisi saat ini)
            // Jika password di-hash, gunakan password_verify($password, $user['password'])
            if ($password === $user['password']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_nama'] = $user['nama'];
                
                header('Location: admin/dashboard.php');
                exit();
            } else {
                $error_message = "Nama atau password admin salah!";
            }
        } else {
            $error_message = "Nama atau password admin salah!";
        }
        $stmt->close();
    } else {
        $error_message = "Terjadi kesalahan pada server.";
    }
}

// Handle regular user login (no credentials needed)
if (isset($_POST['user_login'])) {
    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_nama'] = 'Pengguna Biasa';
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MarketVision</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow-hover);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--text-light);
        }
        
        .login-form {
            text-align: left;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--neutral-color);
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
            background: var(--light-bg);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: var(--white);
        }
        
        .login-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 1rem;
        }
        
        .login-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .error-message {
            background: #ff6b6b;
            color: var(--white);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 14px;
        }
        
        .login-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--neutral-color);
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            color: var(--secondary-color);
        }
        
        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        
        .back-home:hover {
            transform: translateX(-5px);
        }
        
        .user-login-btn {
            width: 100%;
            padding: 15px;
            background: var(--secondary-color);
            color: var(--text-dark);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 1rem;
        }
        
        .user-login-btn:hover {
            background: var(--primary-color);
            color: var(--white);
        }
        
        .or-divider {
            margin: 2rem 0 1rem 0;
            text-align: center;
            color: var(--text-light);
            font-size: 15px;
            position: relative;
        }
        
        .or-divider:before, .or-divider:after {
            content: '';
            display: inline-block;
            width: 40%;
            height: 1px;
            background: var(--neutral-color);
            vertical-align: middle;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-home">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Beranda
    </a>
    
    <div class="login-page">
        <div class="login-container">
            <div class="login-header">
                <h1><i class="fas fa-chart-line"></i> MarketVision</h1>
                <p>Masuk sebagai Admin atau Pengguna Biasa</p>
            </div>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="nama">Nama Admin</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama admin" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password Admin</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password admin" required>
                </div>
                
                <button type="submit" name="admin_login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login Admin
                </button>
            </form>
            
            <div class="or-divider">atau</div>
            
            <form method="POST" action="">
                <button type="submit" name="user_login" class="user-login-btn">
                    <i class="fas fa-user"></i> Masuk sebagai Pengguna Biasa
                </button>
            </form>
        </div>
    </div>
</body>
</html> 