<?php
include('../db/koneksi.php');

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

// Ambil data pasien
$sql_pasien = "SELECT * FROM pasien";
$result_pasien = mysqli_query($conn, $sql_pasien);

if ($result_pasien === false) {
    handleError("query pasien");
}

$pasien = [];
while ($row = mysqli_fetch_assoc($result_pasien)) {
    $pasien[] = $row;
}


// Ambil data dokter
$sql_dokter = "SELECT * FROM dokter";
$result_dokter = mysqli_query($conn, $sql_dokter);

if ($result_dokter === false) {
    handleError("query appointment");
}

$dokters = [];
while ($row = mysqli_fetch_assoc($result_dokter)) {
    $dokters[] = $row;
}

// Ambil data treatment
$sql_treatment = "SELECT * FROM treatment";
$result_treatment = mysqli_query($conn, $sql_treatment);

if ($result_treatment === false) {
    handleError("query appointment");
}

$treatment = [];
while ($row = mysqli_fetch_assoc($result_treatment)) {
    $treatment[] = $row;
}


// Ambil data Appointment
$sql_appointment = "SELECT a.id_appointment, p.nama AS nama_pasien, d.nama_dokter, t.nama_treatment, a.jadwal_appointment, a.catatan FROM Appointment a JOIN Dokter d ON a.id_dokter = d.id_dokter JOIN Treatment t ON a.id_treatment = t.id_treatment JOIN Pasien p ON a.id_pasien = p.id_pasien;";
$result_appointment = mysqli_query($conn, $sql_appointment);

if ($result_appointment === false) {
    handleError("query appointment");
}

$data_appointment = [];
while ($row = mysqli_fetch_assoc($result_appointment)) {
    $data_appointment[] = $row;
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $id_appointment = uniqid();
            $nama_pasien = mysqli_real_escape_string($conn, $_POST['id_pasien']);
            $id_dokter = mysqli_real_escape_string($conn, $_POST['id_dokter']);
            $id_treatment = mysqli_real_escape_string($conn, $_POST['id_treatment']);
            $jadwal_appointment = mysqli_real_escape_string($conn, $_POST['jadwal_appointment']);
            $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
        
            $sql_create = "INSERT INTO appointment (id_appointment, id_pasien, id_dokter, id_treatment, jadwal_appointment, catatan) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql_create);
            mysqli_stmt_bind_param($stmt, 'ssssss', $id_appointment, $nama_pasien, $id_dokter, $id_treatment, $jadwal_appointment, $catatan);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Tambah appointment");
            }
            $_SESSION['crud_success'] = "Appointment berhasil ditambahkan.";
            break;
        
        case 'update':
            $id_appointment = mysqli_real_escape_string($conn, $_POST['id_appointment']);
            $nama_pasien = mysqli_real_escape_string($conn, $_POST['id_pasien']);
            $id_dokter = mysqli_real_escape_string($conn, $_POST['id_dokter']);
            $id_treatment = mysqli_real_escape_string($conn, $_POST['id_treatment']);
            $jadwal_appointment = mysqli_real_escape_string($conn, $_POST['jadwal_appointment']);
            $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
        
            $sql_update = "UPDATE appointment SET id_pasien = ?, id_dokter = ?, id_treatment = ?, jadwal_appointment = ?, catatan = ? WHERE id_appointment = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, 'ssssss', $nama_pasien, $id_dokter, $id_treatment, $jadwal_appointment, $catatan, $id_appointment);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Edit appointment");
            }
            $_SESSION['crud_success'] = "Appointment berhasil diupdate.";
            break;

        case 'delete':
            $id_appointment = $_POST['id_appointment'];

            $sql_delete = "DELETE FROM appointment WHERE id_appointment = ?";
            $stmt = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt, 's', $id_appointment);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Delete appointment");
            }
            $_SESSION['crud_success'] = "Appointment berhasil dihapus.";
            break;

        default:
            handleError("Invalid action");
    }

    header('location: appointment.php');
    exit;
}

mysqli_close($conn);
?>
