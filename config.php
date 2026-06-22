<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost:3307"; // Sesuaikan port kamu
$user = "root";
$pass = "";
$db   = "profile_fachira_webpro";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Otorisasi API via API Key
function checkAPIKey() {
    global $conn;
    $headers = getallheaders();
    if (!isset($headers['X-API-KEY'])) {
        http_response_code(401);
        echo json_encode(["message" => "Unauthorized"]);
        exit();
    }
    $api_key = mysqli_real_escape_string($conn, $headers['X-API-KEY']);
    $query = "SELECT * FROM buyers WHERE api_key = '$api_key'";
    $res = mysqli_query($conn, $query);
    if (mysqli_num_rows($res) == 0) {
        http_response_code(403);
        echo json_encode(["message" => "Forbidden"]);
        exit();
    }
    return mysqli_fetch_assoc($res);
}
?>