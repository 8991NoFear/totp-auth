<p>Hello, <b>{{ $username }}</b></p>
<br />
<p>We are very happy that you have registered an account at our TOTP-AUTH website. Now, Please verify your account by click below link, this link will be expired for {{ config('authentication.reset_password.token_timeout')/3600 }} hours:</p>
<br />
<a href="{{ route('auth.register.verify', [$userId, $token]) }}">{{ route('auth.register.verify', [$userId, $token]) }}</a>