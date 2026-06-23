<?php
// Set header agar merespons dalam format JSON dan mengizinkan akses CORS
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

include '../Koneksi.php'; // Naik satu folder untuk mengambil koneksi

// 1. VALIDASI API KEY (Dengan Fallback HTTP_X_API_KEY)
$api_key_valid = "titipkeunu123";
$headers = getallheaders();

// Cari API Key dari getallheaders atau dari $_SERVER (cadangan Apache)
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

// Cek Method HTTP
$method = $_SERVER['REQUEST_METHOD'];

// 2. METHOD GET: Mengambil Semua Kategori
if ($method === 'GET') {
// ... (sisa kode GET dan POST di bawahnya sudah aman, biarkan tetap sama)
    $query = "SELECT * FROM kategori ORDER BY id_kategori ASC";
    $result = mysqli_query($koneksi, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    http_response_code(200);
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}