<?php
    include '../koneksi.php';
    mysqli_query($koneksi,"DELETE FROM anggota_polisi WHERE nrp=".$_GET['nrp']);
    header("location:daftar_anggota.php");
?>