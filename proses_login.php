<?php
include 'Koneksi.php';

// Menangkap data yang dikirim dari form login.php
$nim      = mysqli_real_escape_string($koneksi, $_POST['nim']);
$password = $_POST['password']; // Di database contoh kita masih plain-text 'password123'

// Query untuk mencari user dengan NIM tersebut DAN rolenya wajib admin
$query  = "SELECT * FROM users WHERE nim='$nim' AND role='admin' LIMIT 1";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) === 1) {
    $data = mysqli_fetch_assoc($result);
    
    // Validasi password (sesuai data dummy database kita: 'password123')
    if ($password === $data['password']) {
        
        // JIKA COCOK, SET SESSION DATA ADMIN (AUTHENTICATION & AUTHORIZATION)
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama']    = $data['nama'];
        $_SESSION['nim']     = $data['nim'];
        $_SESSION['role']    = $data['role']; // Isinya 'admin'
        
        // Alihkan ke halaman dashboard utama admin
        header("Location: dashboard_admin.php");
        exit;
    }
}

// Jika proses di atas gagal/tidak lolos, kembalikan ke login.php dengan pesan error
header("Location: login.php?pesan=gagal");
exit;
?>