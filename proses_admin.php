<?php
include 'koneksi.php';
$aksi = $_GET['aksi'];
if($aksi == "tambah"){
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $passwd = $_POST['password'];
    $repeatPasswd = $_POST['repeatPassword'];
    if($repeatPasswd == $passwd){
        mysqli_query($koneksi,"INSERT INTO admin VALUES('','$nama','$username','$passwd','$email')");
        header("location:login.php");
    }else{
        echo "Password tidak benar, mohon ulangi!";
        header("location:register.php");
    }

}
?>