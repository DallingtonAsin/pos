<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDebtPayment extends Model
{
    protected $table = "customer_debt_payments";
    
    protected $fillable = [
        'customer_id',
        'paid_amount',
        'balance',
        'date',
        'recorded_by'
    ];

    public $timestamps = true;

}
