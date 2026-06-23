<?php
include 'Koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?pesan=gagal");
    exit;
}

if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    $keterangan    = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    if (!empty($nama_kategori)) {
        $query_insert = "INSERT INTO kategori (nama_kategori, keterangan) VALUES ('$nama_kategori', '$keterangan')";
        mysqli_query($koneksi, $query_insert);
        header("Location: kategori.php?status=sukses_tambah");
        exit;
    }
}

if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $query_delete = "DELETE FROM kategori WHERE id_kategori = '$id_hapus'";
    mysqli_query($koneksi, $query_delete);
    header("Location: kategori.php?status=sukses_hapus");
    exit;
}

$query_select = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$result_kategori = mysqli_query($koneksi, $query_select);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori - Titipkeun-U Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; }
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding-top: 20px; }
        .sidebar h3 { text-align: center; margin-bottom: 30px; color: #ecf0f1; }
        .sidebar a { display: block; color: #bdc3c7; padding: 15px 20px; text-decoration: none; font-size: 16px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left: 4px solid #b30000; }
        .main-content { margin-left: 250px; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
        h2, h3 { color: #333; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #666; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; font-size: 14px; }
        .btn-primary { background: #2980b9; }
        .btn-primary:hover { background: #2471a3; }
        .btn-danger { background: #c0392b; }
        .btn-danger:hover { background: #922b21; }
        .btn-edit { background: #f39c12; }
        .btn-edit:hover { background: #d35400; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>Titipkeun-U Admin</h3>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="kategori.php" class="active">Kelola Kategori & Area</a>
        <a href="users.php">Verifikasi Kurir & User</a>
    </div>

    <div class="main-content">
        <h2>Manajemen Kategori Barang Jastip</h2>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_tambah'): ?>
            <div class="alert-success">Kategori baru berhasil ditambahkan!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'sukses_hapus'): ?>
            <div class="alert-success">Kategori berhasil dihapus!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'sukses_edit'): ?>
            <div class="alert-success">Kategori berhasil diperbarui!</div>
        <?php endif; ?>

        <div class="card">
            <h3>Tambah Kategori Baru</h3>
            <form action="kategori.php" method="POST">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" placeholder="Contoh: Makanan Berat, Snack, Fotocopy" required>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" rows="3" placeholder="Deskripsi singkat mengenai kategori ini..."></textarea>
                </div>
                <button type="submit" name="tambah_kategori" class="btn btn-primary">Simpan Kategori</button>
            </form>
        </div>

        <div class="card">
            <h3>Daftar Kategori Aktif</h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama Kategori</th>
                        <th width="45%">Keterangan</th>
                        <th width="25%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result_kategori)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                        <td>
                            <a href="edit_kategori.php?id=<?php echo $row['id_kategori']; ?>" class="btn btn-edit">Edit</a>
                            <a href="kategori.php?hapus=<?php echo $row['id_kategori']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>