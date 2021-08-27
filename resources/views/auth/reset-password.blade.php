@extends('auth.layouts.app')

@section('title', 'Reset password')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
	<div class="container-fluid">
		<div class="row">
			<h2>RESET PASSWORD</h2>
			<p>Email: {{ $user->email }}</p>
		</div>
		<div class="row">
			<form control="" class="form-group" action="{{ route('auth.forgot-password.change-password', ['user' => $user->id, 'token' => $token]) }}" method="post">
				@CSRF
				<div class="row">
					<input type="password" name="password" id="password" class="form__input @error('password') is-invalid @enderror" placeholder="New password">
					@error('password')
					<div id="invalid">
						{{ $message }}
					</div>
					@enderror
				</div>
                <div class="row">
					<input type="password" name="password_confirmation" id="password" class="form__input" placeholder="Confirm new password">
				</div>
				<div class="row">
					<input type="submit" value="Submit" class="btn">
				</div>
			</form>
		</div>
	</div>
</div>
@endsection