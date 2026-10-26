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

    // =========================================
    // Role constants (avoid magic strings)
    // =========================================
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_PARENT = 'parent';

    // =========================================
    // Status constants + lifecycle transitions
    // =========================================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DEACTIVATED = 'deactivated';

    /**
     * Eloquent query scopes (SRP on the model:
     * filtering logic lives with the data it filters).
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * تقييد الاستعلام على مستخدمي نفس المدرسة (المدرسة هي الكيان المركزي).
     */
    public function scopeSchool($query, ?School $school)
    {
        if ($school) {
            $query->where('school_id', $school->id);
        }

        return $query;
    }

    /** Search by name or school name (via the school relation, single source of truth). */
    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhereHas('school', fn ($s) => $s->where('name', 'like', "%{$term}%"));
        });
    }

    /**
     * State transitions live on the model that owns the state.
     */
    public function approve(): void
    {
        $this->update(['status' => self::STATUS_APPROVED]);
    }

    public function deactivate(): void
    {
        $this->update(['status' => self::STATUS_DEACTIVATED]);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    /** هل ينتمي المستخدمان لنفس المدرسة؟ (يُستخدم لفحص الملكية) */
    public function belongsToSameSchoolAs(User $other): bool
    {
        return $this->school_id !== null
            && $this->school_id === $other->school_id;
    }

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
        'school_id',
        'status',
        'role',
        'notification_preferences',
    ];

    public function childrenOfParent()
{
    return $this->hasMany(Child::class, 'parent_id');
}

public function students()
{
    return $this->belongsToMany(Child::class, 'child_teacher', 'teacher_id', 'child_id');
}

public function teacherDailyChanges()
{
    return $this->hasMany(DailyChange::class, 'teacher_id');
}

public function school()
{
    return $this->belongsTo(School::class);
}

/**
 * مدارس ولي الأمر (Many-to-Many عبر school_parent):
 * ولي الأمر يستطيع الانتماء لأكثر من مدرسة (كل طفل في مدرسته).
 */
public function schools()
{
    return $this->belongsToMany(School::class, 'school_parent', 'parent_id', 'school_id');
}

/** هل ولي الأمر مرتبط بهذه المدرسة؟ (يُستخدم للتحقق من الوصول) */
public function belongsToSchoolById(?int $schoolId): bool
{
    return $schoolId !== null
        && $this->role === self::ROLE_PARENT
        && $this->schools()->where('schools.id', $schoolId)->exists();
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
