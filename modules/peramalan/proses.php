<?php error_reporting(0);
	session_start();

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
				"12"=>"Desember");

		
			$query = mysqli_query($mysqli, "SELECT MONTHNAME(tanggal_keluar) as bulan, YEAR(tanggal_keluar) as tahun, jumlah_keluar FROM detail_keluar WHERE kd_barang = '$kd_barang' AND MONTH(tanggal_keluar) BETWEEN '$bln_awal' AND '$bln_akhir' GROUP BY MONTH(tanggal_keluar), YEAR(tanggal_keluar)") or die('Ada kesalahan pada query tampil Transaksi : '.mysqli_error($mysqli));
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
 		 								<td width='120' height='13' align='center' valign='middle'>$data[bulan]</td>
 		 								<td width='80' height='13' align='center' valign='middle'>$data[tahun]</td>
 		 								<td width='80' height='13' align='center' valign='middle'>$data[jumlah_keluar]</td>
 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'>$bobot</td>
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
	 		 								<td width='120' height='13' align='center' valign='middle' colspan='3'>Peramalan Bulan ".$namaBulan[$bulan_depans]." ".$tahun_depans."</td>
	 		 								<td style='padding-left:10px;' width='155' height='13' valign='middle'>".($output/($total_bobot+$bobot))."</td>
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
	}
	?>