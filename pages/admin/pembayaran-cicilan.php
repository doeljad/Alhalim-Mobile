<?php
$success_message = '';
$error_message = '';

// Tambah Pembayaran Cicilan jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('config/connection.php'); // Pastikan koneksi sudah terinclude

    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $id_pembayaran = $_POST['id_pembayaran'];
        $tanggal_cicilan = date('Y-m-d H:i:s');
        $jumlah_cicilan = $_POST['jumlah_cicilan'];

        // Gunakan prepared statements untuk keamanan
        $stmt_insert = $conn->prepare("INSERT INTO cicilan_pembayaran (id_pembayaran, tanggal_cicilan, jumlah_cicilan) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("isi", $id_pembayaran, $tanggal_cicilan, $jumlah_cicilan);

        if ($stmt_insert->execute()) {
            // Query untuk update metode_pembayaran
            $sql_update = "
            UPDATE pembayaran p
            JOIN (
                SELECT p.id_pembayaran, p.id_santri, p.id_iuran, j.nominal,
                       (p.jumlah_bayar + IFNULL((SELECT SUM(c.jumlah_cicilan) FROM cicilan_pembayaran c WHERE c.id_pembayaran = p.id_pembayaran), 0)) AS terbayar
                FROM pembayaran p
                JOIN jenis_iuran j ON p.id_iuran = j.id_iuran
                WHERE p.metode_pembayaran = 'cicilan'
            ) AS subquery ON p.id_pembayaran = subquery.id_pembayaran
            SET p.metode_pembayaran = 'lunas'
            WHERE subquery.nominal = subquery.terbayar;
        ";

            if ($conn->query($sql_update) === TRUE) {
                $success_message = "Cicilan pembayaran baru berhasil ditambahkan dan metode pembayaran diperbarui!";
            } else {
                $error_message = "Error: " . $sql_update . "<br>" . $conn->error;
            }
        } else {
            $error_message = "Error: " . $stmt_insert->error;
        }
    }

    // Edit cicilan pembayaran
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id_cicilan = $_POST['id_cicilan'];
        $id_pembayaran = $_POST['id_pembayaran'];
        $tanggal_cicilan = $_POST['tanggal_cicilan'];
        $jumlah_cicilan = $_POST['jumlah_cicilan'];

        $sql = "UPDATE cicilan_pembayaran SET id_pembayaran='$id_pembayaran', tanggal_cicilan='$tanggal_cicilan', jumlah_cicilan='$jumlah_cicilan' WHERE id_cicilan=$id_cicilan";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Cicilan pembayaran berhasil diperbarui!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Hapus cicilan pembayaran
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id_cicilan = $_POST['id_cicilan'];

        $sql = "DELETE FROM cicilan_pembayaran WHERE id_cicilan=$id_cicilan";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Cicilan pembayaran berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data cicilan pembayaran dari database
$sql = "SELECT cp.*, p.*,s.*,ji.*
        FROM cicilan_pembayaran cp
        JOIN pembayaran p ON cp.id_pembayaran = p.id_pembayaran
        JOIN santri s ON s.id_santri = p.id_santri
        JOIN jenis_iuran ji ON ji.id_iuran = p.id_iuran";
$result = $conn->query($sql);

$conn->close();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Data Cicilan Pembayaran</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Cicilan Pembayaran</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 1) : ?>
                            <button type="button" class="btn btn-gradient-primary float-end mb-4" data-toggle="modal" data-target="#addCicilanPembayaranModal">
                                Tambah Pembayaran Cicilan
                            </button>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Santri</th>
                                        <th>Pembayaran</th>
                                        <th>Tanggal Pembayaran</th>
                                        <th>Jumlah Cicilan</th>
                                        <?php if ($_SESSION['role'] == 1) : ?>
                                            <th>Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $no = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row["nama_santri"] . "</td>";
                                            echo "<td>" . $row["nama_iuran"] . ' - ' . $bulan_mapping[$row["bulan_pembayaran"]] . ', ' . $row["tahun_pembayaran"] . "</td>";
                                            echo "<td>" . $row["tanggal_cicilan"] . "</td>";
                                            echo "<td>Rp " . number_format($row["jumlah_cicilan"], 0, ',', '.') . "</td>";
                                            if ($_SESSION['role'] == 1) {
                                                echo "<td>
                        <button class='btn btn-gradient-warning btn-rounded btn-sm' onclick='editCicilanPembayaran(" . json_encode($row) . ")'><i class='fa fa-pencil' aria-hidden='true'></i></button>
                        <button class='btn btn-gradient-danger btn-rounded btn-sm' onclick='deleteCicilanPembayaran(" . $row["id_cicilan"] . ")'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
                        </td>";
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>Tidak ada data cicilan pembayaran ditemukan</td></tr>";
                                    }
                                    ?>
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

