<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pembayaran = isset($_POST['id_pembayaran']) ? intval($_POST['id_pembayaran']) : 0;
    $id_santri = intval($_POST['id_santri']);
    $id_iuran = intval($_POST['id_iuran']);
    $tanggal_pembayaran = date('Y-m-d H:i:s'); // Format tanggal dan waktu saat ini
    $jumlah_bayar = floatval($_POST['jumlah_bayar']);
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $bulan_pembayaran = $_POST['bulan_pembayaran'];
    $tahun_pembayaran = date('Y'); // Atau gunakan tahun yang sesuai jika diubah

    if ($id_pembayaran) {
        // Update pembayaran yang sudah ada
        $sql = "UPDATE pembayaran SET 
            id_santri = ?, 
            id_iuran = ?, 
            tanggal_pembayaran = ?, 
            jumlah_bayar = ?, 
            metode_pembayaran = ?
            WHERE id_pembayaran = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iisssi', $id_santri, $id_iuran, $tanggal_pembayaran, $jumlah_bayar, $metode_pembayaran, $id_pembayaran);
    } else {
        // Insert pembayaran baru
        $sql = "INSERT INTO pembayaran (id_santri, id_iuran, tanggal_pembayaran, jumlah_bayar, metode_pembayaran, bulan_pembayaran, tahun_pembayaran)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iisssss', $id_santri, $id_iuran, $tanggal_pembayaran, $jumlah_bayar, $metode_pembayaran, $bulan_pembayaran, $tahun_pembayaran);
    }

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pembayaran berhasil disimpan.',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '?page=laporan-uang-makan&tahun=$tahun_pembayaran';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyimpan data pembayaran.',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}



// Ambil tahun saat ini dan tahun sebelumnya
$current_year = date('Y');
$previous_year = $current_year - 1;

// Dapatkan tahun yang dipilih dari parameter URL (default ke tahun ini)
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : $current_year;

// Validasi tahun yang dipilih
if (!in_array($tahun, [$previous_year, $current_year])) {
    $tahun = $current_year;
}

// Ambil data pembayaran dari database berdasarkan tahun yang dipilih
$sql = "SELECT p.*, s.nama_santri, i.nama_iuran, i.nominal
        FROM pembayaran p
        JOIN santri s ON p.id_santri = s.id_santri
        JOIN jenis_iuran i ON p.id_iuran = i.id_iuran
        JOIN iuran_kelas ik ON i.id_iuran = ik.id_iuran
        WHERE ik.status_aktif = 1 AND YEAR(p.tanggal_pembayaran) = $tahun
        ORDER BY p.tanggal_pembayaran DESC";

$result = $conn->query($sql);

// Ambil data santri untuk membuat daftar
$santri_sql = "SELECT id_santri, nama_santri FROM santri";
$santri_result = $conn->query($santri_sql);

$santri_data = [];
if ($santri_result->num_rows > 0) {
    while ($santri_row = $santri_result->fetch_assoc()) {
        $santri_data[$santri_row['id_santri']] = [
            'nama_santri' => $santri_row['nama_santri'],
            'pembayaran' => []
        ];
    }
}

// Populasi data pembayaran ke dalam array
while ($row = $result->fetch_assoc()) {
    // Pastikan id_santri ada dalam santri_data
    if (isset($santri_data[$row['id_santri']])) {
        // Pastikan bulan_pembayaran ada dalam row
        if (isset($row['bulan_pembayaran'])) {
            $santri_data[$row['id_santri']]['pembayaran'][$row['bulan_pembayaran']] = $row;
        } else {
            // Tangani kasus jika bulan_pembayaran tidak ada
            echo "Warning: 'bulan_pembayaran' key is missing in row.";
        }
    } else {
        // Tangani kasus jika id_santri tidak ada dalam santri_data
        echo "Warning: 'id_santri' key is missing in santri_data.";
    }
}


