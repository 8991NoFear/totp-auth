@php
$user = $passwordReset->user;
$link = route('auth.forgot-password.verify-change-password', [$user->id, $passwordReset->token]);
@endphp
<p>Hello, <b>{{ $user->username }}</b></p>
<br />
<p>We have received your request to get your password at our TOTP-AUTH website. Now, Please reset your password by click the link below, this link will be expired for {{ config('authentication.reset_password.token_timeout', 10800)/3600 }} hours:</p>
<br />
<a href="{{ $link }}">{{ $link }}</a>