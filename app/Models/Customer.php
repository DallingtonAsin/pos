<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
	protected $table = 'customers';
	// public $timestamps = true;
	// protected $dateFormat = 'U';
	// const CREATED_AT = 'creattion_date';
	// const UPDATED_AT = 'last_update';
	// protected $connection ='connection-name';

    protected $fillable = [
        'name',
         'contact',
         'debt',
         'credit',      
    ];

public $timestamps = true;

}
