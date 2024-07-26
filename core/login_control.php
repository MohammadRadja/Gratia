<?php
include('../db/koneksi.php');
session_start();

if (isset($_POST['btn_login'])) {
    // Ambil dan sanitasi input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Menggunakan md5 untuk hashing password

    // Query untuk memeriksa user dengan prepared statement
    $sql_user = "SELECT * FROM user WHERE username = ? AND password = ?";
    $stmt_user = mysqli_prepare($conn, $sql_user);

    if (!$stmt_user) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Bind parameter
    mysqli_stmt_bind_param($stmt_user, 'ss', $username, $password);
    mysqli_stmt_execute($stmt_user);
    
    // Ambil hasil
    $result = mysqli_stmt_get_result($stmt_user);

    if (mysqli_num_rows($result) > 0) {
        while ($data_user = mysqli_fetch_array($result)) {
            $_SESSION['status'] = 'login';
            $_SESSION['id_users'] = $data_user['id_user'];
            $_SESSION['username'] = $data_user['username'];
            $_SESSION['level'] = $data_user['level'];

            if ($data_user['level'] == 'admin') {
                $_SESSION['admin_logged_in'] = true; // Mengatur sesi admin
                header('location:../admin/dashboard.php');
                exit(); // Pastikan untuk menghentikan script setelah header
            } else if ($data_user['level'] == 'pasien') {
                $_SESSION['pasien_logged_in'] = true; // Mengatur sesi siswa
                header('location:../pasien/dashboard.php');
                exit(); // Pastikan untuk menghentikan script setelah header
            }
        }
    } else {
        $_SESSION['login_error'] = "Username atau password salah!";
        header('location:../login.php');
        exit(); // Pastikan untuk menghentikan script setelah header
    }

    // Menutup statement
    mysqli_stmt_close($stmt_user);
} else {
    header('location:../login.php');
    exit(); // Pastikan untuk menghentikan script setelah header
}

// Menutup koneksi
mysqli_close($conn);
?>
