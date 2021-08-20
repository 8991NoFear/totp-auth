<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Helpers\SecurityActivityLogger;
use Illuminate\Support\Facades\App;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('auth.change-password');
    }

    public function update(ChangePasswordRequest $request)
    {
        $credentials = $request->only(['old_password', 'password', 'password_confirmation']);
        $userId = $request->session()->get('user.userId');
        $user = User::find($userId);

        if (Hash::check($credentials['old_password'],  $user->password)) {
            // clear cookie
            Cookie::expire('remember_token');
            
            // update
            $user->update([
                'password' => Hash::make($credentials['password']),
                'remember_token' => null,
            ]);

            // log
            $logger = App::make(SecurityActivityLogger::class);
            $description = config('security.strings.change-password');
            $securityActivity = $logger->getModelForSave($request, $userId, $description);
            $securityActivity->save();

            return redirect(route('account.security.index'));
        }

        return back()
            ->withErrors(['old-password' => 'current password is wrong']);
    }
}
