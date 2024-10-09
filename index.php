<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Alhalimpay</title>

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.8.55/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Favicon -->
  <link rel="shortcut icon" href="assets/images/favicon.png" />

  <style>
    table.dataTable thead th,
    table.dataTable thead td {
      border-bottom: none !important;
    }

    table.dataTable.no-footer {
      border-bottom: none !important;
    }

    table.dataTable tbody th,
    table.dataTable tbody td {
      border-top: none !important;
    }

    table.dataTable {
      border-collapse: collapse;
    }

    table.dataTable td,
    table.dataTable th {
      border: none !important;
    }
  </style>

</head>

<body>

  <?php
  date_default_timezone_set('Asia/Jakarta'); // Mengatur zona waktu ke Jakarta
  session_start();

  $bulan_mapping = array(
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
  );
  // Memeriksa apakah pengguna sudah login
  if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Memeriksa role dan memuat konten yang sesuai
    if ($_SESSION['role'] == '1') {
      include('pages/admin/home.php');
    } else if ($_SESSION['role'] == '2') {
      include('pages/manager/index.php');
    } else {
      header('Location: santri.php');
      exit();
    }
  } else {
    header('Location: santri.php');
    exit();
  }
  ?>

  <!-- JS -->

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.0.1/chart.umd.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.1.4/js/dataTables.bootstrap5.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/misc.js"></script>
  <script src="assets/js/settings.js"></script>
  <script src="assets/js/todolist.js"></script>
  <script src="assets/js/jquery.cookie.js"></script>
  <script src="assets/js/dashboard.js"></script>
  <script>
    function getJakartaTime() {
      // Mendapatkan waktu saat ini di zona waktu Asia/Jakarta
      const now = new Date();
      const options = {
        timeZone: 'Asia/Jakarta',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
      };

      // Format waktu dengan Intl.DateTimeFormat
      const formatter = new Intl.DateTimeFormat('en-GB', options);
      const parts = formatter.formatToParts(now);

      // Menyusun waktu dalam format YYYY-MM-DDTHH:MM
      const year = parts.find(part => part.type === 'year').value;
      const month = parts.find(part => part.type === 'month').value;
      const day = parts.find(part => part.type === 'day').value;
      const hour = parts.find(part => part.type === 'hour').value;
      const minute = parts.find(part => part.type === 'minute').value;

      return `${year}-${month}-${day}T${hour}:${minute}`;
    }
  </script>
  <?php if (!empty($success_message)) : ?>
    <script>
      $(document).ready(function() {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: '<?php echo addslashes($success_message); ?>'
        });
      });
    </script>
  <?php endif; ?>

  <?php if (!empty($error_message)) : ?>
    <script>
      $(document).ready(function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: '<?php echo addslashes($error_message); ?>'
        });
      });
    </script>
  <?php endif; ?>
  <script>
    $(document).ready(function() {
      $('.table').DataTable(); // Replace '#example' with the ID of your table
    });
  </script>
</body>

</html>