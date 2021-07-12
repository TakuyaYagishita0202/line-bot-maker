<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyState extends Model
{
    use HasFactory;

    public function reply_pattern()
    {
        return $this->hasOne(ReplyPattern::class, 'id', 'reply_pattern_id');
    }
}
