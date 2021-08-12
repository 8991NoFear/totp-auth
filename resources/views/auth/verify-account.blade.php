<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
        }
    </style>
</head>
<body>
     <div class="row m-0">
         <div class="col-md-6 offset-md-3">
             <div class="d-flex justify-content-center align-items-center flex-column vh-100 text-center">
                <img src="{{ asset('/default-images/email_black_48dp.svg') }}" width="10%" alt="mail" srcset="" style="display: block;">
                <h2>Almost done!</h2>
                <p>A verification email has been sent to {{ $email }}</p>  
                <p>For improved security, you must verify your email address by <span class="fw-bold">{{ $expired_at }}</span> so that you can continue using our website. Please check your email and follow the link to activate your account</p>  
                <a href="#" class="btn btn-primary text-uppercase">resend email</a>
            </div>
         </div>
     </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>

