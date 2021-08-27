<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuthenticationService;
use App\Helpers\SecurityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Login2FARequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;

class LoginController extends Controller
{
    public $authenticationService;
    public $securityService;

    public function __construct()
    {
        $this->authenticationService = App::make(AuthenticationService::class);
        $this->securityService = App::make(SecurityService::class);
    }
    
    public function index(Request $request)
    {
        // if loggined before
        $user = $this->authenticationService->userOrNull();
        if ($user != null) {
            $levelLogin = $this->authenticationService->levelLogin();
            if ($user->secret_key == null) {
                if ($levelLogin == AuthenticationService::LEVEL_LOGIN_NORMAL) {
                    return redirect(route('account.security.index'));
                }
            } else if ($levelLogin == AuthenticationService::LEVEL_LOGIN_ADVANCED) {
                return redirect(route('account.security.index'));
            }
        }
        return view('auth.login');
    }

    public function index2FA(Request $request)
    {
        // if loggined before
        $user = $this->authenticationService->userOrNull();
        if ($user != null) {
            $levelLogin = $this->authenticationService->levelLogin();
            if ($user->secret_key == null) {
                if ($levelLogin == AuthenticationService::LEVEL_LOGIN_NORMAL) {
                    return redirect(route('account.security.index'));
                }
            } else if ($levelLogin == AuthenticationService::LEVEL_LOGIN_ADVANCED) {
                return redirect(route('account.security.index'));
            }
        }
        return view('auth.login2fa');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password', 'remember_me']);

        $user = $this->authenticationService->attempt($credentials['email'], $credentials['password']);
        if ($user != null) {
            if ($request->has('remember_me')) {
                $this->authenticationService->remember($user); // remember user
            }

            if ($user->secret_key != null) { // Return 2fa login page if user enabled 2fa 
                return redirect(route('auth.login.index2fa'));
            }

            // log
            $this->securityService->log($request, $user->id, 'login');
            return redirect(route('account.security.index'));
        }
        return back()
            ->withInput(['email' => $credentials['email']])
            ->withErrors(['email' => 'wrong username or password']);
    }

    public function login2fa(Login2FARequest $request)
    {
        $code = $request->input('code');
        $user = $this->authenticationService->attempt2FA($code);

        if ($user != null) {
            $this->securityService->log($request, $user->id, 'login'); // log
            return redirect(route('account.security.index'));
        }
        return back()->with('code-error', 'Wrong TOTP Code or wrong backup code');
    }
}