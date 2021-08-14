@extends('auth.layouts.app')

@section('title', 'Login')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
    <div class="container-fluid">
        <div class="row">
            <h2>LOGIN - TOTP</h2>
        </div>
        <div class="row">
            <form control="" class="form-group" action="{{ route('auth.login.login2fa') }}" method="post">
                @CSRF
                <div class="row">
                    <input type="number" name="totp_code" id="username" class="form__input @if(session()->has('totp-error')) is-invalid @endif " placeholder="6 digits TOTP Code">
                    @if(session()->has('totp-error'))
                    <div id="invalid">
                        {{ session()->get('totp-error') }}
                    </div>
                    @endif
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
@endsection