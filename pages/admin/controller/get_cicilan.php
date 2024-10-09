<?php
include('../../../config/connection.php');

$id_santri = $_GET['id_santri'];

// Ambil data cicilan dari database
$sql = "SELECT 
    p.id_pembayaran, 
    p.bulan_pembayaran, 
    p.tahun_pembayaran, 
    j.nama_iuran, 
    j.nominal, 
    IFNULL((SELECT SUM(c.jumlah_cicilan) FROM cicilan_pembayaran c WHERE c.id_pembayaran = p.id_pembayaran), 0) AS total_cicilan,
    (p.jumlah_bayar + IFNULL((SELECT SUM(c.jumlah_cicilan) FROM cicilan_pembayaran c WHERE c.id_pembayaran = p.id_pembayaran), 0)) AS jumlah_bayar
FROM 
    pembayaran p
JOIN 
    jenis_iuran j ON p.id_iuran = j.id_iuran
WHERE 
    p.id_santri = ? 
    AND p.metode_pembayaran = 'cicilan';";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_santri);
$stmt->execute();
$result = $stmt->get_result();

$cicilan_data = array();
while ($row = $result->fetch_assoc()) {
    // Hitung kekurangan
    $kekurangan = $row['nominal'] - $row['jumlah_bayar'];
    $row['kekurangan'] = $kekurangan;

    $cicilan_data[] = $row;
}

// Mengembalikan data dalam format JSON
echo json_encode($cicilan_data);

$conn->close();
