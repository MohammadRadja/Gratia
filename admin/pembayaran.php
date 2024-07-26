<?php
// Memuat file auto_load.php untuk koneksi ke database
include('../db/auto_load.php');

// Memuat file controller untuk logika verifikasi
include('../admin/controller/admin_dashboard_control.php');

// Memuat header admin setelah mendapatkan data pembayaran
include('../template/admin/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
    </div>

    <!-- Tabel Data Pembayaran -->
    <div class="container">
        <h1>Data Pembayaran</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>Nama Dokter</th>
                    <th>Nama Treatment</th>
                    <th>Biaya</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Bayar</th>
                    <th>Jumlah Bayar</th>
                    <th>Bukti Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_pembayaran as $pembayaran) { ?>
                    <tr>
                        <td><?= htmlspecialchars($pembayaran['nama_pasien']); ?></td>
                        <td><?= htmlspecialchars($pembayaran['nama_dokter']); ?></td>
                        <td><?= htmlspecialchars($pembayaran['nama_treatment']); ?></td>
                        <td>Rp<?= number_format($pembayaran['biaya'], 0, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($pembayaran['status_pembayaran']); ?></td>
                        <td><?= htmlspecialchars($pembayaran['tanggal_bayar']); ?></td>
                        <td>Rp<?= number_format($pembayaran['jumlah_bayar'], 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($pembayaran['bukti_pembayaran']) : ?>
                                <a href="<?= htmlspecialchars($pembayaran['bukti_pembayaran']); ?>" target="_blank">Lihat Bukti</a>
                            <?php else : ?>
                                <span class="badge badge-warning">Belum Ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($pembayaran['status_pembayaran'] == 'Belum Bayar'): ?>
                                <a href="./controller/admin_dashboard_control.php?id=<?= htmlspecialchars($pembayaran['id_pembayaran']); ?>&action=terima" class="btn btn-success btn-sm">Terima</a>
                                <a href="./controller/admin_dashboard_control.php?id=<?= htmlspecialchars($pembayaran['id_pembayaran']); ?>&action=tolak" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menolak pembayaran ini?')">Tolak</a>
                            <?php else: ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<!-- End of Page Content -->

<?php include('../template/admin/footer.php'); ?>
