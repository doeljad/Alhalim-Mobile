<?php
// Koneksi ke database
include '../../../config/connection.php';

if (isset($_GET['id_iuran'])) {
    $id_iuran = intval($_GET['id_iuran']);

    // Query untuk mendapatkan nominal berdasarkan id_iuran
    $sql = "SELECT nominal FROM jenis_iuran WHERE id_iuran = $id_iuran";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'nominal' => $row['nominal']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}

$conn->close();
