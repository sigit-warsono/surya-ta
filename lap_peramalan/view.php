<!-- content header -->
<section class="content-header">
	<h1>
		<i class="fa fa-file-text-o icon-title"></i>Laporan Peramalan
	</h1>
	<ol class="breadcrumb">
		<li><a href="?module=beranda"><i class="fa fa-home"></i>Beranda</a></li>
		<li class="active">Laporan</li>
		<li class="active">Laporan Peramalan</li>
	</ol>
</section>

<!-- main content -->
<section class="content">
	<div class="row">
		<div class="col-md-12">
			
			<!-- form laporan -->
			<div class="box box-primary">
				<!-- form start -->
				<form role="form" class="form-horizontal" method="GET" action="modules/lap_peramalan/cetak.php" target="_blank">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-1">Bulan</label>
							<div class="col-sm-2">
								<input type="text" class="form-control date-picker-bulan" data-date-format="mm-yyyy" name="bln_awal" autocomplete="off" required>
							</div>

							<label class="col-sm-1">s.d.</label>
							<div class="col-sm-2">
								<input style="margin-left:-35px" type="text" class="form-control date-picker-bulan" data-date-format="mm-yyyy" name="bln_akhir" autocomplete="off" required>
							</div>
						</div>

						<div class="form-group">
						 	<label class="col-sm-1">Barang</label>
						 	<div class="col-sm-5">
						 		<select class="chosen-select" name="kd_barang" data-placeholder="--Pilih--" id="kd_barang">
						 			<option value=""></option>
						 			<?php 
						 				$query_barang = mysqli_query($mysqli, "SELECT kd_barang, nama_barang FROM tb_barang ORDER BY nama_barang ASC") or die('Ada kesalahan pada query tampil barang: '.mysqli_error($mysqli));

						 				while($data_barang = mysqli_fetch_assoc($query_barang)){
						 					echo"<option value=\"$data_barang[kd_barang]\">$data_barang[nama_barang]</option>";
						 				}
						 			 ?>
						 		</select>
						 	</div>
						</div>	

						<div class="form-group">
							<label class="col-sm-1">Peramalan</label>
							<div class="col-sm-5">
								<select class="chosen-select" name="metode" data-placeholder="--Pilih Metode--" autocomplete="off" required>
									<option value=""></option>
									<option value="wma">Weight Moving Avarage</option>
									<option value="kuadratik">Kuadratik</option>
								</select>
							</div>
						</div>		
					</div>

					<div class="box-footer">
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-11">
								<button type="submit" class="btn btn-primary btn-social btn-submit">Lihat
									<!-- <i class="fa fa-print"></i>Lihat -->
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
 	
	$(document).ready(function() {

		$(".date-picker-bulan").datepicker( {
	        format: "mm-yyyy",
	        viewMode: "months", 
	        minViewMode: "months"
	    });

	});

 </script>