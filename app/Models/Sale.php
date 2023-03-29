<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'order_number',
        'item_code',
        'item',
        'quantity',
        'original_price',
        'selling_price',
        'total_cost',
        'discount',
        'amount',
        'customer',
        'tax',
        'date',
        'time',
        'customer_id',
        'cashier_id'
    ];

    public $timestamps = true;
}
