<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\DailyChange;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ChildAssignedNotification;
use App\Notifications\DailyChangeAddedNotification;

class TeacherController extends Controller
{
    /** اختصار: مدرسة المعلم الحالي */
    private function school(): ?School
    {
        return Auth::user()->school;
    }

    private function checkTeacher()
    {
        if (auth()->user()->role !== User::ROLE_TEACHER || auth()->user()->status !== User::STATUS_APPROVED) {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }
    // 1. الداش بورد الرئيسي (المهام وأحدث النشاطات مع استبعاد الطلاب المؤرشفين)
    public function dashboard()
    {
        $this->checkTeacher();
        $teacherId = auth()->id();

        // 1. إجمالي الطلاب المفعلين المربوطين بحساب المعلم (استبعاد المؤرشفين)
        $totalStudents = Child::whereHas('teachers', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->where(function($q) {
            $q->where('status', 'active')->orWhereNull('status');
        })->count();

        // 2. إجمالي عدد الصفوف الفريدة للطلاب المفعلين فقط
        $totalClasses = Child::whereHas('teachers', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->where(function($q) {
            $q->where('status', 'active')->orWhereNull('status');
        })->pluck('class')->unique()->filter()->count();

        // 3. إجمالي السجلات والتقييمات المدخلة للطلاب المفعلين
        $totalLogs = DailyChange::where('teacher_id', $teacherId)
            ->whereHas('child', function($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->count();

        // 4. أحدث 5 سجلات مضافة مؤخراً للطلاب المفعلين
        $recentRecords = DailyChange::where('teacher_id', $teacherId)
            ->whereHas('child', function($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('totalStudents', 'totalClasses', 'totalLogs', 'recentRecords'));
    }

    public function profile()
    {
        $this->checkTeacher();
        $teacher = Auth::user();
        return view('teacher.profile', compact('teacher'));
    }

    // 2. حفظ وتحديث معلومات المعلم في قاعدة البيانات
    public function updateProfile(Request $request)
    {
        $this->checkTeacher();
        $teacher = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $teacher->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $teacher->name = $request->name;
        $teacher->email = $request->email;

        if ($request->filled('password')) {
            $teacher->password = Hash::make($request->password);
        }

        $teacher->save();

        return redirect()->route('teacher.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }

    // صفحة نموذج إضافة طفل
    public function createChild()
    {
        $this->checkTeacher();

        $parents = $this->availableParents();
        $school = $this->school();

        return view('teacher.create_child', compact('parents', 'school'));
    }

    /**
     * أولياء الأمور المتاحون للمعلمة:
     * - أولياء أمور مدرستها (approved)
     * - وأولياء أمور بدون مدرسة (school_id = NULL) لكي يمكن ربطهم عند الإضافة
     */
    private function availableParents()
    {
        $schoolId = $this->school()?->id;

        // أولياء الأمور المرتبطون بمدرسة المعلم عبر الجدول الوسيط (school_parent)
        return User::query()
            ->role(User::ROLE_PARENT)
            ->status(User::STATUS_APPROVED)
            ->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->orWhereNull('school_id')
                  ->orWhereHas('schools', fn ($s) => $s->where('schools.id', $schoolId));
            })
            ->orderBy('name')
            ->get();
    }

    // حفظ الطفل الجديد وربطه بمدير المدرسة المعني تلقائياً
    public function storeChild(Request $request)
    {
        $this->checkTeacher();

        $request->validate([
            'name'          => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'age'           => 'required|integer|min:1|max:20',
            'class'         => 'required|string|max:255',
            'section'       => 'required|string|max:100',
            'health_status' => 'nullable|string',
            'parent_id'     => 'nullable|exists:users,id',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // التحقق من أن الصف والشعبة من قوائم المدرسة (التي أضافها المدير)
        $school = $this->school();
        if ($school) {
            if (!in_array($request->class, $school->classes ?? [])) {
                return back()->withErrors(['class' => 'الصف المختار غير موجود في قوائم المدرسة.'])->withInput();
            }
            if (!in_array($request->section, $school->sections ?? [])) {
                return back()->withErrors(['section' => 'الشعبة المختارة غير موجودة في قوائم المدرسة.'])->withInput();
            }
        }

        $teacher = Auth::user();

        // 1. الطالب الجديد يرتبط مباشرة بمدرسة المعلم (المدرسة هي الكيان المركزي)
        // ويُسند أيضًا لأول مدير في المدرسة للحفاظ على التوافق مع العروض القديمة.
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('children_images', 'public');
        }

        // 2. إنشاء الطالب الجديد وتعيين حالته تلقائياً كـ active
        // ربط ولي الأمر بالمدرسة في الجدول الوسيط (والتحديث المتبقي لschool_id للتوافق)
        if ($request->parent_id) {
            $selectedParent = User::find($request->parent_id);
            if ($selectedParent && $this->school()) {
                $selectedParent->schools()->syncWithoutDetaching([$this->school()->id]);
                if (is_null($selectedParent->school_id)) {
                    $selectedParent->update(['school_id' => $this->school()->id]);
                }
            }
        }

        $child = Child::create([
            'name'          => $request->name,
            'gender'        => $request->gender,
            'age'           => $request->age,
            'class'         => $request->class,
            'section'       => $request->section,
            'health_status' => $request->health_status ?? '',
            'parent_id'     => $request->parent_id,
            'school_id'     => $this->school()?->id, // الربط الأساسي
            'image'         => $imagePath,
            'status'        => 'active',
        ]);

        // 3. البحث عن جميع الأساتذة في نفس المدرسة الذين لديهم طلاب في هذا الصف
        $teachersInSameClassAndSchool = User::query()
            ->role(User::ROLE_TEACHER)
            ->school($this->school())
            ->whereHas('students', function ($query) use ($request) {
                $query->where('class', $request->class);
            })
            ->pluck('id')
            ->toArray();

        if (!in_array($teacher->id, $teachersInSameClassAndSchool)) {
            $teachersInSameClassAndSchool[] = $teacher->id;
        }

        // 4. ربط الطالب بجميع أساتذة هذا الصف دفعة واحدة
        $child->teachers()->sync($teachersInSameClassAndSchool);

        // 5. إرسال إشعار لولي الأمر (الأم)
        if ($child->parent_id) {
            $parent = User::find($child->parent_id);
            if ($parent) {
                $parent->notify(new \App\Notifications\ChildAssignedNotification($child));
            }
        }

        return redirect()->route('teacher.classes')->with('success', 'تم إضافة الطالب بنجاح وربطه بجميع أساتذة الصف.');
    }

    // 3. صفحة الصفوف المسؤول عنها (مصفاة بالكامل لتستبعد المؤرشفين)
   public function classes(Request $request)
{
    $this->checkTeacher();

    $teacher = auth()->user();
    $selectedYear = $request->get('year', date('Y'));
    $selectedClass = $request->get('class_name');
    $search = $request->input('search');

    // 1. جلب الأعوام الدراسية المتاحة للطلاب المفعلين فقط
    $availableYears = $teacher->students()
        ->where(function($q) {
            $q->where('children.status', 'active')->orWhereNull('children.status');
        })
        ->selectRaw('YEAR(children.created_at) as year')
        ->distinct()
        ->pluck('year')
        ->push((int)date('Y'))
        ->unique()
        ->sortDesc();

    // 2. جلب وتحديد قائمة الصفوف المتاحة للطلاب المفعلين فقط
    $availableClasses = $teacher->students()
        ->where(function($q) {
            $q->where('children.status', 'active')->orWhereNull('children.status');
        })
        ->pluck('children.class')
        ->unique()
        ->filter()
        ->values();

    // 3. بناء استعلام جلب الطلاب واستبعاد أي طالب مؤرشف
    $query = $teacher->students()
        ->with(['dailyChanges', 'parent'])
        ->where(function($q) {
            $q->where('children.status', 'active')->orWhereNull('children.status');
        })
        ->where(function($q) use ($selectedYear) {
            $q->whereYear('children.created_at', $selectedYear)
              ->orWhere('children.class', 'LIKE', "%{$selectedYear}%");
        });

    if ($selectedClass) {
        $query->where('children.class', $selectedClass);
    }

    // فلتر الشعبة
    $selectedSection = $request->get('section_name');
    if ($selectedSection) {
        $query->where('children.section', $selectedSection);
    }

    if ($search) {
        $query->where('children.name', 'like', "%{$search}%");
    }

    $students = $query->get();

    // حساب المعدل العام لكل طالب بناءً على الدرجات المسجلة في dailyChanges
    $students->transform(function ($student) {
        $scores = $student->dailyChanges->whereNotNull('score')->pluck('score');
        $student->average_score = $scores->count() > 0 ? round($scores->avg(), 1) : null;
        return $student;
    });

    // 4. ترتيب الصفوف مدرسياً
    $classOrder = [
        'الصف الأول'   => 1,
        'الصف الثاني'  => 2,
        'الصف الثالث'  => 3,
        'الصف الرابع'  => 4,
        'الصف الخامس'  => 5,
        'الصف السادس'  => 6,
        'الصف السابع'  => 7,
        'الصف الثامن'  => 8,
        'الصف التاسع'  => 9,
        'الصف العاشر'  => 10,
        'الصف الحادي عشر'  => 11,
        'الصف الثاني عشر'  => 12,
    ];

    // قائمة الشعب والمواد المتاحة (من قوائم المدرسة التي أضافها المدير)
    $availableSections = $this->school()?->sections ?? [];
    $availableSubjects = $this->school()?->subjects ?? [];

    // 5. تجميع الطلاب هرمياً حسب الصف أولاً، ثم حسب الشعبة داخله
    $groupedClasses = $students->groupBy('class')->sortKeysUsing(function ($a, $b) use ($classOrder) {
        $numA = $classOrder[$a] ?? 99;
        $numB = $classOrder[$b] ?? 99;
        return ($numA === $numB) ? strcmp($a, $b) : ($numA <=> $numB);
    })->map(function ($studentsInClass) {
        // داخل كل صف، نقوم بتجميع الطلاب حسب الشعبة
        return $studentsInClass->groupBy(function ($student) {
            return $student->section ?: 'عامة';
        });
    });

    return view('teacher.classes', compact(
        'groupedClasses', 'availableYears', 'selectedYear', 'availableClasses',
        'availableSections', 'availableSubjects', 'selectedClass', 'selectedSection', 'search'
    ));
}

    /**
     * مفك مفتاح البطاقة المركب: "الصف X - الشعبة ص" → [اسم الصف، الشعبة]
     * لأن أزرار العرض بتستقبل المفتاح الكامل من صفحة صفوفي وطلابي.
     */
    private function splitClassName(?string $composite): array
    {
        $composite = trim((string)$composite);

        if (preg_match('/^(.*?)\s*-\s*الشعبة\s*(.+)$/u', $composite, $m)) {
            return [trim($m[1]), trim($m[2])];
        }

        return [$composite, null];
    }

    // 4. صفحة عرض العلامات الخاصة بصف معين (للطلاب المفعلين فقط)
    // 4. صفحة عرض العلامات الخاصة بشعبة صف معين (للطلاب المفعلين فقط)
public function classScores(Request $request, $className)
    {
        $this->checkTeacher();
        $teacher = auth()->user();

        $studentName = $request->input('student_name');
        $subject     = $request->input('subject');
        $date        = $request->input('date');

        [$classOnly, $sectionOnly] = $this->splitClassName($className);

        // جلب معرفات الطلاب المفعلين في هذا الصف وهذه الشعبة حصراً
        $studentIds = $teacher->students()
            ->where('children.class', $classOnly)
            ->when($sectionOnly, function($q) use ($sectionOnly) {
                $q->where('children.section', $sectionOnly);
            })
            ->where(function($q) {
                $q->where('children.status', 'active')->orWhereNull('children.status');
            })
            ->when($studentName, function($q) use ($studentName) {
                $q->where('children.name', 'like', "%{$studentName}%");
            })
            ->pluck('children.id');

        $schoolSubjects = $this->school()?->subjects ?? [];

        $query = DailyChange::whereIn('child_id', $studentIds)
            ->whereNotNull('score')
            ->whereNotIn('source', ['parent_note', 'teacher_reply'])
            ->when(!empty($schoolSubjects), function($q) use ($schoolSubjects) {
                $q->whereIn('subject', $schoolSubjects);
            });

        if ($subject) {
            $query->where('subject', 'like', "%{$subject}%");
        }

        if ($date) {
            $query->whereDate('recorded_at', $date);
        }

        $dailyChanges = $query->join('children', 'daily_changes.child_id', '=', 'children.id')
            ->orderBy('children.name', 'asc')
            ->select('daily_changes.*')
            ->paginate(10)
            ->appends($request->all());

        return view('teacher.class_scores', compact('className', 'dailyChanges', 'studentName', 'subject', 'date'));
    }

    public function classNotes(Request $request, $className)
    {
        $this->checkTeacher();
        $teacher = auth()->user();

        $studentName = $request->input('student_name');
        $subject     = $request->input('subject');
        $date        = $request->input('date');

        [$classOnly, $sectionOnly] = $this->splitClassName($className);

        // جلب معرفات الطلاب المفعلين في هذا الصف وهذه الشعبة حصراً
        $studentIds = $teacher->students()
            ->where('children.class', $classOnly)
            ->when($sectionOnly, function($q) use ($sectionOnly) {
                $q->where('children.section', $sectionOnly);
            })
            ->where(function($q) {
                $q->where('children.status', 'active')->orWhereNull('children.status');
            })
            ->when($studentName, function($q) use ($studentName) {
                $q->where('children.name', 'like', "%{$studentName}%");
            })
            ->pluck('children.id');

        $schoolSubjects = $this->school()?->subjects ?? [];

        $query = DailyChange::whereIn('child_id', $studentIds)
            ->when(!empty($schoolSubjects), function($q) use ($schoolSubjects) {
                $q->where(function($sub) use ($schoolSubjects) {
                    $sub->whereNotIn('subject', $schoolSubjects)
                        ->orWhereNull('subject');
                });
            }, function($q) {
                $q->whereNull('score');
            })
            ->whereNotIn('source', ['parent_note', 'teacher_reply'])
            ->with('child');

        if ($subject) {
            $query->where('subject', 'like', "%{$subject}%");
        }

        if ($date) {
            $query->whereDate('recorded_at', $date);
        }

        $dailyChanges = $query->leftJoin('children', 'daily_changes.child_id', '=', 'children.id')
            ->orderBy('children.name', 'asc')
            ->select('daily_changes.*')
            ->paginate(10)
            ->appends($request->all());

        return view('teacher.class_notes', compact('className', 'dailyChanges', 'studentName', 'subject', 'date'));
    }
    // 6. صفحة السجلات المدخلة مرتبة حسب التاريخ والوقت (تاريخ اليوم افتراضياً + الترقيم 10 بـ 10)
    public function dailyChanges(Request $request)
{
    $this->checkTeacher();

    $teacher = auth()->user();
    $studentIds = $teacher->students()
        ->where(function($q) {
            $q->where('children.status', 'active')->orWhereNull('children.status');
        })->pluck('children.id');

    // 1. تحديد تاريخ اليوم كافتراضي إذا لم يتم اختيار تاريخ محدد من المعلم
    $selectedDate = $request->input('date', date('Y-m-d'));

    // 2. استعلام السجلات اليومية المصفاة مع جلب بيانات الطالب (class & section)
    $query = DailyChange::whereIn('child_id', $studentIds)->with('child');

    if ($selectedDate) {
        $query->whereDate('recorded_at', $selectedDate);
    }

    // جلب السجلات مقسمة 10 بـ 10
    $dailyChanges = $query->orderBy('recorded_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    // جلب طلاب المعلم المفعلين فقط مجمعين حسب الصف لنموذج الإضافة
    $students = $teacher->students()
        ->where(function($q) {
            $q->where('children.status', 'active')->orWhereNull('children.status');
        })->get();

    // تجميع حسب مفتاح مركب "الصف||الشعبة" (للتسلسل: صف ← شعبة ← طفل)
    $studentsGroupedByClassSection = $students->groupBy(function ($child) {
        return $child->class . '||' . ($child->section ?? '');
    })->map(function ($group) {
        return $group->map(fn ($s) => ['id' => $s->id, 'name' => $s->name]);
    });

    // قائمة المواد من قوائم المدرسة (التي أضافها المدير)
    $schoolSubjects = $this->school()?->subjects ?? [];

    return view('teacher.daily_changes', compact(
        'dailyChanges', 'studentsGroupedByClassSection', 'schoolSubjects', 'selectedDate'
    ));
}
    // 7. صفحة سجل الطالب المنفرد (مع الترقيم 10 بـ 10 والفلترة)
    public function studentRecords(Request $request, $studentId)
    {
        $this->checkTeacher();
        $teacher = auth()->user();

        // التأكد من أن الطالب مرتبط بهذا المعلم وغير مؤرشف
        $student = $teacher->students()
            ->where('children.id', $studentId)
            ->where(function($q) {
                $q->where('children.status', 'active')->orWhereNull('children.status');
            })
            ->firstOrFail();

        // بدء استعلام السجلات — نستبعد ملاحظات الأهل وردود الأنسة
        // (هي عندها صفحة لحالها "ملاحظات الأهل" لهيك)
        $query = DailyChange::where('child_id', $student->id)
            ->whereNotIn('source', ['parent_note', 'teacher_reply']);

        // 1. الفلترة حسب التاريخ (إذا أُدخل)
        if ($request->filled('date')) {
            $query->whereDate('recorded_at', $request->date);
        }

        // 2. الفلترة حسب اسم المادة (بحث جزئي)
        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        // جلب السجلات مرتبة بالتاريخ تنازلياً ومع الترقيم 10 بـ 10
        $dailyChanges = $query->orderBy('recorded_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('teacher.student_records', compact('student', 'dailyChanges'));
    }

    public function storeDailyChange(Request $request)
    {
        $this->checkTeacher();

        $request->validate([
            'child_id'    => 'required|exists:children,id',
            'recorded_at' => 'required|date',
            'subject'     => 'required|string|max:255',
            'note'        => 'nullable|string',
            'score'       => 'nullable|numeric|min:0|max:100',
        ]);

        $dailyChange = DailyChange::create([
            'child_id'    => $request->child_id,
            'teacher_id'  => auth()->id(),
            'recorded_at' => $request->recorded_at,
            'subject'     => $request->subject,
            'note'        => $request->note,
            'score'       => $request->score,
        ]);

        $child = Child::find($request->child_id);
        if ($child && $child->parent_id) {
            $parent = User::find($child->parent_id);
            if ($parent) {
                $parent->notify(new \App\Notifications\DailyChangeAddedNotification($child, $dailyChange));
            }
        }

        return back()->with('success', 'تم حفظ السجل اليومي بنجاح وإرسال الإشعار لولي الأمر.');
    }

    public function updateDailyChange(Request $request, DailyChange $dailyChange)
    {
        $this->checkTeacher();

        if ($dailyChange->teacher_id !== auth()->id()) {
            return back()->with('error', 'غير مصرح لك بتعديل هذا السجل.');
        }

        $request->validate([
            'recorded_at' => 'required|date',
            'subject'     => 'required|string|max:255',
            'note'        => 'nullable|string',
            'score'       => 'nullable|numeric|min:0|max:100',
        ]);

        $dailyChange->update([
            'recorded_at' => $request->recorded_at,
            'subject'     => $request->subject,
            'note'        => $request->note,
            'score'       => $request->score,
        ]);

        $child = $dailyChange->child;
        if ($child && $child->parent_id) {
            $parent = User::find($child->parent_id);
            if ($parent) {
                $parent->notify(new \App\Notifications\DailyChangeAddedNotification($child, $dailyChange));
            }
        }

        return back()->with('success', 'تم تعديل السجل بنجاح وإشعار ولي الأمر.');
    }

    public function destroyDailyChange(DailyChange $dailyChange)
    {
        $this->checkTeacher();

        if ($dailyChange->teacher_id !== auth()->id()) {
            return back()->with('error', 'غير مصرح لك بحذف هذا السجل.');
        }

        $dailyChange->delete();

        return back()->with('success', 'تم حذف السجل بنجاح.');
    }

    public function editChild(Child $child)
    {
        $this->checkTeacher();

        $parents = $this->availableParents();

        return view('teacher.edit_child', compact('child', 'parents'))->with('school', $this->school());
    }

    public function updateChild(Request $request, Child $child)
    {
        $this->checkTeacher();

        $request->validate([
            'name'          => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'age'           => 'required|integer|min:1|max:20',
            'class'         => 'required|string|max:255',
            'section'       => 'required|string|max:100',
            'health_status' => 'nullable|string',
            'parent_id'     => 'nullable|exists:users,id',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // التحقق من قوائم المدرسة
        $school = $this->school();
        if ($school) {
            if (!in_array($request->class, $school->classes ?? [])) {
                return back()->withErrors(['class' => 'الصف المختار غير موجود في قوائم المدرسة.'])->withInput();
            }
            if (!in_array($request->section, $school->sections ?? [])) {
                return back()->withErrors(['section' => 'الشعبة المختارة غير موجودة في قوائم المدرسة.'])->withInput();
            }
        }

        $imagePath = $child->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('children_images', 'public');
        }

        // ربط ولي الأمر بالمدرسة في الجدول الوسيط (والتحديث المتبقي للتوافق)
        if ($request->parent_id) {
            $selectedParent = User::find($request->parent_id);
            if ($selectedParent && $this->school()) {
                $selectedParent->schools()->syncWithoutDetaching([$this->school()->id]);
                if (is_null($selectedParent->school_id)) {
                    $selectedParent->update(['school_id' => $this->school()->id]);
                }
            }
        }

        $child->update([
            'name'          => $request->name,
            'gender'        => $request->gender,
            'age'           => $request->age,
            'class'         => $request->class,
            'section'       => $request->section,
            'health_status' => $request->health_status,
            'parent_id'     => $request->parent_id,
            'image'         => $imagePath,
        ]);

        return redirect()->route('teacher.classes')->with('success', 'تم تعديل بيانات الطالب بنجاح.');
    }

    public function destroyChild(Child $child)
    {
        $this->checkTeacher();
        $child->delete();
        return redirect()->route('teacher.classes')->with('success', 'تم حذف الطالب بنجاح.');
    }

    public function destroyProfile(Request $request)
    {
        $this->checkTeacher();
        $teacher = Auth::user();

        if (method_exists($teacher, 'students')) {
            $teacher->students()->detach();
        }

        $teacher->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'تم حذف حسابك الشخصي بنجاح، وباقي بيانات الطلاب محفوظة.');
    }
public function parentNotes(Request $request)
{
    $teacher = auth()->user();
    $search  = $request->input('search');

    // جلب ملاحظات الأهل (source = parent_note) من جدول daily_changes لطلاب المعلمة
    $studentsWithNotes = \App\Models\DailyChange::where('source', 'parent_note')
        ->whereHas('child', function($q) use ($teacher, $search) {
            $q->whereHas('teachers', function($tq) use ($teacher) {
                $tq->where('users.id', $teacher->id);
            })
            ->when($search, function($sq) use ($search) {
                $sq->where('name', 'like', "%{$search}%");
            });
        })
        ->with(['child.parent', 'replies.teacher'])
        ->orderBy('recorded_at', 'desc')
        ->get()
        ->groupBy('child_id');

    return view('teacher.parent_notes', compact('studentsWithNotes', 'search'));
}

public function readNotification($id)
{
    $notification = auth()->user()->notifications()->where('id', $id)->first();

    if ($notification) {
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? route('teacher.parent-notes'));
    }

    return redirect()->back();
}



public function replyToParentNote(Request $request, \App\Models\DailyChange $note)
{
    $request->validate([
        'note' => 'required|string|max:1000',
    ]);

    // حفظ رد المعلمة كسجل جديد في daily_changes مربوط بملاحظة الأم الأصلية
    \App\Models\DailyChange::create([
        'child_id'       => $note->child_id,
        'teacher_id'     => auth()->id(),
        'recorded_at'    => now(),
        'subject'        => 'رد المعلمة',
        'note'           => $request->note,
        'score'          => null,
        'source'         => 'teacher_reply',
        'parent_note_id' => $note->id,
    ]);

    // إرسال إشعار لولي الأمر (الأم) صاحبة الملاحظة
    $parentUser = $note->parent;
    if (!$parentUser && $note->child && $note->child->parent_id) {
        $parentUser = \App\Models\User::find($note->child->parent_id);
    }

    if ($parentUser) {
        try {
            $childName = $note->child?->name ?? 'الطفل';
            $parentUser->notify(new \App\Notifications\TeacherReplyNotification($childName, $request->note));
        } catch (\Exception $e) {}
    }

    return back()->with('success', 'تم إرسال الرد بنجاح.');
}
}