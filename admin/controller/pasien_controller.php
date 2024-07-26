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
    $_SESSION['crud_error'] = $message;
    header('location: pasien.php');
    exit;
}

// Ambil data pasien
$sql_pasien = "SELECT * FROM pasien";
$result_pasien = mysqli_query($conn, $sql_pasien);

if ($result_pasien === false) {
    handleError("Gagal mengambil data pasien: " . mysqli_error($conn));
}

$pasien = [];
while ($row = mysqli_fetch_assoc($result_pasien)) {
    $pasien[] = $row;
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    var_dump($action);
    var_dump($stmt);

    switch ($action) {
        case 'create':
            $id_pasien = uniqid();
            $nama_pasien = mysqli_real_escape_string($conn, $_POST['nama']);
            $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
            $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
            $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);

            // Validasi nomor telepon, pastikan panjang antara 10 hingga 15 digit
            if (empty($no_telp) || !preg_match('/^[0-9]{10,15}$/', $no_telp)) {
                $error_messages[] = "Nomor telepon tidak valid. Harus antara 10 hingga 15 digit.";
            } else {
                // Tambahkan kode negara +62
                if (substr($no_telp, 0, 1) === '0') {
                    $no_telp = '+62' . substr($no_telp, 1); // Menghilangkan angka pertama (0) dan menambahkan +62
                } else {
                    $no_telp = '+62' . $no_telp; // Tambahkan +62 jika tidak dimulai dengan 0
                }
            }

            $sql_create = "INSERT INTO pasien (id_pasien, nama, alamat, jenis_kelamin, no_telp) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql_create);
            if ($stmt === false) {
                handleError("Gagal menyiapkan statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, 'sssss', $id_pasien, $nama_pasien, $alamat, $jenis_kelamin, $no_telp);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Gagal menambahkan pasien: " . mysqli_stmt_error($stmt));
            }
            $_SESSION['crud_success'] = "Data Pasien berhasil ditambahkan.";
            break;
        
        case 'update':
            $id_pasien = mysqli_real_escape_string($conn, $_POST['id_pasien']);
            $nama_pasien = mysqli_real_escape_string($conn, $_POST['nama']);
            $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
            $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
            $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);

            // Validasi nomor telepon
            if (!preg_match('/^812\d{8,}$/', $no_telp)) {
                handleError("Nomor telepon tidak valid, harus mulai dengan 812 dan minimal 10 digit.");
            }

            $sql_update = "UPDATE pasien SET nama = ?, alamat = ?, jenis_kelamin = ?, no_telp = ? WHERE id_pasien = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            if ($stmt === false) {
                handleError("Gagal menyiapkan statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, 'sssss', $nama_pasien, $alamat, $jenis_kelamin, $no_telp, $id_pasien);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Gagal mengedit pasien: " . mysqli_stmt_error($stmt));
            }
            $_SESSION['crud_success'] = "Data Pasien berhasil diupdate.";
            break;

        case 'delete':
            $id_pasien = mysqli_real_escape_string($conn, $_POST['id_pasien']);

            $sql_delete = "DELETE FROM pasien WHERE id_pasien = ?";
            $stmt = mysqli_prepare($conn, $sql_delete);
            if ($stmt === false) {
                handleError("Gagal menyiapkan statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, 's', $id_pasien);
            if (!mysqli_stmt_execute($stmt)) {
                handleError("Gagal menghapus pasien: " . mysqli_stmt_error($stmt));
            }
            $_SESSION['crud_success'] = "Data Pasien berhasil dihapus.";
            break;

        default:
            handleError("Aksi tidak valid");
    }

    header('location: pasien.php');
    exit;
}

mysqli_close($conn);
?>
