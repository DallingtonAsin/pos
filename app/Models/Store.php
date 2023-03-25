<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'stores';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'is_deleted',
        'added_by'
    ];
}
