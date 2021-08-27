@extends('auth.layouts.app')

@section('title', 'Change password')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
	<div class="container-fluid">
		<div class="row">
			<h2>CHANGE PASSWORD</h2>
		</div>
		<div class="row">
			<form control="" class="form-group" action="{{ route('auth.change-password.update') }}" method="post">
				@CSRF
				<div class="row">
					<input type="number" name="old_password" id="username" class="form__input @error('old_password') is-invalid @enderror " placeholder="Current password" value="{{ old('old_password', '') }}">
					@error('old_password')
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