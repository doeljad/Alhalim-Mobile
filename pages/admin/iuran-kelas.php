<?php
$success_message = '';
$error_message = '';

// Tambah iuran kelas baru jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('config/connection.php'); // Pastikan koneksi sudah terinclude

    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $id_iuran = $_POST['id_iuran'];
        $id_kelas = $_POST['id_kelas'];
        $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

        // Cek jika "Semua Kelas" dipilih
        if ($id_kelas == 'semua kelas') {
            // Ambil semua id_kelas dari tabel kelas
            $kelas_sql = "SELECT id_kelas FROM kelas";
            $kelas_result = $conn->query($kelas_sql);
            if ($kelas_result->num_rows > 0) {
                while ($kelas_row = $kelas_result->fetch_assoc()) {
                    $kelas_id = $kelas_row["id_kelas"];

                    // Periksa apakah entri sudah ada
                    $check_sql = "SELECT * FROM iuran_kelas WHERE id_iuran = '$id_iuran' AND id_kelas = '$kelas_id'";
                    $check_result = $conn->query($check_sql);

                    if ($check_result->num_rows == 0) {
                        // Entri belum ada, lakukan insert
                        $sql = "INSERT INTO iuran_kelas (id_iuran, id_kelas, status_aktif) VALUES ('$id_iuran', '$kelas_id', '$status_aktif')";
                        if ($conn->query($sql) !== TRUE) {
                            $error_message = "Error: " . $sql . "<br>" . $conn->error;
                            break; // Hentikan proses jika terjadi kesalahan
                        }
                    }
                }
                if (empty($error_message)) {
                    $success_message = "Iuran Kelas baru berhasil ditambahkan untuk semua kelas";
                }
            } else {
                $error_message = "Tidak ada kelas ditemukan untuk ditambahkan.";
            }
        } else {
            // Cek apakah entri sudah ada
            $check_sql = "SELECT * FROM iuran_kelas WHERE id_iuran = '$id_iuran' AND id_kelas = '$id_kelas'";
            $check_result = $conn->query($check_sql);

            if ($check_result->num_rows == 0) {
                // Entri belum ada, lakukan insert
                $sql = "INSERT INTO iuran_kelas (id_iuran, id_kelas, status_aktif) VALUES ('$id_iuran', '$id_kelas', '$status_aktif')";
                if ($conn->query($sql) === TRUE) {
                    $success_message = "Iuran Kelas baru berhasil ditambahkan!";
                } else {
                    $error_message = "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $success_message = "Iuran Kelas sudah ada untuk kelas yang dipilih.";
            }
        }
    }

    // Edit iuran kelas
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id_iuran_kelas = $_POST['id_iuran_kelas'];
        $id_iuran = $_POST['id_iuran'];
        $id_kelas = $_POST['id_kelas'];
        $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

        $sql = "UPDATE iuran_kelas SET id_iuran='$id_iuran', id_kelas='$id_kelas', status_aktif='$status_aktif' WHERE id_iuran_kelas=$id_iuran_kelas";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Iuran Kelas berhasil diperbarui!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Hapus iuran kelas
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id_iuran_kelas = $_POST['id_iuran_kelas'];

        $sql = "DELETE FROM iuran_kelas WHERE id_iuran_kelas=$id_iuran_kelas";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Iuran Kelas berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data iuran kelas dari database
$sql = "SELECT ik.*, i.nama_iuran, k.tingkat
        FROM iuran_kelas ik
        JOIN jenis_iuran i ON ik.id_iuran = i.id_iuran
        JOIN kelas k ON ik.id_kelas = k.id_kelas";
$result = $conn->query($sql);

