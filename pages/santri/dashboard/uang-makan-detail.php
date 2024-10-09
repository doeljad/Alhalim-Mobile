<?php

$id_user = $_SESSION['id'];
$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

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
$data_santri = $result_nama->fetch_assoc();

if ($data_santri) {
    $nama_santri = $data_santri['nama_santri'];
    $id_santri = $data_santri['id_santri'];
} else {
    // Handle case when no data is returned
    echo "Santri not found for the given user ID.";
}

// Query untuk mendapatkan nominal iuran
$sql_nominal = "SELECT j.nominal, j.id_iuran
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
LIMIT 1;";

$stmt_nominal = $conn->prepare($sql_nominal);
$stmt_nominal->bind_param('i', $id_user);
$stmt_nominal->execute();
$result_nominal = $stmt_nominal->get_result();
$data_nominal = $result_nominal->fetch_assoc();

$nominal_iuran = $data_nominal['nominal'];
$id_iuran = $data_nominal['id_iuran'];


// Query untuk mendapatkan data pembayaran
$sql_pembayaran = "SELECT 
    p.tanggal_pembayaran,
    p.jumlah_bayar,
    p.metode_pembayaran,
    s.nama_santri
FROM 
    pembayaran p
JOIN 
    santri s ON p.id_santri = s.id_santri
WHERE 
    s.id_user = ?
    AND p.tahun_pembayaran = ?
    AND p.bulan_pembayaran = ?
ORDER BY 
    p.bulan_pembayaran ASC;
";

$stmt_pembayaran = $conn->prepare($sql_pembayaran);
$stmt_pembayaran->bind_param('iii', $id_user, $tahun, $bulan);
$stmt_pembayaran->execute();
$result_pembayaran = $stmt_pembayaran->get_result();

