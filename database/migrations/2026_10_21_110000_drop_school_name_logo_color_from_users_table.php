<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * حذف نهائي لحقول (school_name, logo, primary_color) من جدول users.
 * جدول schools أصبح المصدر الوحيد لهذه البيانات:
 *   school_name  -> schools.name
 *   logo         -> schools.logo
 *   primary_color-> schools.color
 * قبل الحذف تُنقل أي قيم مفقودة في جدول schools من users (توافق عكسي).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. نقل أي قيم موجودة في users إلى جدول schools قبل حذفها
        DB::statement("
            UPDATE schools
            JOIN users ON users.school_id = schools.id
            SET schools.logo  = COALESCE(schools.logo, users.logo),
                schools.color = COALESCE(schools.color, users.primary_color)
            WHERE users.school_id IS NOT NULL
        ");

        DB::statement("
            UPDATE schools
            JOIN users ON users.school_id = schools.id
            SET schools.name = users.school_name
            WHERE users.school_id IS NOT NULL
              AND users.school_name IS NOT NULL AND users.school_name != ''
        ");

        // 2. حذف الحقول
        Schema::table('users', function ($table) {
            $cols = array_filter(
                ['school_name', 'logo', 'primary_color'],
                fn (string $col) => Schema::hasColumn('users', $col)
            );
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function ($table) {
            if (!Schema::hasColumn('users', 'school_name')) {
                $table->string('school_name')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'logo')) {
                $table->string('logo')->nullable()->after('school_name');
            }
            if (!Schema::hasColumn('users', 'primary_color')) {
                $table->string('primary_color')->default('#0d6efd')->after('logo');
            }
        });

        // استرجاع القيم من جدول schools
        DB::statement("
            UPDATE users
            JOIN schools ON schools.id = users.school_id
            SET users.school_name = schools.name,
                users.logo = schools.logo,
                users.primary_color = COALESCE(schools.color, '#0d6efd')
        ");
    }
};
