<?php
// Konfigurasi Database
$host     = "localhost";
$username = "root";
$password = ""; // Kosongkan jika pakai XAMPP default bawaan windows
$database = "db_titipkeun_u";

// Membuat Koneksi ke MySQL
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek Apakah Koneksi Berhasil
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Otorisasi Session (Biar otomatis aktif di halaman yang include file ini)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>