$payment_detail = $result_pembayaran->fetch_assoc();
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
            <h3 class="card-title m-0">Detail Pembayaran Uang Makan</h3>
        </div>


        <div class="card <?= $payment_detail ? 'sukses' : 'belum-bayar' ?> mt-4">
            <!-- Header Nota -->
            <?php if ($payment_detail): ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#0099ff" fill-opacity="1" d="M0,128L40,154.7C80,181,160,235,240,224C320,213,400,139,480,101.3C560,64,640,64,720,90.7C800,117,880,171,960,176C1040,181,1120,139,1200,128C1280,117,1360,139,1400,149.3L1440,160L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z"></path>
                </svg>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#ffd700" fill-opacity="1" d="M0,128L40,154.7C80,181,160,235,240,224C320,213,400,139,480,101.3C560,64,640,64,720,90.7C800,117,880,171,960,176C1040,181,1120,139,1200,128C1280,117,1360,139,1400,149.3L1440,160L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z"></path>
                </svg>

            <?php endif; ?>
            <div class="text-center mb-4">
                <img src="assets/images/logo-alhalim.png" alt="Logo Alhalim" style="width: 100px;">
                <h5 class="mt-2"><?= $payment_detail ? 'BUKTI PEMBAYARAN LUNAS' : 'PEMBAYARAN UANG MAKAN' ?></h5>
                <p class="text-muted">Tanggal: <?= $payment_detail ? htmlspecialchars($payment_detail['tanggal_pembayaran']) : "-" ?></p>
            </div>

            <table class="table">
                <tr>
                    <th>Nama</th>
                    <td>: <?= htmlspecialchars($nama_santri) ?></td>
                </tr>
                <th>Bulan</th>
                <td>: <?= $bulanIndo[$bulan] ?></td>
                </tr>
                <tr>
                    <th>Tahun</th>
                    <td>: <?= htmlspecialchars($tahun) ?></td>
                </tr>
                <tr>
                    <th>Nominal Tagihan</th>
                    <td>: <?= number_format($nominal_iuran, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <th>Jumlah Bayar</th>
                    <td>: <?= $payment_detail ? number_format($payment_detail['jumlah_bayar'], 0, ',', '.') : "<span class='text-danger'>Belum Bayar</span>" ?></td>
                </tr>
                <tr>
                    <th>Status Pembayaran</th>
                    <td>: <?= $payment_detail ? htmlspecialchars($payment_detail['metode_pembayaran']) : "-" ?></td>
                </tr>
                <tr>
                    <th>Tanggal Pembayaran</th>
                    <td>: <?= $payment_detail ? htmlspecialchars($payment_detail['tanggal_pembayaran']) : "-" ?></td>
                </tr>
            </table>
            <!-- Footer Nota -->
            <?php if ($payment_detail): ?>
                <div class="text-center mt-4">
                    <p class="text-muted">Terima kasih atas pembayaran Anda.</p>
                    <p class="text-muted">Informasi lebih lanjut hubungi: <a href="https://wa.me/+6281553332118" target="_blank" class="text-decoration-none">081334900662</a></p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#0099ff" fill-opacity="1" d="M0,128L40,154.7C80,181,160,235,240,224C320,213,400,139,480,101.3C560,64,640,64,720,90.7C800,117,880,171,960,176C1040,181,1120,139,1200,128C1280,117,1360,139,1400,149.3L1440,160L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z"></path>
                </svg>
            <?php else: ?>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-gradient-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        Bayar Sekarang
                    </button>
                </div>

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#ffd700" fill-opacity="1" d="M0,128L40,154.7C80,181,160,235,240,224C320,213,400,139,480,101.3C560,64,640,64,720,90.7C800,117,880,171,960,176C1040,181,1120,139,1200,128C1280,117,1360,139,1400,149.3L1440,160L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z"></path>
                </svg>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Pilih Metode Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="paymentAccordion">
                    <!-- Transfer Manual -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne">
                                Transfer Manual
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse show" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                <p>Untuk melakukan pembayaran secara manual, silakan transfer sejumlah:</p>
                                <div class="d-flex align-items-center">
                                    <span id="nominalIuran"><?= number_format($nominal_iuran, 0, ',', '.') ?></span>
                                    <button class="btn btn-gradient-success btn-sm ms-2" onclick="copyToClipboard('#nominalIuran')">Copy</button>
                                </div>
                                <br>
                                <p>ke rekening:</p>
                                <div class="d-flex align-items-center">
                                    <span id="nomorRekening">205001016498505</span>
                                    <span>&nbsp;| BRI An. M Diky F</span>
                                    <button class="btn btn-gradient-success btn-sm ms-2" onclick="copyToClipboard('#nomorRekening')">Copy</button>
                                </div>
                                <br>
                                <p>Setelah melakukan transfer, konfirmasikan pembayaran Anda kepada admin dengan menyertakan nomor transaksi atau bukti transfer melalui email atau aplikasi pesan yang tersedia.</p>
                                <p>Pastikan untuk menyertakan informasi yang jelas dan lengkap agar proses verifikasi dapat dilakukan dengan cepat. Jika ada pertanyaan atau kendala, jangan ragu untuk menghubungi admin.</p>
                                <a href="transfer-manual.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-primary">Saya sudah Transfer</a>
                            </div>
                        </div>
                    </div>

                    <!-- Pembayaran menggunakan Midtrans -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                Pembayaran menggunakan Midtrans
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                <p>Untuk pembayaran menggunakan Midtrans, Anda dapat memilih metode pembayaran yang tersedia di platform Midtrans seperti kartu kredit, transfer bank, atau dompet digital. Proses pembayaran ini akan diarahkan ke halaman Midtrans untuk menyelesaikan transaksi.</p>
                                <p>Harap diperhatikan bahwa setiap transaksi melalui Midtrans akan dikenakan biaya transaksi sebesar Rp 4000 dan PPN sebesar Rp 400 yang akan ditambahkan pada total pembayaran Anda.</p>
                                <a id="pay-button" class="btn btn-success">Bayar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(element) {
        var textToCopy = document.querySelector(element).innerText;

        // Memastikan Clipboard API tersedia
        if (navigator.clipboard) {
            navigator.clipboard.writeText(textToCopy).then(() => {
                // Menggunakan SweetAlert untuk notifikasi
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Teks telah disalin!',
                    timer: 1000,
                    showConfirmButton: false
                });
            }).catch(err => {
                console.error('Gagal menyalin teks: ', err);
            });
        } else {
            // Fallback ke method lama jika Clipboard API tidak tersedia
            var temp = document.createElement('textarea');
            temp.value = textToCopy;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Teks telah disalin!',
                timer: 1000,
                showConfirmButton: false
            });
        }
    }
</script>
<?php include('pages/santri/dashboard/controller/pembayaran-uang-makan.php') ?>