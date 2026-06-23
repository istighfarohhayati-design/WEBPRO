<?php
header("Content-Type: application/json");
include 'config.php';

$metode = $_SERVER['REQUEST_METHOD'];

// 1. AMBIL DATA UNTUK TABEL (GET)
if ($metode == 'GET') {
    $sql = mysqli_query($koneksi, "SELECT * FROM riwayat_pesanan ORDER BY id DESC");
    $data_array = array();
    while ($row = mysqli_fetch_assoc($sql)) {
        $data_array[] = $row;
    }
    echo json_encode($data_array);
    exit;
}

// 2. PROSES REKUES POST (TAMBAH & EDIT DATA)
if ($metode == 'POST') {
    
    // Fitur Edit Data Status
    if (isset($_POST['ubah_status_pesanan'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        mysqli_query($koneksi, "UPDATE riwayat_pesanan SET status = '$status' WHERE id = '$id'");
        echo json_encode(array("message" => "Status sukses diubah"));
        exit;
    }

    // Fitur Hapus Bukti Foto Jastip
    if (isset($_POST['hapus_bukti_foto'])) {
        $id = $_POST['id'];
        
        $cek = mysqli_query($koneksi, "SELECT bukti FROM riwayat_pesanan WHERE id = '$id'");
        $r = mysqli_fetch_assoc($cek);
        if ($r && $r['bukti'] != '-' && file_exists("uploads/" . $r['bukti'])) {
            unlink("uploads/" . $r['bukti']);
        }

        mysqli_query($koneksi, "UPDATE riwayat_pesanan SET bukti = '-' WHERE id = '$id'");
        echo json_encode(array("message" => "Bukti foto berhasil dihapus"));
        exit;
    }

    // Fitur Tambah Data Baru
    $nama_barang = $_POST['nama_barang'];
    $status = $_POST['status'];
    $file_db = '-';

    if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['name'] != '') {
        $folder = "uploads/";
        
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        
        $ext = pathinfo($_FILES['bukti_foto']['name'], PATHINFO_EXTENSION);
        $file_db = "img_" . time() . "." . $ext;
        
        move_uploaded_file($_FILES['bukti_foto']['tmp_name'], $folder . $file_db);
    }

    $simpan = mysqli_query($koneksi, "INSERT INTO riwayat_pesanan (nama_barang, status, bukti) VALUES ('$nama_barang', '$status', '$file_db')");
    if ($simpan) {
        echo json_encode(array("status" => "success", "message" => "Data pesanan disimpan"));
    }
    exit;
}

// 3. PROSES REKUES DELETE TOTAL DATA
if ($metode == 'DELETE') {
    $terima = json_decode(file_get_contents('php://input'), true);
    $id = $terima['id'];
    
    $cek = mysqli_query($koneksi, "SELECT bukti FROM riwayat_pesanan WHERE id = '$id'");
    $r = mysqli_fetch_assoc($cek);
    if ($r && $r['bukti'] != '-' && file_exists("uploads/" . $r['bukti'])) {
        unlink("uploads/" . $r['bukti']);
    }

    mysqli_query($koneksi, "DELETE FROM riwayat_pesanan WHERE id = '$id'");
    echo json_encode(array("status" => "success", "message" => "Data pesanan dihapus"));
    exit;
}
?>