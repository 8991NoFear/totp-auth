<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Notifications\RegisteredNotification;
use App\Models\User;
use App\Models\PasswordReset;
use App\Helpers\SecurityService;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $this->prepareDataForSave($request);

        DB::beginTransaction();
        try {
            // create a record in 'users' table
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // create a record in 'password_resets' table
            PasswordReset::create([
                'email' => $data['email'],
                'token' => $data['token'],
                'expired_at' => $data['expired_at'],
            ]);
            $user->notify(new RegisteredNotification($user)); // notify for sending email

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return abort(500);
        }

        return view('auth.verify-account', [
            'email' => $data['email'],
            'expired_at' => $data['expired_at'],
        ]);
    }

    public function verify(Request $request, User $user, $token)
    {
        if ($this->checkToken($user, $token)) {
            DB::beginTransaction();
            try {
                $user->update([
                    'email_verified_at' => date('Y-m-d H:i:s'),
                ]);
                $user->passwordReset()->update([
                    'expired_at' => date('Y-m-d H:i:s'),
                ]);
                App::make(SecurityService::class)
                    ->log($request, $user->id, 'verify-email');
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return abort(500);
            }
            return view('account.verify-complete');
        }
        return abort(400);
    }

    /**
     * @return array
     */
    private function prepareDataForSave(RegisterRequest $request)
    {
        $data = [];
        $data['email'] = $request->input('email');
        $data['username'] = extractNameFrom($data['email']);
        $data['password'] = $request->input('password');

        $tokenLength = config('security.token.length', 32);
        $data['token'] = bin2hex(openssl_random_pseudo_bytes($tokenLength));

        $expiry = time() + config('security.password_reset.token_timeout', 10800);
        $data['expired_at'] = date("Y-m-d H:i:s", $expiry);

        return $data;
    }

    private function checkToken(User $user, $inputToken)
    {
        $this->validateInputToken($inputToken);

        $passwordReset = $user->passwordReset;
        if ($passwordReset == null) {
            abort(500);
        }
        $isValidTime = strtotime($passwordReset->expired_at) >= time();
        $isValidToken = ($inputToken == $passwordReset->token);
        return $isValidTime && $isValidToken;
    }

    private function validateInputToken($inputToken)
    {
        $validator = Validator::make([
            'input_token' => $inputToken,
        ], [
            'input_token' => 'required|string',
        ]);
        if ($validator->fails()) {
            abort(400);
        }
    }
}
