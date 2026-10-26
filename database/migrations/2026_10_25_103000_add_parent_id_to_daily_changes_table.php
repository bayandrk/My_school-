<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة حقل parent_id لجدول daily_changes:
 * عندما تكتب الأم ملاحظة، يُسجل مُعرّفها (users.id) تلقائياً في هذا الحقل
 * حتى يعرف النظام من كتبت الملاحظة.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_changes', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_changes', 'parent_id')) {
                $table->foreignId('parent_id')
                      ->nullable()
                      ->after('teacher_id')
                      ->constrained('users')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_changes', function (Blueprint $table) {
            if (Schema::hasColumn('daily_changes', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
    }
};
