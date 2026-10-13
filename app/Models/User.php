<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'username',
    'email',
    'phone',
    'password',
    'school_name',
    'status',
    'role',
    'logo',         
    'primary_color',
    ];
public function childrenOfParent()
{
    return $this->hasMany(Child::class, 'parent_id');
}

public function childrenOfManager()
{
    return $this->hasMany(Child::class, 'manager_id');
}
public function students()
{
    return $this->belongsToMany(Child::class, 'child_teacher', 'teacher_id', 'child_id');
}
public function teacherDailyChanges()
{
    return $this->hasMany(DailyChange::class, 'teacher_id');
}
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
