<?php

namespace App\Http\Middleware;

use App\Helpers\AuthenticationService;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AuthAdvanced
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

        $rememberToken = $request->cookie('remember_token');
        if ($rememberToken != null) {
            $authenticationService->attemptRemember($rememberToken);
        }

        $levelLogin = $authenticationService->levelLogin();
        switch ($levelLogin) {
            case AuthenticationService::LEVEL_LOGIN_NORMAL:
                $user = $authenticationService->userOrFail();
                if ($user->secret_key != null) {
                    return redirect(route('auth.login.index2fa'));
                } else {
                    return $next($request);
                }
                break;

            case AuthenticationService::LEVEL_LOGIN_ADVANCED:
                return $next($request);
                break;

            default:
                return redirect(route('auth.login.index'));
        }
    }
}
