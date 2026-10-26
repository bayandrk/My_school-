<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول طلبات انضمام أولياء الأمور للمدارس:
 * الأم ترسل طلب انضمام لمدرسة، والطلب يبقى معلقاً (pending)
 * حتى يقبل المدير (approved) فيتم الانضمام فعلياً، أو يرفضه (rejected).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // المدير الذي عالج الطلب
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // منع تكرار طلب معلق لنفس الأم على نفس المدرسة
            // $table->unique(['parent_id', 'school_id', 'status'], 'unique_pending_request');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_join_requests');
    }
};
