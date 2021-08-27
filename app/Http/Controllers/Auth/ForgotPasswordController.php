<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuthenticationService;
use App\Helpers\SecurityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPassowrdRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
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
        return view('auth.forgot-password');
    }

    public function forgotPassword(ForgotPassowrdRequest $request)
    {
        $email = $request->input('email');
        $passwordReset = $this->authenticationService->forgotPassword($email);
        if ($passwordReset != null) {
            $passwordReset->notify(new ForgotPasswordNotification($passwordReset));
            return view('auth.reset-password-notify-sending-email', [
                'email' => $email,
                'expired_at' => $passwordReset->expired_at,
            ]);
        }
        return back()->withErrors(['email' => 'Account doesn\'t exist']);
    }

    public function verifyForgotPassword(Request $request, User $user, $token)
    {
        if ($this->authenticationService->verifyForgotPassword($user, $token)) {
            return view('auth.reset-password', compact('user', 'token'));
        }
        return abort(400);
    }

    public function changePassword(ResetPasswordRequest $request, User $user, $token)
    {
        $newPassword = $request->input('password');
        $this->authenticationService->changePassword($user, $token);
        $this->securityService->log($request, $user->id, 'change-password');
        return view('auth.reset-password-complete');
    }
}
