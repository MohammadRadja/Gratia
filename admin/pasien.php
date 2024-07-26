<?php
// Memuat file auto_load.php untuk koneksi ke database
include('../db/auto_load.php');

// Memuat file controller untuk logika verifikasi
include('../admin/controller/admin_dashboard_control.php');

// Memuat header admin setelah mendapatkan data pasien
include('../template/admin/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
        <button class="btn btn-primary" onclick="showAddModal()">Tambah Pasien</button>
    </div>

    <!-- Tabel Data Pasien -->
    <div class="container">
        <h1>Data Pasien</h1>
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
                    <th>Alamat</th>
                    <th>Jenis Kelamin</th>
                    <th>No Telp</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data_pasien as $pasien) { ?>
                <tr>
                    <td><?= htmlspecialchars($pasien['nama']); ?></td>
                    <td><?= htmlspecialchars($pasien['alamat']); ?></td>
                    <td><?= htmlspecialchars($pasien['jenis_kelamin']); ?></td>
                    <td><?= htmlspecialchars($pasien['no_telp']); ?></td>
                    <td><?= htmlspecialchars($pasien['status_pembayaran']); ?></td>
                    <td>
                        <form method="POST" action="pasien.php" style="display:inline;">
                            <input type="hidden" name="id_pasien" value="<?= $pasien['id_pasien']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pasien ini?');">Delete</button>
                        </form>
                        <button class="btn btn-warning" onclick="editPasien('<?= $pasien['id_pasien']; ?>', '<?= htmlspecialchars($pasien['nama']); ?>', '<?= htmlspecialchars($pasien['alamat']); ?>', '<?= htmlspecialchars($pasien['jenis_kelamin']); ?>', '<?= htmlspecialchars($pasien['no_telp']); ?>')">Edit</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Create/Edit Pasien -->
    <div class="modal" id="pasienModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="pasienForm" method="POST" action="pasien.php" onsubmit="return validateForm()">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Pasien</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="create">
                        <input type="hidden" name="id_pasien" id="id_pasien">
                        <div class="form-group">
                            <label for="nama_pasien">Nama Pasien</label>
                            <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                        </div>
                        <div class="form-group">
                            <label for="gender" class="form-check-label">Jenis Kelamin</label>
                            <br>
                            <input type="radio" name="jenis_kelamin" class="form-check-input" id="gender_laki" value="Laki-laki" required> Laki-laki
                            <input type="radio" name="jenis_kelamin" class="form-check-input" id="gender_perempuan" value="Perempuan" required> Perempuan
                        </div>
                        <div class="form-group">
                            <label for="no_telp">No Telepon</label>
                            <input type="tel" class="form-control" id="no_telp" name="no_telp" required>
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

    <script>
    function showAddModal() {
        document.getElementById('id_pasien').value = '';
        document.getElementById('nama_pasien').value = '';
        document.getElementById('alamat').value = '';
        document.querySelector('input[name="jenis_kelamin"]:checked').checked = false;
        document.getElementById('no_telp').value = '';
        document.getElementById('action').value = 'create';
        $('#pasienModal').modal('show');
    }

    function editPasien(id, nama, alamat, jenis_kelamin, no_telp) {
        document.getElementById('id_pasien').value = id;
        document.getElementById('nama_pasien').value = nama;
        document.getElementById('alamat').value = alamat;
        document.getElementById('no_telp').value = no_telp;
        document.querySelector(`input[name="jenis_kelamin"][value="${jenis_kelamin}"]`).checked = true; // Set radio button
        document.getElementById('action').value = 'update'; // Set action ke update
        $('#pasienModal').modal('show'); // Tampilkan modal
    }

    function validateForm() {
        const namaPasien = document.getElementById('nama_pasien').value;
        const noTelp = document.getElementById('no_telp').value;

        if (!namaPasien || !noTelp) {
            alert("Semua field harus diisi!");
            return false;
        }

        if (namaPasien.length < 3) {
            alert("Nama pasien harus lebih dari 2 karakter!");
            return false;
        }

        if (noTelp.length < 10) {
            alert("No Telepon harus lebih dari 9 karakter!");
            return false;
        }

        return true; // Jika semua validasi lulus
    }
    </script>

<?php include('../template/admin/footer.php'); ?>
