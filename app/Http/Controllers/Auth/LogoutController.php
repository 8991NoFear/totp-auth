<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

use App\Models\User;

use App\Helpers\SecurityActivityLogger;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // log
        $logger = App::make(SecurityActivityLogger::class);
        $userId = $request->session()->get('user.userId');
        $description = config('security.strings.logout');
        $securityActivity = $logger->getModelForSave($request, $userId, $description);
        $securityActivity->save();

        // clear session
        $request->session()->invalidate();

        // invalidate remember
        Cookie::expire('remember_token');
        User::find($userId)->update(['remember_token' => null]);

        return redirect(route('auth.login.index'));
    }
}
