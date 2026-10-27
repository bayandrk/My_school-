<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\DailyChange;
use App\Models\School;
use App\Models\User;
use App\Notifications\TeacherAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * ManagerController — يعتمد على المدرسة (school_id) ككيان مركزي.
 *
 * كل الاستعلامات تُقيَّد بمدرسة المدير (Auth::user()->school) بدلاً من
 * manager_id أو school_name، وكل عمليات الملكية تمر عبر belongsToSchool(). */
class ManagerController extends Controller
{
    /** اختصار: مدرسة المدير الحالي */
    private function school(): ?School
    {
        return Auth::user()->school;
    }

    /** فحص أن الطالب يتبع مدرسة المدير الحالي */
    private function authorizeChild(Child $child): void
    {
        if (! $child->belongsToSchool($this->school())) {
            abort(403);
        }
    }

    /** فحص أن الأستاذ يتبع مدرسة المدير الحالي */
    private function authorizeTeacher(User $teacher): void
    {
        if (! $teacher->belongsToSameSchoolAs(Auth::user())) {
            abort(403, 'غير مصرح لك بالتعامل مع هذا الأستاذ.');
        }
    }

    /** استعلام أساسي: أساتذة مدرسة المدير */
    private function teachersQuery()
    {
        return User::query()
            ->role(User::ROLE_TEACHER)
            ->school($this->school());
    }

    /** استعلام أساسي: طلاب مدرسة المدير */
    private function childrenQuery()
    {
        return Child::query()->where('school_id', $this->school()?->id);
    }

    private function checkManager(): void
    {
        if (Auth::user()?->role !== User::ROLE_MANAGER) {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }

public function dashboard(Request $request)
    {
        $this->checkManager();
        $school = $this->school();

        // إحصائيات المدرسة الحالية للمخططات والبطاقات
        $studentsCount     = $this->childrenQuery()->count();
        $teachersCount     = $this->teachersQuery()->status(User::STATUS_APPROVED)->count();
        
        // حساب عدد أولياء الأمور المرتبطين بمدرسة المدير
        $parentsCount = User::query()
            ->role(User::ROLE_PARENT)
            ->whereHas('schools', function($q) use ($school) {
                $q->where('schools.id', $school?->id);
            })->count();

        // حساب عدد المتغيرات اليومية الخاصة بطلاب مدرسة المدير
        $dailyChangesCount = DailyChange::whereHas('child', function($q) use ($school) {
            $q->where('school_id', $school?->id);
        })->count();

        return view('manager.dashboard', compact(
            'studentsCount',
            'teachersCount',
            'parentsCount',
            'dailyChangesCount'
        ));
    }

    // صفحة المهام والطلبات المعلقة الخاصة بالمدير
    public function tasks(Request $request)
    {
        $this->checkManager();
        $school = $this->school();

        // طلبات الأساتذة المعلقة
        $pendingTeachers = $this->teachersQuery()
            ->status(User::STATUS_PENDING)
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // طلبات انضمام الأهل المعلقة
        $pendingJoinRequests = collect();
        if ($school) {
            $pendingJoinRequests = \App\Models\SchoolJoinRequest::where('school_id', $school->id)
                ->where('status', 'pending')
                ->with(['parent', 'school'])
                ->latest()
                ->get();
        }

        return view('manager.tasks', compact('pendingTeachers', 'pendingJoinRequests'));
    }

    // دالة إلغاء التعيين المتوافقة مع دالة التعيين
    public function unassignTeacherClass(Request $request)
    {
        $this->checkManager();

        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string',
        ]);

        $children = $this->childrenQuery()
            ->where('class', $request->class_name)
            ->get();

        if ($children->isEmpty()) {
            return back()->with('error', 'لم يتم العثور على طلاب في هذا الصف.');
        }

        foreach ($children as $child) {
            $child->teachers()->detach($request->teacher_id);
        }

