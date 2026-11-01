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

    /**
     * ضمان تخزين البيانات العربية بشكل مقروء في حقول الـ JSON
     */
    public function setClassesAttribute($value)
    {
        $this->attributes['classes'] = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    public function setSectionsAttribute($value)
    {
        $this->attributes['sections'] = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    public function setSubjectsAttribute($value)
    {
        $this->attributes['subjects'] = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

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