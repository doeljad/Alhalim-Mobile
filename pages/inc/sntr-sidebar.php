<?php
// Periksa apakah session 'id' ada dan tidak kosong
if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
    // Jika user telah login, jalankan kode di bawah ini
    $user_id = intval($_SESSION['id']);

    // Query to count unread notifications for the logged-in user
    $sql = "SELECT COUNT(*) AS unread_count FROM notifikasi_penerima WHERE id_user = $user_id AND is_read = 0";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        // Store the unread count
        $unread_count = intval($row['unread_count']);

        // Free result set
        mysqli_free_result($result);
    } else {
        // Handle query error if necessary
        $unread_count = 0;
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // Jika user belum login, tidak menjalankan query dan variabel unread_count tetap kosong atau default
    $unread_count = 0;
}