<!-- Modal Tambah/Edit Cicilan Pembayaran -->
<div class="modal fade" id="addCicilanPembayaranModal" tabindex="-1" role="dialog" aria-labelledby="addCicilanPembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCicilanPembayaranModalLabel">Tambah Pembayaran Cicilan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="action" value="add" id="modalAction">
                    <input type="hidden" name="id_cicilan" id="cicilanId">
                    <input type="hidden" class="form-control" id="id_pembayaran" name="id_pembayaran" required>
                    <div class="form-group">
                        <label for="id_santri">Nama Santri</label>
                        <select class="form-control" id="id_santri" name="id_santri" required>
                            <option value="">Pilih Santri</option>
                            <?php
                            // Ambil data santri dari database
                            include('config/connection.php');
                            $santri_sql = "SELECT id_santri, nama_santri FROM santri";
                            $santri_result = $conn->query($santri_sql);
                            if ($santri_result->num_rows > 0) {
                                while ($santri_row = $santri_result->fetch_assoc()) {
                                    echo "<option value='" . $santri_row["id_santri"] . "'>"
                                        . $santri_row["nama_santri"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group" id="select-iuran-container" style="display: none;">
                        <label for="id_iuran">Pembayaran Cicilan</label>
                        <select class="form-control" id="id_iuran" name="id_iuran" required>
                            <option value="">Pilih Cicilan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kekurangan">Kekurangan</label>
                        <input type="number" class="form-control bg-white" id="kekurangan" name="kekurangan" readonly>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_cicilan">Jumlah Cicilan</label>
                        <input type="number" class="form-control" id="jumlah_cicilan" name="jumlah_cicilan" required>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Hapus Cicilan Pembayaran -->
<form id="deleteCicilanPembayaranForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_cicilan" id="deleteCicilanPembayaranId">
</form>

<!-- Peringatan -->


<script>
    document.getElementById('id_santri').addEventListener('change', function() {
        var idSantri = this.value;
        var selectIuran = document.getElementById('id_iuran');
        var selectIuranContainer = document.getElementById('select-iuran-container');
        var kekuranganInput = document.getElementById('kekurangan');
        var jumlah_cicilanInput = document.getElementById('jumlah_cicilan');
        var id_pembayaranInput = document.getElementById('id_pembayaran');

        // Clear existing options
        selectIuran.innerHTML = '<option value="">Pilih Cicilan</option>';

        // Hide the select box and reset the kekurangan input by default
        selectIuranContainer.style.display = 'none';
        kekuranganInput.value = '';
        jumlah_cicilanInput.value = '';
        id_pembayaranInput.value = '';

        if (idSantri) {
            // AJAX request to get cicilan data
            fetch(`pages/admin/controller/get_cicilan.php?id_santri=${idSantri}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        // Populate the cicilan options
                        data.forEach(cicilan => {
                            var option = document.createElement('option');
                            var bulanMapping = [
                                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                            ];

                            var namaBulan = bulanMapping[cicilan.bulan_pembayaran - 1];

                            option.value = cicilan.id_pembayaran;
                            option.textContent = `${cicilan.nama_iuran} - ${namaBulan}, ${cicilan.tahun_pembayaran}`;
                            option.dataset.nominal = cicilan.nominal;
                            option.dataset.jumlahBayar = cicilan.jumlah_bayar;
                            option.dataset.idPembayaran = cicilan.id_pembayaran;
                            selectIuran.appendChild(option);
                        });

                        // Show the select box
                        selectIuranContainer.style.display = 'block';

                        // Calculate and display the kekurangan for the first cicilan by default
                        var firstCicilan = data[0];
                        kekuranganInput.value = firstCicilan.nominal - firstCicilan.jumlah_bayar;
                        jumlah_cicilanInput.value = firstCicilan.nominal - firstCicilan.jumlah_bayar;
                        id_pembayaranInput.value = firstCicilan.id_pembayaran;

                        // Update kekurangan when the cicilan selection changes
                        selectIuran.addEventListener('change', function() {
                            var selectedOption = selectIuran.options[selectIuran.selectedIndex];
                            var nominal = selectedOption.dataset.nominal;
                            var jumlahBayar = selectedOption.dataset.jumlahBayar;
                            var idPembayaran = selectedOption.dataset.idPembayaran;

                            kekuranganInput.value = nominal - jumlahBayar;
                            jumlah_cicilanInput.value = nominal - jumlahBayar;
                            id_pembayaranInput.value = idPembayaran;
                        });
                    } else {
                        // Show warning if no cicilan found
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada Tagihan',
                            text: 'Nama Santri tidak memiliki tagihan cicilan.',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan dalam memuat data cicilan.',
                        confirmButtonText: 'OK'
                    });
                });
        }
    });

    function editCicilanPembayaran(cicilanPembayaran) {
        $('#addCicilanPembayaranModalLabel').text('Edit Cicilan Pembayaran');
        $('#modalAction').val('edit');
        $('#cicilanId').val(cicilanPembayaran.id_cicilan);
        $('#id_pembayaran').val(cicilanPembayaran.id_pembayaran);
        $('#jumlah_cicilan').val(cicilanPembayaran.jumlah_cicilan);
        $('#addCicilanPembayaranModal').modal('show');
    }

    function deleteCicilanPembayaran(id_cicilan) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteCicilanPembayaranId').val(id_cicilan);
                $('#deleteCicilanPembayaranForm').submit();
            }
        });
    }

    $('#addCicilanPembayaranModal').on('hidden.bs.modal', function() {
        $('#addCicilanPembayaranModalLabel').text('Tambah Pembayaran Cicilan');
        $('#modalAction').val('add');
        $('#cicilanId').val('');
        $('#id_pembayaran').val('');
        $('#jumlah_cicilan').val('');
    });
</script>