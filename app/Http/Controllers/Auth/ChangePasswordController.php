<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuthenticationService;
use App\Helpers\SecurityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class ChangePasswordController extends Controller
{
    public $authenticationService;
    public $securityService;

    public function __construct()
    {
        $this->authenticationService = App::make(AuthenticationService::class);
        $this->securityService = App::make(SecurityService::class);
    }

    public function index()
    {
        return view('auth.change-password');
    }

    public function update(ChangePasswordRequest $request)
    {
        $credentials = $request->only(['old_password', 'password', 'password_confirmation']);
        $user = $this->authenticationService->userOrNull();
        if (Hash::check($credentials['old_password'],  $user->password)) {
            // clear remember cookie
            Cookie::expire('remember_token');
            
            // update
            $user->update([
                'password' => Hash::make($credentials['password']),
                'remember_token' => null,
            ]);
            
            $this->securityService->log($request, $user->id, 'change-password'); // log

            return redirect(route('account.security.index'));
        }

        return back()
            ->withErrors(['old-password' => 'current password is wrong']);
    }
}
