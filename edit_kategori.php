<?php
include 'Koneksi.php';

// Otorisasi: Hanya Admin yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?pesan=gagal");
    exit;
}

// Ambil data kategori yang mau diedit berdasarkan ID
if (!isset($_GET['id'])) {
    header("Location: kategori.php");
    exit;
}
$id_kategori = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = "SELECT * FROM kategori WHERE id_kategori = '$id_kategori' LIMIT 1";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: kategori.php");
    exit;
}

// Proses Update ketika tombol Simpan ditekan
if (isset($_POST['update_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    $keterangan    = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    if (!empty($nama_kategori)) {
        $query_update = "UPDATE kategori SET nama_kategori = '$nama_kategori', keterangan = '$keterangan' WHERE id_kategori = '$id_kategori'";
        if (mysqli_query($koneksi, $query_update)) {
            header("Location: kategori.php?status=sukses_edit");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - Titipkeun-U Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h3 { text-align: center; margin-bottom: 30px; color: #ecf0f1; }
        .sidebar a { display: block; color: #bdc3c7; padding: 15px 20px; text-decoration: none; font-size: 16px; }
        .main-content { margin-left: 250px; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); max-width: 500px; }
        h2 { color: #333; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; }
        .btn-primary { background: #b30000; }
        .btn-secondary { background: #7f8c8d; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>Titipkeun-U Admin</h3>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="kategori.php">Kelola Kategori & Area</a>
        <a href="users.php">Verifikasi Kurir & User</a>
    </div>

    <div class="main-content">
        <h2>Edit Kategori Barang</h2>
        
        <div class="card">
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" value="<?= htmlspecialchars($data['nama_kategori']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" rows="4"><?= htmlspecialchars($data['keterangan']); ?></textarea>
                </div>
                <button type="submit" name="update_kategori" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kategori.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>