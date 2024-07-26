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

// Query to fetch all doctors
$sql_dokter = "SELECT * FROM dokter";
$result_dokter = mysqli_query($conn, $sql_dokter);

if ($result_dokter === false) {
    handleError("query dokter");
}

$data_dokter = [];
while ($row = mysqli_fetch_assoc($result_dokter)) {
    $data_dokter[] = $row;
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $nama_dokter = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
            $spesialisasi = mysqli_real_escape_string($conn, $_POST['spesialisasi']);

            $id_dokter = uniqid();

            $sql_create = "INSERT INTO dokter (id_dokter, nama_dokter, spesialisasi) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql_create);
            mysqli_stmt_bind_param($stmt, 'sss', $id_dokter , $nama_dokter, $spesialisasi);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Tambah dokter");
            }
            $_SESSION['crud_success'] = "Dokter berhasil ditambahkan.";
            break;

        case 'update':
            $id_dokter = mysqli_real_escape_string($conn, $_POST['id_dokter']);
            $nama_dokter = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
            $spesialisasi = mysqli_real_escape_string($conn, $_POST['spesialisasi']);
            
            $sql_update = "UPDATE dokter SET nama_dokter = ?, spesialisasi = ? WHERE id_dokter = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, 'sss', $nama_dokter, $spesialisasi, $id_dokter);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Edit dokter");
            }
            $_SESSION['crud_success'] = "Dokter berhasil diupdate.";
            break;

        case 'delete':
            $id_dokter = $_POST['id_dokter'];

            $sql_delete = "DELETE FROM dokter WHERE id_dokter = ?";
            $stmt = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt, 's', $id_dokter);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("delete dokter");
            }
            $_SESSION['crud_success'] = "Dokter berhasil dihapus.";
            break;

        default:
            handleError("invalid action");
    }

    header('location: dokter.php');
    exit;
}

mysqli_close($conn);
?>
