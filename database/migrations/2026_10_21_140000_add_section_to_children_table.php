<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة حقل section (الشعبة) لجدول children.
 * الصفوف والشعب والمواد تُدار من قبل المدير في جدول schools (classes/sections/subjects)
 * والمعلم يختارها من القوائم عند إضافة طفل أو سجل جديد.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            if (!Schema::hasColumn('children', 'section')) {
                // نضعه بعد class مباشرة
                $table->string('section')->nullable()->after('class');
            }
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            if (Schema::hasColumn('children', 'section')) {
                $table->dropColumn('section');
            }
        });
    }
};
