<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Cart extends Model
{
	use HasFactory;
	protected $table = 'cart';
	// public $timestamps = false;
	// protected $dateFormat = 'U';
	// const CREATED_AT = 'creattion_date';
	// const UPDATED_AT = 'last_update';
	// protected $connection ='connection-name';

    protected $fillable = [
         'item',
         'quantity',
         'price',
         'discount',
         'amount'
    ];

   public $timestamps = false; //Indicates if the model should not be timestamped.
	// protected $primaryKey = 'id';
	//public $incrementing = false;
	//protected $keyType = 'string'; //primary key is a string
	// const CREATED_AT = 'creation_date';
  // const UPDATED_AT = 'last_update';
	//protected $dateFormat = 'U'; //storage format of date columns
	//protected $connection = 'connection-name'; //if u want to use a different db connection from the default one


}
