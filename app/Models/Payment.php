<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

	protected $table = 'payments';
	public $timestamps = true;
	protected $fillable = [
		'transaction_id',
		'currency_code',
		'paid_amount',
		'payment_details',
		'payment_status',
	];
	
}
