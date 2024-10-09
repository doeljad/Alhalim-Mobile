<?php
$id_user = $_SESSION['id'];
$tahun_sekarang = date('Y');

// Query untuk nama santri
$sql_nama = "SELECT s.*
FROM 
    santri s 
WHERE 
    s.id_user = ?";

$stmt_nama = $conn->prepare($sql_nama);
$stmt_nama->bind_param('i', $id_user);
$stmt_nama->execute();
$result_nama = $stmt_nama->get_result();
$santri = $result_nama->fetch_assoc();

$nama_santri = $santri['nama_santri'];
$id_kelas = $santri['id_kelas'];

// Query untuk mendapatkan nominal iuran
$sql_nominal = "SELECT j.nominal
FROM 
    jenis_iuran j
JOIN 
    iuran_kelas ik ON j.id_iuran = ik.id_iuran
JOIN 
    santri s ON ik.id_kelas = s.id_kelas
WHERE 
    s.id_user = ?
    AND ik.status_aktif = 1
    AND ik.id_iuran = 1
LIMIT 1;
";

$stmt_nominal = $conn->prepare($sql_nominal);
$stmt_nominal->bind_param('i', $id_user);
$stmt_nominal->execute();
$result_nominal = $stmt_nominal->get_result();
$nominal_iuran = $result_nominal->fetch_assoc()['nominal'];

// Query untuk mendapatkan data pembayaran
$sql_pembayaran = "SELECT 
    p.bulan_pembayaran,
    p.jumlah_bayar,
    p.metode_pembayaran
FROM 
    pembayaran p
JOIN 
    santri s ON p.id_santri = s.id_santri
WHERE 
    s.id_user = ?
    AND p.tahun_pembayaran = ?
    AND p.id_iuran = (SELECT id_iuran FROM jenis_iuran LIMIT 1)
ORDER BY 
    p.bulan_pembayaran ASC;
";

$stmt_pembayaran = $conn->prepare($sql_pembayaran);
$stmt_pembayaran->bind_param('ii', $id_user, $tahun_sekarang);
$stmt_pembayaran->execute();
$result_pembayaran = $stmt_pembayaran->get_result();

$payments = $result_pembayaran->fetch_all(MYSQLI_ASSOC);

// Array untuk menyimpan data pembayaran per bulan
$monthlyPayments = array_fill(1, 12, null);

// Isi array dengan data pembayaran yang ditemukan
foreach ($payments as $payment) {
    $monthlyPayments[intval($payment['bulan_pembayaran'])] = $payment;
}
?>

<div class="container-fluid mb-5" style="padding-bottom: 80px;">
    <div class="container mt-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <button class="btn btn-gradient-success rounded-circle p-0" onclick="window.history.back()" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>
        </div>

        <div class="col text-center">
            <h3 class="m-0">Pembayaran Uang Makan</h3>
            <span>Santri <?= $nama_santri; ?></span>
            <table class="table table-striped mt-4 text-start">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($bulan = 1; $bulan <= 12; $bulan++):
                        $payment = $monthlyPayments[$bulan];
                        $url = "?page=uang-makan-detail&bulan=" . $bulan . "&tahun=" . $tahun_sekarang;
                    ?>
                        <tr onclick="window.location='<?= $url ?>';" style="cursor: pointer;">
                            <td><?= $bulanIndo[$bulan] ?></td>
                            <td>Rp <?= number_format($nominal_iuran, 0, ',', '.') ?></td>
                            <?php if ($payment): ?>
                                <td class="text-success"><?= htmlspecialchars($payment['metode_pembayaran']) ?></td>
                            <?php else: ?>
                                <td class="text-danger">Belum Bayar</td>
                            <?php endif; ?>
                            <td><i class="fa-solid fa-chevron-right"></i></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>