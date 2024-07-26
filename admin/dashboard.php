<?php include('../db/auto_load.php'); ?>
<?php include('../template/admin/header.php'); 

$current_file = basename($_SERVER['PHP_SELF']);
?>
<!-- End of Topbar -->

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
    </div>
    <div class="container">
        <div class="jumbotron p-1 mb-4 bg-light rounded-3">
            <div class="container py-5">
                <h1 class="display-4">Selamat Datang di Gratia Jaya Mulya</h1>
            </div>
        </div>
    </div>
    <?php include('../template/admin/footer.php'); ?>