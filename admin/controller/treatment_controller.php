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
$sql_treatment = "SELECT * FROM treatment";
$result_treatment = mysqli_query($conn, $sql_treatment);

if ($result_treatment === false) {
    handleError("query treatment");
}

$data_treatment = [];
while ($row = mysqli_fetch_assoc($result_treatment)) {
    $data_treatment[] = $row;
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $nama_treatment = mysqli_real_escape_string($conn, $_POST['nama_treatment']);
            $biaya = mysqli_real_escape_string($conn, $_POST['biaya']);

            $id_treatment = uniqid();

            $sql_create = "INSERT INTO treatment (id_treatment, nama_treatment, spesialis, email, no_telp) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql_create);
            mysqli_stmt_bind_param($stmt, 'sss', $id_treatment ,$nama_treatment, $biaya);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Tambah treatment");
            }
            $_SESSION['crud_success'] = "treatment berhasil ditambahkan.";
            break;

        case 'update':
            $id_treatment = $_POST['id_treatment'];
            $nama_treatment = $_POST['nama_treatment'];
            $biaya = $_POST['biaya'];

            $sql_update = "UPDATE treatment SET nama_treatment = ?, biaya = ? WHERE id_treatment = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, 'sss', $nama_treatment, $biaya, $id_treatment);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("update treatment");
            }
            $_SESSION['crud_success'] = "treatment berhasil diupdate.";
            break;

        case 'delete':
            $id_treatment = $_POST['id_treatment'];

            $sql_delete = "DELETE FROM treatment WHERE id_treatment = ?";
            $stmt = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt, 'i', $id_treatment);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("delete treatment");
            }
            $_SESSION['crud_success'] = "treatment berhasil dihapus.";
            break;

        default:
            handleError("invalid action");
    }

    header('location: treatment.php');
    exit;
}

mysqli_close($conn);
?>
