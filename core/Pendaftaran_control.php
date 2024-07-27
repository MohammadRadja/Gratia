<?php
include('../db/koneksi.php');
// Cek apakah sesi sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['btn_registrasi'])) {
    // Ambil dan sanitasi input data pasien
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    // Ambil dan sanitasi input data appointment
    $treatment = mysqli_real_escape_string($conn, $_POST['treatment']);
    $dokter = mysqli_real_escape_string($conn, $_POST['dokter']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    // Inisialisasi pesan kesalahan
    $error_messages = [];

    // Validasi data pasien
    if (empty($nama)) {
        $error_messages[] = "Nama tidak boleh kosong.";
    }

    if (empty($alamat)) {
        $error_messages[] = "Alamat tidak boleh kosong.";
    }

    if (empty($gender) || !in_array($gender, ['Laki-laki', 'Perempuan'])) {
        $error_messages[] = "Jenis kelamin tidak valid.";
    }

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

    // Validasi username dan password
    if (empty($username)) {
        $error_messages[] = "Username tidak boleh kosong.";
    }

    if (empty($_POST['password'])) {
        $error_messages[] = "Password tidak boleh kosong.";
    }

    // Jika ada kesalahan, simpan kesalahan dalam sesi dan redirect
    if (!empty($error_messages)) {
        $_SESSION['pendaftaran_error'] = implode(" ", $error_messages);
        header('Location: ../pendaftaran.php');
        exit;
    }

    // Buat ID unik untuk user dan pasien
    $id_user = uniqid();

    // Buat koneksi ke database
    try {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        // Query untuk memasukkan data ke tabel user
        $sql_user = "INSERT INTO user (id_user, username, password, level) VALUES (?, ?, ?, 'pasien')";
        $stmt_user = mysqli_prepare($conn, $sql_user);
        if (!$stmt_user) {
            throw new Exception("Error preparing statement for user: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_user, 'sss', $id_user, $username, $password);
        mysqli_stmt_execute($stmt_user);

        // Query untuk memasukkan data ke tabel pasien
        $sql_pasien = "INSERT INTO pasien (id_pasien, nama, alamat, jenis_kelamin, no_telp, status_pembayaran) 
        VALUES (?, ?, ?, ?, ?, 'belum dibayar')";
        $stmt_pasien = mysqli_prepare($conn, $sql_pasien);
        if (!$stmt_pasien) {
            throw new Exception("Error preparing statement for pasien: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_pasien, 'sssss', $id_user, $nama, $alamat, $gender, $no_telp);
        mysqli_stmt_execute($stmt_pasien);

        // Komit transaksi
        mysqli_commit($conn);

        $_SESSION['pesan_regisB'] = "Registrasi anda berhasil, login menggunakan username dan password";
        header('Location: ../login.php');
    } catch (Exception $exception) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($conn);

        // Menampilkan pesan kesalahan
        $_SESSION['pendaftaran_error'] = "Terjadi kesalahan: " . $exception->getMessage();
        header('Location: ../pendaftaran.php');
    } finally {
        // Menutup statement dan koneksi
        if (isset($stmt_user)) {
            mysqli_stmt_close($stmt_user);
        }
        
        if (isset($stmt_pasien)) {
            mysqli_stmt_close($stmt_pasien);
        }
        mysqli_close($conn);
    }
} else {
    $_SESSION['pendaftaran_error'] = "Galat, tidak ada data yang diterima.";
    header('Location: ../pendaftaran.php');
}
?>
