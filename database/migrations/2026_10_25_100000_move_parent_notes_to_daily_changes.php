<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * نقل ملاحظات الأهل وردود المعلمة من جدول children إلى جدول daily_changes:
 * - إضافة حقل source لنوع السجل ('teacher' سجل من المعلم / 'parent_note' ملاحظة أم / 'teacher_reply' رد معلمة على ملاحظة أم)
 * - إضافة حقل parent_note_id لربط رد المعلمة بملاحظة الأم الأصلية
 * - نقل البيانات القديمة من children.parent_note / children.teacher_reply إلى daily_changes
 * - حذف الحقلين القديمين من جدول children (بدون فقدان البيانات لأنها نُقلت أولاً)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة الحقول الجديدة لجدول daily_changes
        Schema::table('daily_changes', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_changes', 'source')) {
                $table->string('source')->default('teacher')->after('note'); // teacher | parent_note | teacher_reply
            }
            if (!Schema::hasColumn('daily_changes', 'parent_note_id')) {
                $table->unsignedBigInteger('parent_note_id')->nullable()->after('source'); // id ملاحظة الأم الأصلية
            }
        });

        // 2. نقل البيانات القديمة من children إلى daily_changes (لن نحذف أي بيانات)
        $children = DB::table('children')
            ->whereNotNull('parent_note')
            ->where('parent_note', '!=', '')
            ->get(['id', 'parent_id', 'parent_note']);

        foreach ($children as $child) {
            // تقسيم الملاحظات المدمجة (كل ملاحظة مفصولة بسطرين)
            $notes = array_filter(array_map('trim', explode("\n\n", $child->parent_note)));

            $firstNoteId = null;
            foreach (array_reverse($notes) as $noteText) {
                // الأقدم أولاً حتى يبقى الترتيب صحيحاً
                $id = DB::table('daily_changes')->insertGetId([
                    'child_id'     => $child->id,
                    'teacher_id'   => null,
                    'recorded_at'  => now(),
                    'subject'      => 'ملاحظة ولي الأمر',
                    'note'         => $noteText,
                    'score'        => null,
                    'source'       => 'parent_note',
                    'parent_note_id' => null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                if ($firstNoteId === null) {
                    $firstNoteId = $id; // آخر ملاحظة (الأحدث) سنربط الرد القديم بها
                }
            }

            // نقل رد المعلمة القديم (إن وجد) وربطه بأحدث ملاحظة منقولة
            $reply = DB::table('children')->where('id', $child->id)->value('teacher_reply');
            if (!empty($reply) && $firstNoteId) {
                DB::table('daily_changes')->insertGetId([
                    'child_id'       => $child->id,
                    'teacher_id'     => null,
                    'recorded_at'    => now(),
                    'subject'        => 'رد المعلمة',
                    'note'           => $reply,
                    'score'          => null,
                    'source'         => 'teacher_reply',
                    'parent_note_id' => $firstNoteId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // 3. حذف الحقلين القديمين من جدول children (البيانات نُقلت بالكامل في الخطوة 2)
        Schema::table('children', function (Blueprint $table) {
            if (Schema::hasColumn('children', 'parent_note')) {
                $table->dropColumn('parent_note');
            }
            if (Schema::hasColumn('children', 'teacher_reply')) {
                $table->dropColumn('teacher_reply');
            }
        });
    }

    public function down(): void
    {
        // استرجاع الحقول في جدول children (بدون استرجاع البيانات المحذوفة)
        Schema::table('children', function (Blueprint $table) {
            if (!Schema::hasColumn('children', 'parent_note')) {
                $table->text('parent_note')->nullable()->after('health_status');
            }
            if (!Schema::hasColumn('children', 'teacher_reply')) {
                $table->text('teacher_reply')->nullable()->after('parent_note');
            }
        });

        Schema::table('daily_changes', function (Blueprint $table) {
            if (Schema::hasColumn('daily_changes', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('daily_changes', 'parent_note_id')) {
                $table->dropColumn('parent_note_id');
            }
        });
    }
};
