@extends('auth.layouts.app')

@section('title', 'Confirm password')

@section('right-pane')
<div class="col-md-8 col-xs-12 col-sm-12 login_form ">
    <div class="container-fluid">
        <div class="row">
            <h2>CONFIRM YOUR PASSWORD</h2>
            <p class="text-center">Please re-enter your password to do this action!</p>
        </div>
        <div class="row">
            <form control="" class="form-group" action="{{ route('auth.confirm-password') }}" method="post">
                @CSRF
                <div class="row">
                    <input type="password" name="password" id="username" class="form__input @error('password') is-invalid @enderror " placeholder="Password">
                    @error('password')
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