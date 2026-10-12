<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyChange extends Model
{
protected $fillable = [
        'child_id',
        'teacher_id',
        'recorded_at',
        'subject',
        'note',
        'score',
    ];
    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
