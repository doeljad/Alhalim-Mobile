<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row">
            <!-- Pemasukan Minggu Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-primary card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Pemasukan Minggu Ini <i class="mdi mdi-chart-line mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $query = "SELECT SUM(total) AS total_minggu_ini FROM (
    SELECT COALESCE(SUM(jumlah_bayar), 0) AS total
    FROM pembayaran
    WHERE YEARWEEK(tanggal_pembayaran, 1) = YEARWEEK(CURDATE(), 1)
    UNION ALL
    SELECT COALESCE(SUM(jumlah_cicilan), 0) AS total
    FROM cicilan_pembayaran
    WHERE YEARWEEK(tanggal_cicilan, 1) = YEARWEEK(CURDATE(), 1)
) AS combined_totals;
";
                            $result = mysqli_query($conn, $query);

                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo "Rp " . number_format($row['total_minggu_ini'], 0, ',', '.');
                            }
                            ?>

                        </h2>
                        <h6 class="card-text">Pemasukan dari pembayaran minggu ini</h6>
                    </div>
                </div>
            </div>

            <!-- Pemasukan Bulan Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-warning card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Pemasukan Bulan Ini <i class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $query = " SELECT SUM(total) AS total_bulan_ini FROM (
                                       SELECT SUM(jumlah_bayar) AS total
                                       FROM pembayaran
                                       WHERE MONTH(tanggal_pembayaran) = MONTH(CURDATE()) AND YEAR(tanggal_pembayaran) = YEAR(CURDATE())
                                       UNION ALL
                                       SELECT SUM(jumlah_cicilan) AS total
                                       FROM cicilan_pembayaran
                                       WHERE MONTH(tanggal_cicilan) = MONTH(CURDATE()) AND YEAR(tanggal_cicilan) = YEAR(CURDATE())) AS combined_totals";
                            $result = mysqli_query($conn, $query);

                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo "Rp " . number_format($row['total_bulan_ini'], 0, ',', '.');
                            }
                            ?>

                        </h2>
                        <h6 class="card-text">Pemasukan dari pembayaran bulan ini</h6>
                    </div>
                </div>
            </div>
            <!-- Pemasukan Tahun Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pemasukan Tahun <?php echo date('Y'); ?> <i class="mdi mdi-calendar-clock mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $current_year = date('Y');

                            $query = "SELECT SUM(total) AS pendapatan_tahun FROM (
                            SELECT SUM(jumlah_bayar) AS total
                            FROM pembayaran
                            WHERE YEAR(tanggal_pembayaran) = $current_year
                            UNION ALL
                            SELECT SUM(jumlah_cicilan) AS total
                            FROM cicilan_pembayaran
                            WHERE YEAR(tanggal_cicilan) = $current_year) AS combined_totals";

                            $result = mysqli_query($conn, $query);

                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo "Rp " . number_format($row['pendapatan_tahun'], 0, ',', '.');
                            }
                            ?>

                        </h2>
                        <h6 class="card-text">Pendapatan dari pembayaran tahun ini</h6>
                    </div>
                </div>
            </div>

            <!-- Pengeluaran Minggu Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Pengeluaran Minggu Ini<i class="mdi mdi-cash mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            // Misalkan Anda memiliki tabel `pengeluaran` untuk menyimpan data pengeluaran
                            // $result = mysqli_query($conn, "SELECT SUM(jumlah_pengeluaran) AS total_pengeluaran_minggu_ini FROM pengeluaran WHERE YEARWEEK(tanggal_pengeluaran, 1) = YEARWEEK(CURDATE(), 1)");
                            // if ($result) {
                            //     $row = mysqli_fetch_assoc($result);
                            //     echo "Rp " . number_format($row['total_pengeluaran_minggu_ini'], 0, ',', '.');
                            // }
                            ?>
                            Rp. 0
                        </h2>
                        <h6 class="card-text">Pengeluaran minggu ini</h6>
                    </div>
                </div>
            </div>

            <!-- Pengeluaran Bulan Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Pengeluaran Bulan Ini<i class="mdi mdi-currency-usd mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            // $result = mysqli_query($conn, "SELECT SUM(jumlah_pengeluaran) AS total_pengeluaran_bulan_ini FROM pengeluaran WHERE MONTH(tanggal_pengeluaran) = MONTH(CURDATE()) AND YEAR(tanggal_pengeluaran) = YEAR(CURDATE())");
                            // if ($result) {
                            //     $row = mysqli_fetch_assoc($result);
                            //     echo "Rp " . number_format($row['total_pengeluaran_bulan_ini'], 0, ',', '.');
                            // }
                            ?>
                            Rp. 0
                        </h2>
                        <h6 class="card-text">Pengeluaran bulan ini</h6>
                    </div>
                </div>
            </div>


            <!-- Total Santri -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Santri<i class="mdi mdi-account mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(id_santri) AS total_santri FROM santri");
                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total_santri'];
                            }
                            ?>
                        </h2>
                        <h6 class="card-text">Jumlah total santri saat ini</h6>
                    </div>
                </div>
            </div>

        </div>

        <!-- <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="clearfix">
                            <h4 class="card-title float-start">Visit And Sales Statistics</h4>
                            <div id="visit-sale-chart-legend" class="rounded-legend legend-horizontal legend-top-right float-end"></div>
                        </div>
                        <canvas id="visit-sale-chart" class="mt-4"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Traffic Sources</h4>
                        <div class="doughnutjs-wrapper d-flex justify-content-center">
                            <canvas id="traffic-chart"></canvas>
                        </div>
                        <div id="traffic-chart-legend" class="rounded-legend legend-vertical legend-bottom-left pt-4"></div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
    <?php include('pages/inc/footer.php') ?>
</div>