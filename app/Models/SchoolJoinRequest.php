<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolJoinRequest extends Model
{
    protected $fillable = [
        'parent_id',
        'school_id',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
