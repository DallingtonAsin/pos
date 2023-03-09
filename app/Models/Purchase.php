<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;
    protected $table = 'purchases';

    protected $fillable = [
        'serial_no',
        'receipt_no',
        'item_id',
        'quantity',
        'cost_price_per_item',
        'retail_price',
        'wholesale_price',
        'supplier_id',
        'recorded_by',
        'date_of_purchase'  
    ];
    public $timestamps = true;
}
