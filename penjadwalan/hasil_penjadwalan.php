<?php 
session_start();
if($_SESSION['status']!="login"){
	header("location:../login.php");
}
if(isset($_SESSION['jml_hari'])){
  $jml_hari = $_SESSION['jml_hari'];
}
if(isset($_SESSION['ukuran_populasi'])){
  $ukuran_populasi = $_SESSION['ukuran_populasi'];
}
include 'GenetikaPenjadwalan.php';

$penjadwalan = new GenetikaPenjadwalan;
if (isset($_POST['ukuranPopulasi'])){
    $penjadwalan->setUkuranPopulasi($_POST['ukuranPopulasi']);
}
if (isset($_POST['jmlHari'])){
    $penjadwalan->setJmlHari($_POST['jmlHari']);
}else {
    $penjadwalan->setJmlHari($jml_hari);
}
if (isset($_POST['crossoverRate'])){
    $penjadwalan->setCrossoverRate($_POST['crossoverRate']);
}
if (isset($_POST['mutationRate'])){
    $penjadwalan->setMutationRate($_POST['mutationRate']);
}
if (isset($_POST['maksimalGen'])){
    $penjadwalan->setMaksimalGen($_POST['maksimalGen']);
}


$nama_anggota = $penjadwalan->prosesPenjadwalanGA();
$_SESSION['anggota_polisi'] = $nama_anggota;

// echo "<pre>";
// print_r($nama_anggota);
// echo "</pre>";
// print_r(array_keys($nama_anggota));
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Administrator</title>

  <!-- Custom fonts for this template-->
  <link href="../assets/fontawesome-free/css/all.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="../assets/custom-css/sb-admin-2.min.css" rel="stylesheet">
  <link href="../assets/custom-css/custom.css" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon">
          <img class="img-logo" src="../assets/img/LOGO POLDA JAWA TENGAH.png" alt="polsek">
        </div>
        <div class="sidebar-brand-text mx-3">Polsek</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="../index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-building"></i>
          <span>Kepolisian</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="../kepolisian/daftar_anggota.php">Daftar Anggota</a>
            <a class="collapse-item" href="../kepolisian/daftar_pangkat.php">Pangkat</a>
            <a class="collapse-item" href="../kepolisian/daftar_jabatan.php">Jabatan</a>
          </div>
        </div>
      </li>

      <!-- Penjadwalan -->
      <li class="nav-item active">
        <a class="nav-link" href="../penjadwalan/penjadwalan.php">
          <i class="fas fa-fw fa-table"></i>
          <span>Penjadwalan</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['username']; ?></span>
                <img class="img-profile rounded-circle" src="../assets/img/LOGO POLDA JAWA TENGAH.png">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->

          <!-- Form-->
          <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Penjadwalan</h6>
        </div>
        <div class="card-body">
        <?php

        echo '<table>';
        echo '<tr>
              <td>Populasi<td>
              <td> : <td>
              <td>'.$penjadwalan->getUkuranPopulasi().'<td>
        </tr>';
        echo '<tr>
              <td>Jumlah Hari<td>
              <td> : <td>
              <td>'.$penjadwalan->getJmlHari().'<td>
        </tr>';
        echo '<tr>
              <td>Mutation Rate<td>
              <td> : <td>
              <td>'.$penjadwalan->getMutationRate().'<td>
        </tr>';
        echo '<tr>
              <td>Max Generasi<td>
              <td> : <td>
              <td>'.$penjadwalan->getMaksimalGen().'<td>
        </tr>';
        echo '<tr>
        <td colspan="3">&nbsp<td>
        </tr>';
        echo '<tr>
              <td>Iterasi<td>
              <td> : <td>
              <td>'.$nama_anggota['iterasi'].'<td>
        </tr>';
        echo '<tr>
              <td>Fitness<td>
              <td> : <td>
              <td>'.$nama_anggota['fitness'].'<td>
        </tr>';
        echo '<tr>
              <td>Waktu Eksekusi<td>
              <td> : <td>
              <td>'.implode(" ",$nama_anggota['exec']).'<td>
        </tr>';
        echo '<tr>
              <td>Kesesuaian<td>
              <td> : <td>
              <td>'.$nama_anggota['akurasi'].' %<td>
        </tr>';
        echo '<table>';
        ?>
        <a href="cetak.php" target="_blank" class="btn btn-success btn-icon-split float-right">
            <span class="icon text-white-50">
              <i class="fa fa-print"></i>
            </span>
            <span class="text">Print Jadwal</span>
        </a>
        <button type="button" data-toggle="modal" data-target="#generateModal" target="_blank" class="btn btn-primary btn-icon-split float-right mr-3">
            <span class="icon text-white-50">
              <i class="fa fa-sync"></i>
            </span>
            <span class="text">Generate Ulang</span>
        </button>
        <div class="table-responsive">
            <?php
                echo '<table class="table table-bordered mt-3" align="center" cellspacing="0" cellpadding="2">';
                echo '<tr>
                    <td align="center">Tgl</td>
                    <td align="center">Shift</td>
                    <td colspan="8" align="center">Daftar Anggota</td>
                </tr>';
                for ($i=0; $i < ($penjadwalan->getJmlHari()*3); $i++) {
                    if ($i % 3 == 0) {
                        echo '<tr><td align="center" rowspan="3">'. (($i/3)+1) .'</td>';
                    }
                    echo '<td align="center">' .(($i % 3) + 1). '</td>';
                    //$bg_color = "";
                    for ($j=0; $j < 8; $j++) {
                        echo '<td bgcolor="' .$nama_anggota['kromosom'][$j]['bg']. '">' .$nama_anggota['kromosom'][$j]['nama']. '</td>' ;
                    }
                    array_splice($nama_anggota['kromosom'], 0, 8);
                    echo '</tr>';
                }
                echo '</table>';
            ?>
        </div>
        </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Polsek Magelang Tengah 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Apakah anda ingin Logout?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
          </button>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="../logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="generateModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="generateModalLabel">Generate Ulang Jadwal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Generate Ulang -->
        <form action="hasil_penjadwalan.php" method="post">
            <input type="hidden" name="ukuranPopulasi" value="-1">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Mutation Rate</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="mutationRate" placeholder="" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Maksimal Generasi</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="maksimalGen" placeholder="" required>
                </div>
            </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-primary btn-user btn-block col-sm-3" value="Generate">
        </form>
      </div>
    </div>
  </div>
</div>

  <!-- Bootstrap core JavaScript-->
  <script src="../assets/jquery/jquery.min.js"></script>
  <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <!-- <script src="../assets/jquery-easing/jquery.easing.min.js"></script> -->

  <!-- Custom scripts for all pages-->
  <script src="../assets/js/sb-admin-2.min.js"></script>

</body>

</html>
