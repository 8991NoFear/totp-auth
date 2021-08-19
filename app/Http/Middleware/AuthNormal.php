<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

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
        if ($request->session()->has('user.loginedNormal')) {
            return $next($request);
        }

        $rememberToken = $request->cookie('remember_token');
        if ($rememberToken != null) {
            $user = User::where('remember_token', $rememberToken)->first();
            if ($user != null) {
                $request->session()->put('user.userId', $user->id);
                $request->session()->put('user.loginedNormal', true);
                return $next($request);
            }
        }

        return redirect(route('auth.login.index'));
    }
}
