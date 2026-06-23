<?php
include 'Koneksi.php';

// Cek apakah user sudah login DAN apakah role-nya benar-hand bener 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak memenuhi syarat, tendang kembali ke halaman login
    header("Location: login.php?pesan=gagal");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Titipkeun-U</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h3 { text-align: center; margin-bottom: 30px; color: #ecf0f1; }
        .sidebar a { display: block; color: #bdc3c7; padding: 15px 20px; text-decoration: none; font-size: 16px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left: 4px solid #b30000; }
        .main-content { margin-left: 250px; padding: 40px; }
        .header { background: white; padding: 20px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .welcome-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .btn-logout { background: #b30000; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-logout:hover { background: #990000; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>Titipkeun-U Admin</h3>
        <a href="dashboard_admin.php" class="active">Dashboard</a>
        <a href="kategori.php">Kelola Kategori & Area</a>
        <a href="users.php">Verifikasi Kurir & User</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Halaman Utama Pengelola</h2>
            <div>Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!</div>
        </div>

        <div class="welcome-box">
            <h3>Selamat Datang di Sistem Kendali Titipkeun-U</h3>
            <p>Melalui halaman panel ini, Anda dapat memantau aktivitas jastip mahasiswa Telkom University, mengelola master data kategori barang, lokasi jemput/antar, serta melakukan validasi berkas mahasiswa yang mendaftar sebagai Penjelajah (Kurir).</p>
            <br>
            <a href="logout.php" class="btn-logout">Keluar dari Sistem (Logout)</a>
        </div>
    </div>

</body>
</html>