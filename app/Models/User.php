<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'email',
        'password',
        'enabled_2fa_once',
        'secret_key',
    ];

    public function resetPassword() {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function backupCodes()
    {
        return $this->hasMany(BackupCode::class, 'user_id', 'id');
    }
}
