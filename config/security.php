<?php
return [
    'password_reset' => [
        'token_timeout' => 10800, // 3 hours
    ],
    'backup_codes' => [
        'quantity' => 10, // a user has 10 backup codes
        'length' => 8,
        'timeout' => 31536000, // 1 year
    ],
    'strings' => [
        'logout' => "You logged out",
        'login' => "You logged in",
        'verify-email' => "You verified email",
        'enable-google2fa' => "You enabled Google2FA",
        'disable-google2fa' => "You disabled Google2FA",
        're-enable-google2fa' => "You re-enabled Google2FA",
    ],
];