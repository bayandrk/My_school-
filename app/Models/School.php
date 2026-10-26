<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'color',
        'classes',
        'sections',
        'subjects',
    ];

    protected $casts = [
        'classes'  => 'array',
        'sections' => 'array',
        'subjects' => 'array',
    ];

    // المدرسة تحتوي على عدة مستخدمين (One-to-Many)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // أولياء الأمور المرتبطون بالمدرسة (Many-to-Many عبر school_parent)
    public function parents()
    {
        return $this->belongsToMany(User::class, 'school_parent', 'school_id', 'parent_id')
            ->where('role', 'parent');
    }

    // المدرسة تحتوي على عدة أطفال/طلاب (One-to-Many)
    public function children()
    {
        return $this->hasMany(Child::class);
    }
}