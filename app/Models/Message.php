<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    public function message_type()
    {
        return $this->hasOne(MessageType::class, 'id', 'message_type_id');
    }
}
