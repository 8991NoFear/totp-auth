<?php
return [
    'password_reset' => [
        'token_timeout' => 10800, // 3 hours
    ],
    'backup_codes' => [
        'quantity' => 10, // a user has 10 backup codes
        'length' => 8,
        'timeout' => 31536000, // 1 year
    ]
];