        return redirect()->route('manager.assignments')->with('success', 'تم إلغاء تعيين الأستاذ عن صف (' . $request->class_name . ') بنجاح.');
    }

    public function profile()
    {
        $manager = Auth::user();
        $school = $manager->school;
        return view('manager.profile', compact('manager', 'school'));
    }

    public function updateProfile(Request $request)
    {
        $manager = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $manager->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $manager->name = $request->name;
        $manager->email = $request->email;

        if ($request->filled('password')) {
            $manager->password = Hash::make($request->password);
        }

        $manager->save();

        return redirect()->route('manager.profile')->with('success', 'تم تحديث الملف الشخصي للمدير بنجاح.');
    }

    // الموافقة على الأستاذ
    public function approveTeacher(User $teacher)
    {
        $this->checkManager();
        $this->authorizeTeacher($teacher);

        $teacher->approve();

        return back()->with('success', 'تمت الموافقة على طلب انضمام الأستاذ بنجاح.');
    }

    // رفض طلب الأستاذ (حذفه)
    public function rejectTeacher(User $teacher)
    {
        $this->checkManager();
        $this->authorizeTeacher($teacher);

        $teacher->students()->detach();
        $teacher->delete();

        return back()->with('success', 'تم رفض طلب انضمام الأستاذ بنجاح.');
    }

    // إلغاء تفعيل حساب الأستاذ
    public function deactivateTeacher(User $teacher)
    {
        $this->checkManager();
        $this->authorizeTeacher($teacher);

        $teacher->deactivate();

        return back()->with('success', 'تم إلغاء تفعيل حساب الأستاذ بنجاح.');
    }

    // إعادة تفعيل حساب الأستاذ المعطل
    public function reactivateTeacher(User $teacher)
    {
        $this->checkManager();
        $this->authorizeTeacher($teacher);

        $teacher->approve();

        return back()->with('success', 'تمت إعادة تفعيل حساب الأستاذ بنجاح.');
    }

    public function deactivatedTeachers(Request $request)
    {
        $this->checkManager();
        $search = $request->input('search');

        $teachers = $this->teachersQuery()
            ->status(User::STATUS_DEACTIVATED)
            ->search($search)
            ->get();

        return view('manager.deactivated_teachers', compact('teachers', 'search'));
    }

    // 1. أرشفة طالب
    public function archiveChild(Child $child)
    {
        $this->checkManager();
        $this->authorizeChild($child);

        $child->status = 'archived';
        $child->save();

        return back()->with('success', 'تم أرشفة حساب الطالب بنجاح ونقله للأرشيف.');
    }

    // 2. إعادة تفعيل طالب من الأرشيف
    public function restoreChild(Child $child)
    {
        $this->checkManager();
        $this->authorizeChild($child);

        $child->update(['status' => 'active']);

        return back()->with('success', 'تمت إعادة تفعيل الطالب وإرجاعه لدليل الصفوف بنجاح.');
    }

    // 3. عرض الطلاب المؤرشفين
    public function archivedChildren(Request $request)
    {
        $this->checkManager();
        $search = $request->input('search');

        $query = $this->childrenQuery()
            ->where('status', 'archived');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $children = $query->get();

        return view('manager.archived_children', compact('children', 'search'));
    }

    // صفحة إدارة التعيينات المستقلة
    public function assignmentsIndex(Request $request)
    {
        $this->checkManager();

        // الأساتذة المعتمدون للتعيين
        $approvedTeachers = $this->teachersQuery()
            ->status(User::STATUS_APPROVED)
            ->get();

        // قائمة الصفوف
        $classesList = $this->childrenQuery()
            ->pluck('class')
            ->unique()
            ->sort()
            ->values();

        // جلب التعيينات الحالية وتجميعها حسب الصف
        $currentAssignments = collect();

        try {
            $children = $this->childrenQuery()
                ->has('teachers')
                ->with('teachers')
                ->get();

            $grouped = [];

            foreach ($children as $child) {
                $grouped[$child->class] ??= collect();
                foreach ($child->teachers as $teacher) {
                    $grouped[$child->class]->push($teacher);
                }
            }

            foreach ($grouped as $className => $teachersColl) {
                $currentAssignments->push((object)[
                    'class_name' => $className,
                    'teachers'   => $teachersColl->unique('id')->values(),
                ]);
            }

            $currentAssignments = $currentAssignments->sortBy('class_name')->values();
        } catch (\Exception $e) {
            $currentAssignments = collect();
        }

        return view('manager.assignments', compact('approvedTeachers', 'classesList', 'currentAssignments'));
    }

    // دالة تنفيذ تعيين الأستاذ لصف دراسي
    public function assignTeacherToClass(Request $request)
    {
        $this->checkManager();

        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string',
        ]);

        // التأكد من أن الأستاذ يتبع نفس مدرسة المدير (school_id)
        $teacher = $this->teachersQuery()
            ->where('id', $request->teacher_id)
            ->first();

        if (!$teacher) {
            return back()->with('error', 'الأستاذ المحدد غير موجود أو لا ينتمي إلى مدرستك.');
        }

        // جلب جميع طلاب مدرسة المدير في الصف المحدد
        $children = $this->childrenQuery()
            ->where('class', $request->class_name)
            ->get();

        if ($children->isEmpty()) {
            return back()->with('error', 'لا يوجد طلاب في هذا الصف ليتم ربطهم بالأستاذ.');
        }

        // ربط جميع طلاب الصف بهذا الأستاذ بدون تكرار
        foreach ($children as $child) {
            $child->teachers()->syncWithoutDetaching([$teacher->id]);
        }

        // إرسال إشعار للأستاذ
        try {
            $teacher->notify(new TeacherAssignedNotification($request->class_name));
        } catch (\Exception $e) {
            // تخطي خطأ الإشعار لضمان عدم توقف العملية
        }

        return redirect()->route('manager.assignments')->with('success', 'تم تعيين الأستاذ لصف (' . $request->class_name . ') بنجاح.');
    }

    // صفحة الصفوف الشاملة
    public function classes(Request $request)
{
    $this->checkManager();

    $selectedYear  = $request->get('year', date('Y'));
    $studentSearch = $request->input('search_student');
    $selectedClass = $request->input('class_filter');
    $selectedSection = $request->input('section_filter');
    $selectedSubject = $request->input('subject_filter');

    // 1. جلب جميع السنوات المتاحة ديناميكياً لطلاب المدرسة فقط
    $availableYears = $this->childrenQuery()
        ->selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->pluck('year')
        ->push((int)date('Y'))
        ->unique()
        ->sortDesc();

    // 2. بناء استعلام الطلاب مع جلب العلاقات
    $query = $this->childrenQuery()
        ->with(['dailyChanges', 'teachers', 'parent'])
        ->where(function($q) {
            $q->where('status', 'active')->orWhereNull('status');
        })
        ->where(function($q) use ($selectedYear) {
            $q->whereYear('created_at', $selectedYear)
              ->orWhere('class', 'LIKE', "%{$selectedYear}%");
        });

    if (!empty($selectedClass)) {
        $query->where('class', $selectedClass);
    }

    if (!empty($selectedSection)) {
        $query->where('section', $selectedSection);
    }

    if (!empty($studentSearch)) {
        $query->where('name', 'like', '%' . $studentSearch . '%');
    }

    $children = $query->get();

    // 3. قائمة الصفوف الفريدة للفلتر
    $allClassesList = $this->childrenQuery()
        ->where(function($q) {
            $q->where('status', 'active')->orWhereNull('status');
        })
        ->pluck('class')
        ->unique()
        ->sort()
        ->values();

    // 4. تجميع الطلاب حسب الصفوف أولاً
    moh_grouped:
    $childrenGroupedByClass = $children->groupBy('class');

    $arabicNumbers = [
        'الأول' => 1, 'اول' => 1,
        'الثاني' => 2, 'ثاني' => 2,
        'الثالث' => 3, 'ثالث' => 3,
        'الرابع' => 4, 'رابع' => 4,
        'الخامس' => 5, 'خامس' => 5,
        'السادس' => 6, 'سادس' => 6,
        'السابع' => 7, 'سابع' => 7,
        'الثامن' => 8, 'ثامن' => 8,
        'التاسع' => 9, 'تاسع' => 9,
        'العاشر' => 10, 'عاشر' => 10,
        'الحادي عشر' => 11, 'حادي عشر' => 11,
        'الثاني عشر' => 12, 'ثاني عشر' => 12,
    ];

    $childrenGroupedByClass = $childrenGroupedByClass->sortKeysUsing(function ($a, $b) use ($arabicNumbers) {
        $numA = 99;
        $numB = 99;

        foreach ($arabicNumbers as $word => $val) {
            if (mb_strpos($a, $word) !== false) { $numA = $val; }
            if (mb_strpos($b, $word) !== false) { $numB = $val; }
        }

        return ($numA === $numB) ? strcmp($a, $b) : ($numA <=> $numB);
    });

    // 5. حساب المعدل العام لكل طالب وتوزيع الشعب تحت كل صف
    $structuredClasses = [];
    foreach ($childrenGroupedByClass as $className => $students) {
        foreach ($students as $student) {
            $student->average_score = round($student->dailyChanges->whereNotNull('score')->avg('score'), 2);
        }
        // تجميع شعب الصف الواحد
        $structuredClasses[$className] = $students->groupBy(function($student) {
            return $student->section ?: 'عام';
        });
    }

    return view('manager.classes', compact(
        'structuredClasses',
        'availableYears',
        'selectedYear',
        'allClassesList',
        'studentSearch',
        'selectedClass',
        'selectedSection',
        'selectedSubject'
    ))->with('school', $this->school());
}

    // صفحة سجلات طالب واحد (عرض السجلات كصفحة مستقلة)
    public function studentRecords(Request $request, Child $student)
    {
        $this->checkManager();

        // التأكد أن الطالب ينتمي لمدرسة المدير وغير مؤرشف
        if ($student->school_id !== $this->school()->id) {
            abort(403, 'غير مصرح لك بعرض سجلات هذا الطالب.');
        }

        if ($student->status === 'archived') {
            abort(404);
        }

        $student->load(['dailyChanges', 'teachers', 'parent', 'school']);

        $query = \App\Models\DailyChange::where('child_id', $student->id)
            // نستبعد ملاحظات الأهل وردود الأنسة — هاد "اللي بضل" (سجلات المعلمة العادية)
            ->whereNotIn('source', ['parent_note', 'teacher_reply']);

        // فلترة حسب التاريخ
        if ($request->filled('date')) {
            $query->whereDate('recorded_at', $request->date);
        }

        // فلترة حسب اسم المادة
        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        $dailyChanges = $query->orderBy('recorded_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('manager.student_records', compact('student', 'dailyChanges'))
            ->with('school', $this->school());
    }

    public function teachers(Request $request)
    {
        $this->checkManager();
        $search = $request->input('search');

        // جلب الأساتذة في نفس مدرسة المدير فقط مع الطلاب التابعين لهم
        $teachers = $this->teachersQuery()
            ->status(User::STATUS_APPROVED)
            ->with('students')
            ->search($search)
            ->get();

        return view('manager.teachers', compact('teachers', 'search'));
    }

    // معالجة تحديث بيانات الطالب
    public function updateChild(Request $request, Child $child)
    {
        $this->checkManager();
        $this->authorizeChild($child);

        $request->validate([
            'name'          => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'age'           => 'required|integer|min:1|max:20',
            'class'         => 'required|string|max:255',
            'health_status' => 'nullable|string',
            'parent_id'     => 'nullable|exists:users,id',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $child->image = $request->file('image')->store('children_images', 'public');
        }

        $child->update([
            'name'          => $request->name,
            'gender'        => $request->gender,
            'age'           => $request->age,
            'class'         => $request->class,
            'health_status' => $request->health_status,
            'parent_id'     => $request->parent_id,
            'image'         => $child->image,
        ]);

        return redirect()->route('manager.classes')->with('success', 'تم تعديل بيانات الطالب بنجاح.');
    }

    // حذف الطالب
    public function destroyChild(Child $child)
    {
        $this->checkManager();
        $this->authorizeChild($child);

        $child->delete();

        return redirect()->route('manager.classes')->with('success', 'تم حذف الطالب بنجاح.');
    }

    public function destroyTeacher(User $teacher)
    {
        $this->checkManager();
        $this->authorizeTeacher($teacher);

        // فك ارتباط الأستاذ بالطلاب في الجدول الوسيط (child_teacher)
        $teacher->students()->detach();

        $teacher->delete();

        return redirect()->route('manager.teachers')->with('success', 'تم حذف الأستاذ بنجاح.');
    }

    public function destroyProfile(Request $request)
    {
        $this->checkManager();

        Auth::user()->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'تم حذف حساب المدير الشخصي بنجاح، وباقي بيانات المدرسة والطلاب محفوظة.');
    }

    /**
     * مفك مفتاح البطاقة المركب: "الصف X - الشعبة ص" → [اسم الصف، الشعبة]
     * لازم لأن أزرار العرض بتستقبل المفتاح الكامل من دليل الصفوف.
     */
    private function splitClassName(?string $composite): array
    {
        $composite = trim((string)$composite);

        if (preg_match('/^(.*?)\s*-\s*الشعبة\s*(.+)$/u', $composite, $m)) {
            return [trim($m[1]), trim($m[2])];
        }

        return [$composite, null];
    }

    /** استعلام مشترك لصفحة العلامات: طلاب مدرسة المدير في صف معين مع سجلاتهم المدرّجة */
    private function studentsInClass(string $className, ?string $searchName, ?string $searchSubject)
    {
        [$classOnly, $sectionOnly] = $this->splitClassName($className);

        // العلامات: بس المواد المدرسية المعتمدة (مش المواضيع المخصصة)
        $schoolSubjects = $this->school()?->subjects ?? [];

        return $this->childrenQuery()
            ->where('class', $classOnly)
            ->when($sectionOnly, fn($q) => $q->where('section', $sectionOnly))
            ->when($searchName, fn($q) => $q->where('name', 'like', "%{$searchName}%"))
            ->with(['dailyChanges' => function($q) use ($searchSubject, $schoolSubjects) {
                $q->whereNotNull('score')
                  ->whereNotIn('source', ['parent_note', 'teacher_reply'])
                  ->when(!empty($schoolSubjects), fn($sq) => $sq->whereIn('subject', $schoolSubjects));
                if ($searchSubject) {
                    $q->where('subject', $searchSubject);
                }
            }])
            ->get()
            ->filter(fn($student) => !$searchSubject || $student->dailyChanges->isNotEmpty())
            ->unique('id');
    }

    public function classScores(Request $request)
    {
        $this->checkManager();
        $className     = $request->get('class_name');
        $searchName    = $request->get('search_name');
        $searchSubject = $request->get('search_subject');

        [$classOnly, $sectionOnly] = $this->splitClassName($className);

        $schoolSubjects = $this->school()?->subjects ?? [];

        // المواد المتاحة لهذا الصف لطلاب المدرسة فقط (المواد المعتمدة اللي فيها درجة)
        $subjects = DailyChange::whereHas('child', function($q) use ($classOnly, $sectionOnly) {
            $q->where('class', $classOnly)
              ->when($sectionOnly, fn($sq) => $sq->where('section', $sectionOnly))
              ->where('school_id', $this->school()?->id);
        })->whereNotNull('score')
          ->whereNotIn('source', ['parent_note', 'teacher_reply'])
          ->when(!empty($schoolSubjects), fn($q) => $q->whereIn('subject', $schoolSubjects))
          ->pluck('subject')->unique()->filter();

        $students = $this->studentsInClass($className, $searchName, $searchSubject);

        return view('manager.class_scores', compact('students', 'className', 'subjects', 'searchName', 'searchSubject'));
    }

    // صفحة ملاحظات الأهل لصف معين (ملاحظة ولي الأمر + رد الأنسة)
    public function classParentNotes(Request $request)
    {
        $this->checkManager();
        $className     = $request->get('class_name');
        $searchName    = $request->get('search_name');
        $searchSubject = $request->get('search_subject');

        [$classOnly, $sectionOnly] = $this->splitClassName($className);

        // المواضيع المتوفرة ضمن ملاحظات الأهل لهذا الصف
        $subjects = DailyChange::where('source', 'parent_note')
            ->whereHas('child', function($q) use ($classOnly, $sectionOnly) {
                $q->where('class', $classOnly)
                  ->when($sectionOnly, fn($sq) => $sq->where('section', $sectionOnly))
                  ->where('school_id', $this->school()?->id);
            })
            ->when($searchSubject, fn($q) => $q->where('subject', $searchSubject))
            ->pluck('subject')->unique()->filter()->values();

        // جلب ملاحظات الأهل لطلاب هذا الصف مع ردود الأنسات
        $notes = DailyChange::where('source', 'parent_note')
            ->whereHas('child', function($q) use ($classOnly, $sectionOnly, $searchName) {
                $q->where('class', $classOnly)
                  ->when($sectionOnly, fn($sq) => $sq->where('section', $sectionOnly))
                  ->where('school_id', $this->school()?->id)
                  ->when($searchName, fn($sq) => $sq->where('name', 'like', "%{$searchName}%"));
            })
            ->when($searchSubject, fn($q) => $q->where('subject', $searchSubject))
            ->with(['child.parent', 'parent', 'replies.teacher'])
            ->orderBy('recorded_at', 'desc')
            ->get();

        return view('manager.class_parent_notes', compact('notes', 'className', 'subjects', 'searchName', 'searchSubject'));
    }

    public function classNotes(Request $request)
    {
        $this->checkManager();
        $className     = $request->get('class_name');
        $searchName    = $request->get('search_name');
        $searchSubject = $request->get('search_subject');

        [$classOnly, $sectionOnly] = $this->splitClassName($className);

        $schoolSubjects = $this->school()?->subjects ?? [];

        // "اللي بضل": مواضيع مخصصة (مو من قوائم المواد)، بدون درجة،
        // ونستبعد ملاحظات الأهل وردود الأنسة
        $subjects = DailyChange::whereHas('child', function($q) use ($classOnly, $sectionOnly) {
            $q->where('class', $classOnly)
              ->when($sectionOnly, fn($sq) => $sq->where('section', $sectionOnly))
              ->where('school_id', $this->school()?->id);
        })->whereNull('score')
          ->whereNotIn('source', ['parent_note', 'teacher_reply'])
          ->when(!empty($schoolSubjects), function($q) use ($schoolSubjects) {
              $q->where(function($s) use ($schoolSubjects) {
                  $s->whereNotIn('subject', $schoolSubjects)
                    ->orWhereNull('subject');
              });
          })
          ->pluck('subject')->unique()->filter();

        $students = $this->childrenQuery()
            ->where('class', $classOnly)
            ->when($sectionOnly, fn($q) => $q->where('section', $sectionOnly))
            ->when($searchName, fn($q) => $q->where('name', 'like', "%{$searchName}%"))
            ->with(['dailyChanges' => function($q) use ($searchSubject, $schoolSubjects) {
                $q->whereNull('score')
                  ->whereNotIn('source', ['parent_note', 'teacher_reply'])
                  ->when(!empty($schoolSubjects), function($sq) use ($schoolSubjects) {
                      $sq->where(function($s) use ($schoolSubjects) {
                          $s->whereNotIn('subject', $schoolSubjects)
                            ->orWhereNull('subject');
                      });
                  });
                if ($searchSubject) {
                    $q->where('subject', $searchSubject);
                }
            }])
            ->get()
            ->filter(fn($student) => $student->dailyChanges->isNotEmpty())
            ->unique('id');

        return view('manager.class_notes', compact('students', 'className', 'subjects', 'searchName', 'searchSubject'));
    }

    // تحديث إعدادات مظهر المدرسة (اللوغو واللون) — على المدير والمدرسة معاً
    public function updateBranding(Request $request)
    {
        $this->checkManager();
        $manager = Auth::user();
        $school = $this->school();

        if (!$school) {
            return redirect()->back()->with('error', 'لا توجد مدرسة مرتبطة بحسابك.');
        }

        $request->validate([
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'primary_color' => 'required|string|max:7',
        ]);

        if ($request->hasFile('logo')) {
            $school->logo = $request->file('logo')->store('school_logos', 'public');
        }

        $school->color = $request->primary_color;

        $school->save();

        return redirect()->back()->with('success', 'تم تحديث مظهر المدرسة (اللوغو والألوان) بنجاح.');
    }

    // =========================================
    // إدارة الصفوف والشعب والمواد (تُحفظ في جدول schools كـ JSON)
    // =========================================

    /** إضافة عنصر (صف / شعبة / مادة) لقوائم المدرسة */
    public function addSchoolSetting(Request $request)
    {
        $this->checkManager();
        $school = $this->school();

        if (!$school) {
            return redirect()->back()->with('error', 'لا توجد مدرسة مرتبطة بحسابك.');
        }

        $request->validate([
            'type'  => 'required|in:class,section,subject',
            'value' => 'required|string|max:100',
        ], [
            'value.required' => 'القيمة مطلوبة.',
            'value.max'      => 'القيمة طويلة جداً (الحد 100 حرف).',
        ]);

        $columnMap = [
            'class'   => 'classes',
            'section' => 'sections',
            'subject' => 'subjects',
        ];

        $column  = $columnMap[$request->type];
        $list    = collect($school->{$column} ?? []);
        $newItem = trim($request->value);

        if ($list->contains(fn ($item) => mb_strtolower(trim($item)) === mb_strtolower($newItem))) {
            return redirect()->back()->with('error', 'هذا العنصر موجود مسبقاً.');
        }

        $list->push($newItem);
        $school->{$column} = $list->values()->all();
        $school->save();

        $label = ['class' => 'الصف', 'section' => 'الشعبة', 'subject' => 'المادة'][$request->type];

        return redirect()->back()->with('success', "تمت إضافة {$label}: {$newItem}");
    }

    /** حذف عنصر من قوائم المدرسة */
    public function removeSchoolSetting(Request $request)
    {
        $this->checkManager();
        $school = $this->school();

        if (!$school) {
            return redirect()->back()->with('error', 'لا توجد مدرسة مرتبطة بحسابك.');
        }

        $request->validate([
            'type'  => 'required|in:class,section,subject',
            'value' => 'required|string',
        ]);

        $columnMap = [
            'class'   => 'classes',
            'section' => 'sections',
            'subject' => 'subjects',
        ];

        $column = $columnMap[$request->type];
        $list   = collect($school->{$column} ?? [])
            ->reject(fn ($item) => trim($item) === trim($request->value))
            ->values();

        $school->{$column} = $list->all();
        $school->save();

        $label = ['class' => 'الصف', 'section' => 'الشعبة', 'subject' => 'المادة'][$request->type];

        return redirect()->back()->with('success', "تم حذف {$label}: {$request->value}");
    }

    /** قبول طلب انضمام ولي أمر */
    public function approveJoinRequest(\App\Models\SchoolJoinRequest $joinRequest)
    {
        $this->checkManager();
        $school = $this->school();

        if ($joinRequest->school_id !== $school?->id) {
            abort(403, 'هذا الطلب لا يخص مدرستك.');
        }
        if ($joinRequest->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذا الطلب مسبقاً.');
        }

        // قبول الطلب + إضافة المدرسة فعلياً لولي الأمر
        $joinRequest->update([
            'status'       => 'approved',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $joinRequest->parent->schools()->syncWithoutDetaching([$school->id]);

        return back()->with('success', 'تم قبول طلب انضمام (' . $joinRequest->parent->name . ') وإضافة المدرسة لحسابه.');
    }
    public function readNotification($id)
{
    $this->checkManager();
    
    $notification = auth()->user()->notifications()->where('id', $id)->first();

    if ($notification) {
        $notification->markAsRead();
    }

    // التوجيه المباشر إلى صفحة المهام لمنع أخطاء 404 أو التوجيه للرئيسية
    return redirect()->route('manager.tasks');
}

    /** رفض طلب انضمام ولي أمر */
    public function rejectJoinRequest(\App\Models\SchoolJoinRequest $joinRequest)
    {
        $this->checkManager();
        $school = $this->school();

        if ($joinRequest->school_id !== $school?->id) {
            abort(403, 'هذا الطلب لا يخص مدرستك.');
        }
        if ($joinRequest->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذا الطلب مسبقاً.');
        }

        $joinRequest->update([
            'status'       => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'تم رفض الطلب.');
    }
}