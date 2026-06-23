<?php
session_start();
// profile.php
include 'config_profile_buyer.php';

// 1. Ambil data buyer pertama yang tersedia di database secara dinamis
$check_buyer = mysqli_query($conn, "SELECT id FROM buyers LIMIT 1");
$existing_buyer = mysqli_fetch_assoc($check_buyer);

if ($existing_buyer) {
    // Kalau ada data di DB, pakai ID yang tersedia tersebut
    $_SESSION['buyer_id'] = $existing_buyer['id'];
} else {
    // KALO DATABASE KOSONG (KARENA HABIS KAMU DELETE): Otomatis daftarin user pancingan baru
    $setup_user = mysqli_query($conn, "INSERT INTO buyers (username, email, phone, api_key) VALUES ('budi', 'budi@mail.com', '0812', 'key_budi')");
    // Ambil ID baru yang barusan terbuat otomatis
    $_SESSION['buyer_id'] = mysqli_insert_id($conn);
}

$buyer_id = $_SESSION['buyer_id'];

// ==================== CRUD 1: KELOLA AKUN BUYER ====================
// 1. SELECT (Ambil Profil)
$query_user = "SELECT * FROM buyers WHERE id = $buyer_id";
$res_user = mysqli_query($conn, $query_user);
$buyer = mysqli_fetch_assoc($res_user);

// 2. INPUT / REGISTER (Tambah user baru via Form)
if (isset($_POST['create_buyer'])) {
    $uname = $_POST['username']; $email = $_POST['email']; $phone = $_POST['phone']; $key = $_POST['api_key'];
    mysqli_query($conn, "INSERT INTO buyers (username, email, phone, api_key) VALUES ('$uname', '$email', '$phone', '$key')");
    
    // Langsung pindahkan login session ke user yang baru dibuat ini biar sinkron!
    $_SESSION['buyer_id'] = mysqli_insert_id($conn);
    
    header("Location: profile_buyer.php"); exit();
}

// 3. EDIT / UPDATE Akun
if (isset($_POST['update_buyer'])) {
    $uname = $_POST['username']; $email = $_POST['email']; $phone = $_POST['phone'];
    mysqli_query($conn, "UPDATE buyers SET username='$uname', email='$email', phone='$phone' WHERE id=$buyer_id");
    header("Location: profile_buyer.php"); exit();
}
// 4. DELETE Akun
if (isset($_POST['delete_buyer'])) {
    mysqli_query($conn, "DELETE FROM buyers WHERE id=$buyer_id");
    session_destroy();
    echo "Akun berhasil dihapus!"; exit();
}

