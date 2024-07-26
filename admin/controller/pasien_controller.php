<?php
include ('../db/koneksi.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['level'] != 'admin') {
    header('location:../login.php');
    exit;
}

// Function to handle errors
function handleError($message) {
    die("Error: " . $message . " - " . mysqli_error($GLOBALS['conn']));
}

// Query untuk mengambil semua pasien
$sql_pasien = "SELECT * FROM pasien";
$result_pasien = mysqli_query($conn, $sql_pasien);

if ($result_pasien === false) {
    handleError("query pasien");
}

$data_pasien = [];
while ($row = mysqli_fetch_assoc($result_pasien)) {
    $data_pasien[] = $row;
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $nama_pasien = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
            $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
            $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
            $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);

            // Menghasilkan ID Pasien secara unik
            $id_pasien = uniqid();

            $sql_create = "INSERT INTO pasien (id_pasien, nama, alamat, jenis_kelamin, no_telp) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql_create);
            mysqli_stmt_bind_param($stmt, 'sssss', $id_pasien, $nama_pasien, $alamat, $jenis_kelamin, $no_telp);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Tambah pasien");
            }
            $_SESSION['crud_success'] = "Pasien berhasil ditambahkan.";
            break;

        case 'update':
            $id_pasien = mysqli_real_escape_string($conn, $_POST['id_pasien']);
            $nama_pasien = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
            $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
            $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
            $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
            
            $sql_update = "UPDATE pasien SET nama = ?, alamat = ?, jenis_kelamin = ?, no_telp = ? WHERE id_pasien = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, 'sssss', $nama_pasien, $alamat, $jenis_kelamin, $no_telp, $id_pasien);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Edit pasien");
            }
            $_SESSION['crud_success'] = "Pasien berhasil diupdate.";
            break;

        case 'delete':
            $id_pasien = $_POST['id_pasien'];

            $sql_delete = "DELETE FROM pasien WHERE id_pasien = ?";
            $stmt = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt, 's', $id_pasien);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Delete pasien");
            }
            $_SESSION['crud_success'] = "Pasien berhasil dihapus.";
            break;

        default:
            handleError("Invalid action");
    }

    header('location: pasien.php');
    exit;
}

mysqli_close($conn);
?>
