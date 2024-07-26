<?php
// Periksa apakah session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah admin sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['level'] != 'pasien') {
    header('location:../login.php');
    exit;
}

$id_user = $_SESSION['id_users'] ?? 'default_id'; // Gunakan nilai default jika tidak ada id_users

// Query untuk mendapatkan data pasien
$sql_pasien = "SELECT * FROM pasien WHERE id_pasien = ?";
$stmt_pasien = $conn->prepare($sql_pasien);
$stmt_pasien->bind_param("i", $id_user);
$stmt_pasien->execute();
$result_pasien = $stmt_pasien->get_result();
// Periksa apakah query berhasil dijalankan
if ($result_pasien === false) {
    die("Error pada query pendaftar: " . mysqli_error($conn));
}
// Ambil data siswa
$data_pasien = mysqli_fetch_array($result_pasien);
// Pastikan ada data yang ditemukan
if (!$data_pasien) {
    $error_messages[] = "Data Pasien tidak ditemukan";
}


// Logika Status Pembayaran
if (mysqli_num_rows($result_pasien) > 0) {
    // Ambil data pendaftar dari hasil query sebelumnya
    $id_pasien = $data_pasien['id_pasien'];

    // Query untuk mendapatkan data pembayaran
    $sql_pembayaran = "SELECT * FROM view_pembayaran WHERE id_pasien = ?";
    $stmt_pembayaran = $conn->prepare($sql_pembayaran);
    $stmt_pembayaran->bind_param("i", $id_pasien);
    $stmt_pembayaran->execute();
    $result_bayar = $stmt_pembayaran->get_result();


    // Periksa apakah query berhasil dijalankan
    if (!$result_bayar) {
        die("Error pada query pembayaran: " . $stmt_pembayaran->error);
    }

    if ($result_bayar->num_rows > 0) {
        $data_bayar = $result_bayar->fetch_array(MYSQLI_ASSOC);
        $status = $data_bayar['status_pembayaran'];
        
        // Lakukan sesuatu dengan $status
        $_SESSION['dibayar'] = "Selamat, Pembayaran berhasil!!!.";
    } else {
        $_SESSION['belum bayar'] = "Silakan lengkapi data diri anda dan lakukan pembayaran.";
    }
} else {
    echo "Tidak ada data pasien ditemukan.";
}

// Logika Pembayaran
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_bayar'])) {
    // Ambil data dari form
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $tanggal_bayar = date("Y-m-d H:i:s");

    // Proses unggah bukti pembayaran jika ada
    if (!empty($_FILES['bukti_pembayaran']['name'])) {
        $file_name = $_FILES['bukti_pembayaran']['name'];
        $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
        $file_size = $_FILES['bukti_pembayaran']['size'];
        $file_type = $_FILES['bukti_pembayaran']['type'];

        // Batas ukuran file dalam bytes (misalnya 2MB)
        $max_file_size = 2 * 1024 * 1024; // 2 MB
        // Tipe file yang diizinkan
        $allowed_file_types = ['image/jpeg', 'image/png', 'image/gif'];

        // Direktori upload
        $upload_dir = "../assets/pembayaran/";

        // Inisialisasi pesan kesalahan
        $error_messages = [];

        // Validasi ukuran file
        if ($file_size > $max_file_size) {
            $error_messages[] = "Ukuran file melebihi batas maksimal 2MB.";
        }

        // Validasi tipe file
        if (!in_array($file_type, $allowed_file_types)) {
            $error_messages[] = "Tipe file tidak diperbolehkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.";
        }

        // Jika tidak ada error, lanjutkan proses unggah
        if (empty($error_messages)) {
            // Pindahkan file yang diunggah ke direktori upload
            $target_file = $upload_dir . basename($file_name);
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Masukkan data pembayaran ke dalam tabel transaksi
                $sql_pembayaran = "INSERT INTO transaksi (tanggal_bayar, jumlah_bayar, bukti_pembayaran)
                                   VALUES (?, ?, ?)";
                $stmt_pembayaran = mysqli_prepare($conn, $sql_pembayaran);

                mysqli_stmt_bind_param($stmt_pembayaran, 'sss', $tanggal_bayar, $jumlah_bayar, $file_name);
                $result_pembayaran = mysqli_stmt_execute($stmt_pembayaran);

                if ($result_pembayaran) {
                    $_SESSION['payment_success'] = "Pembayaran berhasil dilakukan dan sedang menunggu verifikasi.";
                } else {
                    $_SESSION['payment_error'] = "Error saat menyimpan data pembayaran: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt_pembayaran);
            } else {
                $_SESSION['payment_error'] = "Terjadi kesalahan saat mengunggah bukti pembayaran.";
            }
        } else {
            // Gabungkan pesan kesalahan menjadi satu string
            $_SESSION['payment_error'] = implode(" ", $error_messages);
        }
    } else {
        $_SESSION['payment_error'] = "Bukti pembayaran tidak diunggah.";
    }

    header('location: ../pasien/dashboard.php');
    exit;
}


// Logika Edit Data Pasien
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_update_profile'])) {
    // Ambil data dari form
    $nama = $_POST['nama'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $jenis_kelamin = $_POST['gender'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';

    // Inisialisasi pesan kesalahan
    $error_messages = [];

    // Validasi data
    if (empty($nama)) {
        $error_messages[] = "Nama tidak boleh kosong.";
    }

    if (empty($alamat)) {
        $error_messages[] = "Alamat tidak boleh kosong.";
    }

    if (empty($jenis_kelamin) || !in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
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

    // Jika tidak ada kesalahan, lakukan update
    if (empty($error_messages)) {
        // Query untuk mengupdate data pasien
        $sql_update_profil = "UPDATE pasien SET 
            nama = ?, 
            alamat = ?, 
            jenis_kelamin = ?, 
            no_telp = ? 
            WHERE id_user = ?";

        // Persiapkan statement
        $stmt_update = mysqli_prepare($conn, $sql_update_profil);
        if (!$stmt_update) {
            die("Error preparing statement: " . mysqli_error($conn));
        }

        // Binding parameter
        mysqli_stmt_bind_param($stmt_update, 'sssss', $nama, $alamat, $jenis_kelamin, $no_telp, $id_user);

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['update_profile_success'] = "Profil berhasil diperbarui.";
        } else {
            $_SESSION['update_profile_error'] = "Error saat memperbarui profil: " . mysqli_error($conn);
        }

        // Tutup statement
        mysqli_stmt_close($stmt_update);
    } else {
        // Gabungkan pesan kesalahan menjadi satu string
        $_SESSION['update_profile_error'] = implode(" ", $error_messages);
    }

    // Redirect ke halaman profil
    header('location: ../pasien/profil.php');
    exit;
}


// Tutup koneksi
mysqli_close($conn);
?>