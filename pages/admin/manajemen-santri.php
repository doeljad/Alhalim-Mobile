<?php
$success_message = '';
$error_message = '';

// Tambah santri baru jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $id_kelas = $_POST['id_kelas'];
        $nama_santri = $_POST['nama_santri'];
        $nis = $_POST['nis'];
        $alamat = $_POST['alamat'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $tanggal_masuk = $_POST['tanggal_masuk'];

        $sql = "INSERT INTO santri (id_kelas, nama_santri, nis, alamat, tanggal_lahir, tanggal_masuk) VALUES ('$id_kelas', '$nama_santri', '$nis', '$alamat', '$tanggal_lahir', '$tanggal_masuk')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Santri baru berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Edit santri
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id_santri = $_POST['id_santri'];
        $id_kelas = $_POST['id_kelas'];
        $nama_santri = $_POST['nama_santri'];
        $nis = $_POST['nis'];
        $alamat = $_POST['alamat'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $tanggal_masuk = $_POST['tanggal_masuk'];

        $sql = "UPDATE santri SET id_kelas='$id_kelas', nama_santri='$nama_santri', nis='$nis', alamat='$alamat', tanggal_lahir='$tanggal_lahir', tanggal_masuk='$tanggal_masuk' WHERE id_santri=$id_santri";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Santri berhasil diperbarui!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Hapus santri
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id_santri = $_POST['id_santri'];

        $sql = "DELETE FROM santri WHERE id_santri=$id_santri";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Santri berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data santri dari database
$sql = "SELECT s.*,k.*
        FROM santri s
        JOIN kelas k ON s.id_kelas = k.id_kelas";
$result = $conn->query($sql);

$conn->close();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Data Santri</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Santri</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 1) : ?>
                            <button type="button" class="btn btn-gradient-primary float-end mb-4" data-toggle="modal" data-target="#addSantriModal">
                                Tambah Santri
                            </button>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Santri</th>
                                        <th>NIS</th>
                                        <th>Alamat</th>
                                        <th>Kelas</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Tanggal Masuk</th>
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
                                            echo "<td>" . $row["nis"] . "</td>";
                                            echo "<td>" . $row["alamat"] . "</td>";
                                            echo "<td>" . $row["tingkat"] . "</td>";
                                            echo "<td>" . $row["tanggal_lahir"] . "</td>";
                                            echo "<td>" . $row["tanggal_masuk"] . "</td>";
                                            if ($_SESSION['role'] == 1) {
                                                echo "<td>
                        <button class='btn btn-gradient-warning btn-rounded btn-sm' onclick='editSantri(" . json_encode($row) . ")'><i class='fa fa-pencil' aria-hidden='true'></i></button>
                        <button class='btn btn-gradient-danger btn-rounded btn-sm' onclick='deleteSantri(" . $row["id_santri"] . ")'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
                        </td>";
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>Tidak ada santri ditemukan</td></tr>";
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

<!-- Modal Tambah/Edit Santri -->
<div class="modal fade" id="addSantriModal" tabindex="-1" role="dialog" aria-labelledby="addSantriModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSantriModalLabel">Tambah Santri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="action" value="add" id="modalAction">
                    <input type="hidden" name="id_santri" id="santriId">
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
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_santri">Nama Santri</label>
                        <input type="text" class="form-control" id="nama_santri" name="nama_santri" required>
                    </div>
                    <div class="form-group">
                        <label for="nis">NIS</label>
                        <input type="text" class="form-control" id="nis" name="nis" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_masuk">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Hapus Santri -->
<form id="deleteSantriForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_santri" id="deleteSantriId">
</form>
<script>
    function editSantri(santri) {
        $('#addSantriModalLabel').text('Edit Santri');
        $('#modalAction').val('edit');
        $('#santriId').val(santri.id_santri);
        $('#id_kelas').val(santri.id_kelas);
        $('#nama_santri').val(santri.nama_santri);
        $('#nis').val(santri.nis);
        $('#alamat').val(santri.alamat);
        $('#tanggal_lahir').val(santri.tanggal_lahir);
        $('#tanggal_masuk').val(santri.tanggal_masuk);
        $('#addSantriModal').modal('show');
    }

    function deleteSantri(id_santri) {
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
                $('#deleteSantriId').val(id_santri);
                $('#deleteSantriForm').submit();
            }
        });
    }

    $('#addSantriModal').on('hidden.bs.modal', function() {
        $('#addSantriModalLabel').text('Tambah Santri');
        $('#modalAction').val('add');
        $('#santriId').val('');
        $('#id_kelas').val('');
        $('#nama_santri').val('');
        $('#nis').val('');
        $('#alamat').val('');
        $('#tanggal_lahir').val('');
        $('#tanggal_masuk').val('');
    });
</script>