<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use HasFactory;
    use Notifiable;

    public $guarded = ['id'];

    public function passwordReset() {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function backupCodes()
    {
        return $this->hasMany(BackupCode::class, 'user_id', 'id');
    }

    public function securityActivities() {
        return $this->hasMany(SecurityActivity::class, 'user_id', 'id');
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        // Return email address only...
        return $this->email;
    }
}
