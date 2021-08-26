@extends('account.layouts.2fa')

@section('title', 'Google 2FA')

@section('body')
<div class="container-fluid m-0">
    <div class="row">
        <div class="col-sm-6 offset-sm-3 mt-3">
            <div class="d-flex flex-row justify-content-between align-items-center border rounded-3 p-3">
                @if ($user->secret_key != null)
                <p class="p-0 m-0">Google2FA has turned on in 23 Feb, 2018</p>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal"
                    id="toggleBtn" data="off">TURN OFF</button>
                @else
                <p class="p-0 m-0">Turn on Google2FA to protect your account</p>
                <a href="#totp-code" class="btn btn-primary">TURN ON</a>
                @endif
            </div>
        </div>
        <form action="{{ route('account.security.setup-g2fa') }}" method="post">
            @CSRF
            <img class="mb-2 mt-2" width="20%" src="data:image/png;base64, {{ $qrcode }}" alt="qr-code" />
            <h2 class="mb-3 fw-normal">Almost Done!</h2>
            <div class="col-sm-6 offset-sm-3">
                <p class="ms-3 me-3 text-start">If you want to turn on Google2FA or re-enable Google2FA. Please using
                    Google2FA app to scan the QRCode, then submit generation TOTP code of that app to confirm.</p>
                <p class="ms-3 me-3 text-start">Notice that if you submit totp code succeed, we will create
                    {{ config('security.backup_codes.quantity', 10) }} new backup codes for you. Please refer to Account
                    > Security > Backup codes to see them.</p>
                <input type="number" id="totp-code"
                    class="my-form-control text-center w-75  @if(session()->has('totp-err')) invalid @endif"
                    id="floatingInput" placeholder="6 digits OTP code" name="totp_code">
                @if(session()->has('totp-err'))
                <div style="color: red">
                    *{{ session()->get('totp-err') }}
                </div>
                @endif
                <a href="{{ route('account.security.index') }}"
                    class="btn btn-lg btn-outline-secondary m-2 mt-4 w-25">Cancel</a>
                <button class="btn btn-lg btn-primary m-2 mt-4 w-25" type="submit">Submit</button>
            </div>
            <p class="mt-4 text-muted">&copy; 2017–2021</p>
        </form>
    </div>
</div>

@if ($user->secret_key != null)
<!-- Modal -->
<div class="modal fade text-start" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Turn off Google2FA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure?
            </div>
            <div class="modal-footer">
                <form action="{{ route('account.security.turn-off-g2fa') }}" method="post">
                    @CSRF
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, I don't</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Yes, I do</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@section('body')
