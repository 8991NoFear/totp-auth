@extends('auth.layouts.app')

@section('title', 'Login')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
	<div class="container-fluid">
		<div class="row">
			<h2>LOGIN</h2>
		</div>
		<div class="row">
			<form control="" class="form-group" action="{{ route('auth.login.login') }}" method="post">
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
				<div class="row remember_me">
					<input type="checkbox" name="remember_me" id="remember_me" class="">
					<label for="remember_me">Remember Me!</label>
				</div>
				<div class="row">
					<input type="submit" value="Submit" class="btn">
				</div>
			</form>
		</div>
		<div class="row">
			<p>Don't have an account? <a href="{{ route('auth.register.index') }}">Register Here</a></p>
			<p>Forgot your password? <a href="{{ route('auth.forgot-password.index') }}">Reset Password Here</a></p>
		</div>
	</div>
</div>
@endsection