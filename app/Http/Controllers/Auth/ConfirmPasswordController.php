<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuthenticationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmPasswordRequest;

use Illuminate\Support\Facades\App;

class ConfirmPasswordController extends Controller
{
    public $authenticationService;

    public function __construct()
    {
        $this->authenticationService = App::make(AuthenticationService::class);
    }

    public function index()
    {
        return view('auth.confirm-password');
    }

    public function confirm(ConfirmPasswordRequest $request)
    {
        $password = $request->input('password');
        
        $user = $this->authenticationService->userOrFail();
        if ($this->authenticationService->confirmPassword($user, $password)) {
            return redirect()->intended(route('account.security.index'));
        }
        return back()->withErrors(['password' => 'Wrong password']);
    }
}
