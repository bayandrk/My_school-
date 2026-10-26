<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * ترحيل البيانات القديمة (Backfill):
 * يملأ school_id في جدولي users و children اعتماداً على school_name القديم،
 * فالنظام الجديد يعتمد على المدرسة (school_id) ككيان مركزي بدل manager_id.
 *
 * الخطوات:
 *  1. إنشاء مدرسة في جدول schools لكل اسم مدرسة فريد موجود في users أو children.
 *  2. ربط كل مستخدم/طفل قديم بمدرسته عبر school_name.
 *  3. ربط الأطفال الذين ليس لديهم school_name مباشرة بمدرسة مديرهم (users.school_id عبر manager_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. إنشاء مدارس لكل اسم فريد من المستخدمين (جدول children لا يحتوي school_name)
        DB::statement("
            INSERT INTO schools (name, color, created_at, updated_at)
            SELECT DISTINCT u.school_name, '#0d6efd', NOW(), NOW()
            FROM users u
            WHERE u.school_name IS NOT NULL AND u.school_name != ''
              AND NOT EXISTS (SELECT 1 FROM schools s WHERE s.name = u.school_name)
        ");

        // 2. تحديث school_id للمستخدمين حسب school_name
        DB::statement("
            UPDATE users
            JOIN schools ON schools.name = users.school_name
            SET users.school_id = schools.id
            WHERE users.school_id IS NULL
        ");

        // 3. تحديث school_id للأطفال عبر مديرهم (manager_id)
        DB::statement("
            UPDATE children
            JOIN users AS managers ON managers.id = children.manager_id
            SET children.school_id = managers.school_id
            WHERE children.school_id IS NULL
              AND managers.school_id IS NOT NULL
        ");

        // 3ب. أطفال بدون مدير: نأخذ المدرسة من أحد أساتذهم المرتبطين
        DB::statement("
            UPDATE children
            JOIN child_teacher ct ON ct.child_id = children.id
            JOIN users AS teachers ON teachers.id = ct.teacher_id
            SET children.school_id = teachers.school_id
            WHERE children.school_id IS NULL
              AND teachers.school_id IS NOT NULL
        ");

        // 3ج. أطفال مرتبطين بولي أمر لديه مدرسة
        DB::statement("
            UPDATE children
            JOIN users AS parents ON parents.id = children.parent_id
            SET children.school_id = parents.school_id
            WHERE children.school_id IS NULL
              AND parents.school_id IS NOT NULL
        ");

        // 4. ملء school_name الناقص للمستخدمين من المدرسة المرتبطة بهم (توافق عكسي مع العروض القديمة)
        DB::statement("
            UPDATE users
            JOIN schools ON schools.id = users.school_id
            SET users.school_name = schools.name
            WHERE users.school_name IS NULL OR users.school_name = ''
        ");
    }

    public function down(): void
    {
        // التراجع: تفريغ الربط فقط (لا نحذف المدارس ولا نلمس school_name)
        DB::table('users')->update(['school_id' => null]);
        DB::table('children')->update(['school_id' => null]);
    }
};
