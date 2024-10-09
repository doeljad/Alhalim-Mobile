<?php

// Mulai sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// if (!isset($_SESSION['id'])) {
//     echo '<script>
//         // Jika sesi tidak ada, alihkan ke login.php
//         window.location.href = "login.php";
//     </script>';
//     exit(); // Keluar dari skrip PHP setelah pengalihan
// }
// Pastikan koneksi database belum ditutup
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data produk dari database
$sql = "SELECT p.*, k.nama AS kategori 
        FROM produk p
        JOIN kategori k ON p.id_kategori=k.id";
$result = mysqli_query($conn, $sql);
?>

<head>
    <style>
        .percent {
            width: 50px;
            height: 25px;
            position: absolute;
            background: rgb(176, 115, 115);
            color: #fff;
            border-radius: 3px;
            display: flex;
            justify-content: center;
            align-items: center;
            right: 0;
            border-bottom-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 5px;
            top: 10px;
        }

        .card-image {
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-image img {
            max-width: 100%;
            height: auto;
        }

        .card-inner {
            padding: 10px;
            color: rgb(112, 64, 64);
        }

        .price span {
            color: rgb(114, 123, 125);
            font-weight: 600;
            font-size: 18px;
        }

        .price sup {
            color: rgb(148, 142, 131);
            font-weight: 600;
            font-size: 12px;
            top: -2px;
        }

        .details {
            border-radius: 15px;
            width: 90px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            background: rgb(176, 115, 115);
        }

        .details:hover {
            color: #fff;
            background: rgb(183, 161, 161);
        }

        .wishlist,
        .cart {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: #fff;
            background: rgb(176, 115, 115);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: 0.5s all;
            font-size: 14px;
        }

        .wishlist:hover,
        .cart:hover {
            color: #fff;
            background: rgb(183, 161, 161);
        }

        .cart {
            margin-left: 5px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -15px;
        }

        .col-md-4 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 15px;
        }

        /* Custom CSS for responsiveness */
        @media (min-width: 576px) and (max-width: 767px) {
            .row {
                display: flex;
                flex-wrap: wrap;
                margin: -15px;
            }

            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
                padding: 15px;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .row {
                display: flex;
                flex-wrap: wrap;
                margin: -15px;
            }

            .col-md-4 {
                flex: 0 0 33.33%;
                max-width: 33.33%;
                padding: 15px;
            }
        }

        @media (min-width: 992px) {
            .row {
                display: flex;
                flex-wrap: wrap;
                margin: -15px;
            }

            .col-md-4 {
                flex: 0 0 25%;
                max-width: 25%;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container mb-5">
        <div class="row justify-content-center">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <!-- Persentase Diskon -->
                        <span class="percent">10%</span>

                        <!-- Gambar Produk -->
                        <div class="card-image">
                            <?php if (!empty($row['gambar'])): ?>
                                <!-- <img src="<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>"> -->
                                <img src="assets/images/logo-alhalim.png" alt="<?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php else: ?>
                                <img src="assets/images/logo-alhalim.png" alt="Default Image">
                            <?php endif; ?>
                        </div>

                        <!-- Detail Produk -->
                        <div class="card-inner">
                            <span class="fw-bold"><?= !empty($row['kategori']) ? htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8') : 'Kategori Tidak Diketahui' ?></span>
                            <h5 class="mb-0 fw-bold"><?= !empty($row['nama']) ? htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') : 'Nama Produk Tidak Diketahui' ?></h5>

                            <!-- Harga -->
                            <div class="price mt-1">
                                <span>Rp <?= !empty($row['harga']) ? number_format($row['harga'], 0, ',', '.') : 'Harga Tidak Diketahui' ?></span>
                            </div>

                            <!-- Tombol dan Ikon Wishlist & Cart -->
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <button class="btn btn text-uppercase btn-sm details">Details</button>
                                <div class="d-flex flex-row">
                                    <span class="wishlist">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
                                        </svg>
                                    </span>
                                    <span class="cart">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16">
                                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
<?php include('pages/inc/sntr-sidebar.php'); ?>
<div class="phone">
    <input type="radio" name="s" id="s1" value="dashboard">
    <input type="radio" name="s" id="s2" value="berita">
    <input type="radio" name="s" id="s3" value="belanja" checked="checked">
    <input type="radio" name="s" id="s4" value="notifikasi">
    <input type="radio" name="s" id="s5" value="pengaturan">

    <label for="s1" data-page="dashboard">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </label>
    <label for="s2" data-page="berita">
        <i class="fas fa-newspaper"></i>
        <span>Berita</span>
    </label>
    <label for="s3" data-page="belanja">
        <i class="fas fa-shopping-cart"></i>
        <span>Belanja</span>
    </label>
    <label for="s4" data-page="notifikasi" class="position-relative">
        <i class="fas fa-bell"></i>
        <span>Notifikasi</span>
        <?php if ($unread_count > 0): ?>
            <span class="position-absolute top-20 start-90 badge rounded-pill bg-danger"
                style="margin-left: 25px; margin-top: -10px; font-size: 0.7rem; padding: 2px 6px;">
                <?= $unread_count ?>
                <span class="visually-hidden">unread messages</span>
            </span>
        <?php endif; ?>
    </label>
    <label for="s5" data-page="pengaturan">
        <i class="fas fa-gear"></i>
        <span>Pengaturan</span>
    </label>

    <div class="circle bg-gradient-primary"></div>
    <div class="phone_content">
        <div class="phone_bottom">
            <span class="indicator"></span>
        </div>
    </div>
</div>