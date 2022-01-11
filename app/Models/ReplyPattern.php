<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyPattern extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reply_pattern_messages()
    {
        return $this->hasMany(ReplyPatternMessage::class)->orderBy('rank');
    }

    public function reply_patterns()
    {
        return $this->hasMany(ReplyPattern::class, 'parent_id')->orderBy('rank');
    }

    public function pattern_type()
    {
        return $this->hasOne(PatternType::class, 'id', 'pattern_type_id');
    }
}
