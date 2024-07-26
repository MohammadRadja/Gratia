<?php
include('./db/koneksi.php');

// Ambil data treatment dari database
$query = "SELECT * FROM Treatment";
$result = $conn->query($query);
$treatments = []; // Inisialisasi variabel treatments

if ($result) {
    if ($result->num_rows > 0) {
        // Ambil semua data treatment
        while ($row = $result->fetch_assoc()) {
            $treatments[] = $row;
        }
    } else {
        // Jika tidak ada treatment
        echo "Tidak ada data treatment ditemukan.";
    }
} else {
    echo "Error dalam query: " . $koneksi->error; // Tampilkan pesan kesalahan
}

// Ambil data dokter dari database
$query = "SELECT * FROM dokter";
$result = $conn->query($query);
$dokter = []; // Inisialisasi variabel treatments

if ($result) {
    if ($result->num_rows > 0) {
        // Ambil semua data treatment
        while ($row = $result->fetch_assoc()) {
            $dokters[] = $row;
        }
    } else {
        // Jika tidak ada treatment
        echo "Tidak ada data treatment ditemukan.";
    }
} else {
    echo "Error dalam query: " . $koneksi->error; // Tampilkan pesan kesalahan
}

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
                <label for="noTlp" class="form-label">No Telepon</label>
                <input type="tel" name="noTlp" class="form-control" id="noTlp" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <h1>Data Appointment</h1>
            <div class="mb-3">
                <label for="treatment" class="form-label">Treatment</label>
                <select name="treatment" class="form-control" id="treatment" required>
                    <?php foreach ($treatments as $treatment) { ?>
                        <option value="<?= $treatment['id_treatment'] ?>"><?= $treatment['nama_treatment'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="dokter" class="form-label">Dokter</label>
                <select name="dokter" class="form-control" id="dokter" required>
                    <?php foreach ($dokters as $dokter) { ?>
                        <option value="<?= $dokter['id_dokter'] ?>"><?= $dokter['nama_dokter'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <input type="text" name="catatan" class="form-control" id="catatan" required>
            </div>
            <button name="btn_registrasi" value="simpan" class="btn btn-primary">Submit</button>
            <a class="btn btn-danger" href="index.php">Kembali</a>
        </form>
    </div>
</div>
<?php include('template/pasien/footer.php') ?>
