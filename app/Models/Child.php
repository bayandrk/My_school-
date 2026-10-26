<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'name',
        'gender',
        'age',
        'health_status',
        'class',
        'section',
        'image',
        'parent_id',
        'school_id',


        'status',
    ];
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /** هل الطالب يتبع مدرسة معينة؟ (يُستخدم لفحص الملكية) */
    public function belongsToSchool(?School $school): bool
    {
        return $school !== null
            && $this->school_id !== null
            && $this->school_id === $school->id;
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'child_teacher', 'child_id', 'teacher_id');
    }

    public function dailyChanges()
    {
        return $this->hasMany(DailyChange::class);
    }
}
