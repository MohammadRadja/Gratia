<?php
// Periksa apakah session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect ke halaman login jika pasien belum login
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['level'] != 'pasien') {
    header('location:../login.php');
    exit;
}

$id_user = $_SESSION['id_users'] ?? 'default_id'; // Nilai default jika tidak ada id_users
$error_messages = [];

// Fungsi untuk mengeksekusi query dan mengembalikan hasil
function executeQuery($conn, $sql, $params = [], $types = "") {
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    // Hanya lakukan binding jika ada parameter
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

// Ambil data pasien
$sql_pasien = "SELECT * FROM pasien WHERE id_pasien = ?";
$result_pasien = executeQuery($conn, $sql_pasien, [$id_user], "s");

if ($result_pasien->num_rows === 0) {
    $error_messages[] = "Data Pasien tidak ditemukan";
} else {
    $data_pasien = $result_pasien->fetch_array(MYSQLI_ASSOC);
}

// Ambil data dokter
$sql_dokter = "SELECT * FROM dokter";
$result_dokter = executeQuery($conn, $sql_dokter);

if ($result_dokter->num_rows === 0) {
    $error_messages[] = "Data Dokter tidak ditemukan";
} else {
    $dokters = $result_dokter->fetch_all(MYSQLI_ASSOC);
}

// Ambil data treatment
$sql_treatment = "SELECT * FROM treatment";
$result_treatment = executeQuery($conn, $sql_treatment);

if ($result_treatment->num_rows === 0) {
    $error_messages[] = "Data Treatment tidak ditemukan";
} else {
    $treatments = $result_treatment->fetch_all(MYSQLI_ASSOC);
}

$statusMessages = [
    'dibayar' => "Pembayaran berhasil!",
    'belum dibayar' => "Lengkapi data dan lakukan pembayaran.",
    'pembayaran_exists' => "Pembayaran anda berhasil.",
    'pembayaran_empty' => "Belum ada pembayaran yang dilakukan. Lengkapi data dan lakukan pembayaran segera.",
    'appointment_exists' => "Anda memiliki appointment yang sudah terjadwal.",
    'appointment_empty' => "Silakan buat appointment terlebih dahulu."
];

// Logika Status Pembayaran
$sql_pembayaran = "SELECT 
    p.nama AS nama_pasien,
    d.nama_dokter,
    t.nama_treatment,
    t.biaya,
    p.status_pembayaran,
    a.jadwal_appointment,
    tr.tanggal_bayar,
    COALESCE(tr.jumlah_bayar, 0) AS jumlah_bayar
FROM 
    Pasien p
JOIN 
    Appointment a ON p.id_pasien = a.id_pasien
JOIN 
    Dokter d ON a.id_dokter = d.id_dokter
JOIN 
    Treatment t ON a.id_treatment = t.id_treatment
LEFT JOIN 
    Transaksi tr ON p.id_pasien = tr.id_pasien AND d.id_dokter = tr.id_dokter AND t.id_treatment = tr.id_treatment
WHERE p.id_pasien = ?";
$result_bayar = executeQuery($conn, $sql_pembayaran, [$data_pasien['id_pasien']], "s");

if ($result_bayar && $result_bayar->num_rows > 0) {
    // Ambil semua baris menjadi array
    $data_bayar = [];
    while ($row = $result_bayar->fetch_assoc()) {
        $data_bayar[] = $row; // Tambahkan setiap baris ke dalam array
    }

    // Jika ingin mengambil satu baris pertama
    $first_row = $data_bayar[0];
    
    // Atur nilai variabel berdasarkan data yang ditemukan
    $nama_pasien = htmlspecialchars($first_row['nama_pasien']);
    $nama_dokter = htmlspecialchars($first_row['nama_dokter']);
    $nama_treatment = htmlspecialchars($first_row['nama_treatment']);
    $biaya = htmlspecialchars($first_row['biaya']);
    $status_pembayaran = htmlspecialchars($first_row['status_pembayaran']);

    $_SESSION['pembayaran_exists'] = $statusMessages['pembayaran_exists'];
    $_SESSION['appointment_exists'] = $statusMessages['appointment_exists'];
} else {
    $_SESSION['appointment_empty'] = $statusMessages['appointment_empty'];
    $_SESSION['pembayaran_empty'] = $statusMessages['pembayaran_empty'];
}



// Fungsi untuk memvalidasi data profil
function validateProfileData($nama, $alamat, $jenis_kelamin, $no_telp) {
    $error_messages = [];
    
    if (empty($nama)) {
        $error_messages[] = "Nama tidak boleh kosong.";
    }
    if (empty($alamat)) {
        $error_messages[] = "Alamat tidak boleh kosong.";
    }
    if (empty($jenis_kelamin) || !in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
        $error_messages[] = "Jenis kelamin tidak valid.";
    }
    if (empty($no_telp) || !preg_match('/^\+62[0-9]{9,14}$/', $no_telp)) {
        $error_messages[] = "Nomor telepon tidak valid. Harus diawali dengan +62 dan memiliki total antara 10 hingga 15 digit.";
        $no_telp = ''; // Set no_telp menjadi kosong jika tidak valid
    } else {
        // Jika validasi berhasil, kita akan menghilangkan angka pertama (0) dan menambahkan +62
        $no_telp = '+62' . ltrim($no_telp, '0'); // Ini jika Anda ingin menyimpan nomor dalam format +62
    }
    
    return [$error_messages, $no_telp];
}

// Fungsi untuk memperbarui profil pasien
function updateProfile($conn, $id_user) {
        $nama = $_POST['nama'] ?? '';
        $alamat = $_POST['alamat'] ?? '';
        $jenis_kelamin = $_POST['gender'] ?? '';
        $no_telp = $_POST['no_telp'] ?? '';

        // Validasi data
        list($error_messages, $no_telp) = validateProfileData($nama, $alamat, $jenis_kelamin, $no_telp);
        if (empty($error_messages)) {
            // Eksekusi query update
            $sql_update_profil = "UPDATE pasien SET nama = ?, alamat = ?, jenis_kelamin = ?, no_telp = ? WHERE id_pasien = ?";
            $stmt = $conn->prepare($sql_update_profil);
            $stmt->bind_param("ssssd", $nama, $alamat, $jenis_kelamin, $no_telp, $id_user);

            if ($stmt->execute()) {
                $_SESSION['update_profile_success'] = "Profil berhasil diperbarui.";
            } else {
                $_SESSION['update_profile_error'] = "Error saat memperbarui profil: " . $stmt->error; 
            }
            $stmt->close();
        } else {
            $_SESSION['update_profile_error'] = implode(" ", $error_messages);
    }
}

// Fungsi untuk mengunggah bukti pembayaran
function uploadPaymentProof($file) {
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_type = $file['type'];

    $max_file_size = 2 * 1024 * 1024; // 2 MB
    $allowed_file_types = ['image/jpeg', 'image/png', 'image/gif'];
    $upload_dir = "../assets/pembayaran/";

    if ($file_size > $max_file_size) {
        return ['error' => true, 'message' => "Ukuran file melebihi batas maksimal 2MB."];
    }

    if (!in_array($file_type, $allowed_file_types)) {
        return ['error' => true, 'message' => "Tipe file tidak diperbolehkan. Hanya JPG, PNG, dan GIF yang diperbolehkan."];
    }

    if (move_uploaded_file($file_tmp, $upload_dir . basename($file_name))) {
        return ['error' => false, 'file_name' => $file_name];
    } else {
        return ['error' => true, 'message' => "Terjadi kesalahan saat mengunggah bukti pembayaran."];
    }
}

// Fungsi untuk memproses pembayaran
function processPayment($conn) {
        $jumlah_bayar = mysqli_real_escape_string($conn, $_POST['jumlah_bayar']);
        $tanggal_bayar = mysqli_real_escape_string($conn, $_POST['tanggal_bayar']);


        if (empty($jumlah_bayar) || !is_numeric($jumlah_bayar) || $jumlah_bayar <= 0) {
            $_SESSION['payment_error'] = "Jumlah bayar tidak valid.";
            header('location: ../pasien/pembayaran.php');
            exit;
        }

        $id_pasien = $_SESSION['id_users'] ?? 'default_id';

        if (!empty($_FILES['bukti_pembayaran']['name'])) {
            $uploadResult = uploadPaymentProof($_FILES['bukti_pembayaran']);
            if ($uploadResult['error']) {
                $_SESSION['payment_error'] = $uploadResult['message'];
            } else {
                $bukti_pembayaran = $uploadResult['file_name'];
                // Pastikan untuk menyesuaikan query sesuai dengan struktur tabel
                $sql_pembayaran = "UPDATE transaksi SET tanggal_bayar = ?, jumlah_bayar = ?, bukti_pembayaran = ? WHERE id_pasien = ?";
                $stmt = $conn->prepare($sql_pembayaran);
                $stmt->bind_param("sdss", $tanggal_bayar, $jumlah_bayar, $bukti_pembayaran, $id_pasien);
                
                if ($stmt->execute()) {
                    $_SESSION['payment_success'] = "Pembayaran berhasil dilakukan dan sedang menunggu verifikasi.";
                } else {
                    $_SESSION['payment_error'] = "Error saat melakukan pembayaran: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $_SESSION['payment_error'] = "Bukti pembayaran tidak diunggah.";
        }

        header('location: ../pasien/pembayaran.php');
        exit;
}

// Fungsi untuk memvalidasi data appointment
function validateAppointmentData($treatment, $dokter) {
    $error_messages = [];
    
    if (empty($treatment)) {
        $error_messages[] = "Treatment tidak boleh kosong.";
    }
    if (empty($dokter)) {
        $error_messages[] = "Dokter tidak boleh kosong.";
    }
    
    return $error_messages;
}

// Fungsi untuk menjadwalkan appointment
function scheduleAppointment($conn) {
    $treatment = mysqli_real_escape_string($conn, $_POST['treatment']);
    $dokter = mysqli_real_escape_string($conn, $_POST['dokter']);
    $jadwal = mysqli_real_escape_string($conn, $_POST['jadwal']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $error_messages = validateAppointmentData($treatment, $dokter);

    if (!empty($error_messages)) {
        $_SESSION['appointment_error'] = implode(" ", $error_messages);
        header('Location: ../pasien/appointment.php');
        exit;
    }

    $id_appointment = uniqid();
    $id_transaksi = uniqid();
    $id_pasien = $_SESSION['id_users'] ?? 'default_id';

    try {
        mysqli_begin_transaction($conn);

         // Insert ke tabel appointment
         $sql_appointment = "INSERT INTO appointment (id_appointment, id_pasien, id_dokter, id_treatment, jadwal_appointment, catatan) VALUES (?, ?, ?, ?, ?, ?)";
         $stmt_appointment = executeQuery($conn, $sql_appointment, [$id_appointment, $id_pasien, $dokter, $treatment, $jadwal, $catatan], "ssssss");
         
         // Insert ke tabel transaksi
         $sql_transaksi = "INSERT INTO transaksi (id_transaksi, id_pasien, id_dokter, id_treatment, tanggal_bayar, jumlah_bayar, bukti_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?)";
         $stmt_transaksi = executeQuery($conn, $sql_transaksi, [$id_transaksi, $id_pasien, $dokter, $treatment, NULL, NULL, NULL], "sssssss");
         
         mysqli_commit($conn);
        $_SESSION['appointment_success'] = "Appointment berhasil dijadwalkan.";
    } catch (Exception $exception) {
        mysqli_rollback($conn);
        $_SESSION['appointment_error'] = "Terjadi kesalahan: " . $exception->getMessage();
    }

    header('Location: ../pasien/appointment.php');
    exit;
}


// Proses Pembayaran
if (isset($_POST['btn_bayar'])) {
    processPayment($conn);
}

// Proses Edit Data Pasien
if (isset($_POST['btn_update_profile'])) {
    updateProfile($conn, $id_user);
}

// Proses Penjadwalan Appointment
if (isset($_POST['btn_appointment'])) {
    scheduleAppointment($conn);
}

// Tutup koneksi
mysqli_close($conn);
?>
