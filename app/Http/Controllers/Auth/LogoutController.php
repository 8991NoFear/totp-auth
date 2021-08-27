<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuthenticationService;
use App\Helpers\SecurityService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LogoutController extends Controller
{
    public $authenticationService;
    public $securityService;

    public function __construct()
    {
        $this->authenticationService = App::make(AuthenticationService::class);
        $this->securityService = App::make(SecurityService::class);
    }

    public function logout(Request $request)
    {
        // logout
        $user = $this->authenticationService->userOrFail(); 
        $this->authenticationService->logout($user);
        
        // log activity
        $this->securityService->log($request, $user->id, 'logout');

        return redirect(route('auth.login.index'));
    }
}
