<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ConfirmPasswordController extends Controller
{
    public function index()
    {
        return view('auth.confirm-password');
    }

    public function confirm (Request $request)
    {
        // Validate Request Data
        $credentials = $request->only('password');
        $validator = Validator::make($credentials, [
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('password-error', 'Password is required');
        }
        $password = $credentials['password'];
        
        $userId = $request->session()->get('user.userId');
        $user = User::find($userId);
        if (Hash::check($credentials['password'], $user->password)) {
            $now = date("Y-m-d H:i:s", time());
            $request->session()->put('user.password_confirmed_at', $now);
            return redirect()->intended(route('account.security.index'));
        }

        // TODO: else
        return back()->with('password-error', 'Wrong password');
    }
}
