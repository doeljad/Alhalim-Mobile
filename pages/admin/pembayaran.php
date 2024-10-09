<?php
$success_message = '';
$error_message = '';

// Tambah pembayaran baru jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('config/connection.php'); // Pastikan koneksi sudah terinclude

    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $id_santri = $_POST['id_santri'];
        $id_iuran = $_POST['id_iuran'];
        $tanggal_pembayaran = date('Y-m-d H:i:s');
        $jumlah_bayar = $_POST['jumlah_bayar'];
        $metode_pembayaran = $_POST['metode_pembayaran'];
        $bulan_pembayaran = $_POST['bulan_pembayaran'];
        $tahun_pembayaran = $_POST['tahun_pembayaran'];

        // Cek apakah santri sudah membayar pada bulan dan tahun yang dipilih
        $cek_sql = "SELECT COUNT(*) as count FROM pembayaran 
                    WHERE id_santri = '$id_santri' 
                    AND id_iuran = '$id_iuran' 
                    AND bulan_pembayaran = '$bulan_pembayaran' 
                    AND tahun_pembayaran = '$tahun_pembayaran'";

        $result = $conn->query($cek_sql);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            // Jika santri sudah membayar pada bulan dan tahun yang dipilih
            $error_message = "Santri sudah membayar pada bulan tersebut";
        } else {
            // Jika belum membayar, tambahkan data pembayaran
            $sql = "INSERT INTO pembayaran (id_santri, id_iuran, tanggal_pembayaran, jumlah_bayar, metode_pembayaran, bulan_pembayaran, tahun_pembayaran) 
                    VALUES ('$id_santri', '$id_iuran', '$tanggal_pembayaran', '$jumlah_bayar', '$metode_pembayaran', '$bulan_pembayaran', '$tahun_pembayaran')";

            if ($conn->query($sql) === TRUE) {
                $success_message = "Pembayaran baru berhasil ditambahkan!";
            } else {
                $error_message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }


    // Edit pembayaran
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id_pembayaran = $_POST['id_pembayaran'];
        $id_santri = $_POST['id_santri'];
        $id_iuran = $_POST['id_iuran'];
        $tanggal_pembayaran = $_POST['tanggal_pembayaran'];
        $jumlah_bayar = $_POST['jumlah_bayar'];
        $metode_pembayaran = $_POST['metode_pembayaran'];
        $bulan_pembayaran = $_POST['bulan_pembayaran'];
        $tahun_pembayaran = $_POST['tahun_pembayaran'];

        $sql = "UPDATE pembayaran SET id_santri='$id_santri', id_iuran='$id_iuran', tanggal_pembayaran='$tanggal_pembayaran', jumlah_bayar='$jumlah_bayar', metode_pembayaran='$metode_pembayaran', bulan_pembayaran='$bulan_pembayaran', tahun_pembayaran='$tahun_pembayaran' WHERE id_pembayaran=$id_pembayaran";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Pembayaran berhasil diperbarui!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Hapus pembayaran
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id_pembayaran = $_POST['id_pembayaran'];

        $sql = "DELETE FROM pembayaran WHERE id_pembayaran=$id_pembayaran";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Pembayaran berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data pembayaran dari database
$sql = "SELECT p.*, s.nama_santri, i.nama_iuran, i.nominal
FROM pembayaran p
JOIN santri s ON p.id_santri = s.id_santri
JOIN jenis_iuran i ON p.id_iuran = i.id_iuran
ORDER BY p.tanggal_pembayaran DESC;";
$result = $conn->query($sql);

$conn->close();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Data Pembayaran</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Pembayaran</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 1) : ?>
                            <button type="button" class="btn btn-gradient-primary float-end mb-4" data-toggle="modal" data-target="#addPembayaranModal">
                                Tambah Pembayaran
                            </button>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Santri</th>
                                        <th>Nama Iuran</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Status</th>
                                        <th>Bulan</th>
                                        <th>Tahun</th>
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
                                            // Tentukan status pembayaran
                                            $card_class = ($row["metode_pembayaran"] === 'lunas') ? 'bg-success text-white' : 'bg-warning text-white';
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row["nama_santri"] . "</td>";
                                            echo "<td>" . $row["nama_iuran"] . "</td>";
                                            echo "<td>" . $row["tanggal_pembayaran"] . "</td>";
                                            echo "<td>Rp " . number_format($row["jumlah_bayar"], 0, ',', '.') . "</td>";
                                            echo "<td class='$card_class'>" . $row["metode_pembayaran"] . "</td>";
                                            echo "<td>" . $bulan_mapping[$row["bulan_pembayaran"]] . "</td>";
                                            echo "<td>" . $row["tahun_pembayaran"] . "</td>";
                                            if ($_SESSION['role'] == 1) {
                                                echo "<td>
                        <button class='btn btn-gradient-warning btn-rounded btn-sm' onclick='editPembayaran(" . json_encode($row) . ")'><i class='fa fa-pencil' aria-hidden='true'></i></button>
                        <button class='btn btn-gradient-danger btn-rounded btn-sm' onclick='deletePembayaran(" . $row["id_pembayaran"] . ")'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
                        </td>";
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'>Tidak ada data pembayaran ditemukan</td></tr>";
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

<!-- Modal Tambah/Edit Pembayaran -->
<div class="modal fade" id="addPembayaranModal" tabindex="-1" role="dialog" aria-labelledby="addPembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPembayaranModalLabel">Tambah Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="action" value="add" id="modalAction">
                    <input type="hidden" name="id_pembayaran" id="pembayaranId">

                    <div class="form-group">
                        <label for="id_santri">Santri</label>
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

                    <div class="form-group" style="display: none;" id="iuran-container">
                        <label for="id_iuran">Iuran</label>
                        <select class="form-control" id="id_iuran" name="id_iuran" required>
                            <option value="">Pilih Iuran</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_bayar">Jumlah Bayar</label>
                        <input type="number" class="form-control" id="jumlah_bayar" name="jumlah_bayar" required>
                    </div>

                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select class="form-control bg-white" id="metode_pembayaran" name="metode_pembayaran" readonly required>
                            <option value="lunas">Lunas</option>
                            <option value="cicilan">Cicilan</option>
                        </select>
                    </div>

                    <?php
                    // Mendapatkan waktu saat ini dalam format yang sesuai untuk input datetime-local
                    ?>
                    <div class="form-group">
                        <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                        <input type="datetime-local" class="form-control bg-white" id="tanggal_pembayaran" name="tanggal_pembayaran" readonly>
                    </div>
                    <div class="form-group">
                        <label for="bulan_pembayaran">Bulan Pembayaran</label>
                        <select class="form-control" id="bulan_pembayaran" name="bulan_pembayaran" required>
                            <?php
                            foreach ($bulan_mapping as $key => $value) {
                                echo "<option value='$key'>$value</option>";
                            }
                            ?>
                        </select>
                    </div>


                    <?php $current_year = date('Y'); ?>
                    <div class="form-group">
                        <label for="tahun_pembayaran">Tahun Pembayaran</label>
                        <input type="text" class="form-control" id="tahun_pembayaran" name="tahun_pembayaran" value="<?php echo $current_year; ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Hapus Pembayaran -->
<form id="deletePembayaranForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_pembayaran" id="deletePembayaranId">
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mendapatkan tanggal bulan dan tahun saat ini
        var today = new Date();
        var currentMonth = today.getMonth() + 1; // GetMonth is zero-based, so add 1
        var bulanSelect = document.getElementById('bulan_pembayaran');

        // Mengatur nilai bulan saat ini sebagai terpilih
        bulanSelect.value = currentMonth.toString();


    });

    $(document).ready(function() {
        var santriSelect = $('#id_santri');
        var iuranSelect = $('#id_iuran');
        var iuranContainer = $('#iuran-container');
        var metodePembayaranSelect = $('#metode_pembayaran');
        var jumlahBayarInput = $('#jumlah_bayar');

        santriSelect.change(function() {
            var santriId = $(this).val();

            if (santriId) {
                // Tampilkan iuran jika santri dipilih
                iuranContainer.show();

                // Lakukan AJAX request untuk memperbarui dropdown iuran
                $.ajax({
                    url: 'pages/admin/controller/get_iuran.php',
                    type: 'GET',
                    data: {
                        id_santri: santriId
                    },
                    success: function(response) {
                        var iuranData = JSON.parse(response);

                        // Kosongkan dropdown iuran sebelum diisi ulang
                        iuranSelect.empty().append('<option value="">Pilih Iuran</option>');

                        // Isi dropdown dengan data iuran
                        iuranData.forEach(function(iuran) {
                            var option = $('<option></option>').val(iuran.id_iuran)
                                .text(iuran.nama_iuran)
                                .attr('data-nominal', iuran.nominal);
                            iuranSelect.append(option);
                        });
                    }
                });
            } else {
                // Sembunyikan iuran jika tidak ada santri yang dipilih
                iuranContainer.hide();
                iuranSelect.empty().append('<option value="">Pilih Iuran</option>');
            }
        });

        // Event listener untuk perubahan pada dropdown Iuran
        iuranSelect.change(function() {
            var selectedOption = $(this).find('option:selected');
            var nominal = selectedOption.data('nominal');

            // Set nilai ke input jumlah_bayar
            jumlahBayarInput.val(nominal);

            // Tentukan metode pembayaran
            jumlahBayarInput.on('input', function() {
                var jumlahBayar = parseFloat($(this).val());
                if (jumlahBayar === parseFloat(nominal)) {
                    metodePembayaranSelect.val('lunas');
                } else {
                    metodePembayaranSelect.val('cicilan');
                }
            });
        });

        // Set nilai default saat modal pertama kali dibuka
        $('#addPembayaranModal').on('shown.bs.modal', function() {
            document.getElementById('tanggal_pembayaran').value = getJakartaTime();
        });

        // Reset form saat modal ditutup
        $('#addPembayaranModal').on('hidden.bs.modal', function() {
            $('#modalAction').val('add');
            $('#pembayaranId').val('');
            santriSelect.val('');
            iuranSelect.empty().append('<option value="">Pilih Iuran</option>');
            $('#tanggal_pembayaran').val('');
            jumlahBayarInput.val('');
            metodePembayaranSelect.val('lunas');
            $('#bulan_pembayaran').val('');
            $('#tahun_pembayaran').val('');
        });
    });


    function editPembayaran(pembayaran) {
        $('#addPembayaranModalLabel').text('Edit Pembayaran');
        $('#modalAction').val('edit');
        $('#pembayaranId').val(pembayaran.id_pembayaran);
        $('#id_santri').val(pembayaran.id_santri);
        $('#id_iuran').val(pembayaran.id_iuran);
        $('#tanggal_pembayaran').val(pembayaran.tanggal_pembayaran);
        $('#jumlah_bayar').val(pembayaran.jumlah_bayar);
        $('#metode_pembayaran').val(pembayaran.metode_pembayaran);
        $('#bulan_pembayaran').val(pembayaran.bulan_pembayaran);
        $('#tahun_pembayaran').val(pembayaran.tahun_pembayaran);
        $('#addPembayaranModal').modal('show');
    }

    function deletePembayaran(id_pembayaran) {
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
                $('#deletePembayaranId').val(id_pembayaran);
                $('#deletePembayaranForm').submit();
            }
        });
    }

    $('#addPembayaranModal').on('hidden.bs.modal', function() {
        $('#addPembayaranModalLabel').text('Tambah Pembayaran');
        $('#modalAction').val('add');
        $('#pembayaranId').val('');
        $('#id_santri').val('');
        $('#id_iuran').val('');
        $('#tanggal_pembayaran').val('');
        $('#jumlah_bayar').val('');
        $('#metode_pembayaran').val('');
        $('#bulan_pembayaran').val('');
        $('#tahun_pembayaran').val('');
    });
</script>