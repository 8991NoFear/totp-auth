<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\Registered;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request) {
        $credentials = $request->except('_token');
        $user = User::where('email', $credentials['email'])->first();
        if ($user != null) { // account exists
            return back()
                ->withInput(['email' => $credentials['email']])
                ->withErrors(['email' => 'Account exists. Please choose another email!']);
        }

        $username = $this->getUsernameFromEmail($credentials['email']);
        $token = bin2hex(openssl_random_pseudo_bytes(32));
        $expiry = time() + config('security.password_reset.token_timeout', 10800);
        $expiry = date("Y-m-d H:i:s", $expiry);

        DB::beginTransaction();
        try {
            // create a record in 'users' table
            $user = User::create([
                'username' => $username,
                'email' => $credentials['email'],
                'password' => Hash::make($credentials['password']),
            ]);

            // create a record in 'password_resets' table
            PasswordReset::create([
                'email' => $credentials['email'],
                'token' => $token,
                'expired_at' => $expiry,
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return abort(500);
        }
        
        Mail::to($credentials['email'])->send(new Registered($user->id, $username, $token)); // send email
        return view('auth.verify-account', [
            'email' => $credentials['email'],
            'expired_at' => $expiry,
        ]);
    }

    private function getUsernameFromEmail($email) {
        $nameArr = explode('@', $email);
        $nameArr = explode('.', $nameArr[0]);
        return $nameArr[0];
    }

    public function verify(Request $request, $id, $token)
    {
        $this->validateVerificationRequest($id, $token); // validate request URI
        
        $user = $this->findUserByIdOrFail($id);
        $resetPassword = $user->resetPassword;

        $isValid = strtotime($resetPassword->expired_at) >= time();
        $isValid = $isValid && ($token == $resetPassword->token);
        if ($isValid) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $resetPassword->expired_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            try {
                $user->save();
                $resetPassword->save();
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return abort(500); // not handle
            }
            return view('account.verify-complete');
        }
        return abort(400); // not handle
    }

    private function validateVerificationRequest($id, $token) {
        $validator = Validator::make([
            'id' => $id,
            'token' => $token
        ], [
            'id' => 'required|integer',
            'token' => 'required|string',
        ]);
        if ($validator->fails()) {
            return abort(400);
        }
    }

    private function findUserByIdOrFail($id) {
        $user = User::find($id);
        if ($user == null) {
            return abort(400);
        }
        return $user;
    }
}
