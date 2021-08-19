@extends('auth.layouts.app')

@section('title', 'Login')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
    <div class="container-fluid">
        <div class="row">
            <h2>LOGIN - TOTP</h2>
            <p class="text-center">Open google authentication app in your phone and submit TOTP code. If your phone is present, you can use backup code instead!</p>
        </div>
        <div class="row">
            <form control="" class="form-group" action="{{ route('auth.login.login2fa') }}" method="post">
                @CSRF
                <div class="row">
                    <input type="number" name="code" id="username" class="form__input @if(session()->has('code-error')) is-invalid @endif " placeholder="TOTP Code or Backup Code">
                    @if(session()->has('code-error'))
                    <div id="invalid">
                        {{ session()->get('code-error') }}
                    </div>
                    @endif
                </div>
                <div class="row">
                    <input type="submit" value="Submit" class="btn">
                </div>
            </form>
        </div>
        <div class="row">
            <p>Don't have an account? <a href="{{ route('auth.register.index') }}">Register Here</a></p>
            <p>Or want to change account? <a href="{{ route('auth.login.index') }}">Go back login</a></p>
        </div>
    </div>
</div>
@endsection