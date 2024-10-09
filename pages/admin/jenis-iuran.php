<?php
$success_message = '';
$error_message = '';

// Tambah jenis iuran baru jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $nama_iuran = $_POST['nama_iuran'];
        $deskripsi = $_POST['deskripsi'];
        $nominal = $_POST['nominal'];
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $dapat_dicicil = isset($_POST['dapat_dicicil']) ? 1 : 0;

        $sql = "INSERT INTO jenis_iuran (nama_iuran, deskripsi, nominal, tanggal_mulai, tanggal_selesai, dapat_dicicil) 
                VALUES ('$nama_iuran', '$deskripsi', '$nominal', '$tanggal_mulai', '$tanggal_selesai', '$dapat_dicicil')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Jenis iuran baru berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Edit jenis iuran
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id_iuran = $_POST['id_iuran'];
        $nama_iuran = $_POST['nama_iuran'];
        $deskripsi = $_POST['deskripsi'];
        $nominal = $_POST['nominal'];
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $dapat_dicicil = isset($_POST['dapat_dicicil']) ? 1 : 0;

        $sql = "UPDATE jenis_iuran SET nama_iuran='$nama_iuran', deskripsi='$deskripsi', nominal='$nominal', 
                tanggal_mulai='$tanggal_mulai', tanggal_selesai='$tanggal_selesai', dapat_dicicil='$dapat_dicicil' 
                WHERE id_iuran=$id_iuran";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Jenis iuran berhasil diperbarui!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Hapus jenis iuran
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id_iuran = $_POST['id_iuran'];

        $sql = "DELETE FROM jenis_iuran WHERE id_iuran=$id_iuran";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Jenis iuran berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data jenis iuran dari database
$sql = "SELECT * FROM jenis_iuran";
$result = $conn->query($sql);

$conn->close();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Data Jenis Iuran</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Jenis Iuran</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 1) : ?>
                            <button type="button" class="btn btn-gradient-primary float-end mb-4" data-toggle="modal" data-target="#addJenisIuranModal">
                                Tambah Jenis Iuran
                            </button>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id="jenisIuranTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Iuran</th>
                                        <th>Deskripsi</th>
                                        <th>Nominal</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Dapat Dicicil</th>
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
                                            echo "<td>" . $row["nama_iuran"] . "</td>";
                                            echo "<td>" . $row["deskripsi"] . "</td>";
                                            echo "<td>Rp " . number_format($row["nominal"], 0, ',', '.') . "</td>";
                                            echo "<td>" . $row["tanggal_mulai"] . "</td>";
                                            echo "<td>" . $row["tanggal_selesai"] . "</td>";
                                            echo "<td>" . ($row["dapat_dicicil"] ? 'Ya' : 'Tidak') . "</td>";
                                            if ($_SESSION['role'] == 1) {
                                                echo "<td>
                        <button class='btn btn-gradient-warning btn-rounded btn-sm' onclick='editJenisIuran(" . json_encode($row) . ")'><i class='fa fa-pencil' aria-hidden='true'></i></button>
                        <button class='btn btn-gradient-danger btn-rounded btn-sm' onclick='deleteJenisIuran(" . $row["id_iuran"] . ")'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
                        </td>";
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>Tidak ada jenis iuran ditemukan</td></tr>";
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

<!-- Modal Tambah/Edit Jenis Iuran -->
<div class="modal fade" id="addJenisIuranModal" tabindex="-1" role="dialog" aria-labelledby="addJenisIuranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addJenisIuranModalLabel">Tambah Jenis Iuran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="action" value="add" id="modalAction">
                    <input type="hidden" name="id_iuran" id="iuranId">
                    <div class="form-group">
                        <label for="nama_iuran">Nama Iuran</label>
                        <input type="text" class="form-control" id="nama_iuran" name="nama_iuran" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input type="number" class="form-control" id="nominal" name="nominal" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                    </div>
                    <div class="form-group ">
                        <div class="form-check ms-5 form-switch card">
                            <input class="form-check-input" type="checkbox" id="dapat_dicicil" name="dapat_dicicil" checked>
                            <label class="form-check-label" for="dapat_dicicil">Dapat Dicicil</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Hapus Jenis Iuran -->
<form id="deleteJenisIuranForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_iuran" id="deleteIuranId">
</form>
<script>
    $(document).ready(function() {
        $('#jenisIuranTable').DataTable();
    });

    function editJenisIuran(iuran) {
        $('#addJenisIuranModalLabel').text('Edit Jenis Iuran');
        $('#modalAction').val('edit');
        $('#iuranId').val(iuran.id_iuran);
        $('#nama_iuran').val(iuran.nama_iuran);
        $('#deskripsi').val(iuran.deskripsi);
        $('#nominal').val(iuran.nominal);
        $('#tanggal_mulai').val(iuran.tanggal_mulai);
        $('#tanggal_selesai').val(iuran.tanggal_selesai);
        $('#dapat_dicicil').prop('checked', iuran.dapat_dicicil == 1);
        $('#addJenisIuranModal').modal('show');
    }

    function deleteJenisIuran(id_iuran) {
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
                $('#deleteIuranId').val(id_iuran);
                $('#deleteJenisIuranForm').submit();
            }
        });
    }

    $('#addJenisIuranModal').on('hidden.bs.modal', function() {
        $('#addJenisIuranModalLabel').text('Tambah Jenis Iuran');
        $('#modalAction').val('add');
        $('#iuranId').val('');
        $('#nama_iuran').val('');
        $('#deskripsi').val('');
        $('#nominal').val('');
        $('#tanggal_mulai').val('');
        $('#tanggal_selesai').val('');
        $('#dapat_dicicil').prop('checked', false);
    });
</script>