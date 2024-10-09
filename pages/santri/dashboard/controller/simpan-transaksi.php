<?php
date_default_timezone_set('Asia/Jakarta');
include('../../../../config/connection.php');

// Ambil data dari request Midtrans
$order_id = $_POST['order_id'];
$payment_type = $_POST['payment_type'];
$transaction_status = $_POST['transaction_status'];
$jumlah_bayar = $_POST['jumlah_bayar']; // Assumes this is the gross amount
$bulan_pembayaran = $_POST['bulan'];
$tahun_pembayaran = $_POST['tahun'];
$fraud_status = $_POST['fraud_status']; // Assumes this is part of the POST data
$signature_key = $_POST['signature_key']; // Assumes this is part of the POST data

// Format transaction_time
$transaction_time = date('Y-m-d H:i:s');

// Detail pembayaran yang harus diambil dari context aplikasi
$id_santri = $_POST['id_santri'];
$id_iuran = $_POST['id_iuran'];

// Debugging: Periksa data yang diterima
error_log("Data received: " . print_r($_POST, true));

// Mulai transaksi MySQL
mysqli_begin_transaction($conn);

try {
    // Simpan data ke tabel pembayaran
    $sql_pembayaran = "INSERT INTO pembayaran (id_santri, id_iuran, tanggal_pembayaran, jumlah_bayar, metode_pembayaran, bulan_pembayaran, tahun_pembayaran)
    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_pembayaran = $conn->prepare($sql_pembayaran);
    $metode_pembayaran = 'lunas'; // Sesuaikan jika diperlukan
    $stmt_pembayaran->bind_param("iisdssi", $id_santri, $id_iuran, $transaction_time, $jumlah_bayar, $metode_pembayaran, $bulan_pembayaran, $tahun_pembayaran);

    if (!$stmt_pembayaran->execute()) {
        throw new Exception("Error inserting into pembayaran: " . $stmt_pembayaran->error);
    }

    // Ambil ID pembayaran terakhir yang baru disimpan
    $id_pembayaran = $conn->insert_id;
    error_log("Last inserted pembayaran ID: " . $id_pembayaran);

    // Simpan data ke tabel transaksi
    $id_transaksi_midtrans = uniqid(); // Buat ID unik untuk transaksi Midtrans
    $sql_transaksi = "INSERT INTO transaksi (id_transaksi_midtrans, id_pembayaran, order_id, gross_amount, payment_type, transaction_time, transaction_status, fraud_status, signature_key)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_transaksi = $conn->prepare($sql_transaksi);
    $stmt_transaksi->bind_param("sisdsssss", $id_transaksi_midtrans, $id_pembayaran, $order_id, $jumlah_bayar, $payment_type, $transaction_time, $transaction_status, $fraud_status, $signature_key);

    if (!$stmt_transaksi->execute()) {
        throw new Exception("Error inserting into transaksi: " . $stmt_transaksi->error);
    }

    // Commit transaksi
    mysqli_commit($conn);
    echo "Transaction saved successfully";
} catch (Exception $e) {
    // Rollback jika terjadi error
    mysqli_rollback($conn);
    echo "Transaction failed: " . $e->getMessage();
}

// Tutup koneksi
mysqli_close($conn);
