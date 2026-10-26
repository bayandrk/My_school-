<?php

namespace App\Http\Controllers;
use App\Models\School;
use App\Notifications\ParentNoteNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Child;
use Carbon\Carbon;

class ParentController extends Controller
{
    public function dashboard()
    {
        $this->checkParent();

        $parent = Auth::user();

        // مدارس ولي الأمر (من الجدول الوسيط) + كل المدارس المتاحة للانضمام
        $mySchools   = $parent->schools()->orderBy('name')->get();
        $otherSchools = School::whereNotIn('id', $mySchools->pluck('id'))->orderBy('name')->get();

        // جلب أطفال ولي الأمر النشطين فقط (استبعاد المؤرشفين) مع سجل اليوم
        $children = Child::where('parent_id', $parent->id)
            ->where(function($q) {
                $q->where('status', 'active')
                  ->orWhereNull('status');
            })
            ->with(['dailyChanges' => function ($query) {
                // جلب السجلات التابعة لتاريخ اليوم فقط
                $query->whereDate('created_at', Carbon::today());
            }])
            ->get();

        return view('parent.dashboard', compact('children', 'mySchools', 'otherSchools'));
    }

    /** صفحة الانضمام لمدرسة (بحث + قائمة المدارس + حالة الطلبات) */
    public function joinSchoolPage(Request $request)
    {
        $this->checkParent();

        $parent = Auth::user();
        $search = $request->input('search');

        // مدارس الأم الحالية (المضمومة فعلياً)
        $mySchools = $parent->schools()->orderBy('name')->get();

        // المدارس المتاحة للانضمام (غير المضمومة) مع دعم البحث بالاسم
        $availableSchools = School::whereNotIn('id', $mySchools->pluck('id'))
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        // طلباتي المعلقة والسابقة
        $myRequests = \App\Models\SchoolJoinRequest::with('school')
            ->where('parent_id', $parent->id)
            ->latest()
            ->get();

        return view('parent.join_school', compact('mySchools', 'availableSchools', 'myRequests', 'search'));
    }

    /** إرسال طلب انضمام لمدرسة (يحتاج موافقة المدير) */
    public function joinSchool(Request $request)
    {
        $this->checkParent();

        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        $parent = Auth::user();

        // إذا هي مضمومة أصلاً
        if ($parent->schools()->where('schools.id', $request->school_id)->exists()) {
            return back()->with('error', 'أنتي منضمة لهذه المدرسة بالفعل.');
        }

        // إذا عندها طلب معلق على نفس المدرسة
        $pending = \App\Models\SchoolJoinRequest::where('parent_id', $parent->id)
            ->where('school_id', $request->school_id)
            ->where('status', 'pending')
            ->first();
        if ($pending) {
            return back()->with('error', 'عندك طلب معلق لهذه المدرسة بالفعل — بانتظار موافقة المدير.');
        }

        // إنشاء الطلب (معلق حتى يقبله المدير)
        $joinRequest = \App\Models\SchoolJoinRequest::create([
            'parent_id' => $parent->id,
            'school_id' => $request->school_id,
            'status'    => 'pending',
        ]);

        // إرسال إشعار لمدير المدرسة
        $school = \App\Models\School::find($request->school_id);
        if ($school) {
            $manager = \App\Models\User::where('school_id', $school->id)->where('role', 'manager')->first();
            if ($manager) {
                try {
                    $manager->notify(new \App\Notifications\ManagerJoinRequestNotification($parent->name, 'parent'));
                } catch (\Exception $e) {}
            }
        }

        return redirect()->route('parent.schools.join.page')->with('success', 'تم إرسال طلب الانضمام — سيتم إضافة المدرسة عند موافقة المدير.');
    }

    /** ترك مدرسة من داشبورد الأم */
    public function leaveSchool(Request $request)
    {
        $this->checkParent();

        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        $parent = Auth::user();
        $parent->schools()->detach($request->school_id);

        $school = School::find($request->school_id);

        return redirect()->route('parent.dashboard')
            ->with('success', 'تمت إزالة مدرسة: ' . $school->name . ' من حسابك.');
    }

    public function showChild(Child $child)
    {
        $this->checkParent();

        // التأكد من أن الطفل يخص ولي الأمر الحالي وأنه غير مؤرشف
        if ($child->parent_id !== auth()->id() || $child->status === 'archived') {
            abort(403, 'غير مصرح لك بعرض بيانات هذا الطفل.');
        }

        // جلب العلاقات المرتبطة بالطفل (الأساتذة والملاحظات والدرجات + ردود المعلمة على ملاحظات الأم)
        $child->load(['teachers', 'dailyChanges.replies']);

        // سجلات اليوم الخاصة بالمعلمة فقط (التي ليست ملاحظة ولي أمر وليست رد معلمة)
        $todayRecords = $child->dailyChanges()
            ->where('source', '!=', 'parent_note')
            ->where('source', '!=', 'teacher_reply')
            ->whereNull('parent_id')
            ->whereDate('recorded_at', Carbon::today())
            ->orderBy('recorded_at', 'desc')
            ->get();

        // السجلات السابقة للمعلمة فقط (التي ليست ملاحظة ولي أمر وليست رد معلمة)
        $previousRecordsQuery = $child->dailyChanges()
            ->where('source', '!=', 'parent_note')
            ->where('source', '!=', 'teacher_reply')
            ->whereNull('parent_id')
            ->orderBy('recorded_at', 'desc');

        $initialPreviousRecords = (clone $previousRecordsQuery)->take(10)->get();
        $totalPreviousCount = (clone $previousRecordsQuery)->count();

        return view('parent.child_show', compact('child', 'todayRecords', 'initialPreviousRecords', 'totalPreviousCount'));
    }

