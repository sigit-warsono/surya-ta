<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>Login Aplikasi Peramalan Penjualan </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="Aplikasi Peramalan Penjualan Karet Vulkanisir">
    <meta name="author" content="Annisa FZ" />

    <!-- favicon -->
    <link rel="shortcut icon" href="assets/img/logo.png" />

    <!-- Bootstrap 3.3.2 -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="assets/plugins/font-awesome-4.6.3/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="assets/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="assets/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body class="login-page bg-login">
	<div class="login-box">
		<div style="color: #1e90ff" class="login-logo">
			<a href="index.php" style="font-size: 24px;"><b>SISTEM PERAMALAN PENJUALAN KARET VULKANISIR DI PT.BERNIKE SEMARANG DENGAN METODE WEIGHTED MOVING AVERAGE DAN KUADRATIK</b><br></a>
		</div>
		<?php 

			// fungsi utk tampilkan pesan
			// jika alert = "" kosong
			// tampilakn pesan
		if(empty($_GET['alert'])) {
			echo "";
		}
		// jika alert 1
		// tampilkan pesan gagal "username / password salah ,silahkan cek kembali"
		elseif($_GET['alert'] == 1) {
			echo "<div class='alert alert-danger alert-dismissable'>
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
				<h4> <i class='icon fa fa-times-circle'></i>Gagal Login!</h4>
				Username atau password salah, cek kembali username dan password Anda!.</div>";
			}
			// jika alert =2
			// tampilkan pesan sukse "anda berhasil logout"
			elseif($_GET['alert'] == 2) {
				echo "<div class='alert alert-success alert-dismissable'>
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
				<h4> <i class='icon fa fa-check-circle'></i>Sukses!</h4>
				Anda Berhasil Logout.</div>";
			}
		 ?>

		 <div class="login-box-body">
		 	<p class="login-box-msg"><i class="fa fa-user icon-title"></i>Silahkan Login</p> <br/>
		 	<form action="login_cek.php" method="POST">
		 		<div class="form-group has-feedback">
		 			<input type="text" class="form-control" name="username" placeholder="Username" autocomplete="off" required />
		 			<span class="glyphicon glyphicon-user form-control-feedback"></span>
		 		</div>

		 		<div class="form-group has-feedback">
		 			<input type="password" class="form-control" name="password" placeholder="Password" required />
		 			<span class="glyphicon glyphicon-lock form-control-feedback"></span>
		 		</div>
		 		<br/>
		 		<div class="row">
		 			<div class="col-xs-12">
		 				<input type="submit" class="btn btn-primary btn-lg btn-block btn-flat" name="login" value="Login" />
		 			</div>
		 		</div>
		 	</form>
		 </div>
	</div>
 <!-- jQuery 2.1.3 -->
    <script src="assets/plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

</body>
</html>