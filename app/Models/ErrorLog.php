<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $table = 'error_logs';

    protected $fillable = [
         'username',
         'error_code',
         'error_message',
         'error_severity',   
         'method',   
    ];

public $timestamps = true;
}
