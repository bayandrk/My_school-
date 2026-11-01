<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * حذف حقلي processed_by / processed_at من جدول school_parent
 * (لم يعدا مطلوبين بعد دمج طلبات الانضمام — يكفي حقل الحالة status)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_parent', function (Blueprint $table) {
            if (Schema::hasColumn('school_parent', 'processed_by')) {
                $table->dropConstrainedForeignId('processed_by');
            }
            if (Schema::hasColumn('school_parent', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_parent', function (Blueprint $table) {
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable()->after('processed_by');
        });
    }
};
