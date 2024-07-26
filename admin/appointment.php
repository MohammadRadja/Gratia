<?php
// Memuat file auto_load.php untuk koneksi ke database
include('../db/auto_load.php');

// Memuat file controller untuk logika verifikasi
include('../admin/controller/appointment_controller.php');

// Memuat header admin setelah mendapatkan data appointment
include('../template/admin/header.php');

$current_file = basename($_SERVER['PHP_SELF']);
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
        <button class="btn btn-primary" onclick="showAddModal()">Tambah Appointment</button>
    </div>

    <!-- Tabel Data Appointment -->
    <div class="container">
        <h1>Data Appointment</h1>
        <?php
        if (isset($_SESSION['crud_error'])) { ?>
        <div class="container">
            <div class="alert alert-danger">
                <h1 class="display-8"><?= $_SESSION['crud_error'] ?></h1>
            </div>
        </div>
        <?php unset($_SESSION['crud_error']); }
        
        if (isset($_SESSION['crud_success'])) { ?>
            <div class="container">
                <div class="alert alert-success">
                    <h1 class="display-8"><?= $_SESSION['crud_success'] ?></h1>
                </div>
            </div>
            <?php unset($_SESSION['crud_success']); }
        ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>Nama Dokter</th>
                    <th>Treatment</th>
                    <th>Jadwal</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_appointment as $appointment) { ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['nama_pasien']); ?></td>
                        <td><?= htmlspecialchars($appointment['nama_dokter']); ?></td>
                        <td><?= htmlspecialchars($appointment['nama_treatment']); ?></td>
                        <td><?= htmlspecialchars($appointment['jadwal_appointment']); ?></td>
                        <td><?= htmlspecialchars($appointment['catatan']); ?></td>
                        <td>
                            <form method="POST" action="appointment.php" style="display:inline;">
                                <input type="hidden" name="id_appointment" value="<?= $appointment['id_appointment']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="button" class="btn btn-danger" onclick="showDeleteModal('<?= $appointment['id_appointment']; ?>', '<?= htmlspecialchars($appointment['nama_pasien']); ?>')">Delete</button>
                            </form>
                            <button class="btn btn-warning" onclick="editAppointment('<?= $appointment['id_appointment']; ?>', '<?= htmlspecialchars($appointment['nama_pasien']); ?>', '<?= htmlspecialchars($appointment['nama_dokter']); ?>', '<?= htmlspecialchars($appointment['nama_treatment']); ?>', '<?= htmlspecialchars($appointment['jadwal_appointment']); ?>', '<?= htmlspecialchars($appointment['catatan']); ?>')">Edit</button>
                        </td>
                    </tr>
                    <?php 
                } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Create/Edit Appointment -->
    <div class="modal" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="appointmentForm" method="POST" action="appointment.php" onsubmit="return validateForm()">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Appointment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="create">
                        <input type="hidden" name="id_appointment" id="id_appointment">
                        <div class="form-group">
                            <label for="id_pasien">Nama Pasien</label>
                            <select class="form-control" id="id_pasien" name="id_pasien" required>
                                <?php foreach ($pasien as $pasien) { ?>
                                    <option value="<?= $pasien['id_pasien'] ?>"><?= $pasien['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_dokter">Nama Dokter</label>
                            <select class="form-control" id="id_dokter" name="id_dokter" required>
                                <?php foreach ($dokters as $dokter) { ?>
                                    <option value="<?= $dokter['id_dokter'] ?>"><?= $dokter['nama_dokter'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_treatment">Treatment</label>
                            <select class="form-control" id="id_treatment" name="id_treatment" required>
                                <?php foreach ($treatment as $treatment) { ?>
                                    <option value="<?= $treatment['id_treatment'] ?>"><?= $treatment['nama_treatment'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jadwal_appointment">Jadwal</label>
                            <input type="datetime-local" class="form-control" id="jadwal_appointment" name="jadwal_appointment" required>
                        </div>
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk Konfirmasi Hapus -->
    <div class="modal" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus appointment <strong id="catatan"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" action="appointment.php">
                        <input type="hidden" name="id_appointment" id="deleteAppointmentId">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
function showAddModal() {
    document.getElementById('id_appointment').value = '';
    document.getElementById('id_appointment').value = '';
    document.getElementById('id_pasien').value = '';
    document.getElementById('id_dokter').value = '';
    document.getElementById('id_treatment').value = '';
    document.getElementById('jadwal_appointment').value = '';
    document.getElementById('catatan').value = '';
    document.getElementById('action').value = 'create';
    $('#appointmentModal').modal('show');
}

function editAppointment(id, namaPasien, idDokter, idTreatment, jadwal, catatan) {
    document.getElementById('id_appointment').value = id;
    document.getElementById('id_pasien').value = namaPasien;
    document.getElementById('id_dokter').value = idDokter; // Menggunakan id_dokter
    document.getElementById('id_treatment').value = idTreatment; // Menggunakan id_treatment
    document.getElementById('jadwal_appointment').value = jadwal;
    document.getElementById('catatan').value = catatan;
    document.getElementById('action').value = 'update';
    $('#appointmentModal').modal('show');
}

function showDeleteModal(id, nama) {
    document.getElementById('deleteAppointmentId').value = id;
    document.getElementById('catatan').innerText = nama; // Ganti dengan ID catatan
    $('#deleteModal').modal('show');
}

function validateForm() {
    const namaPasien = document.getElementById('nama_pasien').value;
    const namaDokter = document.getElementById('nama_dokter').value;
    const namaTreatment = document.getElementById('nama_treatment').value;
    const jadwal = document.getElementById('jadwal_appointment').value;
    const catatan = document.getElementById('catatan').value;

    // Periksa apakah semua field telah diisi
    if (!namaPasien || !namaDokter || !namaTreatment || !jadwal || !catatan) {
        alert("Semua field harus diisi!");
        return false;
    }

    return true; // Jika semua validasi lulus
}
</script>

<?php include('../template/admin/footer.php'); ?>
