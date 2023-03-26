<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTaxTracker extends Model
{
  protected $table = 'sales_tax_tracker';
  protected $fillable=['id', 'item_id', 'item', 'quantity','amount', 'tax'];
  public $timestamps = true;


}
