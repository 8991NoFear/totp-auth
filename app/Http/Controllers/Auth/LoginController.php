<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;

class LoginController extends Controller
{
    public function index() {
        return view('auth.login');
    }

    public function index2FA() {
        return view('auth.login2fa');
    }

    public function login(LoginRequest $request) {
        $credentials = $request->only(['email', 'password', 'remember_me']);
        $user = User::where('email', $credentials['email'])->first();
        if ($user != null) {
            if (Hash::check($credentials['password'],  $user->password)) {
                // Email&Password Login Success
                // Save user identifier into session
                $request->session()->put('userId', $user->id);

                // Return 2fa login page if user enabled 2fa 
                // if ($user->secret_key != null) {
                //     return view('auth.login2fa');
                // }
                return redirect(route('auth.login.index2fa'));
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
            $err = 'TOTP code must be a number with 6 ditgits';
            return view('auth.login2fa', compact('err'));
        }

        // TOTP Check
        $user = User::find($request->session()->get('userId'));
        $google2fa = new Google2FA();
        if ($google2fa->verify($credentials['totp_code'], $user->secret_key)) {
            $request->session()->put('user2FA', true);
            return redirect(route('user.dashboard.index'));
        }
        $err = 'wrong TOTP code';
        return view('auth.login2fa', compact('err'));
    }
}