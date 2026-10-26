<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('school_join_requests', 'processed_by')) {
            Schema::table('school_join_requests', function (Blueprint $table) {
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }
        if (!Schema::hasColumn('school_join_requests', 'processed_at')) {
            Schema::table('school_join_requests', function (Blueprint $table) {
                $table->timestamp('processed_at')->nullable();
            });
        }
    }
    public function down(): void {}
};
