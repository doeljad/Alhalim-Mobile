<?php
// Mulai sesi jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah pengguna sudah login (session 'id' tidak kosong)
if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
    // Mengambil ID pengguna yang sedang login
    $user_id = intval($_SESSION['id']);

    // Ambil notifikasi untuk pengguna yang sedang login
    $sql = "
        SELECT n.judul, n.pesan, n.created_at, np.is_read 
        FROM notifikasi_penerima np 
        JOIN notifikasi n ON np.id_notifikasi = n.id 
        WHERE np.id_user = $user_id 
        ORDER BY np.is_read ASC, n.created_at DESC";
    $result = mysqli_query($conn, $sql);

    // Periksa apakah ada notifikasi
    $notifications = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
    } else {
        $notifications[] = [
            'judul' => '',
            'pesan' => 'Belum ada notifikasi.',
            'is_read' => false,
            'created_at' => null
        ];
    }

    // Update semua notifikasi menjadi 'dibaca' untuk pengguna yang sedang login
    $update_sql = "UPDATE notifikasi_penerima SET is_read = 1 WHERE id_user = $user_id AND is_read = 0";
    mysqli_query($conn, $update_sql);

    // Tutup koneksi database (jika tidak digunakan di tempat lain)
    // mysqli_close($conn);
} else {
    // Jika pengguna belum login, kosongkan array notifikasi
    $notifications = [];
}
?>

<div class="container-fluid mt-3 mb-5">
    <h3 class="mb-4 text-center">Notifikasi</h3>

    <?php if (count($notifications) > 0): ?>
        <div class="row">
            <?php foreach ($notifications as $notification): ?>
                <div class="col-md-12 col-lg-12 mb-3">
                    <div class="card <?= $notification['is_read'] ? 'border-secondary' : 'bg-gradient-success' ?> shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($notification['judul'], ENT_QUOTES, 'UTF-8') ?></h5>
                                <small class="<?= $notification['is_read'] ? 'text-dark' : 'text-white' ?>"><?= $notification['created_at'] ? date('d M Y H:i', strtotime($notification['created_at'])) : '' ?></small>
                            </div>
                            <p class="card-text"><?= htmlspecialchars($notification['pesan'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Tidak ada notifikasi.
        </div>
    <?php endif; ?>
</div>

<?php include('pages/inc/sntr-sidebar.php') ?>
<div class="phone">
    <input type="radio" name="s" id="s1" value="dashboard">
    <input type="radio" name="s" id="s2" value="berita">
    <input type="radio" name="s" id="s3" value="belanja">
    <input type="radio" name="s" id="s4" value="notifikasi" checked="checked">
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