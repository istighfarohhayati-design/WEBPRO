<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "request_order_webro_zahrah"; // Ganti dengan nama database-mu jika berbeda
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id = ""; $nama_barang = ""; $jumlah = ""; $catatan = ""; $sukses = ""; $gagal = "";

// 1. PROSES DELETE (HAPUS)
if (isset($_GET['op']) && $_GET['op'] == 'delete') {
    $id = $_GET['id'];
    $sql_delete = "DELETE FROM pesanan WHERE id = '$id'";
    if (mysqli_query($koneksi, $sql_delete)) {
        $sukses = "Data berhasil dihapus dari sisi Admin";
    } else {
        $gagal = "Gagal menghapus data";
    }
}

// 2. PROSES AMBIL DATA UNTUK EDIT
if (isset($_GET['op']) && $_GET['op'] == 'edit') {
    $id = $_GET['id'];
    $sql_edit = "SELECT * FROM pesanan WHERE id = '$id'";
    $q_edit = mysqli_query($koneksi, $sql_edit);
    $r_edit = mysqli_fetch_array($q_edit);
    if ($r_edit) {
        $nama_barang = $r_edit['nama_barang'];
        $jumlah      = $r_edit['jumlah'];
        $catatan     = $r_edit['catatan'];
    }
}

// 3. PROSES SIMPAN (CREATE & UPDATE)
if (isset($_POST['simpan'])) {
    $nama_barang = $_POST['nama_barang'];
    $jumlah      = $_POST['jumlah'];
    $catatan     = $_POST['catatan'];

    if ($nama_barang && $jumlah) {
        if (isset($_GET['op']) && $_GET['op'] == 'edit') { 
            $id = $_GET['id'];
            $sql_update = "UPDATE pesanan SET nama_barang='$nama_barang', jumlah='$jumlah', catatan='$catatan' WHERE id='$id'";
            if (mysqli_query($koneksi, $sql_update)) {
                $sukses = "Data berhasil diperbarui oleh Admin!";
                header("refresh:1;url=request_order.php");
            }
        } else { 
            $sql_insert = "INSERT INTO pesanan (nama_barang, jumlah, catatan) VALUES ('$nama_barang', '$jumlah', '$catatan')";
            if (mysqli_query($koneksi, $sql_insert)) {
                $sukses = "Data baru berhasil ditambahkan oleh Admin!";
                $nama_barang = ""; $jumlah = ""; $catatan = "";
            }
        }
    } else {
        $gagal = "Nama barang dan jumlah wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan - Sisi Admin (Non-API)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container" style="max-width: 800px;">
    <h2 class="mb-4 text-center">Kelola Pesanan (Role: Admin - Full PHP Non API)</h2>

    <?php if ($gagal): ?><div class="alert alert-danger"><?= $gagal; ?></div><?php endif; ?>
    <?php if ($sukses): ?><div class="alert alert-success"><?= $sukses; ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            <?= (isset($_GET['op']) && $_GET['op'] == 'edit') ? "Edit Pesanan ID: ".$id : "Tambah Pesanan Baru"; ?>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Barang</label>
                    <input type="text" class="form-control" name="nama_barang" value="<?= $nama_barang; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Jumlah</label>
                    <input type="number" class="form-control" name="jumlah" value="<?= $jumlah; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan</label>
                    <textarea class="form-control" name="catatan" rows="2"><?= $catatan; ?></textarea>
                </div>
                <button type="submit" name="simpan" class="btn btn-success w-100">Simpan (Proses Server PHP)</button>
                <?php if (isset($_GET['op']) && $_GET['op'] == 'edit'): ?>
                    <a href="request_order.php" class="btn btn-secondary w-100 mt-2">Batal</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white fw-bold">Data Tables (Hasil SELECT PHP)</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th><th>Barang</th><th>Jumlah</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_select = "SELECT * FROM pesanan ORDER BY id DESC";
                    $query = mysqli_query($koneksi, $sql_select);
                    while ($r = mysqli_fetch_array($query)) {
                        echo "<tr>
                            <td>{$r['id']}</td>
                            <td><strong>{$r['nama_barang']}</strong><br><small>{$r['catatan']}</small></td>
                            <td>{$r['jumlah']}</td>
                            <td>
                                <a href='request_order.php?op=edit&id={$r['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='request_order.php?op=delete&id={$r['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus?\")'>Hapus</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>