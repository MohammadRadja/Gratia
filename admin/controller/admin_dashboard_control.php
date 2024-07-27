<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "gratia"; 

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['level'] != 'admin') {
    header('location:../login.php');
    exit;
}

// Query untuk mendapatkan data pasien
$sql_pasien = "SELECT * FROM pasien";
$result_pasien = mysqli_query($conn, $sql_pasien);

// Periksa apakah query berhasil dijalankan
if ($result_pasien === false) {
    die("Error pada query pasien: " . mysqli_error($conn));
}

// Ambil data pasien dan pastikan ada data yang ditemukan
$data_pasien = [];
while ($row = mysqli_fetch_array($result_pasien, MYSQLI_ASSOC)) {
    $data_pasien[] = $row;
}

// Pastikan ada data yang ditemukan
if (count($data_pasien) === 0) {
    die("Data pasien tidak ditemukan.");
}

// Inisialisasi Array Pembayaran
$data_pembayaran = [];

// Query untuk mendapatkan data semua pembayaran
$sql_pembayaran = "SELECT * FROM view_pembayaran";
$result_pembayaran = mysqli_query($conn, $sql_pembayaran);

// Periksa apakah query berhasil dijalankan
if ($result_pembayaran) {
    while ($row = mysqli_fetch_assoc($result_pembayaran)) {
        // Populate $data_pembayaran array
        $data_pembayaran[] = $row;
    }
} else {
    // Handle query error
    die("Error: " . mysqli_error($conn));
}

// Loop untuk mengambil data pasien dan status pembayaran
foreach ($data_pembayaran as &$pembayaran) {
    $id_pasien = $pembayaran['id_pasien'];

    // Query untuk mendapatkan status pembayaran pasien
    $sql_status_pembayaran = "SELECT status_pembayaran FROM view_pembayaran WHERE id_pasien = '$id_pasien'";
    $result_status_pembayaran = mysqli_query($conn, $sql_status_pembayaran);

    if ($result_status_pembayaran === false) {
        die("Error pada query pembayaran: " . mysqli_error($conn));
    }

    // Dapatkan status pembayaran
    if (mysqli_num_rows($result_status_pembayaran) > 0) {
        $data_status_pembayaran = mysqli_fetch_array($result_status_pembayaran);
        $status_pembayaran = $data_status_pembayaran['status_pembayaran'];
    } else {
        $status_pembayaran = "belum bayar";
    }

    // Tambahkan data pasien ke dalam array $data_pembayaran
    $pembayaran['status_pembayaran'] = $status_pembayaran;
}

// Logika Verif Pembayaran
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id_transaksi = $_GET['id']; // Menggunakan id_transaksi dari GET
    $action = $_GET['action'];

    switch ($action) {
        case 'terima':
            $sql_update = "UPDATE view_pembayaran SET status_pembayaran = 'dibayar' WHERE id_transaksi = ?";
            break;
        case 'tolak':
            $sql_update = "UPDATE view_pembayaran SET status_pembayaran = 'ditolak' WHERE id_transaksi = ?";
            break;
        default:
            $_SESSION['verifikasi_error'] = "Aksi tidak valid.";
            header('location: ./pembayaran.php');
            exit;
    }

    // Prepare statement
    $stmt = mysqli_prepare($conn, $sql_update);
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Tentukan tipe parameter yang sesuai, ubah 'd' menjadi 's' jika id_transaksi adalah string
    mysqli_stmt_bind_param($stmt, 's', $id_transaksi); // Gantilah 'd' dengan 's' jika tipe ID adalah string
    $result_update = mysqli_stmt_execute($stmt);

    if ($result_update) {
        $action_text = ($action == 'terima') ? 'accepted' : 'rejected';
        $_SESSION['verifikasi_success'] = "Pembayaran berhasil " . $action_text;
    } else {
        $_SESSION['verifikasi_error'] = "Gagal memproses verifikasi: " . mysqli_error($conn);
    }

    // Redirect back to payment verification page
    header('location: ../pembayaran.php');
    exit;
}

// Close database connection
mysqli_close($conn);
?>
