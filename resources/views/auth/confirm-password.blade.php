@extends('auth.layouts.app')

@section('title', 'Confirm password')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
    <div class="container-fluid">
        <div class="row">
            <h2>CONFIRM YOUR PASSWORD</h2>
        </div>
        <div class="row">
            <form control="" class="form-group" action="{{ route('auth.confirm-password') }}" method="post">
                @CSRF
                <div class="row">
                    <input type="password" name="password" id="username" class="form__input @if(session()->has('password-error')) is-invalid @endif " placeholder="Password">
                    @if(session()->has('password-error'))
                    <div id="invalid">
                        {{ session()->get('password-error') }}
                    </div>
                    @endif
                </div>
                <div class="row">
                    <input type="submit" value="Submit" class="btn">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection