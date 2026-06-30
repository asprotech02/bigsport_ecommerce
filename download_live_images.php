<?php
// download_live_images.php

// 1. Definisikan URL base web live Anda (tempat mengambil gambar)
$liveUrl = 'https://bagindojaya.up.railway.app/storage/';

// 2. Hubungkan ke database LOKAL Laragon Anda
// (Pastikan Anda sudah meng-ekspor database live dari Railway dan meng-impornya ke database lokal 'ecommerce_bigsport' terlebih dahulu)
$host = '127.0.0.1';
$port = '3306';
$user = 'root';
$pass = '';
$db   = 'ecommerce_bigsport';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Lokal Gagal: " . $e->getMessage() . "\nPastikan Anda sudah mengaktifkan MySQL di Laragon dan database '$db' sudah terisi data live.");
}

// Helper untuk download file
function downloadFile($url, $savePath) {
    $dir = dirname($savePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    echo "Mengunduh: $url -> ";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $data !== false) {
        file_put_contents($savePath, $data);
        echo "SUKSES\n";
    } else {
        echo "GAGAL (HTTP Code: $httpCode)\n";
    }
}

// A. Download Foto Produk
echo "=== DOWNLOAD FOTO PRODUK ===\n";
$stmt = $pdo->query("SELECT image_path FROM product_images");
while ($row = $stmt->fetch()) {
    $path = $row['image_path'];
    if ($path) {
        $url = $liveUrl . $path;
        $savePath = __DIR__ . '/storage/app/public/' . $path;
        downloadFile($url, $savePath);
    }
}

// B. Download Foto Banner
echo "\n=== DOWNLOAD FOTO BANNER ===\n";
$stmt = $pdo->query("SELECT image_path FROM banners");
while ($row = $stmt->fetch()) {
    $path = $row['image_path'];
    if ($path) {
        $url = $liveUrl . $path;
        $savePath = __DIR__ . '/storage/app/public/' . $path;
        downloadFile($url, $savePath);
    }
}

echo "\nSemua proses download selesai! Silakan periksa folder storage/app/public Anda.\n";
