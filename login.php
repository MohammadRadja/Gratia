<?php include('template/pasien/header.php'); ?>
<div class="login">
    <div class="container">
        <?php
        // Menampilkan pesan registrasi sukses
        if (isset($_SESSION['pesan_regisB'])) { ?>
            <div class="alert alert-success">
                <?= $_SESSION['pesan_regisB'] ?>
            </div>
        <?php 
            unset($_SESSION['pesan_regisB']); // Menghapus pesan setelah ditampilkan
        }

        // Menampilkan pesan login error
        if (isset($_SESSION['login_error'])) { ?>
            <div class="alert alert-danger">
                <?= $_SESSION['login_error'] ?>
            </div>
        <?php 
            unset($_SESSION['login_error']); // Menghapus pesan setelah ditampilkan
        }

        // Menampilkan pesan logout
        if (isset($_SESSION['logout'])) { ?>
            <div class="alert alert-danger">
                <?= $_SESSION['logout'] ?>
            </div>
        <?php 
            unset($_SESSION['logout']); // Menghapus pesan setelah ditampilkan
        }
        ?>
     
        <form class="user" action="core/login_control.php" method="POST">
            <h1>LOGIN</h1>
            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required>
            <div></div>
            <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Masukkan Password" required>
            <div class="mb-1"></div>
            <button type="submit" value="login" name="btn_login" class="btn btn-primary btn-submit">Masuk</button>
            <a class="btn btn-primary btn-submit" href="pendaftaran.php">Registrasi akun</a>
        </form> 
    </div>
</div>
<?php include('template/pasien/footer.php'); ?>
