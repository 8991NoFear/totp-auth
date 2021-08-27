<p>Hello, <b>{{ $user->username }}</b></p>
<br />
<p>We are very happy that you have registered an account at our TOTP-AUTH website. Now, Please verify your account by click below link, this link will be expired for {{ config('authentication.reset_password.token_timeout', 10800)/3600 }} hours:</p>
<br />
<?php
    $verifiableLink = route('auth.register.verify', [$user->id, $user->passwordReset->token]);
?>
<a href="{{ $verifiableLink }}">{{ $verifiableLink }}</a>