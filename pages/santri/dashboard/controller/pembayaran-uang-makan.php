<?php
include('midtrans_config.php');
// Data transaksi
$transaction_details = [
    'order_id' => rand(),
    'gross_amount' => $nominal_iuran + 4000 + 400, // Total harga + Biaya Transaksi + PPN
];

// Data item yang dibeli
$item_details = [
    [
        'id' => 'uang_makan',
        'price' => $nominal_iuran,
        'quantity' => 1,
        'name' => "Pembayaran Uang Makan Bulan $bulan Tahun $tahun"
    ],
    [
        'id' => 'biaya_transaksi',
        'price' => 4000,
        'quantity' => 1,
        'name' => "Biaya Transaksi"
    ],
    [
        'id' => 'ppn',
        'price' => 400,
        'quantity' => 1,
        'name' => "PPN"
    ],
];

// Data pelanggan
$customer_details = [
    'first_name' => $nama_santri,
    'email' => 'email_santri@example.com', // Ganti dengan email santri
    'phone' => '08123456789', // Ganti dengan nomor telepon santri
];

// Data transaksi yang akan dikirim ke Midtrans
$transaction = [
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
    'customer_details' => $customer_details,
];

// Buat token snap
$snapToken = \Midtrans\Snap::getSnapToken($transaction);
?>

<!-- <script src="https://app.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-Zb4Bsg-fpAM4ytjm"></script> -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-Zb4Bsg-fpAM4ytjm"></script>
<script type="text/javascript">
    // Ketika tombol Midtrans diklik, tampilkan popup Snap
    document.getElementById('pay-button').onclick = function() {
        snap.pay('<?= $snapToken ?>', {
            // Menghilangkan tampilan ringkasan order (yang biasanya juga menampilkan tombol close)
            skipOrderSummary: true,
            // Tanpa tombol close dan fullscreen
            onSuccess: function(result) {
                console.log(result);
                // Kirim data ke server untuk disimpan ke database
                $.ajax({
                    url: 'pages/santri/dashboard/controller/simpan-transaksi.php',
                    type: 'POST',
                    data: {
                        id_santri: '<?= $id_santri ?>',
                        id_iuran: '<?= $id_iuran ?>',
                        jumlah_bayar: <?= $nominal_iuran ?>,
                        bulan: '<?= $bulan ?>',
                        tahun: '<?= $tahun ?>',
                        payment_type: result.payment_type,
                        order_id: result.order_id,
                        transaction_status: result.transaction_status,
                        transaction_time: result.transaction_time,
                        fraud_status: result.fraud_status || '',
                        signature_key: result.signature_key
                    },
                    success: function(response) {
                        // Tampilkan notifikasi SweetAlert untuk transaksi sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil',
                            text: 'Pembayaran uang makan bulan <?= $bulanIndo[$bulan] ?> berhasil!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "?page=uang-makan";
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        // Tampilkan notifikasi SweetAlert untuk transaksi error
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembayaran Gagal',
                            text: 'Terjadi kesalahan saat memproses transaksi.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "?page=uang-makan";
                            }
                        });
                    }
                });
            },
            onPending: function(result) {
                console.log(result);
                // Kirim data ke server untuk disimpan ke database
                $.ajax({
                    url: 'pages/santri/dashboard/controller/simpan-transaksi.php',
                    type: 'POST',
                    data: {
                        id_santri: '<?= $id_santri ?>',
                        id_iuran: '<?= $id_iuran ?>',
                        jumlah_bayar: <?= $nominal_iuran ?>,
                        bulan: '<?= $bulan ?>',
                        tahun: '<?= $tahun ?>',
                        payment_type: result.payment_type,
                        order_id: result.order_id,
                        transaction_status: result.transaction_status,
                        transaction_time: result.transaction_time,
                        fraud_status: result.fraud_status || '',
                        signature_key: result.signature_key
                    },
                    success: function(response) {
                        // Tampilkan notifikasi SweetAlert untuk transaksi pending
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pembayaran Tertunda',
                            text: 'Transaksi sedang menunggu konfirmasi.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "?page=uang-makan";
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        // Tampilkan notifikasi SweetAlert untuk transaksi error
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembayaran Gagal',
                            text: 'Terjadi kesalahan saat memproses transaksi.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "?page=uang-makan";
                            }
                        });
                    }
                });
            },
            onError: function(result) {
                console.log(result);
                // Tampilkan notifikasi SweetAlert untuk transaksi error
                Swal.fire({
                    icon: 'error',
                    title: 'Pembayaran Gagal',
                    text: 'Terjadi kesalahan saat memproses transaksi.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "?page=uang-makan";
                    }
                });
            },
            onClose: function() {
                console.log('customer closed the popup without finishing the payment');
            }
        });
    };
</script>