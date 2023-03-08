<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    protected $table = 'expenses';
    public $timestamps = true;
    protected $fillable = [
        'expense_type',
        'amount',
        'date_of_expenditure'
    ];
}
