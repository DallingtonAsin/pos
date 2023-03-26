<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierCredit extends Model
{
    use HasFactory;

    protected $table = 'supplier_credits';
    
    protected $fillable = [
        'supplier_id',
        'amount',
        'date',
        'is_deleted',
        'added_by'
    ];

    public $timestamps = true;

}
