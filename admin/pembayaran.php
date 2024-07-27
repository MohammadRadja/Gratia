<?php
// Memuat file auto_load.php untuk koneksi ke database
include('../db/auto_load.php');

// Memuat file controller untuk logika verifikasi
include('../admin/controller/admin_dashboard_control.php');

// Memuat header admin setelah mendapatkan data pembayaran
include('../template/admin/header.php');

$current_file = basename($_SERVER['PHP_SELF']);
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
    </div>

    <!-- Kartu Data Pembayaran -->
    <div class="container">
        <h1>Data Pembayaran</h1>
        <div class="row">
            <?php foreach ($data_pembayaran as $pembayaran): ?>
                <div class="col-md-4 mb-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($pembayaran['nama_pasien']); ?></h5>
                            <p class="card-text"><strong>Dokter:</strong> <?= htmlspecialchars($pembayaran['nama_dokter']); ?></p>
                            <p class="card-text"><strong>Treatment:</strong> <?= htmlspecialchars($pembayaran['nama_treatment']); ?></p>
                            <p class="card-text"><strong>Biaya:</strong> Rp<?= number_format($pembayaran['biaya'], 0, ',', '.'); ?></p>
                            <p class="card-text"><strong>Status:</strong> <?= htmlspecialchars($pembayaran['status_pembayaran']); ?></p>
                            <p class="card-text"><strong>Tanggal Bayar:</strong> <?= htmlspecialchars($pembayaran['tanggal_bayar']); ?></p>
                            <p class="card-text"><strong>Jumlah Bayar:</strong> Rp<?= number_format($pembayaran['jumlah_bayar'], 0, ',', '.'); ?></p>
                            
                            <?php if (!empty($pembayaran['bukti_pembayaran'])): ?>
                                <a href="../assets/pembayaran/<?= htmlspecialchars($pembayaran['bukti_pembayaran']); ?>" class="btn btn-primary" target="_blank">Lihat Bukti</a>
                            <?php else: ?>
                                <span class="badge badge-warning">Belum Ada</span>
                            <?php endif; ?>

                            <?php if (trim($pembayaran['status_pembayaran']) === 'belum dibayar'): ?>
                                <a href="./controller/admin_dashboard_control.php?id=<?= htmlspecialchars($pembayaran['id_transaksi']); ?>&action=terima" class="btn btn-success btn-sm">Terima</a>
                                <a href="./controller/admin_dashboard_control.php?id=<?= htmlspecialchars($pembayaran['id_transaksi']); ?>&action=tolak" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menolak pembayaran ini?')">Tolak</a>
                            <?php else: ?>
                                <span class="badge badge-success">Pembayaran Sudah Diproses</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- End of Page Content -->

<?php include('../template/admin/footer.php'); ?>
