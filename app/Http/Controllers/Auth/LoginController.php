<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use PragmaRX\Google2FA\Support\Constants as SupportConstants;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

use App\Helpers\SecurityActivityLogger;

class LoginController extends Controller
{
    public $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(SupportConstants::SHA1); // TRUNCATE(HMAC-SHA256(K, T)) instead of TRUNCATE(HMAC-SHA1(K, C))
    }
    
    public function index(Request $request)
    {
        // if loggined before
        if ($request->session()->has('user.userId')) {
            $userId = $request->session()->get('user.userId');
            $user = User::find($userId);

            if ($user->secret_key == null) {
                if ($request->session()->has('user.loginedNormal')) {
                    return redirect(route('account.security.index'));
                }
            } else if ($request->session()->has('user.loginedAdvanced')) {
                return redirect(route('account.security.index'));
            }
        }
        return view('auth.login');
    }

    public function index2FA(Request $request)
    {
        // if loggined before
        if ($request->session()->has('user.userId')) {
            $userId = $request->session()->get('user.userId');
            $user = User::find($userId);

            if ($user->secret_key == null) {
                if ($request->session()->has('user.loginedNormal')) {
                    return redirect(route('account.security.index'));
                }
            } else if ($request->session()->has('user.loginedAdvanced')) {
                return redirect(route('account.security.index'));
            }
        }
        return view('auth.login2fa');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password', 'remember_me']);
        $user = User::where('email', $credentials['email'])->first();

        if ($user != null) {
            if (Hash::check($credentials['password'],  $user->password)) { // Email&Password Login
                if ($user->email_verified_at == null) {
                    return back()
                        ->withInput(['email' => $credentials['email']])
                        ->withErrors(['email' => 'email has not verified yet']);
                }
                
                if ($request->has('remember_me')) {
                    $rememberMeToken = bin2hex(openssl_random_pseudo_bytes(32));
                    $user->remember_token = $rememberMeToken;
                    $user->save();
                    $minutes = config('security.auth.remember_timeout', 129600);
                    Cookie::queue('remember_token', $rememberMeToken, $minutes);
                }

                $request->session()->put('user.userId', $user->id); // Save user id into session
                $request->session()->put('user.loginedNormal', true); // remember logined normal
                $request->session()->regenerate(); // Regenerate session id

                if ($user->secret_key != null) { // Return 2fa login page if user enabled 2fa 
                    redirect(route('auth.login.index2fa'));
                }
                
                // log
                $logger = App::make(SecurityActivityLogger::class);
                $description = config('security.strings.login');
                $securityActivity = $logger->getModelForSave($request, $user->id, $description);
                $securityActivity->save();
                return redirect(route('account.security.index'));
            }
        }
        return back()
            ->withInput(['email' => $credentials['email']])
            ->withErrors(['email' => 'wrong username or password']);
    }

    public function login2fa(Request $request)
    {
        // Validate Request Data
        $credentials = $request->only('code');
        $validator = Validator::make($credentials, [
            'code' => 'required|digits_between:6,8',
        ]);
        if ($validator->fails()) {
            return back()->with('code-error', 'The code must be a number between 6 and 8 ditgits');
        }
        $code = $credentials['code'];

        $userId = $request->session()->get('user.userId');
        $user = User::find($userId);

        // Google2FA Check || Backup Code Check
        $verifyResult = $this->google2fa->verify($code, $user->secret_key) || $this->verifyBackupCode($user, $code);
        if ($verifyResult) {
            $request->session()->put('user.loginedAdvance', true); // remember logined advance
            return redirect(route('account.security.index'));
        }

        return back()->with('code-error', 'Wrong TOTP Code or wrong backup code');
    }

    public function verifyBackupCode(User $user, $bc) {
        $backupCodes = $user->backupCodes->all();
        foreach ($backupCodes as $backupCode) {
            if ($bc == $backupCode->code) {
                if (strtotime($backupCode->expired_at) >= time()) {
                    $backupCode->used_at = date('Y-m-d H:i:s', time());
                    $backupCode->save();
                    return true;
                }
            }
        }
        return false;
    }
}