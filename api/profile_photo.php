<?php
// api/profile_photo.php
include '../config_profile_buyer.php';
header("Content-Type: application/json");

$buyer = checkAPIKey();
$buyer_id = $buyer['id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': // Method 1
        echo json_encode(["photo_url" => $buyer['photo_url']]);
        break;

    case 'POST': // Method 2
        $data = json_decode(file_get_contents("php://input"), true);
        $photo = mysqli_real_escape_string($conn, $data['photo_url']);
        mysqli_query($conn, "UPDATE buyers SET photo_url='$photo' WHERE id=$buyer_id");
        echo json_encode(["message" => "Foto profil berhasil diperbarui"]);
        break;

    default:
        http_response_code(405);
        break;
}
?>