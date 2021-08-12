@extends('auth.layouts.app')

@section('title', 'Register')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
	<div class="container-fluid">
		<div class="row">
			<h2>REGISTER</h2>
		</div>
		<div class="row">
			<form control="" class="form-group" action="{{ route('auth.register.register') }}" method="post">
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
					<input type="password" name="password_confirmation" id="password" class="form__input" placeholder="Re-Password">
				</div>
				<div class="row">
					<input type="submit" value="Submit" class="btn">
				</div>
			</form>
		</div>
		<div class="row">
			<p>Already have an account? <a href="{{ route('auth.login.index') }}">Login Here</a></p>
		</div>
	</div>
</div>
@endsection