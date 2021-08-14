<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use PragmaRX\Google2FA\Support\Constants as SupportConstants;
use PragmaRX\Google2FA\Google2FA;

class LoginController extends Controller
{
    public $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(SupportConstants::SHA1); // TRUNCATE(HMAC-SHA256(K, T)) instead of TRUNCATE(HMAC-SHA1(K, C))
    }
    
    public function index()
    {
        return view('auth.login');
    }

    public function index2FA()
    {
        return view('auth.login2fa');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password', 'remember_me']);
        $user = User::where('email', $credentials['email'])->first();
        if ($user != null) {
            if (Hash::check($credentials['password'],  $user->password)) {
                // Email&Password Login Success
                $request->session()->put('userId', $user->id); // Save user identifier into session
                $request->session()->regenerate(); // Regenerate session id

                // Return 2fa login page if user enabled 2fa 
                if ($user->secret_key != null) {
                    redirect(route('auth.login.index2fa'));
                }
                // else
                return redirect(route('account.dashboard.index'));
            }
        }
        return back()
            ->withInput(['email' => $credentials['email']])
            ->withErrors(['email' => 'wrong username or password']);
    }

    public function login2fa(HttpRequest $request)
    {
        // Validate Request Data
        $credentials = $request->only('totp_code');
        $validator = Validator::make($credentials, [
            'totp_code' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return back()->with('totp-error', 'TOTP code must be a number with 6 ditgits');
        }
        $totpCode = $credentials['totp_code'];

        // TOTP Check
        $userId = $request->session()->get('userId');
        $user = User::find($userId);
        if ($this->google2fa->verify($totpCode, $user->secret_key)) {
            $request->session()->put('user2FA', true);
            $request->session()->regenerate(); // Regenerate session id
            return redirect(route('account.dashboard.index'));
        }
        return back()->with('totp-error', 'Wrong TOTP Code');
    }
}