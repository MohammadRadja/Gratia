<?php 
include('../db/auto_load.php'); 
include('./controller/pasien_dashboard_control.php');
include('../template/pasien/dashboard_header.php');

// Cek apakah pasien sudah login dan data pasien tersedia
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['level'] != 'pasien') {
    header('location:../login.php');
    exit;
}

// Ambil ID pasien dari session
$id_user = $_SESSION['id_users'] ?? 'default_id';

// Ambil data pasien dari database
$sql_pasien = "SELECT * FROM pasien WHERE id_pasien = ?";
$result_pasien = executeQuery($conn, $sql_pasien, [$id_user], "s");

if ($result_pasien->num_rows > 0) {
    $data_pasien = $result_pasien->fetch_array(MYSQLI_ASSOC);
} else {
    $_SESSION['update_profile_error'] = "Data Pasien tidak ditemukan";
    header('location:../pasien/dashboard.php'); // Redirect jika tidak ada data pasien
    exit;
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Profile Pasien</h1>
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                </div>
                <div class="card-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <?php if (isset($_SESSION['update_profile_error'])): ?>
                        <div class="container">
                            <div class="alert alert-danger">
                                <h1 class="display-8 text-center"><?= $_SESSION['update_profile_error'] ?></h1>
                            </div>
                        </div>
                        <?php unset($_SESSION['update_profile_error']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['update_profile_success'])): ?>
                        <div class="container">
                            <div class="alert alert-success">
                                <h1 class="display-8 text-center"><?= $_SESSION['update_profile_success'] ?></h1>
                            </div>
                        </div>
                        <?php unset($_SESSION['update_profile_success']); ?>
                    <?php endif; ?>
                    
                    <h1>Data Pasien</h1>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($data_pasien['nama'] ?? '') ?>" class="form-control" id="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" name="alamat" value="<?= htmlspecialchars($data_pasien['alamat'] ?? '') ?>" class="form-control" id="alamat" required>
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <br>
                                <input type="radio" id="gender_l" name="gender" value="Laki-laki" <?= ($data_pasien['jenis_kelamin'] == 'Laki-laki') ? 'checked' : '' ?> required> Laki-laki
                                <br>
                                <input type="radio" id="gender_p" name="gender" value="Perempuan" <?= ($data_pasien['jenis_kelamin'] == 'Perempuan') ? 'checked' : '' ?> required> Perempuan
                            </div>
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">No Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="tel" name="no_telp" value="<?= htmlspecialchars(substr($data_pasien['no_telp'] ?? '', 0)) ?>" class="form-control" id="no_telp" placeholder="Masukkan nomor telepon tanpa 0 di depan (misal: 81234567890)" required>
                                </div>
                                <small class="form-text text-muted">Masukkan nomor telepon tanpa 0 di depan (misal: 81234567890).</small>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Pembayaran</label>
                                <input type="text" name="status" value="<?= htmlspecialchars($data_pasien['status_pembayaran'] ?? '') ?>" class="form-control" id="status" disabled>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="btn_update_profile" value="update_profil" class="btn btn-primary">Edit</button>
                    <a href="../pasien/dashboard.php" class="btn btn-danger">Kembali</a>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../template/pasien/dashboard_footer.php'); ?>
