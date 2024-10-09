<?php
$id_user = $_SESSION['id'];

// Query untuk mendapatkan daftar iuran yang tersedia
$sql_iuran = "SELECT ji.nama_iuran 
FROM 
    iuran_kelas ik
JOIN 
    jenis_iuran ji ON ik.id_iuran = ji.id_iuran
JOIN 
    santri s ON ik.id_kelas = s.id_kelas
WHERE 
    s.id_user = ?
    AND ik.status_aktif = 1";

$stmt_iuran = $conn->prepare($sql_iuran);
$stmt_iuran->bind_param('i', $id_user);
$stmt_iuran->execute();
$result_iuran = $stmt_iuran->get_result();

// Buat array untuk menyimpan nama iuran yang tersedia
$available_iurans = [];
while ($row = $result_iuran->fetch_assoc()) {
    $available_iurans[] = $row['nama_iuran'];
}
?>

<div class="container-fluid mb-lg-5" style="padding-bottom: 80px;">
    <div class="container mt-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <button class="btn btn-gradient-success rounded-circle p-0" onclick="window.history.back()" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>
        </div>
        <div class="col text-center">
            <h1 class="m-0">AlhalimPay</h1>
        </div>
        <p class="text-center text-muted">Sistem pembayaran online uang makan dan pembayaran lainnya</p>
        <hr>
        <div class="row text-center g-3 mt-3">
            <?php if (in_array('Uang Makan', $available_iurans)):  ?>
                <div class="col-3">
                    <a href="?page=uang-makan" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="assets/images/dashboard/uang-makan.png" alt="Uang Makan" class="img-fluid mb-2" width="70">
                                <p class="card-text text-dark">Uang Makan</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if (in_array('Sumbangan Pembangunan', $available_iurans)):  ?>
                <div class="col-3">
                    <a href="link-ke-halaman-pembangunan.html" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="assets/images/dashboard/pembangunan.png" alt="Pembangunan" class="img-fluid mb-2" width="70">
                                <p class="card-text text-dark">Sumbangan Pembangunan</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if (in_array('Seragam Ma\'had', $available_iurans)):  ?>
                <div class="col-3">
                    <a href="link-ke-halaman-seragam-mahad.html" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="assets/images/dashboard/seragam.png" alt="Seragam Ma'had" class="img-fluid mb-2" width="70">
                                <p class="card-text text-dark">Seragam Ma'had</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if (in_array('Ziarah Wali', $available_iurans)):  ?>
                <div class="col-3">
                    <a href="link-ke-halaman-ziarah-wali.html" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="assets/images/dashboard/ziarah.png" alt="Ziarah Wali" class="img-fluid mb-2" width="70">
                                <p class="card-text text-dark">Ziarah Wali</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>