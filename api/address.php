<?php
// api/address.php
include '../config_profile_buyer.php';
header("Content-Type: application/json");

$buyer = checkAPIKey();
$buyer_id = $buyer['id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': // Method 1
        $res = mysqli_query($conn, "SELECT * FROM buyer_addresses WHERE buyer_id = $buyer_id");
        echo json_encode(mysqli_fetch_all($res, MYSQLI_ASSOC));
        break;

    case 'POST': // Method 2
        $data = json_decode(file_get_contents("php://input"), true);
        $label = mysqli_real_escape_string($conn, $data['label']);
        $detail = mysqli_real_escape_string($conn, $data['detail_address']);
        mysqli_query($conn, "INSERT INTO buyer_addresses (buyer_id, label, detail_address) VALUES ($buyer_id, '$label', '$detail')");
        echo json_encode(["message" => "Alamat berhasil ditambahkan"]);
        break;

    default:
        http_response_code(405);
        break;
}
?>