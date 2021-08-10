<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Yinka Enoch Adedokun">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/auth/login.css">
	<title>Login Page</title>
</head>

<body>
	<!-- Main Content -->
	<div class="container-fluid">
		<div class="row main-content bg-success text-center">
			<div class="col-md-4 text-center company__info">
				<span class="company__logo">
					<h2><span class="fa fa-android"></span></h2>
				</span>
				<h4 class="company_title">Your Company Logo</h4>
			</div>
			<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
				<div class="container-fluid">
					<div class="row">
						<h2>Log In</h2>
					</div>
					<div class="row">
						<form control="" class="form-group" action="{{ route('auth.login') }}" method="post">
							@CSRF
							<div class="row">
								<input type="text" name="email" id="username" class="form__input @error('email') is-invalid @enderror " placeholder="Email" value="{{ old('email', '') }}">
								@error('email')
								<div id="invalid">
									{{ $message }}
								</div>
								@enderror
							</div>
							<div class="row">
								<input type="password" name="password" id="password" class="form__input @error('password') is-invalid @enderror" placeholder="Password">
								@error('password')
								<div id="invalid">
									{{ $message }}
								</div>
								@enderror
							</div>
							<div class="row">
								<input type="checkbox" name="remember_me" id="remember_me" class="">
								<label for="remember_me">Remember Me!</label>
							</div>
							<div class="row">
								<input type="submit" value="Submit" class="btn">
							</div>
						</form>
					</div>
					<div class="row">
						<p>Don't have an account? <a href="#">Register Here</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer -->
	<div class="container-fluid text-center footer">
		Coded with &hearts; by <a href="https://github.com/8991NoFear">BinhLD.</a></p>
	</div>
</body>