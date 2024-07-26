<?php
include('../db/koneksi.php');
// Cek apakah sesi sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_POST['btn_registrasi'])){
    // Ambil dan sanitasi input data pasien
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $noTlp = mysqli_real_escape_string($conn, $_POST['noTlp']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    // Ambil dan sanitasi input data pasien
    $treatment = mysqli_real_escape_string($conn, $_POST['treatment']);
    $dokter = mysqli_real_escape_string($conn, $_POST['dokter']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    // Buat ID unik untuk user dan pasien
    $id_user = uniqid();
    $id_pasien = uniqid();
    $id_appointment = uniqid();
    $id_treatment = uniqid();
    $id_transaksi = uniqid();

    // Buat koneksi ke database
    try {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        // Query untuk memasukkan data ke tabel user
        $sql_user = "INSERT INTO user (id_user, username, password, level) VALUES (?, ?, ?, 'pasien')";
        $stmt_user = mysqli_prepare($conn, $sql_user);
        if (!$stmt_user) {
            die("Error preparing statement for user: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_user, 'sss', $id_user, $username, $password);
        mysqli_stmt_execute($stmt_user);

        // Query untuk memasukkan data ke tabel pasien
        $sql_pasien = "INSERT INTO pasien (id_pasien, id_user, nama, alamat, jenis_kelamin, no_telp, status_pembayaran) 
        VALUES (?, ?, ?, ?, ?, ?, 'belum dibayar')";
        $stmt_pasien = mysqli_prepare($conn, $sql_pasien);
        if (!$stmt_pasien) {
            die("Error preparing statement for pasien: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_pasien, 'ssssss', $id_pasien, $id_user, $nama, $alamat, $gender, $noTlp);
        mysqli_stmt_execute($stmt_pasien);

        // Query untuk memasukkan data ke tabel Appointment
        $sql_appoinment = "INSERT INTO appointment (id_appointment, id_user, id_dokter, id_treatment, catatan) 
        VALUES (?, ?, ?, ?, ?)";
        $stmt_appointment = mysqli_prepare($conn, $sql_appoinment);
        if (!$stmt_appointment) {
            die("Error preparing statement for appointment: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_appointment, 'sssss', $id_appointment, $id_user, $dokter, $treatment, $catatan);
        mysqli_stmt_execute($stmt_appointment);

        // Query untuk memasukkan data ke tabel transaksi
        $sql_transaksi = "INSERT INTO transaksi (id_transaksi, id_user, id_dokter, id_treatment) 
        VALUES (?, ?, ?, ?)";
        $stmt_transaksi = mysqli_prepare($conn, $sql_transaksi);
        if (!$stmt_transaksi) {
            die("Error preparing statement for transaksi: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_transaksi, 'ssss',$id_transaksi, $id_user, $dokter, $treatment);
        mysqli_stmt_execute($stmt_transaksi);

        // Komit transaksi
        mysqli_commit($conn);

        $_SESSION['pesan_regisB'] = "Registrasi anda berhasil, login menggunakan username dan password";
        header('Location: ../login.php');
    } catch (mysqli_sql_exception $exception) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($conn);

        // Menampilkan pesan kesalahan
        $_SESSION['pendaftaran_error'] = "Terjadi kesalahan: " . $exception->getMessage();
        header('Location: ../pendaftaran.php');
    } finally {
        // Menutup statement dan koneksi
        if ($stmt_user) {
            mysqli_stmt_close($stmt_user);
        }
        
        if ($stmt_pasien) {
            mysqli_stmt_close($stmt_pasien);
        }
        mysqli_close($conn);
    }
} else {
    $_SESSION['pendaftaran_error'] = "Galat, tidak ada data yang diterima.";
    header('Location: ../pendaftaran.php');
}
?>
