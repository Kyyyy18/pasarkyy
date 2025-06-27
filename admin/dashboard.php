<?php
session_start();
include '../koneksi.php'; // Hubungkan ke database

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Proses form untuk tambah/update harga
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_harga'])) {
    $id = $_POST['id'] ?? null;
    $produk = $_POST['produk'];
    $pasar = $_POST['pasar'];
    $harga = $_POST['harga'];
    $satuan = $_POST['satuan'];

    if ($id) {
        // Update data yang ada
        $sql = "UPDATE harga_sembako SET nama_produk = ?, nama_pasar = ?, harga = ?, satuan = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $produk, $pasar, $harga, $satuan, $id);
    } else {
        // Tambah data baru
        $sql = "INSERT INTO harga_sembako (nama_produk, nama_pasar, harga, satuan, tanggal_update) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $produk, $pasar, $harga, $satuan);
    }
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?status=sukses");
    } else {
        header("Location: dashboard.php?status=gagal");
    }
    $stmt->close();
    exit();
}

// Proses hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM harga_sembako WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: dashboard.php?status=dihapus");
    } else {
        header("Location: dashboard.php?status=gagalhapus");
    }
    $stmt->close();
    exit();
}

// Ambil data untuk form edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM harga_sembako WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
    $stmt->close();
}

// Ambil semua data harga untuk ditampilkan
$harga_list = [];
$sql = "SELECT * FROM harga_sembako ORDER BY tanggal_update DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $harga_list[] = $row;
    }
}

// Data pasar yang terdaftar
$markets = ['Pasar Cepiring', 'Pasar Kendal', 'Pasar Kaliwungu', 'Pasar Weleri'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MarketVision</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-page { padding: 2rem; background-color: var(--light-bg); min-height: 100vh; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .admin-card { background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: var(--shadow); margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
        .btn-submit { background: var(--primary-color); color: var(--white); padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; transition: var(--transition); font-size: 16px; }
        .btn-submit:hover { background: var(--secondary-color); }
        .btn-cancel { background: #f39c12; color: var(--white); text-decoration: none; padding: 12px 25px; border-radius: 8px; margin-left: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 12px; border: 1px solid var(--neutral-color); text-align: left; vertical-align: middle; }
        th { background-color: var(--primary-color); color: var(--white); }
        .action-btns a { margin-right: 10px; color: var(--primary-color); text-decoration: none; }
        .action-btns a.delete { color: #e74c3c; }
        .status-msg { padding: 15px; border-radius: 8px; margin-bottom: 1rem; color: white; }
        .status-msg.sukses { background: #2ecc71; }
        .status-msg.gagal { background: #e74c3c; }
        select { width: 100%; padding: 12px 15px; border: 2px solid var(--neutral-color); border-radius: 8px; font-size: 16px; background: var(--light-bg); -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: .65em auto; }
    </style>
</head>
<body>
    <div class="admin-page">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <img src="kendal logo.png" alt="Logo Kabupaten Kendal" style="height:60px; margin-right: 20px;">
                <h2><i class="fas fa-user-shield"></i> Adminnnnnnn</h2>
            </div>
            <div>
                <span>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></strong></span>
                <a href="../logout.php" class="logout-btn" style="margin-left: 1rem; color: #e74c3c; text-decoration: none; font-weight: bold;"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <?php if(isset($_GET['status'])): ?>
            <div class="status-msg <?php echo $_GET['status'] === 'sukses' || $_GET['status'] === 'dihapus' ? 'sukses' : 'gagal'; ?>">
                <?php 
                    if($_GET['status'] === 'sukses') echo 'Data berhasil disimpan!';
                    if($_GET['status'] === 'dihapus') echo 'Data berhasil dihapus!';
                    if($_GET['status'] === 'gagal') echo 'Terjadi kesalahan, data gagal disimpan.';
                    if($_GET['status'] === 'gagalhapus') echo 'Terjadi kesalahan, data gagal dihapus.';
                ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah/Edit Harga -->
        <div class="admin-card">
            <h3><i class="fas fa-tag"></i> <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Harga Sembako</h3>
            <form method="POST" action="dashboard.php">
                <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? ''; ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="produk">Nama Produk</label>
                        <input type="text" name="produk" placeholder="Contoh: Beras Premium" required value="<?php echo htmlspecialchars($edit_data['nama_produk'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="pasar">Pilih Pasar</label>
                        <select name="pasar" required>
                            <option value="">-- Pilih Pasar --</option>
                            <?php foreach ($markets as $market): ?>
                                <option value="<?php echo $market; ?>" <?php echo (isset($edit_data) && $edit_data['nama_pasar'] == $market) ? 'selected' : ''; ?>>
                                    <?php echo $market; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga (Rp)</label>
                        <input type="number" name="harga" placeholder="Contoh: 12500" required value="<?php echo htmlspecialchars($edit_data['harga'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <input type="text" name="satuan" placeholder="Contoh: kg, liter" required value="<?php echo htmlspecialchars($edit_data['satuan'] ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" name="simpan_harga" class="btn-submit"><i class="fas fa-save"></i> <?php echo $edit_data ? 'Update' : 'Simpan'; ?> Harga</button>
                <?php if ($edit_data): ?>
                    <a href="dashboard.php" class="btn-cancel">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabel Data Harga -->
        <div class="admin-card">
            <h3><i class="fas fa-list"></i> Daftar Harga Terkini</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Pasar</th>
                        <th>Harga</th>
                        <th>Satuan</th>
                        <th>Update Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($harga_list)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Belum ada data harga.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($harga_list as $harga_item): ?>
                        <tr>
                            <td><?php echo $harga_item['id']; ?></td>
                            <td><?php echo htmlspecialchars($harga_item['nama_produk']); ?></td>
                            <td><?php echo htmlspecialchars($harga_item['nama_pasar']); ?></td>
                            <td>Rp <?php echo number_format($harga_item['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($harga_item['satuan']); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($harga_item['tanggal_update'])); ?></td>
                            <td class="action-btns">
                                <a href="dashboard.php?edit=<?php echo $harga_item['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                <a href="dashboard.php?hapus=<?php echo $harga_item['id']; ?>" class="delete" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 