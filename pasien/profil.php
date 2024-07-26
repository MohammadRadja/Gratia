<?php
// Include necessary files and start session
include('../db/auto_load.php');
include('../template/pasien/dashboard_header.php');
include('../pasien/controller/pasien_dashboard_control.php');
?>


<!-- Begin Page Content -->
<div class="container-fluid">
<?php
if (isset($_SESSION['update_profile_error'])) { ?>
    <div class="container">
        <div class="alert alert-danger">
            <h1 class="display-4"><?= $_SESSION['update_profile_error'] ?></h1>
            <p class="lead"></p>
        </div>
    </div>
    <?php unset($_SESSION['update_profile_error']); // Hapus session setelah digunakan ?>
<?php } ?>

<?php
if (isset($_SESSION['update_profile_success'])) { ?>
    <div class="container">
        <div class="alert alert-success">
            <h1 class="display-4"><?= $_SESSION['update_profile_success'] ?></h1>
            <p class="lead"></p>
        </div>
    </div>
    <?php unset($_SESSION['update_profile_success']); // Hapus session setelah digunakan ?>
<?php } ?>
    <h1 class="h3 mb-4 text-gray-800">Formulir Pasien</h1>
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                </div>
                <div class="card-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <h1>Data Pasien</h1>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($data_pasien['nama'] ?? '') ?>" class="form-control" id="nama">
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" name="alamat" value="<?= htmlspecialchars($data_pasien['alamat'] ?? '') ?>" class="form-control" id="alamat">
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <br>
                                <input type="radio" id="gender_l" name="gender" value="Laki-laki" <?= ($data_pasien['jenis_kelamin'] == 'Laki-laki') ? 'checked' : '' ?>> Laki-laki
                                <br>
                                <input type="radio" id="gender_p" name="gender" value="Perempuan" <?= ($data_pasien['jenis_kelamin'] == 'Perempuan') ? 'checked' : '' ?>> Perempuan
                            </div>
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">No Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="tel" name="no_telp" value="<?= htmlspecialchars(substr($data_pasien['no_telp'] ?? '', 3)) ?>" class="form-control" id="no_telp" placeholder="Nomor telepon tanpa 0 di depan">
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
                    <a href="../siswa/dashboard.php" class="btn btn-danger">Kembali</a>
                </form>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->

<?php include('../template/pasien/dashboard_footer.php'); ?>
