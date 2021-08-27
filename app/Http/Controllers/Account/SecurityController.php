<?php

namespace App\Http\Controllers\Account;

use App\Helpers\AuthenticationService;
use App\Helpers\SecurityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetupG2FARequest;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use PragmaRX\Google2FA\Support\Constants as SupportConstants;
use PragmaRX\Google2FA\Google2FA;

class SecurityController extends Controller
{
    public $google2fa;

    public $authenticationService;
    public $securityService;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(SupportConstants::SHA1); // TRUNCATE(HMAC-SHA256(K, T)) instead of TRUNCATE(HMAC-SHA1(K, C))
        $this->authenticationService = App::make(AuthenticationService::class);
        $this->securityService = App::make(SecurityService::class);
    }

    public function index(Request $request)
    {
        $userId = $request->session()->get('user.userId');
        $user = User::with('backupCodes')
            ->with('securityActivities')
            ->find($userId);
        return view('account.security', compact('user'));
    }

    public function temporarySetupG2FA(Request $request)
    {
        $user = $this->authenticationService->userOrFail();
        $g2faRes = $this->securityService->temporarySetupG2FA();
        return view('account.google2fa', ['qrcode' => $g2faRes['qr_code'], 'user' => $user]);
    }

    public function setupG2FA(SetupG2FARequest $request)
    {
        $totpCode = $request->input('totp_code');
        $user = $this->authenticationService->userOrFail();
        
        $type = 'enable-g2fa';
        if ($user->enabled_2fa_once != null) {
            $type = 're-enable-g2fa';
        } 

        if ($this->securityService->setupG2FA($user, $totpCode)) {
            $this->securityService->log($request, $user->id, $type);
            return redirect(route('account.security.index'))
                ->with('alert-class', 'alert-success')
                ->with('alert-message', 'Setup G2FA Successfully');
        }
        return back()->withErrors(['totp_code' => 'Wrong TOTP Code']);
    }

    public function turnOffG2FA(Request $request)
    {
        $user = $this->authenticationService->userOrFail();
        if ($this->securityService->turnOffG2FA($user)) {
            $this->securityService->log($request, $user->id, 'disable-g2fa');
        }

        return redirect(route('account.security.index'))
            ->with('alert-class', 'alert-warning')
            ->with('alert-message', 'Turn Off G2FA Successfully');
    }

    public function viewBackupCode(Request $request)
    {
        $user = $this->authenticationService->userOrFail();
        $backupCodes = $user->backupCodes;
        return view('account.view-backup-codes', compact('backupCodes'));
    }

    public function downloadBackupCodes(Request $request)
    { 
        $user = $this->authenticationService->userOrFail();
        $responseText = $this->securityService->backupCodesAsString($user);
        
        $headers = [
            'Content-type'        => 'text/plain',
            'Content-Disposition' => 'attachment; filename="totp-backup-codes.txt"',
        ];
        return \Response::make($responseText, 200, $headers);
    }
}
