<?php
// Memuat file auto_load.php untuk koneksi ke database
include('../db/auto_load.php');

// Memuat file controller untuk logika verifikasi
include('../admin/controller/treatment_controller.php');

// Memuat header admin setelah mendapatkan data siswa
include('../template/admin/header.php');

$current_file = basename($_SERVER['PHP_SELF']);
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
        <button class="btn btn-primary" onclick="showAddModal()">Tambah Treatment</button>
    </div>

    <!-- Tabel Data Siswa -->
    <div class="container">
        <h1>Data Treatment</h1>
        <?php
        if (isset($_SESSION['crud_error'])) { ?>
        <div class="container">
            <div class="alert alert-danger">
                <h1 class="display-8 text-center"><?= $_SESSION['crud_error'] ?></h1>
                <p class="lead"></p>
            </div>
        </div>
        <?php unset($_SESSION['crud_error']); // Hapus session setelah digunakan ?>
        <?php }
        if (isset($_SESSION['crud_success'])) { ?>
            <div class="container">
                <div class="alert alert-success">
                    <h1 class="display-8 text-center"><?= $_SESSION['crud_success'] ?></h1>
                </div>
            </div>
            <?php unset($_SESSION['crud_success']); // Hapus session setelah digunakan ?>
        <?php }
    ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Treatment</th>
                    <th>Biaya</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data_treatment as $treatment) { ?>
                    <tr>
                        <td><?= htmlspecialchars($treatment['nama_treatment']); ?></td>
                        <td>Rp<?= number_format(htmlspecialchars($treatment['biaya']), 0, ',', '.'); ?></td>
                        <td>
                            <form method="POST" action="treatment.php" style="display:inline;">
                                <input type="hidden" name="id_treatment" value="<?= $treatment['id_treatment']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="button" class="btn btn-danger" onclick="showDeleteModal('<?= $treatment['id_treatment']; ?>', '<?= htmlspecialchars($treatment['nama_treatment']); ?>')">Delete</button>
                            </form>
                            <button class="btn btn-warning" onclick="edittreatment('<?= $treatment['id_treatment']; ?>', '<?= htmlspecialchars($treatment['nama_treatment']); ?>', '<?= htmlspecialchars($treatment['biaya']); ?>')">Edit</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- Modal for Create/Edit Treatment -->
<div class="modal" id="treatmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="treatmentForm" method="POST" action="treatment.php" onsubmit="return validateForm()">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">treatment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="action" value="create">
                    <input type="hidden" name="id_treatment" id="id_treatment">
                    <div class="form-group">
                        <label for="nama_treatment">Nama treatment</label>
                        <input type="text" class="form-control" id="nama_treatment" name="nama_treatment" required>
                    </div>
                    <div class="form-group">
                        <label for="biaya">biaya</label>
                        <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="biaya" name="biaya" required>
                        </div>
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
                Apakah Anda yakin ingin menghapus treatment <strong id="nama_treatment"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="treatment.php">
                    <input type="hidden" name="id_treatment" id="deleteTreatmentId">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('id_treatment').value = '';
    document.getElementById('nama_treatment').value = '';
    document.getElementById('biaya').value = '';
    document.getElementById('action').value = 'create';
    $('#treatmentModal').modal('show');
}

function edittreatment(id, nama, biaya) {
    document.getElementById('id_treatment').value = id;
    document.getElementById('nama_treatment').value = nama;
    document.getElementById('biaya').value = biaya;
    document.getElementById('action').value = 'update';
    $('#treatmentModal').modal('show');
}

function showDeleteModal(id, nama) {
    document.getElementById('deleteTreatmentId').value = id;
    document.getElementById('nama_treatment').innerText = nama;
    $('#deleteModal').modal('show');
}

function validateForm() {
    const namatreatment = document.getElementById('nama_treatment').value;
    const biaya = document.getElementById('biaya').value;

    if (!namatreatment || !biaya) {
        alert("Semua field harus diisi!");
        return false;
    }

    if (namatreatment.length < 3) {
        alert("Nama treatment harus lebih dari 2 karakter!");
        return false;
    }

    if (biaya.length < 3) {
        alert("biaya harus lebih dari 2 karakter!");
        return false;
    }
    return true; // Jika semua validasi lulus
}

function formatRupiah(input) {
    let value = input.value.replace(/[^,\d]/g, '');
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    input.value = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
}

</script>

<?php include('../template/admin/footer.php'); ?>