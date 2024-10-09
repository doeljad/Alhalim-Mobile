<?php
$page = isset($_GET['page']) ? $_GET['page'] : '';
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <img src="assets/images/logo.png" alt="profile" />
        </li>

        <li class="nav-item mt-3 <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=dashboard">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo $page == 'manajemen-kelas' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=manajemen-kelas">
                <span class="menu-title">Manajemen Kelas</span>
                <i class="mdi mdi-school menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo $page == 'manajemen-santri' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=manajemen-santri">
                <span class="menu-title">Manajemen Santri</span>
                <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo in_array($page, ['iuran-kelas', 'jenis-iuran']) ? 'active' : ''; ?>">
            <a class="nav-link" data-bs-toggle="collapse" href="#iuran-collapse" aria-expanded="<?php echo in_array($page, ['pembayaran', 'pembayaran-cicilan']) ? 'true' : 'false'; ?>" aria-controls="iuran-collapse">
                <span class="menu-title">Manajemen Iuran</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-wallet menu-icon"></i>
            </a>
            <div class="collapse <?php echo in_array($page, ['iuran-kelas', 'jenis-iuran']) ? 'show' : ''; ?>" id="iuran-collapse">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'jenis-iuran' ? 'active' : ''; ?>" href="?page=jenis-iuran">Jenis Iuran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'iuran-kelas' ? 'active' : ''; ?>" href="?page=iuran-kelas">Iuran per kelas</a>
                    </li>

                </ul>
            </div>
        </li>
        <li class="nav-item <?php echo in_array($page, ['pembayaran', 'pembayaran-cicilan']) ? 'active' : ''; ?>">
            <a class="nav-link" data-bs-toggle="collapse" href="#pembayaran-collapse" aria-expanded="<?php echo in_array($page, ['pembayaran', 'pembayaran-cicilan']) ? 'true' : 'false'; ?>" aria-controls="pembayaran-collapse">
                <span class="menu-title">Pembayaran</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-wallet menu-icon"></i>
            </a>
            <div class="collapse <?php echo in_array($page, ['pembayaran', 'pembayaran-cicilan']) ? 'show' : ''; ?>" id="pembayaran-collapse">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'pembayaran' ? 'active' : ''; ?>" href="?page=pembayaran">Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'pembayaran-cicilan' ? 'active' : ''; ?>" href="?page=pembayaran-cicilan">Pembayaran Cicilan</a>
                    </li>

                </ul>
            </div>
        </li>

        <li class="nav-item <?php echo in_array($page, ['laporan-uang-makan', 'laporan-pembayaran', 'laporan-iuran']) ? 'active' : ''; ?>">
            <a class="nav-link" data-bs-toggle="collapse" href="#laporan-collapse" aria-expanded="<?php echo in_array($page, ['laporan-uang-makan', 'laporan-pembayaran', 'laporan-iuran']) ? 'true' : 'false'; ?>" aria-controls="laporan-collapse">
                <span class="menu-title">Laporan</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-file-document menu-icon"></i>
            </a>
            <div class="collapse <?php echo in_array($page, ['laporan-uang-makan', 'laporan-pembayaran', 'laporan-iuran']) ? 'show' : ''; ?>" id="laporan-collapse">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'laporan-uang-makan' ? 'active' : ''; ?>" href="?page=laporan-uang-makan">Laporan Uang Makan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'laporan-pembayaran' ? 'active' : ''; ?>" href="?page=laporan-pembayaran">Laporan Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'laporan-iuran' ? 'active' : ''; ?>" href="?page=laporan-iuran">Laporan Iuran</a>
                    </li>
                </ul>
            </div>
        </li>


        <li class="nav-item <?php echo $page == 'transaksi-midtrans' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=transaksi-midtrans">
                <span class="menu-title">Transaksi Midtrans</span>
                <i class="mdi mdi-credit-card menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo $page == 'log-admin' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=log-admin">
                <span class="menu-title">Log Admin</span>
                <i class="mdi mdi-history menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo $page == 'pengaturan' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=pengaturan">
                <span class="menu-title">Pengaturan</span>
                <i class="mdi mdi-settings menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>