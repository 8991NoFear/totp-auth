<?php

namespace App\Http\Middleware;

use App\Helpers\AuthenticationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ConfirmPassword
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
        $this->setIntendedURL($request);
        // if confirmed before
        $authenticationService = App::make(AuthenticationService::class);
        if ($authenticationService->checkConfirmedPassword()) {
            return $next($request);
        }
        return redirect(route('auth.confirm-password.index'));
    }

    public function setIntendedURL(Request $request) {
        $url = $request->url();
        app('redirect')->setIntendedUrl($url);
    }
}
