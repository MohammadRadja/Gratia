<?php
// Include necessary files and start session
include('../db/auto_load.php');
include('../template/pasien/dashboard_header.php');
include('../pasien/controller/pasien_dashboard_control.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Janji Temu</h1>
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Janji Temu</h6>
                </div>
                <div class="card-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                        <h1>Data Appointment</h1>
                        <?php if (isset($_SESSION['appointment_error'])) { ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['appointment_error'] ?>
                            </div>
                            <?php unset($_SESSION['appointment_error']); ?>
                        <?php } ?>

                        <?php if (isset($_SESSION['appointment_success'])) { ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['appointment_success'] ?>
                            </div>
                            <?php unset($_SESSION['appointment_success']); ?>
                        <?php } ?>

                        <?php if (isset($_SESSION['appointment_exists'])) { ?>
                            <div class="alert alert-info">
                                <?= $_SESSION['appointment_exists'] ?>
                                <ul>
                                    <?php foreach ($data_bayar as $appointment) { ?>
                                        <li>
                                            <strong>Dokter:</strong> <?= $appointment['nama_dokter'] ?><br>
                                            <strong>Treatment:</strong> <?= $appointment['nama_treatment'] ?><br>
                                            <strong>Jadwal:</strong> <?= $appointment['jadwal_appointment'] ?><br>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php unset($_SESSION['appointment_exists']); ?>
                        <?php } else { ?>
                        <div class="mb-3">
                            <label for="dokter" class="form-label">Dokter</label>
                            <select name="dokter" class="form-control" id="dokter" required>
                                <?php foreach ($dokters as $dokter) { ?>
                                    <option value="<?= $dokter['id_dokter'] ?>"><?= $dokter['nama_dokter'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="treatment" class="form-label">Treatment</label>
                            <select name="treatment" class="form-control" id="treatment" required>
                                <?php foreach ($treatments as $treatment) { ?>
                                    <option value="<?= $treatment['id_treatment'] ?>"><?= $treatment['nama_treatment'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jadwal" class="form-label">Jadwal</label>
                            <input type="date" name="jadwal" class="form-control" id="jadwal" required>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control" id="catatan" required>
                        </div>
                        <button type="submit" name="btn_appointment" value="appointment" class="btn btn-primary">Buat Janji Temu</button>
                        <a class="btn btn-danger" href="dashboard.php">Kembali</a>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->

<?php include('../template/pasien/dashboard_footer.php'); ?>
