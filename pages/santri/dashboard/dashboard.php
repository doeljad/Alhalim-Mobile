<?php
// Memeriksa apakah session 'id' ada dan tidak kosong
$user_id = isset($_SESSION['id']) && !empty($_SESSION['id']) ? $_SESSION['id'] : '';

if ($user_id) {
    // Hanya jika user telah login (SESSION id tidak kosong)
    $query = "SELECT * FROM santri WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $santri = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="container-fluid mb-lg-5" style="padding-bottom: 100px;">
    <div class="header">
        <img src="assets/images/logo-alhalim.png" alt="Logo" width="60" height="60" class="mt-5">
        <h1>Alhalim Mobile</h1>
    </div>

    <!-- Tampilkan nama santri hanya jika user telah login -->
    <div class="welcome-message text-center">
        <p>Selamat <?= $waktu; ?> <img src="assets/images/dashboard/<?= $gambar; ?>" style="width: 14px; height: 14px;">
            <?php if ($user_id): ?>
                <?= htmlspecialchars($santri['nama_santri']); ?></p>
    <?php endif; ?>
    </div>

    <div class="datetime">
        <p id="time"></p>
        <p id="date"></p>
    </div>
    <hr>
    <div class="container">
        <div class="row text-center g-3 mt-3">
            <?php if ($user_id): ?>
                <!-- Tampilkan menu ini hanya jika user telah login -->
                <div class="col-3 mb-3">
                    <a href="?page=alhalimpay" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="assets/images/dashboard/payment.png" alt="AlhalimPay" class="img-fluid mb-2" width="70">
                                <p class="card-text text-dark">AlhalimPay</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-3 mb-3">
                    <a href="link-to-aktivitas.html" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="assets/images/dashboard/to-do-list.png" alt="Aktivitas" class="img-fluid mb-2" width="70">
                                <p class="card-text text-dark">Aktivitas</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-3 mb-3">
                    <a href="link-to-rapor.html" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="assets/images/dashboard/document.png" alt="Rapor" class="img-fluid mb-2" width="70">
                                <p class="card-text text-dark">Rapor</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <div class="col-3 mb-3">
                <a href="link-to-tata-tertib.html" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body p-0">
                            <img src="assets/images/dashboard/tatib.png" alt="Tata Tertib" class="img-fluid mb-2" width="70">
                            <p class="card-text text-dark">Tata Tertib</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3 mb-3">
                <a href="link-to-profil-pondok.html" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body p-0">
                            <img src="assets/images/dashboard/profil_pondok.png" alt="Profil Pondok" class="img-fluid mb-2" width="70">
                            <p class="card-text text-dark">Profil Pondok</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3 mb-3">
                <a href="link-to-media-sosial.html" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body p-0">
                            <img src="assets/images/dashboard/medsos.png" alt="Media Sosial" class="img-fluid mb-2" width="70">
                            <p class="card-text text-dark">Media Sosial</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include('pages/inc/sntr-sidebar.php') ?>
<div class="phone">
    <input type="radio" name="s" id="s1" value="dashboard" checked="checked">
    <input type="radio" name="s" id="s2" value="berita">
    <input type="radio" name="s" id="s3" value="belanja">
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
<script>
    function gmod(n, m) {
        return ((n % m) + m) % m;
    }

    function kuwaiticalendar(adjust) {
        var today = new Date();
        if (adjust) {
            var adjustmili = 1000 * 60 * 60 * 24 * adjust;
            var todaymili = today.getTime() + adjustmili;
            today = new Date(todaymili);
        }
        var day = today.getDate();
        var month = today.getMonth();
        var year = today.getFullYear();
        var m = month + 1;
        var y = year;
        if (m < 3) {
            y -= 1;
            m += 12;
        }

        var a = Math.floor(y / 100.);
        var b = 2 - a + Math.floor(a / 4.);
        if (y < 1583) b = 0;
        if (y == 1582) {
            if (m > 10) b = -10;
            if (m == 10) {
                b = 0;
                if (day > 4) b = -10;
            }
        }

        var jd = Math.floor(365.25 * (y + 4716)) + Math.floor(30.6001 * (m + 1)) + day + b - 1524;

        b = 0;
        if (jd > 2299160) {
            a = Math.floor((jd - 1867216.25) / 36524.25);
            b = 1 + a - Math.floor(a / 4.);
        }
        var bb = jd + b + 1524;
        var cc = Math.floor((bb - 122.1) / 365.25);
        var dd = Math.floor(365.25 * cc);
        var ee = Math.floor((bb - dd) / 30.6001);
        day = (bb - dd) - Math.floor(30.6001 * ee);
        month = ee - 1;
        if (ee > 13) {
            cc += 1;
            month = ee - 13;
        }
        year = cc - 4716;

        var wd = gmod(jd + 1, 7) + 1;

        var iyear = 10631. / 30.;
        var epochastro = 1948084;
        var epochcivil = 1948085;

        var shift1 = 8.01 / 60.;

        var z = jd - epochastro;
        var cyc = Math.floor(z / 10631.);
        z = z - 10631 * cyc;
        var j = Math.floor((z - shift1) / iyear);
        var iy = 30 * cyc + j;
        z = z - Math.floor(j * iyear + shift1);
        var im = Math.floor((z + 28.5001) / 29.5);
        if (im == 13) im = 12;
        var id = z - Math.floor(29.5001 * im - 29);

        var myRes = new Array(8);

        myRes[0] = day; //calculated day (CE)
        myRes[1] = month - 1; //calculated month (CE)
        myRes[2] = year; //calculated year (CE)
        myRes[3] = jd - 1; //julian day number
        myRes[4] = wd - 1; //weekday number
        myRes[5] = id - 1; //islamic date
        myRes[6] = im - 1; //islamic month
        myRes[7] = iy; //islamic year

        return myRes;
    }

    function writeIslamicDate(adjustment) {
        // var wdNames = ["Ahad", "Ithnin", "Thulatha", "Arbaa", "Khams", "Jumuah", "Sabt"];
        var iMonthNames = ["Muharram", "Safar", "Rabi'ul Awwal", "Rabi'ul Akhir", "Jumadal Ula", "Jumadal Akhira", "Rajab", "Sha'ban", "Ramadan", "Shawwal", "Dhul Qa'ada", "Dhul Hijja"];
        var iDate = kuwaiticalendar(adjustment);
        var outputIslamicDate = iDate[5] + " " + iMonthNames[iDate[6]] + " " + iDate[7] + " H";
        return outputIslamicDate;
    }

    function updateDateTime() {
        var now = new Date();

        // Format waktu
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');
        var timeString = `${hours}:${minutes}:${seconds} WIB`;

        // Format tanggal
        var day = String(now.getDate()).padStart(2, '0');
        var month = String(now.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
        var year = now.getFullYear();
        var dateString = `${day} ${getMonthName(month)} ${year} / ${writeIslamicDate(1)}`;

        // Update elemen HTML
        document.getElementById('time').textContent = timeString;
        document.getElementById('date').textContent = dateString;
    }

    function getMonthName(month) {
        var months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        return months[parseInt(month) - 1];
    }

    // Perbarui waktu setiap detik
    setInterval(updateDateTime, 1000);

    // Panggil fungsi awal
    updateDateTime();
</script>