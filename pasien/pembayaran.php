<?php 
include('../db/auto_load.php'); 
include('./controller/pasien_dashboard_control.php');
include('../template/pasien/dashboard_header.php');

$current_file = basename($_SERVER['PHP_SELF']);
?>

<!-- End of Topbar -->

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pembayaran</h1>
    </div>

    <div class="container">
        <div class="jumbotron p-1 mb-4 bg-light rounded-3">
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <form class="user" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                                <h1>Data Pembayaran</h1>
                                <?php 
                                // Memeriksa apakah ada input yang null
                                $input_fields = [
                                    $nama_pasien ?? null, 
                                    $nama_dokter ?? null, 
                                    $nama_treatment ?? null, 
                                    $biaya ?? null, 
                                    $status_pembayaran ?? null
                                ];
                                $has_null_input = in_array(null, $input_fields) || in_array('', $input_fields);

                                if (isset($_SESSION['pembayaran_exists']) && !$has_null_input) { ?>
                                        <div class="alert alert-info">
                                            <?= $_SESSION['pembayaran_exists'] ?>
                                            <ul>
                                                <?php foreach ($data_bayar as $data) { ?>
                                                    <li>
                                                        <strong>Nama Pasien:</strong> <?= isset($data['nama_pasien']) ? htmlspecialchars($data['nama_pasien']) : 'Tidak tersedia' ?>
                                                    </li>
                                                    <li>
                                                        <strong>Nama Dokter:</strong> <?= isset($data['nama_dokter']) ? htmlspecialchars($data['nama_dokter']) : 'Tidak tersedia' ?>
                                                    </li>
                                                    <li>
                                                        <strong>Treatment:</strong> <?= isset($data['nama_treatment']) ? htmlspecialchars($data['nama_treatment']) : 'Tidak tersedia' ?>
                                                    </li>
                                                    <li>
                                                        <strong>Jadwal:</strong> <?= isset($data['jadwal_appointment']) ? htmlspecialchars($data['jadwal_appointment']) : 'Tidak tersedia' ?>
                                                    </li>
                                                    <li>
                                                        <strong>Tanggal Bayar:</strong> <?= isset($data['tanggal_bayar']) ? htmlspecialchars($data['tanggal_bayar']) : 'Tidak tersedia' ?>
                                                    </li>
                                                    <li>
                                                        <strong>Jumlah Bayar:</strong> Rp <?= isset($data['jumlah_bayar']) ? number_format($data['jumlah_bayar'], 0, ',', '.') : '0' ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <?php unset($_SESSION['pembayaran_exists']); 
                                } elseif ($has_null_input) { // Jika ada input yang null, tampilkan form ?>
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama Pasien</label>
                                        <input type="text" name="nama" value="<?= htmlspecialchars($nama_pasien ?? ''); ?>" class="form-control" id="nama" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="dokter" class="form-label">Nama Dokter</label>
                                        <input type="text" name="dokter" value="<?= htmlspecialchars($nama_dokter ?? ''); ?>" class="form-control" id="dokter" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="treatment" class="form-label">Nama Treatment</label>
                                        <input type="text" name="treatment" value="<?= htmlspecialchars($nama_treatment ?? ''); ?>" class="form-control" id="treatment" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="biaya" class="form-label">Biaya Treatment</label>
                                        <input type="text" name="biaya" value="<?= htmlspecialchars($biaya ?? ''); ?>" class="form-control" id="biaya" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                                        <input type="text" name="status_pembayaran" value="<?= htmlspecialchars($status_pembayaran ?? ''); ?>" class="form-control" id="status_pembayaran" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggal_bayar" class="form-label">Tanggal Pembayaran</label>
                                        <input type="date" name="tanggal_bayar" class="form-control" id="tanggal_bayar" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jumlah_bayar" class="form-label">Jumlah Pembayaran</label>
                                        <input type="number" name="jumlah_bayar" class="form-control" id="jumlah_bayar" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bukti_pembayaran" class="form-label">Unggah Bukti Pembayaran</label>
                                        <input type="file" name="bukti_pembayaran" class="form-control" id="bukti_pembayaran" required>
                                    </div>
                                    <button type="submit" name="btn_bayar" value="bayar" class="btn btn-primary">Bayar</button>
                                    <a href="../siswa/profil.php" class="btn btn-danger">Kembali</a>
                                <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('../template/pasien/dashboard_footer.php'); ?>