$conn->close();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Laporan Uang Makan</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan Uang Makan</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!-- Form Pilihan Tahun -->
                        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mb-4">
                            <input type="hidden" name="page" value="laporan-uang-makan">
                            <div class="form-group">
                                <label for="tahun">Pilih Tahun</label>
                                <select class="form-select" id="tahun" name="tahun">
                                    <option value="<?php echo $previous_year; ?>" <?php echo $tahun == $previous_year ? 'selected' : ''; ?>><?php echo $previous_year; ?></option>
                                    <option value="<?php echo $current_year; ?>" <?php echo $tahun == $current_year ? 'selected' : ''; ?>><?php echo $current_year; ?></option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <?php
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                                        foreach ($months as $month) {
                                            echo "<th>$month</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($santri_data as $id_santri => $data) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['nama_santri']); ?></td>
                                            <?php foreach ($months as $key => $month) : ?>
                                                <?php
                                                $month_index = $key + 1;
                                                $pembayaran = $data['pembayaran'][$month_index] ?? null;
                                                $checked = $pembayaran ? '✔' : '';
                                                $btn_class = 'light'; // Default button class

                                                if ($pembayaran) {
                                                    // Check the payment method and set button class accordingly
                                                    if ($pembayaran['metode_pembayaran'] === 'lunas') {
                                                        $btn_class = 'success'; // Green color for "lunas"
                                                    } elseif ($pembayaran['metode_pembayaran'] === 'cicilan') {
                                                        $btn_class = 'warning'; // Yellow color for "cicilan"
                                                    }
                                                }
                                                ?>
                                                <td data-bs-toggle="tooltip" title="<?php echo $checked ? 'Sudah dibayar' : 'Belum dibayar'; ?>">
                                                    <button class='btn btn-sm btn-<?php echo $btn_class; ?>'
                                                        onclick='editPembayaran(
                <?php echo json_encode($pembayaran); ?>, 
                "<?php echo $month; ?>", 
                "<?php echo $id_santri; ?>", 
                "<?php echo htmlspecialchars($data['nama_santri']); ?>", 
                "<?php echo htmlspecialchars($data['jenis_iuran'] ?? 'Uang Makan'); ?>", 
                "<?php echo 1 ?>", 
                <?php echo $month_index; ?>, 
                <?php echo $tahun; ?>)'>
                                                        <?php echo $checked; ?>
                                                    </button>
                                                </td>
                                            <?php endforeach; ?>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('pages/inc/footer.php') ?>
</div>

