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
            <!-- Total Pesanan Minggu Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-primary card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pesanan Minggu Ini <i class="mdi mdi-chart-line mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(id) AS total_minggu_ini FROM pesanan WHERE YEARWEEK(tanggal_pesanan, 1) = YEARWEEK(CURDATE(), 1)");
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total_minggu_ini'];
                            ?>
                        </h2>
                        <h6 class="card-text">Pesanan yang diterima minggu ini</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pesanan Minggu Lalu -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-warning card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pesanan Minggu Lalu <i class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(id) AS total_minggu_lalu FROM pesanan WHERE YEARWEEK(tanggal_pesanan, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)");
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total_minggu_lalu'];
                            ?>
                        </h2>
                        <h6 class="card-text">Pesanan yang diterima minggu lalu</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pesanan Bulan Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pesanan Bulan Ini <i class="mdi mdi-calendar mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(id) AS total_bulan_ini FROM pesanan WHERE MONTH(tanggal_pesanan) = MONTH(CURDATE()) AND YEAR(tanggal_pesanan) = YEAR(CURDATE())");
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total_bulan_ini'];
                            ?>
                        </h2>
                        <h6 class="card-text">Pesanan yang diterima bulan ini</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan Minggu Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pendapatan Minggu Ini <i class="mdi mdi-cash mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $result = mysqli_query($conn, "SELECT SUM(total_harga) AS pendapatan_minggu_ini FROM pesanan WHERE YEARWEEK(tanggal_pesanan, 1) = YEARWEEK(CURDATE(), 1)");
                            $row = mysqli_fetch_assoc($result);
                            echo "Rp " . number_format($row['pendapatan_minggu_ini'], 0, ',', '.');
                            ?>
                        </h2>
                        <h6 class="card-text">Pendapatan dari pesanan minggu ini</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan Bulan Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pendapatan Bulan Ini <i class="mdi mdi-currency-usd mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $result = mysqli_query($conn, "SELECT SUM(total_harga) AS pendapatan_bulan_ini FROM pesanan WHERE MONTH(tanggal_pesanan) = MONTH(CURDATE()) AND YEAR(tanggal_pesanan) = YEAR(CURDATE())");
                            $row = mysqli_fetch_assoc($result);
                            echo "Rp " . number_format($row['pendapatan_bulan_ini'], 0, ',', '.');
                            ?>
                        </h2>
                        <h6 class="card-text">Pendapatan dari pesanan bulan ini</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan Tahun 2024 -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pendapatan Tahun <?php echo date('Y'); ?> <i class="mdi mdi-calendar-clock mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">
                            <?php
                            $current_year = date('Y');
                            $result = mysqli_query($conn, "SELECT SUM(total_harga) AS pendapatan_tahun FROM pesanan WHERE YEAR(tanggal_pesanan) = $current_year");
                            $row = mysqli_fetch_assoc($result);
                            echo "Rp " . number_format($row['pendapatan_tahun'], 0, ',', '.');
                            ?>
                        </h2>
                        <h6 class="card-text">Pendapatan dari pesanan tahun ini</h6>
                    </div>
                </div>
            </div>

        </div>



        <div class="row">
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
        </div>
    </div>
    <?php include('pages/inc/footer.php') ?>
</div>