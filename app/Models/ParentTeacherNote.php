<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentTeacherNote extends Model
{
    protected $table = 'parent_teacher_notes';

    protected $fillable = [
        'child_id',
        'parent_id',
        'parent_note',
        'teacher_reply',
        'replied_by_teacher_id',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by_teacher_id');
    }
}
