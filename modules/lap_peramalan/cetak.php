<?php error_reporting(0);
  session_start();
  ob_start();

  // panggil koneksi database.php utk koneksi db
  require_once "../../config/database.php";
  // panggil fungsi utk format tgl
  include "../../config/fungsi_tanggal.php";
  // panggil fungsi utk format rupiah
  include "../../config/fungsi_rupiah.php";

  $hari_ini = date("d-m-Y");

  // ambil data hasil submit dr form
  $bln_awal   = mysqli_real_escape_string($mysqli, trim($_GET['bln_awal']));
  $bln_akhir  = mysqli_real_escape_string($mysqli, trim($_GET['bln_akhir']));
  // $nama_supplier   = mysqli_real_escape_string($mysqli, trim($_GET['nama_supplier']));
  $kd_barang  = mysqli_real_escape_string($mysqli, trim($_GET['kd_barang']));

  $timestamp = strtotime('01-'.$bln_akhir);

  $tahun_depan = date('Y', strtotime('+1 MONTH', $timestamp));
  $bulan_depan = date('m', strtotime('+1 MONTH', $timestamp));

  $namaBulan = array(
    "01"=>"JANUARI",
    "02"=>"FEBRUARI",
    "03"=>"MARET",
    "04"=>"APRIL",
    "05"=>"MEI",
    "06"=>"JUNI",
    "07"=>"JULI",
    "08"=>"AGUSTUS",
    "09"=>"SEPTEMBER",
    "10"=>"OKTOBER",
    "11"=>"NOVEMBER",
    "12"=>"DESEMBER");

  $no =1;
  $query = mysqli_query($mysqli, "SELECT MONTHNAME(tanggal_keluar) as bulan, YEAR(tanggal_keluar) as tahun, jumlah_keluar FROM detail_keluar WHERE kd_barang = '$kd_barang' AND MONTH(tanggal_keluar) BETWEEN '$bln_awal' AND '$bln_akhir' GROUP BY MONTH(tanggal_keluar), YEAR(tanggal_keluar)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
  $count = mysqli_num_rows($query);
 ?>

<!-- bagian halaman html yg akan dikonvert -->
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>LAPORAN PERAMALAN PENJUALAN</title>
    <link rel="stylesheet" type="text/css" href="../../assets/css/laporan.css" />
</head>

<body>
    <div id="title">
        LAPORAN PERAMALAN PENJUALAN
    </div>

    <?php
      if($_GET['metode'] == 'wma'){ ?>
    <div id="title">
        MENGGUNAKAN METODE WEIGHT MOVING AVARAGE
    </div>
    <?php 
      }else { ?>
    <div id="title">
        MENGGUNAKAN METODE KUADRATIK
    </div>
    <?php 
      }
     ?>

    <div id="title-tanggal">BULAN <?php echo $namaBulan[$bulan_depan];?> <?php echo $tahun_depan;?></div>

    <hr><br>


    <div id="isi">
        <table width="100%" border="0.3" cellpadding="0" cellspacing="0">
            <thead style="background: #e8ecee">
                <?php if($_GET['metode'] == 'wma'){ ?>
                <tr class="tr-title">
                    <th width="50" align="center" valign="middle">NO.</th>
                    <th width="200" align="center" valign="middle">PERIODE</th>
                    <th width="150" align="center" valign="middle">PENJUALAN</th>
                    <th width="100" align="center" valign="middle">t1</th>
                </tr>
                <?php } else { ?>
                <tr class="tr-title">
                    <th width="3%" align="center" valign="middle">NO.</th>
                    <th width="12%" align="center" valign="middle">PERIODE</th>
                    <th width="15%" align="center" valign="middle">PENJUALAN</th>
                    <th width="8%" align="center" valign="middle">t1</th>
                    <th width="15%" align="center" valign="middle">y1-t1</th>
                    <th width="10%" align="center" valign="middle">t1 2</th>
                    <th width="15%" align="center" valign="middle">y1-t1 2</th>
                    <th width="10%" align="center" valign="middle">t1.4</th>
                    <th width="10%" align="center" valign="middle">LOG(Y1)</th>
                    <th width="10%" align="center" valign="middle">LOG(Y1)*t1</th>
                </tr>
                <?php } ?>
            </thead>
            <tbody>
                <?php 
            // jika ada data
            if($count == 0) {
              echo "<tr>
                  <td width='3%' height='13' align='center' valign='middle'></td>
                  <td width='12%' height='13' valign='middle'></td>
                  <td width='15%' height='13' align='center' valign='middle'></td>
                  <td width='8%' height='13' align='center' valign='middle'></td>
                  <td width='15%' height='13' align='center' valign='middle'></td>
                  <td width='10%' height='13' align='center' valign='middle'></td>
                  <td width='15%' height='13' align='center' valign='middle'></td>
                  <td width='10%' height='13' align='center' valign='middle'></td>
                  <td width='10%' height='13' align='center' valign='middle'></td>
                  <td width='10%' height='13' align='center' valign='middle'></td>
                </tr>";

            }
            // jika data tdk ada
            else{
              if($_GET['metode'] == 'wma'){
                $bobot = 1;
                $peramalan = 0;
                $total_bobot = 0;
				 $ramal1 = 0; $ramal2 = 0; $ramal3 = 0; $t1 = -11; $tot1 = 0; $tot2 = 0; $tot3 = 0; $tot4 = 0; $tot5 = 0; $tot6 = 0; $tot7 = 0; $tot8 = 0;
                while ($data=mysqli_fetch_assoc($query)){
                  if ($no == 1) {
                    $tt = -11;
                    $ttot = $data[jumlah_keluar]*$tt;

                  } elseif ($no == 2) {
                    $tt = -9;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 3) {
                    $tt = -7;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 4) {
                    $tt = -5;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 5) {
                    $tt = -3;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 6) {
                    $tt = -1;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 7) {
                    $tt = 1;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 8) {
                    $tt = 3;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 9) {
                    $tt = 5;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 10) {
                    $tt = 7;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 11) {
                    $tt = 9;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } elseif ($no == 12) {
                    $tt = 11;
                    $ttot = $data[jumlah_keluar]*$tt;
                  } else {
                    $tt = 0;
                    $ttot = $data[jumlah_keluar]*2;
                  }
                  $ttot = number_format($ttot);
                  $tt2 = number_format($tt*$tt);
                  $tpangkat = number_format($tt2*$tt2,2);
                  $tt3 = number_format($tt2*$data[jumlah_keluar]);
                  $logy = LOG($data[jumlah_keluar],10);
                 // $logy = number_format($logi);
                  $logt = number_format($logy*$tt,5);
                  $jkluar = number_format($data[jumlah_keluar]);
                  // tampilkan isi tabel dr db ke tabel di app
                  echo "<tr>
                      <td width='5%' height='13' align='center' valign='middle'>$no</td>
                      <td width='40%' height='13' valign='middle'>$data[bulan] $data[tahun]</td>
                      <td width='35%' height='13' align='center' valign='middle'>$jkluar</td>
                      <td width='20%' height='13' align='center' valign='middle'>$tt</td>
                    </tr>";
                  $peramalan += $data['jumlah_keluar']*$bobot;  
                  $total_bobot += $bobot;
                  
				  
          $tot1 += $data['jumlah_keluar'];
          $tot2 += $tt;
          $tot3 += $data[jumlah_keluar]*$tt;
          $tot4 += $tt2;
          $tot5 += $tt2*$data[jumlah_keluar];
          $tot6 += $tt2*$tt2;
          $tot7 += $logy;
          $tot8 += $logt;
				 
				  if ($no == 10) {
					$ramal1 += ($data['jumlah_keluar']*1);
                  }
				  if ($no == 11) {
					$ramal2 += ($data['jumlah_keluar']*2);
                  }
				  if ($no == 12) {
					$ramal3 += ($data['jumlah_keluar']*3);
                  }
					//$hasil_ramal = ($ramal1+$ramal2+$ramal3);
				$bobot++;
                  $no++;
                  $t1++;
                }
                $n21 = ((12*$tot5)-($tot4*$tot1))/((12*$tot6)-($tot4)*($tot4));
                $n22 = ($tot3/$tot4);
                $n23 = ($tot1-($n21*$tot4))/12;
                echo "<tr>
                      <td width='45%' height='13' align='center' valign='middle' colspan='2'>SIGMA</td>
                      <td width='35%' height='13' align='center' valign='middle'>".$tot1."</td>
                      <td width='20%' height='13' align='center' valign='middle'>".$tot2."</td>
                    </tr>";
              } else {
                $means = $count/2;
                $bobot = -(floor($means));
              
                if($count % 2 == 0){
                  while ($rs=mysqli_fetch_assoc($query)){
                    if ($no == 1) {
                    $tt = -11;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 2) {
                    $tt = -9;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 3) {
                    $tt = -7;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 4) {
                    $tt = -5;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 5) {
                    $tt = -3;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 6) {
                    $tt = -1;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 7) {
                    $tt = 1;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 8) {
                    $tt = 3;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 9) {
                    $tt = 5;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 10) {
                    $tt = 7;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 11) {
                    $tt = 9;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } elseif ($no == 12) {
                    $tt = 11;
                    $ttot = $rs[jumlah_keluar]*$tt;
                  } else {
                    $tt = 0;
                    $ttot = $rs[jumlah_keluar]*2;
                  }
                  $ttot = number_format($ttot);
                  $tt2 = number_format($tt*$tt);
                  $tpangkat = number_format($tt2*$tt2);
                  $tt3 = number_format($tt2*$rs[jumlah_keluar]);
                  $logy = LOG($rs[jumlah_keluar],10);
                  //$logy = number_format($logi,5);
                  $logt = number_format($logy*$tt,2);
                  $jkluar = number_format($rs[jumlah_keluar]);
                    if($bobot==0){
                      $bobot = $bobot+1;
                    } else {
                      $bobot = $bobot;
                    }
                  // tampilkan isi tabel dr db ke tabel di app
                    echo "<tr>
                        <td width='3%' height='13' align='center' valign='middle'>$no</td>
                        <td width='12%' height='13' valign='middle'>$rs[bulan] $rs[tahun]</td>
                        <td width='15%' height='13' align='center' valign='middle'>$jkluar</td>
                        <td width='8%' height='13' align='center' valign='middle'>$tt</td>
                      <td width='15%' height='13' align='center' valign='middle'>$ttot</td>
                        <td width='10%' height='13' align='center' valign='middle'>$tt2</td>
                        <td width='15%' height='13' align='center' valign='middle'>$tt3</td>
                        <td width='15%' height='13' align='center' valign='middle'>$tpangkat</td>
                        <td width='15%' height='13' align='center' valign='middle'>$logy</td>
                        <td width='15%' height='13' align='center' valign='middle'>$logt</td>
                      </tr>";
                    // $peramalan += $data[jumlah_keluar]*$bobot; 
                    // $total_bobot += $bobot;
                    $x4[] = pow($bobot,4);
                    $x2y[] = pow($bobot,2)*$rs['jumlah_keluar'];
                    $xy[] = $bobot*$rs['jumlah_keluar'];
                    $jml_keluar[] = $rs['jumlah_keluar'];
                    $x2[] = pow($bobot,2);
                    $x[] = $bobot;
                    $bobot++;
                    $no++;
                    $t1++;
                    $tot1 += $rs['jumlah_keluar'];
                    $tot2 += $tt;
                    $tot3 += $rs[jumlah_keluar]*$tt;
                    $tot4 += $tt2;
                    $tot5 += $tt2*$rs[jumlah_keluar];
                    $tot6 += $tt2*$tt2;
                    $tot7 += $logy;
                    $tot8 += $logt;
                  }
                } else {
                  while ($rs=mysqli_fetch_assoc($query)){
                    if ($no == 1) {
                    $tt = -11;
                    $ttot = $tt;
                  } elseif ($no == 2) {
                    $tt = -9;
                    $ttot = $tt;
                  } elseif ($no == 3) {
                    $tt = -7;
                    $ttot = $tt;
                  } elseif ($no == 4) {
                    $tt = -5;
                    $ttot = $tt;
                  } elseif ($no == 5) {
                    $tt = -3;
                    $ttot = $tt;
                  } elseif ($no == 6) {
                    $tt = -1;
                    $ttot = $tt;
                  } elseif ($no == 7) {
                    $tt = 1;
                    $ttot = $tt;
                  } elseif ($no == 8) {
                    $tt = 3;
                    $ttot = $tt;
                  } elseif ($no == 9) {
                    $tt = 5;
                    $ttot = $tt;
                  } elseif ($no == 10) {
                    $tt = 7;
                    $ttot = $tt;
                  } elseif ($no == 11) {
                    $tt = 9;
                    $ttot = $tt;
                  } elseif ($no == 12) {
                    $tt = 11;
                    $ttot = $tt;
                  } else {
                    $tt = 0;
                    $ttot = $tt;
                  }
                  $ttot = number_format($ttot);
                  $tt2 = number_format($tt*$tt);
                  $tpangkat = number_format($tt2*$tt2);
                  $tt3 = number_format($tt2*$rs[jumlah_keluar]);
                  $logy = LOG($rs[jumlah_keluar],10);
                  //$logy = number_format($logi,5);
                  $logt = number_format($logy*$tt,2);
                  $jkluar = number_format($rs[jumlah_keluar]);
                  // tampilkan isi tabel dr db ke tabel di app
                    echo "<tr>
                        <td width='40' height='13' align='center' valign='middle'>$no</td>
                        <td width='480' height='13' valign='middle'>$rs[bulan] $rs[tahun]</td>
                        <td width='80' height='13' align='center' valign='middle'>$tt</td>
                      <td width='15%' height='13' align='center' valign='middle'>$ttot</td>
                        <td width='15%' height='13' align='center' valign='middle'>$tt</td>
                      </tr>";
                    // $peramalan += $data[jumlah_keluar]*$bobot; 
                    // $total_bobot += $bobot;
                    $x4[] = pow($bobot,4);
                    $x2y[] = pow($bobot,2)*$rs['jumlah_keluar'];
                    $xy[] = $bobot*$rs['jumlah_keluar'];
                    $jml_keluar[] = $rs['jumlah_keluar'];
                    $x2[] = pow($bobot,2);
                    $x[] = $bobot;
                    $bobot++;
                    $no++;
                    $t1++;
                    $tot1 += $rs['jumlah_keluar'];
                    $tot2 += $tt;
                    $tot3 += $rs[jumlah_keluar]*$tt;
                    $tot4 += $tt2;
                    $tot5 += $tt2*$rs[jumlah_keluar];
                    $tot6 += $tt2*$tt2;
                    $tot7 += $logy;
                    $tot8 += $logt;
                  }
                }

                $b = (array_sum($xy)/array_sum($x2));
                $c = (($count*array_sum($x2y))-(array_sum($x2)*array_sum($jml_keluar)))/(($count*array_sum($x4))-pow(array_sum($x2),4));
                $a = (array_sum($jml_keluar)-($c*array_sum($x2)))/$count;

                $y = $a + ($b*($count+1)) + ($c*pow(($count+1),2));
                $n21 = ((12*$tot5)-($tot4*$tot1))/((12*$tot6)-($tot4)*($tot4));
                $n22 = ($tot3/$tot4);
                $n23 = ($tot1-($n21*$tot4))/12;

                echo "<tr>
                      <td width='15%' height='13' align='center' valign='middle' colspan='2'>SIGMA</td>
                      <td width='15%' height='13' align='center' valign='middle'>".$tot1."</td>
                      <td width='8%' height='13' align='center' valign='middle'>".$tot2."</td>
                      <td width='15%' height='13' align='center' valign='middle'>".$tot3."</td>
                      <td width='10%' height='13' align='center' valign='middle'>".$tot4."</td>
                      <td width='15%' height='13' align='center' valign='middle'>".$tot5."</td>
                      <td width='15%' height='13' align='center' valign='middle'>".$tot6."</td>
                      <td width='15%' height='13' align='center' valign='middle'>".$tot7."</td>
                      <td width='15%' height='13' align='center' valign='middle'>".$tot8."</td>
                    </tr>
                    <tr>
                      <td width='15%' height='13' align='center' valign='middle' colspan='2'>Peramalan Bulan ".ucfirst($namaBulan[$bulan_depan])." ".$tahun_depan."</td>
                      <td width='8%'>a.</td>
                      <td colspan='2' width='15%' height='13' align='center' valign='middle'>".number_format(((12*$tot5)-($tot4*$tot1))/((12*$tot6)-($tot4)*($tot4)),0,0,".")."</td>
                    </tr>
                    <tr>
                      <td width='15%' height='13' align='center' valign='middle' colspan='2'>Peramalan Bulan ".ucfirst($namaBulan[$bulan_depan])." ".$tahun_depan."</td>
                      <td width='8%'>b.</td>
                      <td colspan='2' width='15%' height='13' align='center' valign='middle'>".number_format(($tot3/$tot4),0,0,".")."</td>
                    </tr>

                    <tr>
                      <td width='15%' height='13' align='center' valign='middle' colspan='2'>Peramalan Bulan ".ucfirst($namaBulan[$bulan_depan])." ".$tahun_depan."</td>
                      <td width='8%'>c.</td>
                      <td colspan='2' width='15%' height='13' align='center' valign='middle'>".number_format(($tot1-($n21*$tot4))/12,0,0,".")."</td>
                    </tr>
                    <tr>
                      <td width='15%' height='13' align='center' valign='middle' colspan='2'>Peramalan Bulan ".ucfirst($namaBulan[$bulan_depan])." ".$tahun_depan."</td>
                      <td width='8%'>:</td>
                      <td colspan='2' width='15%' height='13' align='center' valign='middle'>".number_format(($n23+$n22*(-11)+$n21*((-11)*(-11))),0,0,".")."</td>
                    </tr>";
              }
            }
           ?>
            </tbody>
        </table>

        <div id="footer-tanggal">
            Semarang, <?php echo tgl_eng_to_ind("$hari_ini"); ?>
        </div>
        <div id="footer-jabatan">
            Pemilik
        </div>

        <div id="footer-nama">
            Surya
        </div>
    </div>
</body>

</html>
<?php 
$filename="LAPORAN PERAMALAN PENJUALAN.pdf";

$content = ob_get_clean();
$content = '<page style="font-familiy: freeserif">'.($content).'</page>';

// panggil library html2pdf
require_once('../../assets/plugins/html2pdf_v4.03/html2pdf.class.php');
try{
  $html2pdf = new HTML2PDF('P','F4','en', false, 'ISO-8859-15',array(10, 10, 10, 10));
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->Output($filename);
}
catch(HTML2PDF_exception $e) { echo $e; }
  ?>