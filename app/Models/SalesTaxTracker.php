<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTaxTracker extends Model
{
  protected $table = 'salestaxtracker';
  protected $fillable=['id', 'item_id', 'item', 'quantity','amount', 'tax'];
  public $timestamps = true;


}
