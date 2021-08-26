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
        'enable-g2fa' => "You enabled Google2FA",
        'disable-g2fa' => "You disabled Google2FA",
        're-enable-g2fa' => "You re-enabled Google2FA",
        'change-password' => "You changed password",
    ],
    'auth' => [
        'remember_timeout' => 129600, // 3 months
    ],
    'token' => [
        'length' => 32,
    ],
    'confirm_password' => [
        'timeout' => 10800,
    ],
    'remember_token' => [
        'length' => 32,
        'timeout' => 324000, // 3 months
    ]
];