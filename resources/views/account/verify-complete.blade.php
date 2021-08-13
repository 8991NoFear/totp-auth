<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
    </style>
</head>
<body>
    <div class="row m-0">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-center align-items-center flex-column vh-100 text-center">
                <img src="{{ asset('/default-images/task_alt_black_48dp.svg') }}" width="5%" alt="tick" srcset="">
                <h2>Thank you</h2>
                <p class="fs-4">You have verified your account</p>
                <p>Our website support Google Two Factor Authentication (Google 2FA), would you like to enable it now? Google 2FA will improve your account security but not require.</p>
                <div class="w-100 flex-row justify-content-around">
                    <a href="{{ route('auth.login.index') }}" class="btn btn-outline-secondary">NO, maybe later</a>
                    <a href="#" class="btn btn-primary">YES, set up it now</a>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>