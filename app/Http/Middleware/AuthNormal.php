<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        if ($request->session()->has('userId')) {
            return $next($request);
        }
        return redirect(route('auth.login.index'));
    }
}
