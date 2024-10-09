<?php
// Periksa apakah URL hanya "/index.php" tanpa parameter
if (!isset($_GET['page'])) {
    // Redirect ke "/index.php?page=dashboard"
    echo "<script>window.location.href = 'santri.php?page=dashboard'</script>";
    exit(); // Hentikan eksekusi skrip setelah melakukan redirect
}
// Ambil parameter dari URL
$params = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Definisikan route
$routes = [
    // Login 
    'login' => 'login.php',

    'dashboard' => 'pages/santri/dashboard/dashboard.php',
    //alhalimpay
    'alhalimpay' => 'pages/santri/dashboard/alhalimpay.php',
    'uang-makan' => 'pages/santri/dashboard/uang-makan.php',
    'uang-makan-detail' => 'pages/santri/dashboard/uang-makan-detail.php',


    //Berita
    'berita' => 'pages/santri/berita/berita.php',
    'berita-nu' => 'pages/santri/berita/berita-nu.php',
    'detail-berita' => 'pages/santri/berita/detail-berita.php',

    'belanja' => 'pages/santri/belanja/belanja.php',
    'notifikasi' => 'pages/santri/notifikasi/notifikasi.php',
    'pengaturan' => 'pages/santri/pengaturan/pengaturan.php',
];

// Periksa apakah URL ada di route
if (isset($routes[$params])) {
    // Tentukan halaman yang akan dimuat
    $page = $routes[$params];
    // Include halaman yang sesuai
    include_once($page);
} else {
    // Jika URL tidak ada di route, redirect ke halaman 404 atau halaman lain yang sesuai
    echo "<script>window.location.href = 'pages/template/error-404.html'</script>";
    exit(); // Penting untuk menghentikan eksekusi skrip setelah melakukan redirect
}
