<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\DailyChange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    // 1. الداش بورد الرئيسي (المهام وأحدث النشاطات)
    public function dashboard()
    {
        $this->checkTeacher();
        $teacherId = auth()->id();

        // 1. إجمالي الطلاب المربوطين بحساب المعلم
        $totalStudents = Child::whereHas('teachers', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->count();

        // 2. إجمالي عدد الصفوف الفريدة التي يدرسها المعلم
        $totalClasses = Child::whereHas('teachers', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->pluck('class')->unique()->filter()->count();

        // 3. إجمالي السجلات والتقييمات المدخلة
        $totalLogs = DailyChange::where('teacher_id', $teacherId)->count();

        // 4. أحدث 5 سجلات مضافة مؤخراً
        $recentRecords = DailyChange::where('teacher_id', $teacherId)
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

        $parents = User::where('role', 'parent')
            ->where('status', 'approved')
            ->get();

        return view('teacher.create_child', compact('parents'));
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
            'health_status' => 'nullable|string',
            'parent_id'     => 'nullable|exists:users,id',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $teacher = Auth::user();

        // 1. جلب المدير المسؤول عن مدرسة هذا المعلم لربط الطالب به
        $manager = User::where('role', 'manager')
            ->where('school_name', $teacher->school_name)
            ->first();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('children_images', 'public');
        }

        // 2. إنشاء الطالب الجديد
        $child = Child::create([
            'name'          => $request->name,
            'gender'        => $request->gender,
            'age'           => $request->age,
            'class'         => $request->class,
            'health_status' => $request->health_status,
            'parent_id'     => $request->parent_id,
            'manager_id'    => $manager ? $manager->id : null,
            'image'         => $imagePath,
        ]);

        // 3. البحث عن جميع الأساتذة في نفس المدرسة الذين لديهم طلاب في هذا الصف
        $teachersInSameClassAndSchool = User::where('role', 'teacher')
            ->where('school_name', $teacher->school_name)
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

        return redirect()->route('teacher.classes')->with('success', 'تم إضافة الطالب بنجاح وربطه بجميع أساتذة الصف.');
    }

    // 3. صفحة الصفوف المسؤول عنها مع دعم البحث وفلترة العام الدراسي
    // صفحة الصفوف المسؤول عنها مع دعم البحث باسم الطالب وفلترة الصف والعام الدراسي
// 
// صفحة الصفوف المسؤول عنها مع ترتيب الصفوف أبجدياً/رقمياً
public function classes(Request $request)
{
    $this->checkTeacher();

    $teacher = auth()->user();
    $selectedYear = $request->get('year', date('Y'));
    $selectedClass = $request->get('class_name');
    $search = $request->input('search');

    // 1. جلب الأعوام الدراسية المتاحة
    $availableYears = $teacher->students()
        ->selectRaw('YEAR(children.created_at) as year')
        ->distinct()
        ->pluck('year')
        ->push((int)date('Y'))
        ->unique()
        ->sortDesc();

    // 2. جلب وتحديد قائمة الصفوف المتاحة
    $availableClasses = $teacher->students()
        ->pluck('children.class')
        ->unique()
        ->filter()
        ->values();

    // 3. بناء استعلام جلب الطلاب
    $query = $teacher->students()
        ->with(['dailyChanges', 'parent'])
        ->where(function($q) use ($selectedYear) {
            $q->whereYear('children.created_at', $selectedYear)
              ->orWhere('children.class', 'LIKE', "%{$selectedYear}%");
        });

    if ($selectedClass) {
        $query->where('children.class', $selectedClass);
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

    // 4. ترتيب الصفوف مدرسياً (الأول، الثاني، الثالث...)
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

    $groupedClasses = $students->groupBy('class')->sortBy(function ($students, $className) use ($classOrder) {
        return $classOrder[$className] ?? 99;
    });

    return view('teacher.classes', compact('groupedClasses', 'availableYears', 'selectedYear', 'availableClasses', 'selectedClass', 'search'));
}

    // 4. صفحة عرض العلامات الخاصة بصف معين
public function classScores(Request $request, $className)
{
    $this->checkTeacher();
    $teacher = auth()->user();
    
    $studentName = $request->input('student_name'); // بحث باسم الطالب
    $subject = $request->input('subject');         // بحث باسم المادة
    $date = $request->input('date');               // بحث بالتاريخ

    // 1. جلب معرفات طلاب هذا الصف مع إمكانية الفلترة باسم الطالب
    $studentIds = $teacher->students()
        ->where('class', $className)
        ->when($studentName, function($q) use ($studentName) {
            $q->where('name', 'like', "%{$studentName}%");
        })
        ->pluck('children.id');

    // 2. استعلام جلب سجلات العلامات بناءً على الطلاب المصفين
    $query = DailyChange::whereIn('child_id', $studentIds)
        ->whereNotNull('score')
        ->with('child');

    // الفلترة حسب المادة إذا تم تحديدها
    if ($subject) {
        $query->where('subject', 'like', "%{$subject}%");
    }

    // الفلترة حسب التاريخ إذا تم تحديده
    if ($date) {
        $query->whereDate('recorded_at', $date);
    }

    $dailyChanges = $query->orderBy('recorded_at', 'desc')->get();

    return view('teacher.class_scores', compact('className', 'dailyChanges', 'studentName', 'subject', 'date'));
}

    // 5. صفحة عرض الملاحظات الخاصة بصف معين
   public function classNotes(Request $request, $className)
{
    $this->checkTeacher();
    $teacher = auth()->user();

    $studentName = $request->input('student_name'); // بحث باسم الطالب
    $subject     = $request->input('subject');      // بحث بالموضوع / المادة
    $date        = $request->input('date');         // بحث بالتاريخ

    // 1. جلب معرفات طلاب هذا الصف مع الفلترة باسم الطالب إن وجد
    $studentIds = $teacher->students()
        ->where('class', $className)
        ->when($studentName, function($q) use ($studentName) {
            $q->where('name', 'like', "%{$studentName}%");
        })
        ->pluck('children.id');

    // 2. استعلام جلب سجلات الملاحظات فقط (ملاحظة غير فارغة)
    $query = DailyChange::whereIn('child_id', $studentIds)
        ->whereNotNull('note')
        ->where('note', '!=', '')
        ->with('child');

    // الفلترة حسب الموضوع / المادة
    if ($subject) {
        $query->where('subject', 'like', "%{$subject}%");
    }

    // الفلترة حسب التاريخ
    if ($date) {
        $query->whereDate('recorded_at', $date);
    }

    $dailyChanges = $query->orderBy('recorded_at', 'desc')->get();

    return view('teacher.class_notes', compact('className', 'dailyChanges', 'studentName', 'subject', 'date'));
}

    // 6. صفحة السجلات المدخلة مرتبة حسب التاريخ والوقت
public function dailyChanges(Request $request)
{
    $this->checkTeacher();

    $teacher = auth()->user();
    $studentIds = $teacher->students()->pluck('children.id');

    // جلب السجلات المصفاة
    $query = DailyChange::whereIn('child_id', $studentIds)->with('child');

    if ($request->filled('date')) {
        $query->whereDate('recorded_at', $request->date);
    }

    $dailyChanges = $query->orderBy('recorded_at', 'desc')->get();

    // جلب طلاب المعلم مجمعين حسب الصف لفرز القائمة
    $students = $teacher->students()->get();
    $studentsGroupedByClass = $students->groupBy('class');

    return view('teacher.daily_changes', compact('dailyChanges', 'studentsGroupedByClass'));
}
    // عرض سجل الطالب الكامل (الملاحظات والعلامات)
public function studentRecords($studentId)
{
    $this->checkTeacher();
    $teacher = auth()->user();

    // التأكد من أن الطالب مرتبط بهذا المعلم
    $student = $teacher->students()->where('children.id', $studentId)->firstOrFail();

    // جلب كل السجلات الخاصة بهذا الطالب مرتبة تنازلياً
    $dailyChanges = DailyChange::where('child_id', $student->id)
        ->orderBy('recorded_at', 'desc')
        ->get();

    return view('teacher.student_records', compact('student', 'dailyChanges'));
}

    // إضافة سجل يومي جديد
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

        DailyChange::create([
            'child_id'    => $request->child_id,
            'teacher_id'  => auth()->id(),
            'recorded_at' => $request->recorded_at,
            'subject'     => $request->subject,
            'note'        => $request->note,
            'score'       => $request->score,
        ]);

        return back()->with('success', 'تم حفظ السجل اليومي بنجاح.');
    }

    // تعديل سجل يومي
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

        return back()->with('success', 'تم تعديل السجل بنجاح.');
    }

    // حذف سجل يومي
    public function destroyDailyChange(DailyChange $dailyChange)
    {
        $this->checkTeacher();

        if ($dailyChange->teacher_id !== auth()->id()) {
            return back()->with('error', 'غير مصرح لك بحذف هذا السجل.');
        }

        $dailyChange->delete();

        return back()->with('success', 'تم حذف السجل بنجاح.');
    }

    // عرض صفحة تعديل بيانات الطالب
    public function editChild(Child $child)
    {
        $this->checkTeacher();

        $parents = User::where('role', 'parent')
            ->where('status', 'approved')
            ->get();

        return view('teacher.edit_child', compact('child', 'parents'));
    }

    // تحديث بيانات الطالب
    public function updateChild(Request $request, Child $child)
    {
        $this->checkTeacher();

        $request->validate([
            'name'          => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'age'           => 'required|integer|min:1|max:20',
            'class'         => 'required|string|max:255',
            'health_status' => 'nullable|string',
            'parent_id'     => 'nullable|exists:users,id',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = $child->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('children_images', 'public');
        }

        $child->update([
            'name'          => $request->name,
            'gender'        => $request->gender,
            'age'           => $request->age,
            'class'         => $request->class,
            'health_status' => $request->health_status,
            'parent_id'     => $request->parent_id,
            'image'         => $imagePath,
        ]);

        return redirect()->route('teacher.classes')->with('success', 'تم تعديل بيانات الطالب بنجاح.');
    }

    // حذف الطالب
    public function destroyChild(Child $child)
    {
        $this->checkTeacher();
        $child->delete();
        return redirect()->route('teacher.classes')->with('success', 'تم حذف الطالب بنجاح.');
    }

    // حذف حساب المعلم الشخصي
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

    private function checkTeacher()
    {
        if (auth()->user()->role !== 'teacher' || auth()->user()->status !== 'approved') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }
}