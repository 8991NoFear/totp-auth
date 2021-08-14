<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verification</title>

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Custom styles for this template -->
    <link href="{{ asset('/css/account/verify-setup2fa.css') }}" rel="stylesheet">
</head>

<body class="text-center">

    <div class="container-fluid m-0">
        <div class="row">
            <form action="{{ route('account.security.verify-setup2fa') }}" method="post">
                @CSRF
                <img class="mb-2 mt-2" width="20%" src="data:image/png;base64, {{ $qrcode }}" alt="qr-code" />
                <h2 class="mb-3 fw-normal">Almost Done!</h2>
                <div class="col-sm-4 offset-sm-4">
                    <p>Using Google2FA app to scan the QRCode, then submit generation TOTP code of that app</p>
                    <input type="number" class="my-form-control text-center w-75  @if(session()->has('totp-err')) invalid @endif" id="floatingInput" placeholder="6 digits OTP code"
                        name="totp_code">
                    @if(session()->has('totp-err'))
                    <div style="color: red">
                        *{{ session()->get('totp-err') }}
                    </div>
                    @endif
                    <button class="w-75 btn btn-lg btn-primary mt-4" type="submit">Submit</button>
                </div>
                <p class="mt-4 text-muted">&copy; 2017–2021</p>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>

</html>