$conn->close();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Data Iuran Kelas</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Iuran Kelas</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 1) : ?>
                            <button type="button" class="btn btn-gradient-primary float-end mb-4" data-toggle="modal" data-target="#addIuranKelasModal">
                                Tambah Iuran Kelas
                            </button>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Iuran</th>
                                        <th>Kelas</th>
                                        <th>Status Aktif</th>
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
                                            echo "<td>" . $row["tingkat"] . "</td>";
                                            echo "<td>" . ($row["status_aktif"] ? 'Aktif' : 'Non-Aktif') . "</td>";
                                            if ($_SESSION['role'] == 1) {
                                                echo "<td>
                        <button class='btn btn-gradient-warning btn-rounded btn-sm' onclick='editIuranKelas(" . json_encode($row) . ")'><i class='fa fa-pencil' aria-hidden='true'></i></button>
                        <button class='btn btn-gradient-danger btn-rounded btn-sm' onclick='deleteIuranKelas(" . $row["id_iuran_kelas"] . ")'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
                        </td>";
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>Tidak ada data iuran kelas ditemukan</td></tr>";
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

<!-- Modal Tambah/Edit Iuran Kelas -->
<div class="modal fade" id="addIuranKelasModal" tabindex="-1" role="dialog" aria-labelledby="addIuranKelasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIuranKelasModalLabel">Tambah Iuran Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="action" value="add" id="modalAction">
                    <input type="hidden" name="id_iuran_kelas" id="iuranKelasId">
                    <div class="form-group">
                        <label for="id_iuran">Iuran</label>
                        <select class="form-control" id="id_iuran" name="id_iuran" required>
                            <option value="">Pilih Iuran</option>
                            <?php
                            // Ambil data iuran dari database
                            include('config/connection.php');
                            $iuran_sql = "SELECT id_iuran, nama_iuran FROM jenis_iuran";
                            $iuran_result = $conn->query($iuran_sql);
                            if ($iuran_result->num_rows > 0) {
                                while ($iuran_row = $iuran_result->fetch_assoc()) {
                                    echo "<option value='" . $iuran_row["id_iuran"] . "'>"
                                        . $iuran_row["nama_iuran"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_kelas">Kelas</label>
                        <select class="form-control" id="id_kelas" name="id_kelas" required>
                            <option value="">Pilih Kelas</option>
                            <?php
                            // Ambil data kelas dari database
                            include('config/connection.php');
                            $kelas_sql = "SELECT id_kelas, tingkat FROM kelas";
                            $kelas_result = $conn->query($kelas_sql);
                            if ($kelas_result->num_rows > 0) {
                                while ($kelas_row = $kelas_result->fetch_assoc()) {
                                    echo "<option value='" . $kelas_row["id_kelas"] . "'>"
                                        . $kelas_row["tingkat"] . "</option>";
                                }
                            }
                            ?>
                            <option value="semua kelas">Pilih Semua Kelas</option>
                        </select>
                    </div>
                    <div class="form-check form-switch ms-5 card">
                        <input class="form-check-input" type="checkbox" role="switch" id="status_aktif" name="status_aktif" checked>
                        <label class="form-check-label" for="status_aktif">Status Akif</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Hapus Iuran Kelas -->
<form id="deleteIuranKelasForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_iuran_kelas" id="deleteIuranKelasId">
</form>

<script>
    function editIuranKelas(iuranKelas) {
        $('#addIuranKelasModalLabel').text('Edit Iuran Kelas');
        $('#modalAction').val('edit');
        $('#iuranKelasId').val(iuranKelas.id_iuran_kelas);
        $('#id_iuran').val(iuranKelas.id_iuran);
        $('#id_kelas').val(iuranKelas.id_kelas);
        $('#status_aktif').prop('checked', iuranKelas.status_aktif == 1);
        $('#addIuranKelasModal').modal('show');
    }

    function deleteIuranKelas(id_iuran_kelas) {
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
                $('#deleteIuranKelasId').val(id_iuran_kelas);
                $('#deleteIuranKelasForm').submit();
            }
        });
    }

    $('#addIuranKelasModal').on('hidden.bs.modal', function() {
        $('#addIuranKelasModalLabel').text('Tambah Iuran Kelas');
        $('#modalAction').val('add');
        $('#iuranKelasId').val('');
        $('#id_iuran').val('');
        $('#id_kelas').val('');
        $('#status_aktif').prop('checked', false);
    });
</script>