<?php

namespace App\Helpers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FA\Support\Constants;

class AuthenticationService {

    const LEVEL_LOGIN_NORMAL = 1;
    const LEVEL_LOGIN_ADVANCED = 2;

    public $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(Constants::SHA1); // TRUNCATE(HMAC-SHA256(K, T)) instead of TRUNCATE(HMAC-SHA1(K, C))
    }

    public function userOrFail()
    {
        $user = $this->userOrNull();
        if ($user == null) {
            return abort(500);
        }
        return $user;
    }

    public function userOrNull()
    {
        $user = null;
        if (session()->has('user.userId')) {
            $userId = session()->get('user.userId');
            $user = User::find($userId);
        }
        return $user;
    }

    /**
     * @param User
     * @return bool
     */
    public function logout(User $user)
    {
        // invalidate remember
        $res = $user->update(['remember_token' => null]);
        Cookie::expire('remember_token');
        
        // clear session
        session()->invalidate();
        return $res;
    }

    /**
     * @param User
     * @return bool
     */
    public function remember(User $user)
    {
        $tokenLength = config('security.remember_token.length', 32);
        $rememberMeToken = bin2hex(openssl_random_pseudo_bytes($tokenLength));
        $tokenTimeout = config('security.remember_token.timeout', 324000);
        $expiryDate = date('Y:m:d H:i:s', time() + $tokenTimeout);
        $res = $user->update([
            'remember_token' => $rememberMeToken,
            'remember_token_expired_at' => $expiryDate,
        ]);
        $minutes = config('security.auth.remember_timeout', 129600);
        Cookie::queue('remember_token', $rememberMeToken, $minutes);
        return $res;
    }

    
    /**
     * @return User
     */
    public function attempt($email, $password)
    {
        $user = User::where('email', $email)->first();
        if ($user != null) {
            if (Hash::check($password,  $user->password)) { // Email&Password Login
                session()->put('user.userId', $user->id); // Save user id into session
                session()->put('user.level_login', 1); // remember logined normal
                session()->regenerate(); // Regenerate session id
                return $user;
            }
        }
        return null;
    }
    
    /**
     * @param string
     * @return User
     */
    public function attempt2FA($code)
    {
        $user =  $this->userOrFail();
        // TOTP Check || Backup Code Check
        $verifyResult = $this->google2fa->verify($code, $user->secret_key) || $this->useBackupCode($user, $code);
        if ($verifyResult) {
            if (session()->has('user.userId') && session()->has('user.level_login')) {
                session()->put('user.level_login', 2); // remember logined advance
                return $user;
            }
        }
        return null;
    }
    
    /**
     * @return User
     */
    public function attemptRemember($rememberToken)
    {
        $user = User::where('remember_token', $rememberToken)->first();
        if ($user != null) {
            $now = date('Y:m:d H:i:s', time());
            if ($user->remember_token_expired_at >= $now) {
                session()->put('user.userId', $user->id);
                session()->put('user.level_login', self::LEVEL_LOGIN_NORMAL);
                return $user;
            }
        }
        return null;
    }

    /**
     * If has a matching backup code --> use it
     * @return bool
     */
    private function useBackupCode(User $user, $bc)
    {
        $backupCodes = $user->backupCodes->all();
        foreach ($backupCodes as $backupCode) {
            if ($bc == $backupCode->code) {
                if (strtotime($backupCode->expired_at) >= time()) {
                    $now = date('Y-m-d H:i:s', time());
                    return $backupCode->update(['used_at' => $now]);
                }
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function levelLogin()
    {
        if (session()->has('user.level_login')) {
            return session()->get('user.level_login');
        }
        return null;
    }

    /**
     * @return bool
     */
    public function confirmPassword(User $user, $password)
    {
        if (Hash::check($password, $user->password)) {
            $now = date("Y-m-d H:i:s", time());
            session()->put('user.password_confirmed_at', $now);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function checkConfirmedPassword()
    {
        if (session()->has('user.password_confirmed_at')) {
            $passwordConfirmedAt = strtotime(session()->get('user.password_confirmed_at'));
            $timeout = config('security.confirm_password.timeout', 10800);
            $now = date("Y-m-d H:i:s", time());
            if ($now <= ($passwordConfirmedAt + $timeout)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string
     */
    public function forgotPassword($email)
    {
        $passwordReset = PasswordReset::where('email', $email)->first();
        if ($passwordReset != null) {
            $token = bin2hex(openssl_random_pseudo_bytes(config('security.token.length', 32)));
            $expired_at = date("Y-m-d H:i:s", time() + config('security.password_reset.token_timeout', 10800));
            $passwordReset->update([
                'token' => $token,
                'expired_at' => $expired_at,
            ]);
            return $passwordReset;
        }
        return false;
    }

    public function verifyForgotPassword($user, $token)
    {
        $passwordReset = $user->passwordReset;
        if ($passwordReset == null) {
            abort(500);
        }
        $isValidTime = strtotime($passwordReset->expired_at) >= time();
        $isValidToken = ($token == $passwordReset->token);
        if ($isValidTime && $isValidToken) {
            $passwordReset->update([
                'expired_at' => date('Y:m:d H:i:s', time()),
            ]);
            return true;
        }
        return false;
    }
}