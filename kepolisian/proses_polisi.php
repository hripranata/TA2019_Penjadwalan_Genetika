<?php
include '../koneksi.php';
$aksi = $_GET['aksi'];
if($aksi == "tambah_pangkat"){
    $pangkat = $_POST['pangkat'];
    mysqli_query($koneksi,"INSERT INTO pangkat VALUES('','$pangkat')");
    header("location:daftar_pangkat.php");
}
if($aksi == "tambah_jabatan"){
    $jabatan = $_POST['jabatan'];
    mysqli_query($koneksi,"INSERT INTO jabatan VALUES('','$jabatan')");
    header("location:daftar_jabatan.php");
}
if($aksi == "tambah_anggota"){
    $nrp = $_POST['nrp'];
    $nama = $_POST['nama'];
    $gender = $_POST['radio'];
    $pangkat = $_POST['pangkat'];
    $jabatan = $_POST['jabatan'];
    $telp = $_POST['telp'];
    mysqli_query($koneksi,"INSERT INTO anggota_polisi VALUES('$nrp','$nama','$gender','$pangkat','$jabatan','$telp')");
    header("location:daftar_anggota.php");
}
if ($aksi == "edit_anggota") {
    $nrp = $_POST['nrp'];
    $nama = $_POST['nama'];
    $gender = $_POST['radio'];
    $pangkat = $_POST['pangkat'];
    $jabatan = $_POST['jabatan'];
    $telp = $_POST['telp'];
    mysqli_query($koneksi,"UPDATE anggota_polisi SET nrp='$nrp', nama_anggota='$nama', gender='$gender', id_pangkat='$pangkat', id_jabatan='$jabatan', telp='$telp' WHERE nrp=$nrp");
    header("location:daftar_anggota.php");
}
?>