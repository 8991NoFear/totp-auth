<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityActivity extends Model
{
    use HasFactory;

    public $table = 'security_activities';

    public $timestamps = false;

    public $guarded = ['id'];
}
