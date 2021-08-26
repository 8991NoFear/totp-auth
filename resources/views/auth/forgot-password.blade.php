@extends('auth.layouts.app')

@section('title', 'Forgot password')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
    <div class="container-fluid">
        <div class="row">
            <h2>ENTER YOUR EMAIL</h2>
            <p class="text-center">Please enter your email to reset password!</p>
        </div>
        <div class="row">
            <form control="" class="form-group" action="{{ route('auth.forgot-password.request-change-password') }}" method="post">
                @CSRF
                <div class="row">
                    <input type="email" name="email" id="username" class="form__input @error('email') is-invalid @enderror " placeholder="Email">
                    @error('email')
                    <div id="invalid">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="row">
                    <input type="submit" value="Submit" class="btn">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection