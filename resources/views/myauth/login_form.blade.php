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
					<div class="login-form-heading">
						PRIMASAKTI <small>USER LOGIN</small>
					</div>
					<div id="login-form-body" style="background-color: white">
						<form method="POST" action="{{ URL::action('MyAuthController@login') }}">
							{!! csrf_field() !!}
							<div class="form-group">
								<label>E-mail</label>
								<input type="text" name="email" class="form-control">
							</div>
							<div class="form-group">
								<label>Password</label>
								<input type="password" name="password" class="form-control">
							</div>
							<div class="row">
								<div class="col-sm-6 text-left">
									<small>
										Belum punya account ?<br>
										<a href="{{ URL::action('MyAuthController@register_form') }}">Daftar disini</a>
									</small>
								</div>
								<div class="col-sm-6 text-right">
									<small>
										Lupa Password ?<br>
										<a href="{{ URL::action('MyAuthController@reset_password_form') }}">Reset Password</a>
									</small>
								</div>
							</div>
							<div class="form-group" style="margin-top: 20px">
								<button type="submit" class="btn btn-primary form-control">Login</button>
							</div>
						</form>
						<a href="{{ URL::action('PagesController@main') }}"><button class="btn btn-danger form-control"><< Back</button></a>
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