// ==================== CRUD 2: KELOLA WISHLIST BUYER ====================
// 1. INPUT Wishlist
if (isset($_POST['add_wishlist'])) {
    $item = mysqli_real_escape_string($conn, $_POST['item_name']); 
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    
    // Berikan tanda petik tunggal ('$buyer_id') untuk mengamankan nilai id
    mysqli_query($conn, "INSERT INTO buyer_wishlists (buyer_id, item_name, notes) VALUES ('$buyer_id', '$item', '$notes')");
    header("Location: profile_buyer.php"); exit();
}
// 2. EDIT / UPDATE Wishlist
if (isset($_POST['update_wishlist'])) {
    $w_id = mysqli_real_escape_string($conn, $_POST['wishlist_id']);
    $item = mysqli_real_escape_string($conn, $_POST['item_name']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    mysqli_query($conn, "UPDATE buyer_wishlists SET item_name='$item', notes='$notes' WHERE id=$w_id");
    header("Location: profile_buyer.php"); exit();
}
// 3. DELETE Wishlist
if (isset($_GET['delete_wishlist'])) {
    $w_id = mysqli_real_escape_string($conn, $_GET['delete_wishlist']);
    mysqli_query($conn, "DELETE FROM buyer_wishlists WHERE id=$w_id");
    header("Location: profile_buyer.php"); exit();
}
// 4. SELECT Wishlist
$res_wish = mysqli_query($conn, "SELECT * FROM buyer_wishlists WHERE buyer_id = $buyer_id");
$wishlists = mysqli_fetch_all($res_wish, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profile Buyer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f6f9; color: #333; }
        .box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .flex { display: flex; gap: 10px; margin-bottom: 10px; }
        input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button, .btn-del { padding: 8px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>

    <h1>Halaman Profile Buyer</h1>

    <div class="box">
        <h2>Kelola Akun Utama</h2>
        <form action="" method="POST">
            <div class="flex">
                <input type="text" name="username" value="<?= $buyer['username'] ?? '' ?>" placeholder="Username" required>
                <input type="email" name="email" value="<?= $buyer['email'] ?? '' ?>" placeholder="Email" required>
                <input type="text" name="phone" value="<?= $buyer['phone'] ?? '' ?>" placeholder="No Hp" required>
            </div>
            <button type="submit" name="update_buyer">Edit</button>
            <button type="submit" name="delete_buyer" class="btn-danger" onclick="return confirm('Hapus akun?')">Delete</button>
        </form>
        <hr>
        <h4>Tambah Akun Baru:</h4>
        <form action="" method="POST" class="flex">
            <input type="text" name="username" placeholder="User Baru" required>
            <input type="email" name="email" placeholder="Email Baru" required>
            <input type="text" name="phone" placeholder="No Hp Baru" required>
            <input type="text" name="api_key" placeholder="API Key Baru" required>
            <button type="submit" name="create_buyer">Tambah User</button>
        </form>
    </div>

    <div class="box">
        <h2>Kelola Wishlist Barang</h2>
        <form action="" method="POST" class="flex">
            <input type="text" name="item_name" placeholder="Nama Barang (Contoh: Sepatu)" required>
            <input type="text" name="notes" placeholder="Catatan (Contoh: Ukuran 42)" required>
            <button type="submit" name="add_wishlist">Input</button>
        </form>

        <h3>Daftar Wishlist Kamu:</h3>
        <?php foreach ($wishlists as $w): ?>
            <div class="flex" style="align-items: center; border-bottom: 1px solid #eee; padding: 5px 0;">
                <form action="" method="POST" class="flex" style="margin: 0; flex: 1;">
                    <input type="hidden" name="wishlist_id" value="<?= $w['id'] ?>">
                    <input type="text" name="item_name" value="<?= $w['item_name'] ?>">
                    <input type="text" name="notes" value="<?= $w['notes'] ?>">
                    <button type="submit" name="update_wishlist">Edit</button>
                </form>
                <a href="profile_buyer.php?delete_wishlist=<?= $w['id'] ?>" class="btn-del btn-danger" onclick="return confirm('Hapus?')">Hapus (DELETE)</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="box" style="background: #eef1f6;">
        <h2>Backend API</h2>
        
        <h3>Endpoint 1: Foto Profil (GET, POST)</h3>
        <p>Foto Saat Ini: <span id="photo-name">Loading...</span></p>
        <div class="flex">
            <input type="text" id="input-photo" placeholder="Ketik nama file foto baru (eg: budi.png)">
            <button onclick="uploadPhoto()">Ganti Foto (POST)</button>
        </div>

        <hr>

        <h3>Endpoint 2: Alamat Pengiriman (GET, POST)</h3>
        <div class="flex">
            <input type="text" id="addr-label" placeholder="Label (Rumah/Kos)">
            <input type="text" id="addr-detail" placeholder="Alamat Lengkap">
            <button onclick="addAddress()">Tambah Alamat (POST)</button>
        </div>
        <div id="address-list" style="margin-top: 10px;"></div>
    </div>

<script>
    const API_KEY = '<?= $buyer['api_key'] ?? '' ?>'; // Mengambil API Key dari user yang sedang aktif
    const optHeaders = { 'Content-Type': 'application/json', 'X-API-KEY': API_KEY };

    // ==================== ENDPOINT 1: FOTO PROFIL ====================
    async function loadPhoto() {
        const res = await fetch('api/profile_photo.php', { headers: optHeaders });
        if(res.ok) {
            const data = await res.json();
            document.getElementById('photo-name').innerText = data.photo_url;
        }
    }
    async function uploadPhoto() {
        const photo = document.getElementById('input-photo').value;
        await fetch('api/profile_photo.php', {
            method: 'POST',
            headers: optHeaders,
            body: JSON.stringify({ photo_url: photo })
        });
        loadPhoto();
    }

    // ==================== ENDPOINT 2: ALAMAT PENGIRIMAN ====================
    async function loadAddresses() {
        const res = await fetch('api/address.php', { headers: optHeaders });
        if(res.ok) {
            const data = await res.json();
            const list = document.getElementById('address-list');
            list.innerHTML = '';
            data.forEach(d => {
                list.innerHTML += `<div>• <b>${d.label}</b>: ${d.detail_address}</div>`;
            });
        }
    }
    async function addAddress() {
        const label = document.getElementById('addr-label').value;
        const detail = document.getElementById('addr-detail').value;
        await fetch('api/address.php', {
            method: 'POST',
            headers: optHeaders,
            body: JSON.stringify({ label, detail_address: detail })
        });
        document.getElementById('addr-label').value = '';
        document.getElementById('addr-detail').value = '';
        loadAddresses();
    }

    // Jalankan API saat halaman dibuka
    if(API_KEY) {
        loadPhoto();
        loadAddresses();
    }
</script>
</body>
</html>