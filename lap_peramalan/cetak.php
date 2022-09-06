<?php error_reporting(0);
  session_start();
  ob_start();
  function pt($angka){
		$ret =  number_format($angka, 0);
		$ret = ereg_replace(",",".",$ret);
		return $ret;
	}

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
 				"01"=>"Januari",
				"02"=>"Februari",
				"03"=>"Maret",
				"04"=>"April",
				"05"=>"Mei",
				"06"=>"Juni",
				"07"=>"Juli",
				"08"=>"Agustus",
				"09"=>"September",
				"10"=>"Oktober",
				"11"=>"November",
				"12"=>"Desember",
				"1"=>"Januari",
				"2"=>"Februari",
				"3"=>"Maret",
				"4"=>"April",
				"5"=>"Mei",
				"6"=>"Juni",
				"7"=>"Juli",
				"8"=>"Agustus",
				"9"=>"September"
	);

  $no =1;
  $query = mysqli_query($mysqli, "SELECT MONTHNAME(tanggal_keluar) as bulan, YEAR(tanggal_keluar) as tahun, MONTH(tanggal_keluar) as bulan1, jumlah_keluar FROM detail_keluar WHERE kd_barang = '$kd_barang' AND MONTH(tanggal_keluar) BETWEEN '$bln_awal' AND '$bln_akhir' GROUP BY MONTH(tanggal_keluar), YEAR(tanggal_keluar)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
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
          MENGGUNAKAN METODE WEIGHTED MOVING AVARAGE
        </div>
    <?php 
      }else { ?>
        <div id="title">
          MENGGUNAKAN METODE KUADRATIK
        </div>
    <?php 
      }
     ?>

     <hr><br>


     <div id="isi">
      <table width="100%" border="0.3" cellpadding="0" cellspacing="0"> 
        <thead style="background: #e8ecee">
          <?php if($_GET['metode'] == 'wma'){ ?>
          <tr class="tr-title">
            <th width="20" align="center" valign="middle">No.</th>
            <th width="100" align="center" valign="middle">Bulan</th>
            <th width="75" align="center" valign="middle">Tahun</th>
	        <th width="150" align="center" valign="middle">Penjualan</th>
            <th width="55" align="center" valign="middle">Bobot</th>
            <th width="55" align="center" valign="middle">Rata-Rata</th>
          </tr>
        <?php } else { ?>
          <tr class="tr-title">
            <th width="20" align="center" valign="middle">No.</th>
            <th width="80" align="center" valign="middle">Periode</th>
            <th width="130" align="center" valign="middle">Penjualan (Y)</th>
            <th width="55" align="center" valign="middle">X</th>
            <th width="55" align="center" valign="middle">X<sup>2</sup></th>
            <th width="130" align="center" valign="middle">XY</th>
            <th width="130" align="center" valign="middle">X<sup>2</sup>Y</th>
            <th width="60" align="center" valign="middle">X<sup>4</sup></th>
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
				 $no=0;
				 $h=0;
				 $periode = 3;
				 for ($i=1;$i<=$periode;$i++) {
				  $bagi = $bagi + $i;	
				 }
				 
                while ($data=mysqli_fetch_assoc($query)){$no++;
     				$h++;
					$lim1=$h-1-$periode;
					
				if ($h<=$periode) { $hasil = 0;}
				 else 
				{
				$pred = mysqli_query($mysqli,"SELECT jumlah_keluar FROM detail_keluar WHERE kd_barang = '$kd_barang' AND MONTH(tanggal_keluar) BETWEEN '$bln_awal' AND '$bln_akhir' GROUP BY MONTH(tanggal_keluar), YEAR(tanggal_keluar) limit ".$lim1.",".$periode."") 					 or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
				$hasil=0; 
				$x=1;while($hit=mysqli_fetch_assoc($pred)){ 
				$hasil = $hasil + ($hit[jumlah_keluar]*$x);
				 $x++;
				}
			 }
								$sql = mysqli_query($mysqli," insert into temp VALUES ($data[bulan1],$data[tahun],$hasil/$bagi)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));	
					
                  // tampilkan isi tabel dr db ke tabel di app
                  echo "<tr>
                      <td height='13' align='right' valign='middle'>$no</td>
                      <td height='13' align='justify' valign='middle'>".$namaBulan[$data[bulan1]]."</td>
					  <td height='13' align='center' valign='middle'>".$data[tahun]."</td>
                      <td height='13' align='right' valign='middle'>".pt($data[jumlah_keluar])."</td>
                      <td height='13' align='right' valign='middle'>$bobot</td>
					  <td height='13' align='right' valign='middle'>".pt($hasil/$bagi)."</td>
                    </tr>";
                  				
 		 						$peramalan += $data['jumlah_keluar']*$bobot;	
 		 						$total_bobot += $bobot;
 		 						$bobot++;
								if ($bobot > $periode) $bobot = 1 ;
 		 						$max_bobot = $bobot;
                }
              				$awal = 0;
 		 					$jbul = 12;
							$bobot = 1;
 		 					for ($i=1; $i <= $jbul; $i++) { 
		 						$lim1=$h+$i-1-$periode;
								$hasil=0; 
								$nomor = $no+$i;
								$pred = mysqli_query($mysqli,"SELECT jumlah FROM temp limit ".$lim1.",".$periode."") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
								$x=1;while($hit=mysqli_fetch_assoc($pred)){ 
									 $hasil = $hasil + ($hit[jumlah]*$x);
									 $x++;
									}
								$tahun_depans = date('Y', strtotime('+'.($i).' MONTH', $timestamp));
								$bulan_depans = date('m', strtotime('+'.($i).' MONTH', $timestamp));
								
								$sql = mysqli_query($mysqli," insert into temp VALUES ($bulan_depans,$tahun_depans,$hasil/$bagi)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
	 		 					echo "<tr>
	 		 					<td height='13' align='right' valign='middle'>".$nomor."</td>
	 		 					<td height='13' align='justify' valign='middle'>".$namaBulan[$bulan_depans]."</td>

	 		 					<td height='13' align='center' valign='middle'>".$tahun_depans."</td>
								<td height='13' align='right' valign='middle'>-</td>
								<td height='13' align='right' valign='middle'>".$bobot."</td>
	 		 					<td height='13' align='right' valign='middle' colspan='2'>".pt($hasil/($bagi))."</td>
	 		 							</tr>";
	 		 					$bobot++;
								if ($bobot > $periode) $bobot = 1 ;
 		 						$max_bobot = $bobot;
	 		 				}
              } else {
              
              
                $nomor=0;if($count % 2 == 0){
					 $bobot2  = 1-$count;
					  $kurang = 2;
                  while ($rs=mysqli_fetch_assoc($query)){
					  $nomor++;
 		 			 
					       
                
                  // tampilkan isi tabel dr db ke tabel di app
                    echo "<tr>
                        <td height='13' align='right' valign='middle'>$nomor</td>
                        <td height='13' valign='middle' align=justify>".$namaBulan[$rs[bulan1]]." $rs[tahun]</td>
                        <td height='13' align='right' valign='middle'>".pt($rs[jumlah_keluar])."</td>
                        <td height='13' align='right' valign='middle'>".$bobot2."</td>
                        <td height='13' align='right' valign='middle'>".pt(pow($bobot2,2))."</td>
                        <td height='13' align='right' valign='middle'>".pt($bobot2*$rs['jumlah_keluar'])."</td>
                        <td height='13' align='right' valign='middle'>".pt(pow($bobot2,2)*$rs['jumlah_keluar'])."</td>
                        <td height='13' align='right' valign='middle'>".pt(pow($bobot2,4))."</td>
          
                      </tr>";
                    // $peramalan += $data[jumlah_keluar]*$bobot; 
                    // $total_bobot += $bobot;
                   $x4[] = pow($bobot2,4);
	 		 	   $x2y[] = pow($bobot2,2)*$rs['jumlah_keluar'];
	 		 	   $xy[] = $bobot2*$rs['jumlah_keluar'];
	 		 	   $jml_keluar[] = $rs['jumlah_keluar'];
	 		 	   $x2[] = pow($bobot2,2);
	 		 	   $x[] = $bobot2;
	 		 	   $bobot2 = $bobot2+$kurang;
                  }$bobot2 = $bobot2-2;
                } else {
					$bobot2  = 1-(($count2-1)/2+1);
							$kurang = 1;
                  while ($rs=mysqli_fetch_assoc($query)){
                   
                
                  // tampilkan isi tabel dr db ke tabel di app
                    echo "<tr>
                      <td height='13' align='right' valign='middle'>$nomor</td>
                        <td height='13' valign='middle' align=justify>".$namaBulan[$rs[bulan1]]." $rs[tahun]</td>
                        <td height='13' align='right' valign='middle'>".pt($rs[jumlah_keluar])."</td>
                        <td height='13' align='right' valign='middle'>".$bobot2."</td>
                        <td height='13' align='right' valign='middle'>".pt(pow($bobot2,2))."</td>
                        <td height='13' align='right' valign='middle'>".pt($bobot2*$rs['jumlah_keluar'])."</td>
                        <td height='13' align='right' valign='middle'>".pt(pow($bobot2,2)*$rs['jumlah_keluar'])."</td>
                        <td height='13' align='right' valign='middle'>".pt(pow($bobot2,4))."</td>
                      </tr>";
                    // $peramalan += $data[jumlah_keluar]*$bobot; 
                    // $total_bobot += $bobot;
                  	$x4[] = pow($bobot2,4);
	 		 						$x2y[] = pow($bobot2,2)*$rs['jumlah_keluar'];
	 		 						$xy[] = $bobot2*$rs['jumlah_keluar'];
	 		 						$jml_keluar[] = $rs['jumlah_keluar'];
	 		 						$x2[] = pow($bobot2,2);
	 		 						$x[] = $bobot2;
	 		 						$bobot2 = $bobot2+$kurang;
                  }$bobot2 = $bobot2-1;
                }

               $a = array_sum($jml_keluar)/$nomor;
			   $b = array_sum($xy)/array_sum($x2);
			   $c = (($nomor*array_sum($x2y))-(array_sum($x2)*array_sum($jml_keluar)))/($nomor*array_sum($x4))-pow(array_sum($x2),2);

                $y = $a + ($b*($count+1)) + ($c*pow(($count+1),2));
                $n21 = ((12*$tot5)-($tot4*$tot1))/((12*$tot6)-($tot4)*($tot4));
                $n22 = ($tot3/$tot4);
                $n23 = ($tot1-($n21*$tot4))/12;

                echo "<tr>
                      <td height='13' align='center' valign='middle' colspan='2'>Jumlah</td>
                      <td height='13' align='right' valign='middle'>".pt(array_sum($jml_keluar))."</td>
                      <td height='13' align='right' valign='middle'>".pt(array_sum($x))."</td>
                      <td height='13' align='right' valign='middle'>".pt(array_sum($x2))."</td>
                      <td height='13' align='right' valign='middle'>".pt(array_sum($xy))."</td>
                      <td height='13' align='right' valign='middle'>".pt(array_sum($x2y))."</td>
                      <td height='13' align='right' valign='middle'>".pt(array_sum($x4))."</td>
                     
                    </tr>
					
					<tr><td colspan='8' height='13' align='justify' valign='middle'>Nilai Persamaan Tren Kuadratik <br> a =  ".pt($a)." <br>
		b =  ".pt($b)." <br> c =  ".pt($c)." <br> Yt = ".pt($a)." + ".pt($b)." t + ".pt($c)." t<sup>2</sup></td> </tr>	";
		
		//$bobot2 = $bobot2-1;
							
 		 					$pertama = 0;
 		 					$jbul = 12;
 		 					for ($i=0; $i < $jbul; $i++) { 
 		 						if($nomor % 2 == 0)
									$bobot2 = $bobot2+2;
	 		 					else $bobot2 = $bobot2+1;
								
								$hasils = $a + ($b*$bobot2) + ($c * pow($bobot2,2)) ;

 		 						$tahun_depans = date('Y', strtotime('+'.($i+1).' MONTH', $timestamp));
								$bulan_depans = date('m', strtotime('+'.($i+1).' MONTH', $timestamp));
	 		 					echo "<tr>
			 								<td height='13' align='justify' valign='middle' colspan='6'>Peramalan Bulan ".$namaBulan[$bulan_depans]." ".$tahun_depans."</td>
			 								<td  height='13' align='right' valign='middle' colspan='2'>".pt($hasils)."</td>
		 							  </tr>";
		 						
		 					}
                  
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
        Widi
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
  $html2pdf = new HTML2PDF('P','A4','en', false, 'ISO-8859-15',array(10, 10, 10, 10));
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->Output($filename);
}
catch(HTML2PDF_exception $e) { echo $e; }
  ?>