    public function getPreviousRecords(Request $request, Child $child)
    {
        $this->checkParent();

        if ($child->parent_id !== auth()->id() || $child->status === 'archived') {
            abort(403);
        }

        $records = $child->dailyChanges()
            ->where('source', '!=', 'parent_note')
            ->where('source', '!=', 'teacher_reply')
            ->whereNull('parent_id')
            ->orderBy('recorded_at', 'desc')
            ->get();

        $html = '';
        foreach ($records as $index => $record) {
            $subject = $record->subject ?? ($record->topic ?? '-');
            $score = ($record->score ?? $record->result) !== null ? '<span class="score-badge">' . ($record->score ?? $record->result) . '</span>' : '<span class="text-muted">-</span>';
            $note = $record->note ?? ($record->notes ?? ($record->description ?? '-'));
            $date = \Carbon\Carbon::parse($record->recorded_at ?? ($record->report_date ?? $record->created_at))->format('H:i | Y-m-d');

            $html .= '<tr class="prev-row-item">
                <td class="fw-bold text-muted">' . ($index + 1) . '</td>
                <td><span class="subject-badge">' . $subject . '</span></td>
                <td>' . $score . '</td>
                <td class="text-secondary fw-semibold">' . $note . '</td>
                <td><span class="date-badge">' . $date . '</span></td>
            </tr>';
        }

        return response()->json(['html' => $html]);
    }
    public function profile()
    {
        $this->checkParent();

        $parent = Auth::user();
        return view('parent.profile', compact('parent'));
    }

    public function updateProfile(Request $request)
    {
        $this->checkParent();

        $parent = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $parent->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $parent->name = $request->name;
        $parent->email = $request->email;

        if ($request->filled('password')) {
            $parent->password = Hash::make($request->password);
        }

        $parent->save();

        return redirect()->route('parent.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }

    public function destroyProfile(Request $request)
    {
        $this->checkParent();

        $parent = Auth::user();

        // فك ارتباط الأطفال بحفظ بياناتهم وجعل parent_id فارغاً
        Child::where('parent_id', $parent->id)->update(['parent_id' => null]);

        // 1. حذف حساب ولي الأمر نفسه
        $parent->delete();

        // 2. تسجيل الخروج وتطهير الجلسة
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'تم حذف حسابك الشخصي بنجاح.');
    }

    public function readNotification($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return redirect($notification->data['url'] ?? route('mother.dashboard'));
        }

        return redirect()->route('mother.dashboard');
    }
public function updateChildNote(Request $request, Child $child)
{
    $request->validate([
        'parent_note' => 'nullable|string|max:1000',
    ]);

    if ($child->parent_id !== auth()->id()) {
        abort(403);
    }

    // حفظ الملاحظة في جدول daily_changes (ملاحظة ولي الأمر)
    // ويُسجل مُعرّف الأم (parent_id) تلقائياً
    if ($request->filled('parent_note')) {
        \App\Models\DailyChange::create([
            'child_id'    => $child->id,
            'teacher_id'  => null,
            'parent_id'   => auth()->id(),
            'recorded_at' => now(),
            'subject'     => 'ملاحظة ولي الأمر',
            'note'        => $request->parent_note,
            'score'       => null,
            'source'      => 'parent_note',
        ]);
    }

    return back()->with('success', 'تم حفظ وتحديث ملاحظة ولي الأمر بنجاح.');
}
public function sendTeacherNote(Request $request, Child $child)
{
    $request->validate([
        'teacher_ids'   => 'required|array|min:1',
        'teacher_ids.*' => 'exists:users,id',
        'note'          => 'required|string|max:1000',
    ]);

    // حفظ الملاحظة في جدول daily_changes (ملاحظة ولي الأمر) - يُحدد النوع تلقائياً source = parent_note
    // ويُسجل مُعرّف الأم (parent_id) تلقائياً
    $parentNote = \App\Models\DailyChange::create([
        'child_id'    => $child->id,
        'teacher_id'  => null,
        'parent_id'   => auth()->id(),
        'recorded_at' => now(),
        'subject'     => 'ملاحظة ولي الأمر',
        'note'        => $request->note,
        'score'       => null,
        'source'      => 'parent_note', // تلقائياً: هذه ملاحظة أم وليست سجل من المعلمة
    ]);

    // أيضاً في جدول ملاحظات الأهل (للربط مع رد المعلمة)
    \App\Models\ParentTeacherNote::create([
        'child_id'     => $child->id,
        'parent_id'    => auth()->id(),
        'parent_note'  => $request->note,
        'daily_change_id' => $parentNote->id,
    ]);

    // إرسال الإشعارات للمعلمين
    foreach ($request->teacher_ids as $teacherId) {
        $teacher = \App\Models\User::find($teacherId);
        if ($teacher && class_exists(\App\Notifications\ParentNoteNotification::class)) {
            try {
                $teacher->notify(new \App\Notifications\ParentNoteNotification($child, $request->note));
            } catch (\Exception $e) {}
        }
    }

    return back()->with('success', 'تم حفظ وإرسال الملاحظة بنجاح.');
}

    private function checkParent()
    {
        if (auth()->user()->role !== 'parent' || auth()->user()->status !== 'approved') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }
}