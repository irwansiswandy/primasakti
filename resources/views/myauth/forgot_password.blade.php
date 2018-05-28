<!DOCTYPE HTML>

<html>

<head>
	<title>
		Primasakti | Login System
	</title>
	<link href="/libraries/bootstrap/dist/css/bootstrap.css" rel="stylesheet"> <!-- BOOTSTRAP CSS -->
	<link href="/css/all.css" rel="stylesheet"> <!-- LOGIN-FORM CSS -->
	<link href="/css/sweetalert.css" rel="stylesheet"> <!-- SWEET-ALERT CSS -->
</head>

<body>

<div class="container-fluid">
	<div id="login-form">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div id="login-form-frame">
					<div id="login-form-body" style="background-color: white">
						<h4 class="text-center" style="margin-bottom: 30px">
							LUPA PASSWORD<br>
							<small>
								Untuk melakukan reset ulang password, silahkan masukkan alamat e-mail anda dibawah.<br>
								Kami akan mengirimkan link untuk memasukkan password baru.
							</small>
						</h4>
						<form method="POST" action="{{ URL::action('MyAuthController@login') }}">
							{!! csrf_field() !!}
							<div class="form-group">
								<label>E-mail</label>
								<input type="text" name="email" class="form-control">
							</div>
							<div class="form-group text-right" style="margin-top: 40px">
								<button type="submit" class="btn btn-success form-control">Reset Password</button>
							</div>
						</form>
						<a href="{{ URL::action('MyAuthController@login_form') }}">
							<button class="btn btn-danger form-control"><< Back</button>
						</a>
					</div>
				</div>
				<div id="validation">
					@include('includes.validation_errors')
				</div>
			</div>
		</div>
	</div>
</div>

@include('includes/flash')

</body>

</html>
