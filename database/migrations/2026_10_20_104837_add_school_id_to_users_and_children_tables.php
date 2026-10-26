<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة school_id إلى جدول المستخدمين
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'school_id')) {
                $table->foreignId('school_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('schools')
                      ->onDelete('cascade');
            }
        });

        // 2. إضافة school_id إلى جدول الأطفال
        Schema::table('children', function (Blueprint $table) {
            if (!Schema::hasColumn('children', 'school_id')) {
                $table->foreignId('school_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('schools')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};