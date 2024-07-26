<?php
$host = "localhost";
$username =  "root";
$password = "";
$database = "gratia";

$conn = new mysqli($host,$username,$password,$database);
if($conn -> connect_error){
    echo "Koneksi gagal".mysqli_connect_error();
    die;
}

?>