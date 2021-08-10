<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

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
        if ($request->session()->has('userId')) {
            $userId = $request->session()->get('userId');
            $user = User::find($userId);
            if ($user->secret_key != null) {
                if ($request->session()->has('user2FA')) {
                    return $next($request);
                }
                return redirect(route('auth.login.index2fa'));
            }
            return $next($request);
        }
        return redirect(route('auth.login.index'));
    }
}
