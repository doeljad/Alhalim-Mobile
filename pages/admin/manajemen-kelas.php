<?php
$success_message = '';
$error_message = '';

// Tambah kelas baru jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $tingkat = $_POST['tingkat'];

        $sql = "INSERT INTO kelas (tingkat) 
                VALUES ( '$tingkat')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Kelas baru berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Edit kelas
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id_kelas = $_POST['id_kelas'];
        $tingkat = $_POST['tingkat'];

        $sql = "UPDATE kelas SET tingkat='$tingkat' 
                WHERE id_kelas=$id_kelas";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Kelas berhasil diperbarui!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Hapus kelas
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id_kelas = $_POST['id_kelas'];

        $sql = "DELETE FROM kelas WHERE id_kelas=$id_kelas";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Kelas berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data kelas dari database
$sql = "SELECT * FROM kelas";
$result = $conn->query($sql);

$conn->close();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Data Kelas</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Kelas</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 1) : ?>
                            <button type="button" class="btn btn-gradient-primary float-end mb-4" data-toggle="modal" data-target="#addKelasModal">
                                Tambah Kelas
                            </button>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id="kelasTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tingkat</th>
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
                                            echo "<td>" . $row["tingkat"] . "</td>";
                                            if ($_SESSION['role'] == 1) {
                                                echo "<td>
                        <button class='btn btn-gradient-warning btn-rounded btn-sm' onclick='editKelas(" . json_encode($row) . ")'><i class='fa fa-pencil' aria-hidden='true'></i></button>
                        <button class='btn btn-gradient-danger btn-rounded btn-sm' onclick='deleteKelas(" . $row["id_kelas"] . ")'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
                        </td>";
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>Tidak ada kelas ditemukan</td></tr>";
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

<!-- Modal Tambah/Edit Kelas -->
<div class="modal fade" id="addKelasModal" tabindex="-1" role="dialog" aria-labelledby="addKelasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addKelasModalLabel">Tambah Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="action" value="add" id="modalAction">
                    <input type="hidden" name="id_kelas" id="kelasId">
                    <div class="form-group">
                        <label for="tingkat">Tingkat</label>
                        <input type="text" class="form-control" id="tingkat" name="tingkat" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Hapus Kelas -->
<form id="deleteKelasForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_kelas" id="deleteKelasId">
</form>
<script>
    $(document).ready(function() {
        $('#kelasTable').DataTable();
    });

    function editKelas(kelas) {
        $('#addKelasModalLabel').text('Edit Kelas');
        $('#modalAction').val('edit');
        $('#kelasId').val(kelas.id_kelas);
        $('#tingkat').val(kelas.tingkat);
        $('#addKelasModal').modal('show');
    }

    function deleteKelas(id_kelas) {
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
                $('#deleteKelasId').val(id_kelas);
                $('#deleteKelasForm').submit();
            }
        });
    }

    $('#addKelasModal').on('hidden.bs.modal', function() {
        $('#addKelasModalLabel').text('Tambah Kelas');
        $('#modalAction').val('add');
        $('#kelasId').val('');
        $('#tingkat').val('');
    });
</script>