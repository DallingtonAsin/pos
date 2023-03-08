<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatBot extends Model
{
    public $table = "chatbox";
    protected $fillable = [
                'chat_command',
                'chat_response',
    ];
    public $timestamps = true;
}
