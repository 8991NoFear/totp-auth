<?php

namespace App\Http\Middleware;

use App\Helpers\AuthenticationService;
use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\App;

class AuthNormal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authenticationService = App::make(AuthenticationService::class);

        if ($authenticationService->levelLogin() == AuthenticationService::LEVEL_LOGIN_NORMAL) {
            return $next($request);
        }

        $rememberToken = $request->cookie('remember_token');
        if ($rememberToken != null) {
            $user = $authenticationService->attemptRemember($rememberToken);
            if ($user != null) {
                return $next($request);
            }
        }

        return redirect(route('auth.login.index'));
    }
}
