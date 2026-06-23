<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

include '../Koneksi.php';

// 1. VALIDASI API KEY (Dengan Fallback HTTP_X_API_KEY)
$api_key_valid = "titipkeunu123";
$headers = getallheaders();

$api_key_input = '';
if (isset($headers['X-API-KEY'])) {
    $api_key_input = $headers['X-API-KEY'];
} elseif (isset($_SERVER['HTTP_X_API_KEY'])) {
    $api_key_input = $_SERVER['HTTP_X_API_KEY'];
}

if ($api_key_input !== $api_key_valid) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Request tidak sah! API Key salah atau tidak ditemukan."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// 2. METHOD GET: Mengambil Semua / Spesifik Pesanan Jastip
if ($method === 'GET') {
    // Menyesuaikan parameter filter menggunakan id_buyer sesuai kolom database
    if (isset($_GET['id_buyer'])) {
        $id_buyer = mysqli_real_escape_string($koneksi, $_GET['id_buyer']);
        $query = "SELECT * FROM pesanan WHERE id_buyer = '$id_buyer' ORDER BY id_pesanan DESC";
    } else {
        $query = "SELECT * FROM pesanan ORDER BY id_pesanan DESC";
    }

    $result = mysqli_query($koneksi, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    http_response_code(200);
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}

// 3. METHOD POST: Membuat Pesanan Jastip Baru
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    // Validasi data input utama (ditambahkan id_lokasi_asal & id_lokasi_tujuan)
    if (!empty($input['id_buyer']) && !empty($input['id_kategori']) && !empty($input['id_lokasi_asal']) && !empty($input['id_lokasi_tujuan']) && !empty($input['nama_barang'])) {
        
        $id_buyer         = mysqli_real_escape_string($koneksi, $input['id_buyer']);
        $id_kategori      = mysqli_real_escape_string($koneksi, $input['id_kategori']);
        $id_lokasi_asal   = mysqli_real_escape_string($koneksi, $input['id_lokasi_asal']);
        $id_lokasi_tujuan = mysqli_real_escape_string($koneksi, $input['id_lokasi_tujuan']);
        $nama_barang      = mysqli_real_escape_string($koneksi, $input['nama_barang']);
        $detail_pesanan   = mysqli_real_escape_string($koneksi, $input['detail_pesanan'] ?? '');
        $status_pesanan   = 'pending'; 

        // Query insert disesuaikan dengan semua kolom wajib database kamu
        $query_insert = "INSERT INTO pesanan (id_buyer, id_kategori, id_lokasi_asal, id_lokasi_tujuan, nama_barang, detail_pesanan, status_pesanan) 
                         VALUES ('$id_buyer', '$id_kategori', '$id_lokasi_asal', '$id_lokasi_tujuan', '$nama_barang', '$detail_pesanan', '$status_pesanan')";
        
        if (mysqli_query($koneksi, $query_insert)) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Pesanan jastip berhasil dibuat!"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan pesanan ke database: " . mysqli_error($koneksi)]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Data input pesanan tidak lengkap. Pastikan id_buyer, id_kategori, id_lokasi_asal, id_lokasi_tujuan, dan nama_barang terisi."]);
    }
    exit;
}