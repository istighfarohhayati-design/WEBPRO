<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "webpro";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM pesanan WHERE id = '$id'";
        $query = mysqli_query($koneksi, $sql);
        $data = mysqli_fetch_assoc($query);
    } else {
        $sql = "SELECT * FROM pesanan ORDER BY id DESC";
        $query = mysqli_query($koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
    }
    echo json_encode(["status" => "success", "data" => $data]);
}

if ($method == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nama_barang = $input['nama_barang'] ?? '';
    $jumlah      = $input['jumlah'] ?? '';
    $catatan     = $input['catatan'] ?? '';

    if ($nama_barang && $jumlah) {
        $sql = "INSERT INTO pesanan (nama_barang, jumlah, catatan) VALUES ('$nama_barang', '$jumlah', '$catatan')";
        if (mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Pesanan berhasil dikirim!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan ke database"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    }
}

if ($method == 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id          = $input['id'] ?? '';
    $nama_barang = $input['nama_barang'] ?? '';
    $jumlah      = $input['jumlah'] ?? '';
    $catatan     = $input['catatan'] ?? '';

    if ($id && $nama_barang && $jumlah) {
        $sql = "UPDATE pesanan SET nama_barang='$nama_barang', jumlah='$jumlah', catatan='$catatan' WHERE id='$id'";
        if (mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Pesanan berhasil diperbarui!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui database"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Data edit tidak lengkap"]);
    }
}

if ($method == 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? '';

    if ($id) {
        $sql = "DELETE FROM pesanan WHERE id = '$id'";
        if (mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Pesanan berhasil dihapus!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menghapus dari database"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
    }
}
?>