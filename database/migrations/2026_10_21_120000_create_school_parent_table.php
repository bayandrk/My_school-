<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * جدول وسيط بين ولي الأمر والمدرسة (Many-to-Many):
 * ولي الأمر يستطيع الانتماء لأكثر من مدرسة (كل طفل في مدرسته).
 * الأطفال يبقون مرتبطين بمدرستهم عبر children.school_id كما هو.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['school_id', 'parent_id']);
        });

        // ترحيل البيانات الحالية: كل ولي أمر لديه school_id يُربط بمدرسته في الجدول الوسيط
        DB::statement("
            INSERT INTO school_parent (school_id, parent_id, created_at, updated_at)
            SELECT u.school_id, u.id, NOW(), NOW()
            FROM users u
            WHERE u.role = 'parent' AND u.school_id IS NOT NULL
              AND NOT EXISTS (
                  SELECT 1 FROM school_parent sp
                  WHERE sp.school_id = u.school_id AND sp.parent_id = u.id
              )
        ");

        // ربط إضافي: أي مدرسة فيها أطفال يخصون ولي أمر بلا school_id — نربطها له
        DB::statement("
            INSERT INTO school_parent (school_id, parent_id, created_at, updated_at)
            SELECT DISTINCT c.school_id, c.parent_id, NOW(), NOW()
            FROM children c
            JOIN users u ON u.id = c.parent_id
            WHERE c.parent_id IS NOT NULL AND c.school_id IS NOT NULL
              AND NOT EXISTS (
                  SELECT 1 FROM school_parent sp
                  WHERE sp.school_id = c.school_id AND sp.parent_id = c.parent_id
              )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('school_parent');
    }
};