<!-- Modal untuk edit pembayaran -->
<div class="modal fade" id="editPembayaranModal" tabindex="-1" aria-labelledby="editPembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPembayaranModalLabel">Edit Pembayaran Bulan <span id="bulan_pembayaran_label"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPembayaranForm" method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id_pembayaran" id="id_pembayaran">
                    <input type="hidden" name="id_santri" id="id_santri">
                    <input type="hidden" name="id_iuran" id="id_iuran">
                    <input type="hidden" name="bulan_pembayaran" id="bulan_pembayaran">
                    <input type="hidden" name="tahun_pembayaran" id="tahun_pembayaran">

                    <div class="form-group">
                        <label for="nama_santri">Nama Santri</label>
                        <input type="text" class="form-control" id="nama_santri" name="nama_santri" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama_iuran">Jenis Iuran</label>
                        <input type="text" class="form-control" id="nama_iuran" name="nama_iuran" readonly>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_bayar">Jumlah Bayar</label>
                        <input type="text" class="form-control" id="jumlah_bayar" name="jumlah_bayar">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                        <input type="datetime-local" class="form-control" id="tanggal_pembayaran" name="tanggal_pembayaran" readonly>
                    </div>

                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <input type="text" class="form-control bg-white" id="metode_pembayaran" name="metode_pembayaran" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Ambil nominal dari id_iuran menggunakan fetch
    function fetchNominal(id_iuran) {
        return fetch(`pages/admin/controller/get-nominal.php?id_iuran=${id_iuran}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data.nominal;
                } else {
                    return 0;
                }
            })
            .catch(error => {
                console.error('Error fetching nominal:', error);
                return 0;
            });
    }

    function editPembayaran(pembayaran, bulan, id_santri, nama_santri, jenis_iuran, id_iuran, bulan_pembayaran, tahun_pembayaran) {
        if (pembayaran) {
            // Jika pembayaran tersedia
            document.getElementById('id_pembayaran').value = pembayaran.id_pembayaran;
            document.getElementById('id_santri').value = pembayaran.id_santri;
            document.getElementById('nama_santri').value = pembayaran.nama_santri;
            document.getElementById('nama_iuran').value = pembayaran.nama_iuran;
            document.getElementById('jumlah_bayar').value = pembayaran.jumlah_bayar;
            document.getElementById('tanggal_pembayaran').value = pembayaran.tanggal_pembayaran;
            document.getElementById('metode_pembayaran').value = pembayaran.metode_pembayaran;
            document.getElementById('id_iuran').value = pembayaran.id_iuran;

            // Ubah teks tombol menjadi "Simpan Perubahan"
            document.querySelector('#editPembayaranForm button[type="submit"]').textContent = 'Simpan Perubahan';
            // Ubah judul modal menjadi "Edit Pembayaran Bulan"
            document.getElementById('editPembayaranModalLabel').innerHTML = 'Edit Pembayaran Bulan <span id="bulan_pembayaran_label"></span>';

        } else {
            // Jika pembayaran belum tersedia
            document.getElementById('id_pembayaran').value = '';
            document.getElementById('id_santri').value = id_santri;
            document.getElementById('nama_santri').value = nama_santri;
            document.getElementById('nama_iuran').value = jenis_iuran;
            document.getElementById('jumlah_bayar').value = '';
            document.getElementById('id_iuran').value = id_iuran;
            document.getElementById('bulan_pembayaran').value = bulan_pembayaran;
            document.getElementById('tahun_pembayaran').value = tahun_pembayaran;

            // Ambil nominal jika belum ada pembayaran
            fetchNominal(id_iuran).then(nominal => {
                document.getElementById('jumlah_bayar').value = nominal;
                updateMetodePembayaran(nominal);
            });
            // Fungsi untuk mendapatkan waktu dalam format YYYY-MM-DDTHH:MM untuk zona waktu Asia/Jakarta


            // Set nilai default pada elemen input
            document.getElementById('tanggal_pembayaran').value = getJakartaTime();


            document.getElementById('metode_pembayaran').value = '';

            // Ubah teks tombol menjadi "Bayar"
            document.querySelector('#editPembayaranForm button[type="submit"]').textContent = 'Bayar';
            // Ubah judul modal menjadi "Pembayaran Bulan"
            document.getElementById('editPembayaranModalLabel').innerHTML = 'Pembayaran Bulan <span id="bulan_pembayaran_label"></span>';
        }

        // Mengisi label bulan
        document.getElementById('bulan_pembayaran_label').innerText = bulan;

        // Tampilkan modal
        var myModal = new bootstrap.Modal(document.getElementById('editPembayaranModal'), {});
        myModal.show();
    }

    function updateMetodePembayaran(nominal) {
        $('#jumlah_bayar').on('input', function() {
            var jumlahBayar = parseFloat($(this).val());

            // Jika jumlah bayar sama dengan nominal, maka metode pembayaran adalah "Lunas"
            if (jumlahBayar == nominal) {
                $('#metode_pembayaran').val('Lunas');
            } else {
                // Jika tidak sama, maka metode pembayaran adalah "Cicilan"
                $('#metode_pembayaran').val('Cicilan');
            }
        });

        // Trigger event input untuk mengecek kondisi awal
        $('#jumlah_bayar').trigger('input');
    }
</script>