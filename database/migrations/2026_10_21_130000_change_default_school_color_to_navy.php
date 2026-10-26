<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * تغيير اللون الافتراضي للمدارس من الأزرق (#0d6efd) إلى الكحلي (#1e3a8a)
 * — لون هادئ واحترافي مناسب للمدارس.
 * يحدّث المدارس التي لا يزال لونها على الافتراضي القديم فقط،
 * ولا يمس المدارس التي اختار صاحبها لوناً مخصصاً.
 */
return new class extends Migration
{
    public function up(): void
    {
        // تحديث اللون الافتراضي في تعريف الجدول
        DB::statement("ALTER TABLE schools ALTER COLUMN color SET DEFAULT '#1e3a8a'");

        // تحديث المدارس التي ما زالت على الافتراضي الأزرق القديم فقط
        DB::statement("UPDATE schools SET color = '#1e3a8a' WHERE color = '#0d6efd'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE schools ALTER COLUMN color SET DEFAULT '#0d6efd'");

        // التراجع: الكحلي الذي لم يُعدَّل يدوياً يعود للأزرق
        DB::statement("UPDATE schools SET color = '#0d6efd' WHERE color = '#1e3a8a'");
    }
};
