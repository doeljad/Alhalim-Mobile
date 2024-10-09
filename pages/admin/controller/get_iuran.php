<?php
include('../../../config/connection.php'); // Pastikan koneksi sudah terinclude

if (isset($_GET['id_santri'])) {
    $id_santri = $_GET['id_santri'];

    // Ambil id_kelas santri
    $kelas_sql = "SELECT id_kelas FROM santri WHERE id_santri = ?";
    $stmt = $conn->prepare($kelas_sql);
    $stmt->bind_param("i", $id_santri);
    $stmt->execute();
    $result = $stmt->get_result();
    $santri = $result->fetch_assoc();

    if ($santri) {
        $id_kelas = $santri['id_kelas'];

        // Ambil iuran aktif untuk kelas tersebut
        $iuran_sql = "
            SELECT i.id_iuran, i.nama_iuran, i.nominal
            FROM iuran_kelas ik
            JOIN jenis_iuran i ON ik.id_iuran = i.id_iuran
            WHERE ik.id_kelas = ? AND ik.status_aktif = 1
        ";
        $stmt = $conn->prepare($iuran_sql);
        $stmt->bind_param("i", $id_kelas);
        $stmt->execute();
        $iuran_result = $stmt->get_result();

        $iuran_data = [];
        while ($row = $iuran_result->fetch_assoc()) {
            $iuran_data[] = $row;
        }

        echo json_encode($iuran_data);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([]);
}
