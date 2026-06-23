<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

if (isset($_POST['simpan_profil'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $fakultas = $_POST['fakultas'];
    $foto_name = '-';

    if (isset($_FILES['foto_profil']['name']) && $_FILES['foto_profil']['name'] != '') {
        $target = "uploads/";
        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }
        $ext = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
        $foto_name = "user_" . time() . "." . $ext;
        move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target . $foto_name);
    }

    // QUERY INI HARUS SESUAI DENGAN KOLOM DATABASE BARU KAMU
    $q = "INSERT INTO profile (nama, email, no_hp, fakultas, foto) VALUES ('$nama', '$email', '$no_hp', '$fakultas', '$foto_name')";
    
    // Eksekusi dan langsung cek jika ada error koneksi
    $eksekusi = mysqli_query($koneksi, $q);

    if ($eksekusi) {
        header("Location: riwayat.html");
        exit;
    } else {
        // Jika gagal nyambung ke database, baris ini akan langsung memunculkan errornya di layar (Anti Layar Putih)
        die("Gagal simpan ke database! Silakan cek error ini ke dosen: " . mysqli_error($koneksi));
    }
}

if (isset($_GET['id_hapus'])) {
    $id = $_GET['id_hapus'];
    $q = "DELETE FROM profile WHERE id = '$id'";
    mysqli_query($koneksi, $q);
    header("Location: profile.php");
    exit;
}

$ambil = mysqli_query($koneksi, "SELECT * FROM profile ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Profil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="nav-container">
        <div></div>
        <a href="riwayat.html" class="btn-nav">Ke Riwayat Pesanan →</a>
    </div>

    <div class="main-title">Kelola Profil</div>

    <div class="card">
        <div class="card-title">Tambah Profil</div>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label>Nomor HP</label>
                <input type="text" name="no_hp" class="form-control" placeholder="Nomor HP" required>
            </div>
            <div class="form-group">
                <label>Fakultas</label>
                <input type="text" name="fakultas" class="form-control" placeholder="Fakultas" required>
            </div>
            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="foto_profil" class="form-control">
            </div>
            <button type="submit" name="simpan_profil" class="btn-submit">Simpan Profil</button>
        </form>
    </div>

    <div class="card">
        <div class="card-title">Data Profil</div>
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Fakultas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($data = mysqli_fetch_assoc($ambil)) { ?>
                <tr>
                    <td>
                        <?php if ($data['foto'] != '-') { ?>
                            <img src="uploads/<?php echo $data['foto']; ?>" class="img-profile">
                        <?php } else { ?>
                            <span class="no-photo">No Photo</span>
                        <?php } ?>
                    </td>
                    <td><b><?php echo htmlspecialchars($data['nama']); ?></b></td>
                    <td><?php echo htmlspecialchars($data['email']); ?></td>
                    <td><?php echo htmlspecialchars($data['no_hp']); ?></td>
                    <td><?php echo htmlspecialchars($data['fakultas']); ?></td>
                    <td class="action-links">
                        <a href="#" class="link-edit">Edit</a> | 
                        <a href="profile.php?id_hapus=<?php echo $data['id']; ?>" class="link-delete" onclick="return confirm('Hapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>