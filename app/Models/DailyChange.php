<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyChange extends Model
{
protected $fillable = [
        'child_id',
        'teacher_id',
        'parent_id',
        'recorded_at',
        'subject',
        'note',
    
        'score',
        'source',
        'parent_note_id',
    ];
    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /** ولي الأمر صاحب الملاحظة (إذا كانت ملاحظة أم) */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /** ردود المعلمة على هذه الملاحظة (إذا كانت ملاحظة أم) */
    public function replies()
    {
        return $this->hasMany(DailyChange::class, 'parent_note_id');
    }

    /** ملاحظة الأم الأصلية (إذا كان هذا السجل رد معلمة) */
    public function parentNote()
    {
        return $this->belongsTo(DailyChange::class, 'parent_note_id');
    }
}
