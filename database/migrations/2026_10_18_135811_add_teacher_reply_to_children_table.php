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
    Schema::table('children', function (Blueprint $table) {
        $table->text('teacher_reply')->nullable()->after('parent_note');
    });
}

public function down(): void
{
    Schema::table('children', function (Blueprint $table) {
        $table->dropColumn('teacher_reply');
    });
}
};
