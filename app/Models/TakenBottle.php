<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakenBottle extends Model
{
    use HasFactory;

    protected $table = 'taken_bottles';
    
    protected $fillable = [
        'customer_id',
        'bottle_id',
        'quantity',
        'taken_on',
        'is_returned',
        'returned_on',
        'added_by'
    ];

    public $timestamps = true;
}
