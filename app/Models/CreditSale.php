<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSale extends Model
{
    use HasFactory;

    protected $table = 'credit_sales';

    protected $fillable = [
        'sale_order_number',
        'customer_id',
        'date',
        'total_cost',
        'amount_paid',
        'amount_due'
    ];

    public $timestamps = true;

    public function sale()
    {
        return $this->hasMany(Sale::class, 'order_number');
    }
}
