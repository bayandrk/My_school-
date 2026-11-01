<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * جدول موحد بين ولي الأمر والمدرسة (school_parent):
 * - status = pending  → طلب انضمام معلق بانتظار موافقة المدير
 * - status = approved → انضمام فعلي (منضم للمدرسة)
 * - status = rejected → طلب مرفوض (يمكن إعادة الإرسال)
 * ملاحظة: عند انضمام ولي أمر بدون طلب (من الإدارة/الأطفال) تُسجل الحالة approved تلقائياً.
 */
class SchoolParent extends Model
{
    protected $table = 'school_parent';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'parent_id',
        'school_id',
        'status',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /** طلبات الانضمام المعلقة لمدرسة معينة */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
