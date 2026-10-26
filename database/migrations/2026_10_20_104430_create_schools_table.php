<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل التغييرات وتطبيق المايجريشن.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();                                    // id المفتاح الرئيسي
            $table->string('name');                          // اسم المدرسة
            $table->string('logo')->nullable();              // مسار شعار/لوغو المدرسة
            $table->string('color')->default('#0d6efd');      // لون الهوية/اللوحة (Hex Color)
            $table->json('classes')->nullable();             // الصفوف الدراسية (مصفوفة JSON)
            $table->json('sections')->nullable();            // الشعب الدراسية (مصفوفة JSON)
            $table->json('subjects')->nullable();            // المواد الدراسية (مصفوفة JSON)
            $table->timestamps();                            // created_at و updated_at
        });
    }

    /**
     * التراجع عن التغييرات.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};