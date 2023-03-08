<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;

class Role extends Model
{
   use HasFactory;

   protected $table = 'roles';

   public $timestamps = true;
   
   protected $fillable = [
   	        'name',
              'is_admin',
              'is_super_admin'
   ];

   public function users()
   {
       return $this->belongsToMany(User::class);
   }

}
