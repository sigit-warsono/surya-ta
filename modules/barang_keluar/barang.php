<?php 
session_start();

// panggil koneksi database.php utk koneksi
require_once "../../config/database.php";

if(isset($_POST['dataidbarang'])){
	$kd_barang = $_POST['dataidbarang'];

	// query untuk tampilkan data dr tabel pakaian
	$query = mysqli_query($mysqli, "SELECT kd_barang,nama_barang,stok FROM tb_barang WHERE kd_barang='$kd_barang'") or die('Ada kesalahan pada query tampil data barang: '.mysqli_error($mysqli));

	// tampilkan data 
	$data  = mysqli_fetch_assoc($query);
	$stok = $data['stok'];

	if($stok != ''){
		echo "<div class='form-group'>
				<label class='col-sm-2 control-label'>Stok</label>
					<div class='col-sm-5'>
						<div class='input-group'>
							<input type='text' class='form-control' id='stok' name='stok' value='$stok' readonly>
							
						</div>
					</div>
				</div>"; 
	}else{
		echo "<div class='form-group'>
				<label class='col-sm-2 control-label'>Stok</label>
					<div class='col-sm-5'>
						<div class='input-group'>
							<input type='text' class='form-control' id='stok' name='stok' value='Stok barang tidak ditemukan' readonly>
						
						</div>
					</div>
				</div>"; 
}
}
 ?>

