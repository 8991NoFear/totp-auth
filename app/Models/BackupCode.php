<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupCode extends Model
{
    use HasFactory;

    public $table = 'backup_codes';

    public $timestamps = false;

    public $guarded = ['id'];
}
