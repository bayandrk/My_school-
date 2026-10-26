<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * إعادة حقول (school_name, primary_color, logo) إلى جدول users.
 * البيانات لا تُحذف — تُملأ القيم من جدول schools المرتبط بكل مستخدم.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
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

        // استرجاع القيم من جدول schools (المصدر الذي نُقلت إليه سابقاً)
        DB::statement("
            UPDATE users
            JOIN schools ON schools.id = users.school_id
            SET users.school_name = schools.name,
                users.logo = schools.logo,
                users.primary_color = COALESCE(schools.color, '#0d6efd')
            WHERE users.school_name IS NULL OR users.school_name = ''
        ");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter(
                ['school_name', 'logo', 'primary_color'],
                fn (string $col) => Schema::hasColumn('users', $col)
            ));
        });
    }
};
