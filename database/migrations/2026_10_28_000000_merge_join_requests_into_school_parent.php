<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * دمج جدولي (school_parent) و(school_join_requests) في جدول واحد:
 * - school_parent يبقى جدول الانتماء (Many-to-Many) بين ولي الأمر والمدرسة
 * - يُضاف له حقل الحالة (status): pending = طلب انضمام معلق، approved = منضم فعلياً، rejected = مرفوض
 * - يُضاف له حقل الحالة (status): pending = طلب انضمام معلق، approved = منضم فعلياً، rejected = مرفوض
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) إضافة الأعمدة الجديدة إلى school_parent
        Schema::table('school_parent', function (Blueprint $table) {
            if (!Schema::hasColumn('school_parent', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('parent_id');
            }
        });

        // 2) تصفير الحالة للصفوف الحالية (كلها انضمامات فعلية)
        DB::table('school_parent')->whereNull('status')->update(['status' => 'approved']);

        // 3) ترحيل بيانات طلبات الانضمام القديمة إلى school_parent
        if (Schema::hasTable('school_join_requests')) {
            $requests = DB::table('school_join_requests')->get();

            foreach ($requests as $req) {
                $existing = DB::table('school_parent')
                    ->where('school_id', $req->school_id)
                    ->where('parent_id', $req->parent_id)
                    ->first();

                if ($existing) {
                    // الصف موجود — نحدّث حالته فقط إذا كان الطلب أحدث معلومة
                    if ($req->status !== 'approved') {
                        DB::table('school_parent')
                            ->where('id', $existing->id)
                            ->update([
                                'status'       => $req->status,
                                'updated_at'   => $req->updated_at ?? now(),
                            ]);
                    }
                } else {
                    DB::table('school_parent')->insert([
                        'school_id'    => $req->school_id,
                        'parent_id'    => $req->parent_id,
                        'status'       => $req->status,
                        'created_at'   => $req->created_at ?? now(),
                        'updated_at'   => $req->updated_at ?? now(),
                    ]);
                }
            }

            // 4) حذف الجدول القديم
            Schema::dropIfExists('school_join_requests');
        }
    }

    public function down(): void
    {
        // إرجاع طلبات الانضمام (غير المعتمدة) إلى جدول مستقل
        Schema::create('school_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        DB::table('school_parent')
            ->whereIn('status', ['pending', 'rejected'])
            ->get()
            ->each(function ($row) {
                DB::table('school_join_requests')->insert([
                    'parent_id'    => $row->parent_id,
                    'school_id'    => $row->school_id,
                    'status'       => $row->status,
                    'created_at'   => $row->created_at,
                    'updated_at'   => $row->updated_at,
                ]);
            });

        // إعادة الصفوف المتبقية إلى approved
        DB::table('school_parent')->update(['status' => 'approved']);

        Schema::table('school_parent', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
