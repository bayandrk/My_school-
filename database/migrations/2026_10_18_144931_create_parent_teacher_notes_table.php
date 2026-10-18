<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('parent_teacher_notes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
        $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
        $table->text('parent_note');
        $table->text('teacher_reply')->nullable();
        $table->foreignId('replied_by_teacher_id')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('parent_teacher_notes');
}
};
