<?php
session_start();

if($_SESSION['status']!="login"){
	header("location:../login.php");
}

require_once '../vendor/autoload.php';

if(isset($_SESSION['anggota_polisi'])){
    $anggota_polisi = $_SESSION['anggota_polisi'];
}

$mpdf = new \Mpdf\Mpdf();

ob_start();
echo '
<!DOCTYPE html>
<head>
    <title>Cetak Jadwal</title>
</head>
<body>
    <h1 align="center">Jadwal Shift Anggota Kepolisian</h1>
    <h2 align="center">Polsek Magelang Tengah Kota Magelang</h2>
    <table>
        <tr>
            <td>Fitness<td>
            <td> : <td>
            <td>'.$anggota_polisi['fitness'].'</td>
        </tr>
        <tr>
            <td>Iterasi<td>
            <td> : <td>
            <td>'.$anggota_polisi['iterasi'].'</td>
        </tr>
        <tr>
            <td>Waktu Eksekusi<td>
            <td> : <td>
            <td>'.implode(" ",$anggota_polisi['exec']).'</td>
        </tr>
        <tr>
            <td>Kesesuaian<td>
            <td> : <td>
            <td>'.$anggota_polisi['akurasi'].' %</td>
        </tr>
    </table>
    <table align="center" border="1" cellpadding="10" cellspacing="0">
        <tr>
            <td align="center">Tgl</td>
            <td align="center">Shift</td>
            <td colspan="8" align="center">Daftar Anggota</td>
        </tr>';
    for ($i=0; $i < $anggota_polisi['jmlHari']*3; $i++) {
        echo "<tr>";
        if ($i % 3 == 0) {
            echo '<td align="center" rowspan="3">'. (($i/3)+1) .'</td>';
        }
        echo '<td align="center">' .(($i % 3) + 1). '</td>';
        for ($j=0; $j < 8; $j++) {
            if($anggota_polisi['kromosom'][$j]['bg'] != 'white'){
                $text_color = 'white';
            }else{
                $text_color = 'black';
            }
            //bgcolor="' .$anggota_polisi['kromosom'][$j]['bg']. '"
            echo '<td color="'.$text_color.'">' .$anggota_polisi['kromosom'][$j]['nama']. '</td>' ;
        }
        array_splice($anggota_polisi['kromosom'], 0, 8);
        echo '</tr>';
    }
echo '
    </table>
</body>
</html>
';
$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html);
$mpdf->Output('Jadwal-Shift-Kepolisian.pdf', \Mpdf\Output\Destination::INLINE);