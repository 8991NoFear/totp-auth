<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\BackupCode;
use App\Models\User;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use PragmaRX\Google2FA\Support\Constants as SupportConstants;
use PragmaRX\Google2FA\Google2FA;

class SecurityController extends Controller
{
    public $google2fa;

    public function __construct() {
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(SupportConstants::SHA1); // TRUNCATE(HMAC-SHA256(K, T)) instead of TRUNCATE(HMAC-SHA1(K, C))
    }

    public function index()
    {
        dd(session()->all());
        return view('account.security');
    }

    public function setupGoogle2FA(Request $request)
    {
        $userId = $request->session()->get('user.userId');
        $user = User::find($userId);
        if(/*$request->session()->has('user.confirmed_password_at')*/ true) {
            // $confirmedAt = $request->session()->get('user.confirmed_password_at');
            // $confirmedTimeout = config('security.password_resets.token_timeout', 10800);
            // $isValid = time() < (strtotime($confirmedAt) + $confirmedTimeout);
            if (/*$isValid*/ true) { // password confirmed lately
                $g2faRes = $this->doSetupGoogle2FA();
                $request->session()->put('user.temp_secret_key', $g2faRes['secret_key']);
                return view('account.google2fa', ['qrcode' => $g2faRes['qr_code']]);
            }
        }
        dd("oke");
        // need verify
        abort(400); // I don't know how to handle this situation
    }

    public function verifySetupGoogle2FA(Request $request)
    {
        $credentials = $request->only('totp_code');
        $validator = Validator::make($credentials, [
            'totp_code' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return back()->with('totp-err', 'TOTP code must be a number with 6 ditgits'); // return object
        }
        $totpCode = $credentials['totp_code'];

        if ($request->session()->has('user.temp_secret_key')) {
            $secretKey = $request->session()->get('user.temp_secret_key');
            if ($this->google2fa->verify($totpCode, $secretKey)) {
                // actually save data to db
                $userId = $request->session()->get('user.userId');
                $user = User::find($userId);
                $user->secret_key = $secretKey;
                $user->enabled_2fa_once = true;
                $user->save();

                $request->session()->forget('user.temp_secret_key'); // clear secret_key in session

                // back to dashboard with alert
                return redirect(route('account.dashboard.index')); // ->with('alert-class', 'alert-success');
            } else {
                return back()->with('totp-err', 'Wrong TOTP Code');
            }
        }
        return abort(400); // I don't know how to handle this situation
    }

    private function doSetupGoogle2FA()
    {
        $secretKey = $this->google2fa->generateSecretKey();
        $companyName = config('app.name', 'TOTP-AUTH');
        $companyEmail = config('app.email', 'totp-auth@totp-auth.com');
        $g2faUrl =  $this->google2fa->getQRCodeUrl($companyName, $companyEmail, $secretKey);

        $imageRenderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($imageRenderer);
        $qrcode = base64_encode($writer->writeString($g2faUrl)); // base64 QRCode image that hold secret key
        return [
            'secret_key' => $secretKey,
            'qr_code' => $qrcode,
        ];
    }

    private function doSetupBackupCode($userId)
    {
        $backupCodes = [];
        $quantity = config('security.backup_codes.quantity', 10);
        $length = config('security.backup_codes.length', 8);
        $expiry = time() + config('security.backup_codes.timeout', 10800);
        for ($i = 0; $i < $quantity; $i++) {
            $bc = new BackupCode();
            $bc->user_id = $userId;
            $bc->code = $this->randomNumberString($length);
            $bc->expired_at = date('Y-m-d H:i:s', $expiry);
            $backupCodes[] = $bc;
        }
        return $backupCodes;
    }

    private function randomNumberString($len)
    {
        $res = '';
        for ($i = 0; $i < $len; $i++) {
            $res .= random_int(0, 9); // cryptographically secure pseudo-random integers
        }
        return $res;
    }
}
