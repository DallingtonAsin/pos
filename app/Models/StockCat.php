<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockCat extends Model
{
    use HasFactory;
    protected $table = 'stockcategories';
    public $timestamps = true;
    protected $fillable = [
      'item_category'
    ];
}
