<?php
include 'Koneksi.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard_admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Titipkeun-U</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #b30000; border: none; color: white; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #990000; }
        .alert { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login Admin</h2>
    
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
        <div class="alert">NIM atau Password salah / Bukan Admin!</div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">
        <div class="form-group">
            <label>NIM Mahasiswa</label>
            <input type="text" name="nim" placeholder="Masukkan NIM" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan Password" required>
        </div>
        <button type="submit">Masuk</button>
    </form>
</div>

</body>
</html>