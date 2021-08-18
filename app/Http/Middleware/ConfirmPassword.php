<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

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
        // Confirm before
        if ($request->session()->has('user.password_confirmed_at')) {
            $pca = strtotime($request->session()->get('user.password_confirmed_at'));
            $pca += config('security.password_reset.token_timeout');
            if ($pca >= time()) {
                return $next($request);
            }
        }
        return redirect(route('auth.confirm-password.index'));
    }

    public function setIntendedURL(Request $request) {
        $url = $request->url();
        app('redirect')->setIntendedUrl($url);
    }
}
