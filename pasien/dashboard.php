<?php include('../db/auto_load.php'); ?>
<?php include('./controller/pasien_dashboard_control.php') ?>
<?php include('../template/pasien/dashboard_header.php'); ?>
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
    <!-- Session Appointment  -->
    <?php
    if (isset($_SESSION['appointment_empty'])) { ?>
        <div class="container">
            <div class="alert alert-danger">
                <h1 class="display-4"><?= $_SESSION['appointment_empty'] ?></h1>
                <p class="lead"></p>
            </div>
        </div>
        <?php unset($_SESSION['appointment_empty']); // Hapus session setelah digunakan ?>
    <?php }  
    if (isset($_SESSION['appointment_exists'])) { ?>
        <div class="container">
            <div class="alert alert-success">
                <h1 class="display-4"><?= $_SESSION['appointment_exists'] ?></h1>
                <p class="lead">Silahkan menghubungi kontak</p>
            </div>
        </div>
        <?php unset($_SESSION['appointment_exists']); // Hapus session setelah digunakan ?>
    <?php }
    ?>
    <!-- Session Pembayaran  -->
    <?php
    if (isset($_SESSION['belum dibayar'])) { ?>
        <div class="container">
            <div class="alert alert-danger">
                <h1 class="display-4"><?= $_SESSION['belum dibayar'] ?></h1>
                <p class="lead"></p>
            </div>
        </div>
        <?php unset($_SESSION['belum dibayar']); // Hapus session setelah digunakan ?>
    <?php }  
    if (isset($_SESSION['dibayar'])) { ?>
        <div class="container">
            <div class="alert alert-success">
                <h1 class="display-4"><?= $_SESSION['dibayar'] ?></h1>
                <p class="lead">Silahkan menghubungi kontak</p>
            </div>
        </div>
        <?php unset($_SESSION['dibayar']); // Hapus session setelah digunakan ?>
    <?php }
?>

    <?php include('../template/pasien/dashboard_footer.php'); ?>