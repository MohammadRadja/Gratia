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
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>
    <div class="container">
        <div class="jumbotron p-1 mb-4 bg-light rounded-3">
            <div class="container py-5">
                <h1 class="display-4">Selamat Datang di Gratia Jaya Mulya</h1>
            </div>
        </div>
    </div>
    <!-- Session Alert  -->
    <?php
    function displaySessionMessage($sessionKey, $alertType, $defaultMessage = '') {
        if (isset($_SESSION[$sessionKey])) { ?>
            <div class="container">
                <div class="alert alert-<?= $alertType ?> text-center">
                    <h1 class="display-8"><?= $_SESSION[$sessionKey] ?></h1>
                    <p class="lead"><?= $defaultMessage ?></p>
                </div>
            </div>
            <?php unset($_SESSION[$sessionKey]); // Hapus session setelah digunakan
        }
    }

    // Tampilkan pesan appointment
    displaySessionMessage('appointment_empty', 'danger');
    displaySessionMessage('appointment_exists', 'success');

    // Tampilkan pesan pembayaran
    displaySessionMessage('pembayaran_empty', 'danger');
    displaySessionMessage('pembayaran_exists', 'success');
    ?>

    <?php include('../template/pasien/dashboard_footer.php'); ?>