<?php
include 'Koneksi.php';

// Otorisasi: Hanya Admin yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?pesan=gagal");
    exit;
}

// 1. PROSES UPDATE (Ubah Role / Verifikasi Kurir)
if (isset($_GET['ubah_role']) && isset($_GET['id'])) {
    $id_user   = mysqli_real_escape_string($koneksi, $_GET['id']);
    $role_baru = mysqli_real_escape_string($koneksi, $_GET['ubah_role']);

    $query_update = "UPDATE users SET role = '$role_baru' WHERE id_user = '$id_user'";
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: users.php?status=sukses_update");
        exit;
    }
}

// 2. PROSES DELETE (Hapus User)
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $query_delete = "DELETE FROM users WHERE id_user = '$id_hapus'";
    if (mysqli_query($koneksi, $query_delete)) {
        header("Location: users.php?status=sukses_hapus");
        exit;
    }
}

// 3. PROSES READ (Tampilkan Semua User selain Admin)
$query_select = "SELECT * FROM users WHERE role != 'admin' ORDER BY id_user DESC";
$result_users = mysqli_query($koneksi, $query_select);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Users - Titipkeun-U Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h3 { text-align: center; margin-bottom: 30px; color: #ecf0f1; }
        .sidebar a { display: block; color: #bdc3c7; padding: 15px 20px; text-decoration: none; font-size: 16px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left: 4px solid #b30000; }
        .main-content { margin-left: 250px; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
        h2, h3 { color: #333; margin-top: 0; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; font-size: 13px; display: inline-block; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #219653; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #d35400; }
        .btn-danger { background: #c0392b; }
        .btn-danger:hover { background: #922b21; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .badge-user { background: #3498db; }
        .badge-kurir { background: #9b59b6; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>Titipkeun-U Admin</h3>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="kategori.php">Kelola Kategori & Area</a>
        <a href="users.php" class="active">Verifikasi Kurir & User</a>
    </div>

    <div class="main-content">
        <h2>Verifikasi Kurir & Manajemen User</h2>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_update'): ?>
            <div class="alert-success">Status/Role user berhasil diperbarui!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'sukses_hapus'): ?>
            <div class="alert-success">Akun user berhasil dihapus dari sistem!</div>
        <?php endif; ?>

        <div class="card">
            <h3>Daftar Mahasiswa Terdaftar</h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">NIM</th>
                        <th width="25%">Nama Mahasiswa</th>
                        <th width="15%">Role Saat Ini</th>
                        <th width="35%">Aksi Otorisasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($result_users) > 0):
                        while($row = mysqli_fetch_assoc($result_users)): 
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nim']); ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td>
                            <?php if($row['role'] === 'kurir'): ?>
                                <span class="badge badge-kurir">Kurir / Penjelajah</span>
                            <?php else: ?>
                                <span class="badge badge-user">Konsumen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['role'] === 'user'): ?>
                                <a href="users.php?ubah_role=kurir&id=<?= $row['id_user']; ?>" class="btn btn-success" onclick="return confirm('Jadikan mahasiswa ini sebagai Kurir resmi?')">Verifikasi Jadi Kurir</a>
                            <?php else: ?>
                                <a href="users.php?ubah_role=user&id=<?= $row['id_user']; ?>" class="btn btn-warning" onclick="return confirm('Turunkan status akun menjadi user biasa?')">Jadikan User Biasa</a>
                            <?php endif; ?>
                            
                            <a href="users.php?hapus=<?= $row['id_user']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus akun mahasiswa ini?')">Hapus Akun</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #888;">Belum ada data konsumen atau kurir terdaftar.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>