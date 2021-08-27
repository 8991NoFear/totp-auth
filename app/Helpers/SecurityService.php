<?php

namespace App\Helpers;

use App\Models\BackupCode;
use App\Models\SecurityActivity;
use App\Models\User;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FA\Support\Constants;

class SecurityService {
    public $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(Constants::SHA1); // TRUNCATE(HMAC-SHA256(K, T)) instead of TRUNCATE(HMAC-SHA1(K, C))
    }

    public function log(Request $request, $userId, $type)
    {
        $action = config("security.strings.{$type}", $type);
        $device = $this->getDevice($request->userAgent());
        $location = $this->getLocation($request->ip());

        SecurityActivity::create([
            'user_id' => $userId,
            'action' => $action,
            'device' => $device,
            'location' => $location,
        ]);
    }

    private function getLocation($ip)
    {
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $location = '';
        try {
            $location = $details->city;
            $location .= ',' . $details->region;
            $location .= ', ' . $details->country;
        } catch (\Throwable $e) {
            if (empty($location)) {
                $location = 'Unknown Location';
            }
        }
        return $location;
    }

    private function getDevice($user_agent)
    {
        $os = $this->getOS($user_agent);
        $browser = $this->getBrowser($user_agent);

        $device = '';
        if (($os == 'Unknown OS Platform') && ($browser == 'Unknown Browser')) {
            $device = 'Unknown Device';
        } else if ($os == 'Unknown OS Platform') {
            $device = $browser;
        } else if ($browser == 'Unknown Browser') {
            $device = $os;
        } else {
            $device = $browser . ', ' . $os;
        }

        return $device;
    }

    private function getOS($user_agent)
    { 
        $os_platform = "Unknown OS Platform";

        $os_array = array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }

    private function getBrowser($user_agent)
    {
        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i'      => 'Internet Explorer',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Chrome',
            '/edge/i'      => 'Edge',
            '/opera/i'     => 'Opera',
            '/netscape/i'  => 'Netscape',
            '/maxthon/i'   => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i'    => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        return $browser;
    }

    /**
     * @return array
     */
    public function setupBackupCodes($userId)
    {
        $backupCodes = [];
        $quantity = config('security.backup_codes.quantity', 10);
        $length = config('security.backup_codes.length', 8);
        $expiry = time() + config('security.backup_codes.timeout', 31536000);
        for ($i = 0; $i < $quantity; $i++) {
            $bc = new BackupCode();
            $bc->user_id = $userId;
            $bc->code = $this->randomNumberString($length);
            $bc->expired_at = date('Y-m-d H:i:s', $expiry);
            $backupCodes[] = $bc;
        }
        return $backupCodes;
    }

    private function randomNumberString($length)
    {
        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $res .= random_int(0, 9); // cryptographically secure pseudo-random integers
        }
        return $res;
    }

    public function temporarySetupG2FA()
    {
        if (session()->has('user.temp_secret_key')) {
            $secretKey = session('user.temp_secret_key');
        } else {
            $secretKey = $this->google2fa->generateSecretKey();
            session()->put('user.temp_secret_key', $secretKey);
        }
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

    public function setupG2FA(User $user, $totpCode) 
    {
        if (session()->has('user.temp_secret_key')) {
            $secretKey = session()->get('user.temp_secret_key');
            if ($this->google2fa->verify($totpCode, $secretKey)) {
                $backupCodes = $this->setupBackupCodes($user->id);
                DB::beginTransaction();
                try {
                    $user->update([
                        'secret_key' => $secretKey,
                        'enabled_2fa_once' => true,
                    ]);
                    $user->backupCodes()->delete();
                    foreach ($backupCodes as $backupCode) {
                        $backupCode->save();
                    }
                    
                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return abort(400);
                }
                session()->forget('user.temp_secret_key'); // clear secret_key in session
                session()->put('user.level_login', 2); // remember logined advanced
                return true;
            }
        }
        return false;
    }

    public function turnOffG2FA(User $user)
    {
        DB::beginTransaction();
        try {
            $user->backupCodes()->delete();
            $user->update([
                'secret_key' => null,
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return abort(400);
        }
        session()->forget('user.loginedAdvance');
        return true;
    }

    public function backupCodesAsString(User $user)
    {
        $backupCodes = $user->backupCodes;
        $responseText =  'TOTP-AUTH Backup Codes';
        foreach ($backupCodes as $backupCode) {
            $line = chr(10);
            if ($this->checkBackupCode($backupCode)) {
                $line .= $backupCode->code . chr(9) . $backupCode->expired_at;
            }
            $responseText .= $line;
        }
        return $responseText;
    }

    private function checkBackupCode($backupCode)
    {
        if ($backupCode->used_at == null) {
            if (strtotime($backupCode->expired_at) > time()) {
                return true;
            }
        }
        return false;
    }
}