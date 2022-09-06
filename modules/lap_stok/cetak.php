<?php 
session_start();
ob_start();

// panggil koneksi database.php utk koneksi
require_once "../../config/database.php";
// panggil fungsi utk format tanggal
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
$query = mysqli_query($mysqli, "SELECT MONTHNAME(tanggal_keluar) as bulan, YEAR(tanggal_keluar) as tahun, sub_total FROM detail_keluar WHERE kd_barang = '$kd_barang' AND MONTH(tanggal_keluar) BETWEEN '$bln_awal' AND '$bln_akhir' GROUP BY MONTH(tanggal_keluar), YEAR(tanggal_keluar)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
$count = mysqli_num_rows($query);
 ?>

 <!-- bagian html yg akan konvert -->
 <html xmlns="http://www.w3.org/1999/xhtml">
 	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 		<title>LAPORAN STOK barang</title>
 		<link rel="stylesheet" type="text/css" href="../../assets/css/laporan.css" />
 	</head>
 	<body>
 		<div id="title">
 			LAPORAN STOK barang
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
 			<table idth="100%" border="0.3" cellpadding="0" cellspacing="0">
                <thead style="background:#e8ecee">
                    <tr class="tr-title">
                        <th height="20" align="center" valign="middle">NO.</th>
                        <th height="20" align="center" valign="middle">PERIODE</th>
                        <th height="20" align="center" valign="middle">PENJUALAN</th>
                    </tr>
                </thead>
               	<tbody>
               		<?php 
					  // jika ada data
					  if($count == 0) {
					  echo "<tr>
					  <td width='40' height='13' align='center' valign='middle'></td>
					  <td width='480' height='13' valign='middle'></td>
					  <td width='80' height='13' align='center' valign='middle'></td>
					  </tr>";

					}
					// jika data tdk ada
					else{
					if($_GET['metode'] == 'wma'){
					$bobot = 1;
					$peramalan = 0;
					$total_bobot = 0;
					while ($data=mysqli_fetch_assoc($query)){
               			while ($data = mysqli_fetch_assoc($query)) {
           					 $harga_beli = format_rupiah($data['harga_beli']);
            				$harga_jual = format_rupiah($data['harga_jual']);
            		// menampilkan isi tabel dari database ke tabel di aplikasi
            		echo "  <tr>
                        <td width='40' height='13' align='center' valign='middle'>$no</td>
						<td width='480' height='13' valign='middle'>$data[bulan] $data[tahun]</td>
						<td width='80' height='13' align='center' valign='middle'>$data[sub_total]</td>
                    </tr>";
					$peramalan += $data[sub_total]*$bobot;  
					$total_bobot += $bobot;
					$bobot++;
            		$no++;
       			 }
				 } else {
                  while ($rs=mysqli_fetch_assoc($query)){
                  // tampilkan isi tabel dr db ke tabel di app
                    echo "<tr>
                        <td width='40' height='13' align='center' valign='middle'>$no</td>
                        <td width='480' height='13' valign='middle'>$rs[bulan] $rs[tahun]</td>
                        <td width='80' height='13' align='center' valign='middle'>$rs[sub_total]</td>
                      </tr>";
                    // $peramalan += $data[sub_total]*$bobot; 
                    // $total_bobot += $bobot;
                    $x4[] = pow($bobot,4);
                    $x2y[] = pow($bobot,2)*$rs[sub_total];
                    $xy[] = $bobot*$rs[sub_total];
                    $jml_keluar[] = $rs[sub_total];
                    $x2[] = pow($bobot,2);
                    $x[] = $bobot;
                    $bobot++;
                    $no++;
                  }
                }

                $b = (array_sum($xy)/array_sum($x2));
                $c = (($count*array_sum($x2y))-(array_sum($x2)*array_sum($jml_keluar)))/(($count*array_sum($x4))-pow(array_sum($x2),4));
                $a = (array_sum($jml_keluar)-($c*array_sum($x2)))/$count;

                $y = $a + ($b*($count+1)) + ($c*pow(($count+1),2));

                echo "<tr>
                      <td width='120' height='13' align='center' valign='middle' colspan='2'>Peramalan Bulan ".ucfirst($namaBulan[$bulan_depan])." ".$tahun_depan."</td>
                      <td width='155' height='13' align='center' valign='middle'>".$y."</td>
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
 				fendiwidiyanto
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