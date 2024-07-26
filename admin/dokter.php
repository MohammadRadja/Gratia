<?php
// Memuat file auto_load.php untuk koneksi ke database
include('../db/auto_load.php');

// Memuat file controller untuk logika verifikasi
include('../admin/controller/dokter_controller.php');

// Memuat header admin setelah mendapatkan data siswa
include('../template/admin/header.php');

$current_file = basename($_SERVER['PHP_SELF']);
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
        <button class="btn btn-primary" onclick="showAddModal()">Tambah Dokter</button>
    </div>

    <!-- Tabel Data Dokter -->
    <div class="container">
        <h1>Data Dokter</h1>
        <?php
        if (isset($_SESSION['crud_error'])) { ?>
        <div class="container">
            <div class="alert alert-danger">
                <h1 class="display-8"><?= $_SESSION['crud_error'] ?></h1>
                <p class="lead"></p>
            </div>
        </div>
        <?php unset($_SESSION['crud_error']); // Hapus session setelah digunakan ?>
        <?php }
        if (isset($_SESSION['crud_success'])) { ?>
            <div class="container">
                <div class="alert alert-success">
                    <h1 class="display-8"><?= $_SESSION['crud_success'] ?></h1>
                </div>
            </div>
            <?php unset($_SESSION['crud_success']); // Hapus session setelah digunakan ?>
        <?php }
    ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Dokter</th>
                    <th>spesialisasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_dokter as $dokter) { ?>
                    <tr>
                        <td><?= htmlspecialchars($dokter['nama_dokter']); ?></td>
                        <td><?= htmlspecialchars($dokter['spesialisasi']); ?></td>
                        <td>
                            <form method="POST" action="dokter.php" style="display:inline;">
                                <input type="hidden" name="id_dokter" value="<?= $dokter['id_dokter']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="button" class="btn btn-danger" onclick="showDeleteModal('<?= $dokter['id_dokter']; ?>', '<?= htmlspecialchars($dokter['nama_dokter']); ?>')">Delete</button>
                                </form>
                            <button class="btn btn-warning" onclick="editDokter('<?= $dokter['id_dokter']; ?>', '<?= htmlspecialchars($dokter['nama_dokter']); ?>', '<?= htmlspecialchars($dokter['spesialisasi']); ?>')">Edit</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Create/Edit Doctor -->
<div class="modal" id="dokterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="dokterForm" method="POST" action="dokter.php" onsubmit="return validateForm()">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dokter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="action" value="create">
                    <input type="hidden" name="id_dokter" id="id_dokter">
                    <div class="form-group">
                        <label for="nama_dokter">Nama Dokter</label>
                        <input type="text" class="form-control" id="nama_dokter" name="nama_dokter" required>
                    </div>
                    <div class="form-group">
                        <label for="spesialisasi">Spesialisasi</label>
                        <input type="text" class="form-control" id="spesialisasi" name="spesialisasi" required>
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
                Apakah Anda yakin ingin menghapus dokter <strong id="dokterNama"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="dokter.php">
                    <input type="hidden" name="id_dokter" id="deleteDokterId">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
function showAddModal() {
    document.getElementById('id_dokter').value = '';
    document.getElementById('nama_dokter').value = '';
    document.getElementById('spesialisasi').value = '';
    document.getElementById('action').value = 'create';
    $('#dokterModal').modal('show');
}

function editDokter(id, nama, spesialisasi) {
    document.getElementById('id_dokter').value = id;
    document.getElementById('nama_dokter').value = nama;
    document.getElementById('spesialisasi').value = spesialisasi;
    document.getElementById('action').value = 'update';
    $('#dokterModal').modal('show');
}

function showDeleteModal(id, nama) {
    document.getElementById('deleteDokterId').value = id;
    document.getElementById('dokterNama').innerText = nama;
    $('#deleteModal').modal('show');
}

function validateForm() {
    const namaDokter = document.getElementById('nama_dokter').value;
    const spesialisasi = document.getElementById('spesialisasi').value;

    if (!namaDokter || !spesialisasi) {
        alert("Semua field harus diisi!");
        return false;
    }

    if (namaDokter.length < 3) {
        alert("Nama dokter harus lebih dari 2 karakter!");
        return false;
    }

    if (spesialisasi.length < 3) {
        alert("Spesialisasi harus lebih dari 2 karakter!");
        return false;
    }

    return true; // Jika semua validasi lulus
}
</script>

<?php include('../template/admin/footer.php'); ?>
