<?php
// require_once '../../../../assets/vendors/midtrans/Midtrans.php';
require_once 'assets/vendors/midtrans/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-bGJzZgY1Ze4kzggfGG_S8G53';
\Midtrans\Config::$isProduction = false; // Ganti ke `true` untuk mode produksi
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
