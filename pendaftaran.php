<?php
include('./db/koneksi.php');
include('template/pasien/header.php');
?>
<div class="jumbotron">
    <div class="container">
        <h1 data-aos="fade-down" data-aos-duration="2000" class="display-4">Selamat Datang di Gratia Jaya Mulya</h1>
        <p data-aos="fade-down" data-aos-duration="2000" class="lead">Silahkan isi data pasien</p>
    </div>
</div>

<div class="pendaftaran mt-5" style="height: 1000px;">
    <div class="container">
        <?php
        session_start();
        if (isset($_SESSION['pendaftaran_error'])) { ?>
            <div class="alert alert-danger">
                <?= $_SESSION['pendaftaran_error'] ?>
            </div>
        <?php }
        session_destroy();
        ?>
        <form action="core/pendaftaran_control.php" method="POST">
            <h1>Data Pasien</h1>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Pasien</label>
                <input type="text" name="nama" class="form-control" id="nama" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" name="alamat" class="form-control" id="alamat" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-check-label">Jenis Kelamin</label>
                <br>
                <input type="radio" name="gender" class="form-check-input" id="gender_laki" value="Laki-laki" required> Laki-laki
                <input type="radio" name="gender" class="form-check-input" id="gender_perempuan" value="Perempuan" required> Perempuan
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
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button name="btn_registrasi" value="simpan" class="btn btn-primary">Submit</button>
            <a class="btn btn-danger" href="index.php">Kembali</a>
        </form>
    </div>
</div>
<?php include('template/pasien/footer.php') ?>
