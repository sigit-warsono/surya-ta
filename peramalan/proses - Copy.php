<?php error_reporting(0);
	session_start();
	function pt($angka){
		$ret =  number_format($angka, 0);
		$ret = ereg_replace(",",".",$ret);
		return $ret;
	}
	// panggil koneksi database.php utk koneksi
	require_once "../../config/database.php";

	// query utk cek status login user
	// jika user blm login alihkan ke halaman login dan tampilkan pesan =1
	if(empty($_SESSION['username']) && empty ($_SESSION['password'])){
		echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
	}

	// jika user sdh login maka jalankan perintah utk insert ,update  dan delete
	else{
		
			// ambil data hasil submit dr form
			$bln_awal 	= mysqli_real_escape_string($mysqli, trim($_POST['bln_awal']));
			$bln_akhir 	= mysqli_real_escape_string($mysqli, trim($_POST['bln_akhir']));
			// $nama_supplier 	= mysqli_real_escape_string($mysqli, trim($_POST['nama_supplier']));
			$kd_barang	= mysqli_real_escape_string($mysqli, trim($_POST['kd_barang']));

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

		
			$query = mysqli_query($mysqli, "SELECT MONTHNAME(tanggal_keluar) as bulan,  MONTH(tanggal_keluar) as bulan1,   YEAR(tanggal_keluar) as tahun, jumlah_keluar FROM detail_keluar WHERE kd_barang = '$kd_barang' AND MONTH(tanggal_keluar) BETWEEN '$bln_awal' AND '$bln_akhir' GROUP BY MONTH(tanggal_keluar), YEAR(tanggal_keluar)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
			$count = mysqli_num_rows($query);
			?>
			<h3>
				<i class="fa fa-refresh icon-title"></i>Peramalan dengan Metode WMA
			</h3>
			<div class="box box-primary">
				<div class="box-body">
					<table id="table-keranjang" class="table table-bordered table-striped table-hover text-center">
						<!-- tampilan tabel header -->
						<thead>
							<tr>
								<th class="center">Bulan</th>
								<th class="center">Tahun</th>
								<th class="center">Penjualan</th>
								<th class="center">Bobot</th>
							</tr>
						</thead>

						<tbody>
 		 				<?php 
 		 				// jika ada data
 		 				if($count == 0) {
 		 					echo "<tr>
 		 							<td width='40' height='13' align='center' valign='middle'></td>
 		 							<td width='120' height='13' align='center' valign='middle'></td>
 		 							<td width='80' height='13' align='center' valign='middle'></td>
 		 							<td width='80' height='13' align='center' valign='middle'></td>
 		 						</tr>";

 		 				}
 		 				// jika data tdk ada
 		 				else{
 		 					// tampilkan data
 		 					$bobot = 1;
 		 					$peramalan = 0;
 		 					$total_bobot = 0;
 		 					while ($data=mysqli_fetch_assoc($query)){
 		 						// tampilkan isi tabel dr db ke tabel di app
 		 						echo "<tr>
 		 								<td width='120' height='13' align='center' valign='middle'><div align=justify>
										".$namaBulan[$data[bulan1]]."</div></td>
 		 								<td width='80' height='13' align='center' valign='middle'><div align=center>$data[tahun]</div></td>
 		 								<td width='80' height='13' align='center' valign='middle'><div align=right>".pt($data[jumlah_keluar])."</div></td>
 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>$bobot</div></td>
 		 							</tr>";
 		 						$peramalan += $data['jumlah_keluar']*$bobot;	
 		 						$total_bobot += $bobot;
 		 						$bobot++;

 		 						$max_bobot = $bobot;
 		 					}
 		 					$awal = 0;
 		 					$jbul = 12;
 		 					for ($i=0; $i < $jbul; $i++) { 
 		 						$output = $awal+$peramalan;
 		 						$peramalan[$i] = $i;
 		 						$bobot = $max_bobot+$i;
 		 						$tahun_depans = date('Y', strtotime('+'.($i+1).' MONTH', $timestamp));
								$bulan_depans = date('m', strtotime('+'.($i+1).' MONTH', $timestamp));
	 		 					echo "<tr>
	 		 								<td width='120' height='13' align='center' valign='middle' colspan='3'><div align=justify>Peramalan Bulan ".$namaBulan[$bulan_depans]." ".$tahun_depans."</div></td>
	 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt($output/($total_bobot+$bobot))."</div></td>
	 		 							</tr>";
	 		 					$awal = $peramalan;
	 		 					$peramalan = $output;
	 		 				}
 		 				}
 		 			 	?>
 		 				</tbody>
					</table>
				</div>
			</div>

			<?php
			$query2 = mysqli_query($mysqli, "SELECT MONTHNAME(tanggal_keluar) as bulan, MONTH(tanggal_keluar) as bulan1, YEAR(tanggal_keluar) as tahun, jumlah_keluar FROM detail_keluar WHERE kd_barang = '$kd_barang' AND MONTH(tanggal_keluar) BETWEEN '$bln_awal' AND '$bln_akhir' GROUP BY MONTH(tanggal_keluar), YEAR(tanggal_keluar)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));

			$count2 = mysqli_num_rows($query2);
			?>

			<h3>
				<i class="fa fa-refresh icon-title"></i>Peramalan dengan Metode Kuadratik
			</h3>
			<div class="box box-primary">
				<div class="box-body">
					<table id="table-keranjang" class="table table-bordered table-striped table-hover text-center">
						<!-- tampilan tabel header -->
						<thead>
							<tr>
								<th class="center">Periode</th>
								<th class="center">Penjualan (Y)</th>
								<th class="center">X</th>
								<th class="center">X<sup>2</sup></th>
								<th class="center">XY</th>
								<th class="center">X<sup>2</sup>Y</th>
								<th class="center">X<sup>4</sup></th>
							</tr>
						</thead>
						<tbody>
	 		 			<?php 
 		 				// jika ada data
 		 				if($count2 == 0) {
 		 					echo "<tr>
 		 							<td width='40' height='13' align='center' valign='middle'></td>
 		 							<td width='120' height='13' align='center' valign='middle'></td>
 		 							<td width='80' height='13' align='center' valign='middle'></td>
 		 							<td width='80' height='13' align='center' valign='middle'></td>
 		 							<td width='80' height='13' align='center' valign='middle'></td>
 		 							<td width='80' height='13' align='center' valign='middle'></td>
 		 							<td width='80' height='13' align='center' valign='middle'></td>
 		 						</tr>";

 		 				}
 		 				// jika data tdk ada
 		 				else{
 		 					// tampilkan data
 		 					
 		 					$means = $count2/2;
 		 					$bobot2 = -(floor($means));
 		 					
 		 					if($count2 % 2 == 0){
 		 						$nomor=0;while ($rs=mysqli_fetch_assoc($query2)){$nomor++;
 		 							if($bobot2==0){
	 		 							$bobot2 = $bobot2+1;
	 		 						} else {
	 		 							$bobot2 = $bobot2;
	 		 						}
 		 						// tampilkan isi tabel dr db ke tabel di app
	 		 						echo "<tr>
	 		 								<td width='120' height='13' align='justify' valign='middle'><div align=justify>".$namaBulan[$rs[bulan1]]." $rs[tahun]</div></td>
	 		 								<td width='80' height='13' align='right' valign='middle'><div align=right>".pt($rs[jumlah_keluar])."</div></td>
	 		 								<td width='80' height='13' align='right' valign='middle'><div align=right>".$bobot2."</td>
	 		 								<td style='padding-left:10px;' align='right' width='155' height='13' valign='middle'><div align=right>".pt(pow($bobot2,2))."</div></td>
	 		 								<td style='padding-left:10px;' align='right' width='155' height='13' valign='middle'><div align=right>".pt($bobot2*$rs['jumlah_keluar'])."</div></td>
	 		 								<td style='padding-left:10px;' align='right' width='155' height='13' valign='middle'><div align=right>".pt(pow($bobot2,2)*$rs['jumlah_keluar'])."</div></td>
	 		 								<td style='padding-left:10px;' align='right' width='155' height='13' valign='middle'><div align=right>".pt(pow($bobot2,4))."</div></td>
	 		 							</tr>";
	 		 						// $peramalan += $data[sub_total]*$bobot;	
	 		 						// $total_bobot += $bobot;
	 		 						$x4[] = pow($bobot2,4);
	 		 						$x2y[] = pow($bobot2,2)*$rs['jumlah_keluar'];
	 		 						$xy[] = $bobot2*$rs['jumlah_keluar'];
	 		 						$jml_keluar[] = $rs['jumlah_keluar'];
	 		 						$x2[] = pow($bobot2,2);
	 		 						$x[] = $bobot2;
	 		 						$bobot2++;
	 		 					}
	 		 				} else {
	 		 					$nomor=0;while ($rs=mysqli_fetch_assoc($query2)){$nomor++;
 		 						// tampilkan isi tabel dr db ke tabel di app
	 		 						echo "<tr>
	 		 								<td width='120' height='13' align='center' valign='middle'><div align=justify>".$namaBulan[$rs[bulan1]]." $rs[tahun]</div></td>
	 		 								<td width='80' height='13' align='center' valign='middle'><div align=right>".pt($rs[jumlah_keluar])."</div></td>
	 		 								<td width='80' height='13' align='center' valign='middle'><div align=right>".$bobot2."</div></td>
	 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt(pow($bobot2,2))."</div></td>
	 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt($bobot2*$rs['jumlah_keluar'])."</div></td>
	 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt(pow($bobot2,2)*$rs['jumlah_keluar'])."</div></td>
	 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt(pow($bobot2,4))."</div></td>
	 		 							</tr>";
	 		 						// $peramalan += $data[sub_total]*$bobot;	
	 		 						// $total_bobot += $bobot;
	 		 						$x4[] = pow($bobot2,4);
	 		 						$x2y[] = pow($bobot2,2)*$rs['jumlah_keluar'];
	 		 						$xy[] = $bobot2*$rs['jumlah_keluar'];
	 		 						$jml_keluar[] = $rs['jumlah_keluar'];
	 		 						$x2[] = pow($bobot2,2);
	 		 						$x[] = $bobot2;
	 		 						$bobot2++;
	 		 					}
	 		 				}


$a = array_sum($jml_keluar)/$nomor;
$b = array_sum($xy)/array_sum($x2);
$c = (($nomor*array_sum($x2y))-(array_sum($x2)*array_sum($jml_keluar)))/($nomor*array_sum($x4))-pow(array_sum($x2),2);
 		 					echo "<tr>
 		 								<td width='120' height='13' align='center' valign='middle'><div align=justify>Jumlah</div></td>
 		 								<td width='80' height='13' align='center' valign='middle'><div align=right>".pt(array_sum($jml_keluar))."</div></td>
 		 								<td width='80' height='13' align='center' valign='middle'><div align=right>".pt(array_sum($x))."</td>
 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt(array_sum($x2))."</div></td>
 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt(array_sum($xy))."</div></td>
 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt(array_sum($x2y))."</div></td>
 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt(array_sum($x4))."</div></td>
 		 							</tr>
									
		<tr><td colspan=7><div align=justify>Nilai Persamaan Tren Kuadratik <br> a =  ".pt($a)." <br>
		b =  ".pt($b)." <br> c =  ".pt($c)." <br> Yt = ".pt($a)." + ".pt($b)." t + ".pt($c)." t<sup>2</sup>
		
		 </div></td> </tr>							
									
									";


 		 					
	 		 				
							$bobot2 = $bobot2-1;
							
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
			 								<td width='120' height='13' align='center' valign='middle' colspan='6'><div align=justify>Peramalan Bulan ".$namaBulan[$bulan_depans]." ".$tahun_depans."</div></td>
			 								<td style='padding-left:10px;' width='155' height='13' valign='middle'><div align=right>".pt($hasils)."</div></td>
		 							  </tr>";
		 						
		 					}


 		 					// $timestamp = strtotime('01-'.$bln_akhir);

 		 					// $tahun_depan = date('Y', strtotime('+1 MONTH', $timestamp));
 		 					// $bulan_depan = date('m', strtotime('+1 MONTH', $timestamp));

 		 					// $namaBulan = array(
 		 					// 	"01"=>"Januari",
 		 					// 	"02"=>"Februari",
 		 					// 	"03"=>"Maret",
 		 					// 	"04"=>"April",
 		 					// 	"05"=>"Mei",
 		 					// 	"06"=>"Juni",
 		 					// 	"07"=>"Juli",
 		 					// 	"08"=>"Agustus",
 		 					// 	"09"=>"September",
 		 					// 	"10"=>"Oktober",
 		 					// 	"11"=>"November",
 		 					// 	"12"=>"Desember");
 		 					// echo "<tr>
 		 					// 			<td width='120' height='13' align='center' valign='middle' colspan='3'>Peramalan Bulan ".$namaBulan[$bulan_depan]." ".$tahun_depan."</td>
 		 					// 			<td style='padding-left:10px;' width='155' height='13' valign='middle'>".($peramalan/$total_bobot)."</td>
 		 					// 		</tr>";
 		 				}
 		 			 	?>
 		 				</tbody>
					</table>
				</div>
			</div>
	<?php	
	}
	?>






					