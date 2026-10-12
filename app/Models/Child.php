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
        'image',
        'parent_id',
        'manager_id',
    ];
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
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

