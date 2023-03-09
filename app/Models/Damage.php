<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Damage extends Model
{
    use HasFactory;
    protected $table = 'damages';
    public $timestamps = true;
    protected $fillable = [
      'item_id',
      'quantity',
      'recorded_on',
      'recorded_by',
    ];
}
