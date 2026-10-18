<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Child;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\TeacherAssignedNotification;

class ManagerController extends Controller
{
public function dashboard(Request $request)
    {
        $this->checkManager();
        $manager = Auth::user();

        // طلبات الأساتذة المعلقة (10 بـ 10)
        $pendingTeachers = User::where('role', 'teacher')
            ->where('status', 'pending')
            ->where('school_name', $manager->school_name)
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // الأساتذة المعتمدون (للإحصائيات فقط)
        $approvedTeachers = User::where('role', 'teacher')
            ->where('status', 'approved')
            ->where('school_name', $manager->school_name)
            ->get();

        return view('manager.dashboard', compact('pendingTeachers', 'approvedTeachers'));
    }

// دالة إلغاء التعيين المتوافقة مع دالة التعيين
public function unassignTeacherClass(Request $request)
    {
        $this->checkManager();
        $managerId = Auth::id();

        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string',
        ]);

        $children = Child::where('manager_id', $managerId)
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
        return view('manager.profile', compact('manager'));
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

    // 2. الموافقة على الأستاذ
    // إلغاء تفعيل حساب الأستاذ
    public function deactivateTeacher(User $teacher)
    {
        $this->checkManager();

        if ($teacher->school_name !== Auth::user()->school_name) {
            abort(403, 'غير مصرح لك بالتعامل مع هذا الأستاذ.');
        }

        // تغيير حالة الحساب إلى معطل
        $teacher->update(['status' => 'deactivated']);

        return back()->with('success', 'تم إلغاء تفعيل حساب الأستاذ بنجاح.');
    }

    // إعادة تفعيل حساب الأستاذ المعطل
    public function reactivateTeacher(User $teacher)
    {
        $this->checkManager();

        if ($teacher->school_name !== Auth::user()->school_name) {
            abort(403, 'غير مصرح لك بالتعامل مع هذا الأستاذ.');
        }

        // إرجاع حالة الحساب إلى مفعل
        $teacher->update(['status' => 'approved']);

        return back()->with('success', 'تمت إعادة تفعيل حساب الأستاذ بنجاح.');
    }
    public function deactivatedTeachers(Request $request)
{
    $this->checkManager();
    $manager = auth()->user();
    $search = $request->input('search');

    $query = User::where('role', 'teacher')
        ->where('status', 'deactivated') // جلب المعطلين فقط
        ->where('school_name', $manager->school_name);

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    $teachers = $query->get();

    return view('manager.deactivated_teachers', compact('teachers', 'search'));
}

// 1. أرشفة طالب
public function archiveChild(Child $child)
{
    $this->checkManager();
    if ($child->manager_id !== Auth::id()) {
        abort(403);
    }

    $child->status = 'archived';
    $child->save(); // استخدام save بدلاً من update لضمان حفظ الحالة

    return back()->with('success', 'تم أرشفة حساب الطالب بنجاح ونقله للأرشيف.');
}

// 2. إعادة تفعيل طالب من الأرشيف
public function restoreChild(Child $child)
{
    $this->checkManager();
    if ($child->manager_id !== Auth::id()) {
        abort(403);
    }

    $child->update(['status' => 'active']);

    return back()->with('success', 'تمت إعادة تفعيل الطالب وإرجاعه لدليل الصفوف بنجاح.');
}

// 3. عرض الطلاب المؤرشفين
public function archivedChildren(Request $request)
{
    $this->checkManager();
    $managerId = Auth::id();
    $search = $request->input('search');

    $query = Child::where('manager_id', $managerId)
        ->where('status', 'archived');

    if ($search) {
        $query->where('name', 'like', "%{$search}%");
    }

    $children = $query->get();

    return view('manager.archived_children', compact('children', 'search'));
}
// 2. صفحة إدارة التعيينات المستقلة
public function assignmentsIndex(Request $request)
{
    // التحقق بأمان من صلاحية المدير
    if (method_exists($this, 'checkManager')) {
        $this->checkManager();
    }
    
    $manager = Auth::user();

    // الأساتذة المعتمدون للتعيين
    $approvedTeachers = User::where('role', 'teacher')
        ->where('status', 'approved')
        ->where('school_name', $manager->school_name)
        ->get();

    // قائمة الصفوف
    $classesList = Child::where('manager_id', $manager->id)
        ->pluck('class')
        ->unique()
        ->sort()
        ->values();

    // جلب التعيينات الحالية وتجميعها حسب الصف
    $currentAssignments = collect();

    try {
        $children = Child::where('manager_id', $manager->id)
            ->has('teachers')
            ->with('teachers')
            ->get();

        $grouped = [];

        foreach ($children as $child) {
            $className = $child->class;
            if (!isset($grouped[$className])) {
                $grouped[$className] = collect();
            }

            foreach ($child->teachers as $teacher) {
                $grouped[$className]->push($teacher);
            }
        }

        foreach ($grouped as $className => $teachersColl) {
            $currentAssignments->push((object)[
                'class_name' => $className,
                'teachers' => $teachersColl->unique('id')->values()
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
        $managerId = Auth::id();

        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string',
        ]);

        // التأكد من أن الأستاذ يتبع لنفس مدرسة المدير
        $teacher = User::where('id', $request->teacher_id)
            ->where('school_name', Auth::user()->school_name)
            ->first();

        if (!$teacher) {
            return back()->with('error', 'الأستاذ المحدد غير موجود أو لا ينتمي إلى مدرستك.');
        }

        // جلب جميع طلاب هذا المدير الذين ينتمون إلى الصف المحدد
        $children = Child::where('manager_id', $managerId)
            ->where('class', $request->class_name)
            ->get();

        if ($children->isEmpty()) {
            return back()->with('error', 'لا يوجد طلاب في هذا الصف ليتم ربطهم بالأستاذ.');
        }

        // ربط جميع طلاب الصف بهذا الأستاذ بدون تكرار
        foreach ($children as $child) {
            if (method_exists($child, 'teachers')) {
                $child->teachers()->syncWithoutDetaching([$teacher->id]);
            }
        }

        // إرسال إشعار للأستاذ بأمان إن وُجد كلاس الإشعار
        try {
            if (class_exists(\App\Notifications\TeacherAssignedNotification::class)) {
                $teacher->notify(new \App\Notifications\TeacherAssignedNotification($request->class_name));
            }
        } catch (\Exception $e) {
            // تخطي خطأ الإشعار لضمان عدم توقف العملية
        }

        return redirect()->route('manager.assignments')->with('success', 'تم تعيين الأستاذ لصف (' . $request->class_name . ') بنجاح.');
    }
    // 5. صفحة الصفوف الشاملة
    public function classes(Request $request)
{
    $this->checkManager();
    $managerId = Auth::id();

    // 1. تحديد العام الدراسي المحدد من القائمة المنسدلة (الافتراضي السنة الحالية)
    $selectedYear = $request->get('year', date('Y'));
    
    // 2. جلب معايير الفلترة الإضافية (حسب اسم الطالب وحسب الصف المحدد)
    $studentSearch = $request->input('search_student');
    $selectedClass = $request->input('class_filter');

    // 3. جلب جميع السنوات المتاحة في النظام ديناميكياً لطلاب المدير الحالي فقط
    $availableYears = Child::where('manager_id', $managerId)
        ->selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->pluck('year')
        ->push((int)date('Y'))
        ->unique()
        ->sortDesc();

    // 4. بناء استعلام الطلاب مع تطبيق فلاتر العام، الصف، البحث بالاسم، واستبعاد المؤرشفين
    $query = Child::with(['dailyChanges', 'teachers'])
        ->where('manager_id', $managerId)
        ->where(function($q) {
            // جلب الطلاب النشطين فقط أو الذين ليس لديهم حقل status (القدامى)
            $q->where('status', 'active')
              ->orWhereNull('status');
        })
        ->where(function($q) use ($selectedYear) {
            $q->whereYear('created_at', $selectedYear)
              ->orWhere('class', 'LIKE', "%{$selectedYear}%");
        });

    // تصفية حسب الصف المحدد من القائمة المنسدلة للفلتر
    if (!empty($selectedClass)) {
        $query->where('class', $selectedClass);
    }

    // تصفية حسب اسم الطالب إذا تم إدخاله
    if (!empty($studentSearch)) {
        $query->where('name', 'like', '%' . $studentSearch . '%');
    }

    $children = $query->get();

    // جلب قائمة الصفوف الفريدة لملء خيارات فلتر الصفوف (لطلاب النشطين فقط)
    $allClassesList = Child::where('manager_id', $managerId)
        ->where(function($q) {
            $q->where('status', 'active')->orWhereNull('status');
        })
        ->pluck('class')
        ->unique()
        ->sort()
        ->values();

    // 5. تجميع الطلاب حسب اسم الصف
    $childrenGroupedByClass = $children->groupBy('class');

    // خريطة تحويل الكلمات العربية للأرقام لضمان الفرز الصحيح لأي صف جديد
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

    // ترتيب المجموعات بشكل ديناميكي بالكامل
    $childrenGroupedByClass = $childrenGroupedByClass->sortKeysUsing(function ($a, $b) use ($arabicNumbers) {
        $numA = 99;
        $numB = 99;

        foreach ($arabicNumbers as $word => $val) {
            if (mb_strpos($a, $word) !== false) { $numA = $val; }
            if (mb_strpos($b, $word) !== false) { $numB = $val; }
        }

        if ($numA === $numB) {
            return strcmp($a, $b);
        }

        return $numA <=> $numB;
    });

    // 6. حساب المعدل العام لكل طالب
    foreach ($childrenGroupedByClass as $className => $students) {
        foreach ($students as $student) {
            $student->average_score = round($student->dailyChanges->whereNotNull('score')->avg('score'), 2);
        }
    }

    return view('manager.classes', compact(
        'childrenGroupedByClass', 
        'availableYears', 
        'selectedYear', 
        'allClassesList', 
        'studentSearch', 
        'selectedClass'
    ));
}


    public function teachers(Request $request)
    {
        $this->checkManager();
        $manager = auth()->user();
        $search = $request->input('search');

        // جلب الأساتذة في نفس مدرسة المدير فقط مع الطلاب التابعين لهم
        $query = User::where('role', 'teacher')
            ->where('status', 'approved')
            ->where('school_name', $manager->school_name)
            ->with('students');

        // البحث باسم الأستاذ، البريد، أو رقم الهاتف إذا تم إدخاله
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $teachers = $query->get();

        return view('manager.teachers', compact('teachers', 'search'));
    }
    // 2. معالجة تحديث بيانات الطالب
    public function updateChild(Request $request, Child $child)
    {
        $this->checkManager();

        if ($child->manager_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا الطالب.');
        }

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

    // 3. حذف الطالب
    public function destroyChild(Child $child)
    {
        $this->checkManager();

        if ($child->manager_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذا الطالب.');
        }

        // حذف الطفل نهائياً من قاعدة البيانات
        $child->delete();

        return redirect()->route('manager.classes')->with('success', 'تم حذف الطالب بنجاح.');
    }

    public function destroyTeacher(User $teacher)
    {
        $this->checkManager();

        if ($teacher->school_name !== Auth::user()->school_name) {
            abort(403, 'غير مصرح لك بحذف هذا الأستاذ.');
        }

        // فك ارتباط الأستاذ بالطلاب في الجدول الوسيط (child_teacher)
        if (method_exists($teacher, 'students')) {
            $teacher->students()->detach();
        }

        // حذف حساب الأستاذ
        $teacher->delete();

        return redirect()->route('manager.teachers')->with('success', 'تم حذف الأستاذ بنجاح.');
    }
    public function destroyProfile(Request $request)
{
    // التأكد من أن المستخدم مدير ومفعل
    $this->checkManager();

    $manager = Auth::user();
  
    $manager->delete();

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('success', 'تم حذف حساب المدير الشخصي بنجاح، وباقي بيانات المدرسة والطلاب محفوظة.');
}
public function classScores(Request $request)
{
    $this->checkManager();
    $manager = auth()->user();
    $className = $request->get('class_name');
    $searchName = $request->get('search_name');
    $searchSubject = $request->get('search_subject');

    // استخراج المواد المتاحة لهذا الصف لملء قائمة المادة في الفلتر
    $subjects = \App\Models\DailyChange::whereHas('child', function($q) use ($className, $manager) {
        $q->where('class', $className)
          ->where(function($subQ) use ($manager) {
              $subQ->whereHas('teachers', function($tQ) use ($manager) {
                  $tQ->where('school_name', $manager->school_name);
              })->orWhere('manager_id', $manager->id);
          });
    })->whereNotNull('score')->pluck('subject')->unique()->filter();

    // جلب الطلاب وربط الفلاتر
    $students = Child::where('class', $className)
        ->where(function($q) use ($manager) {
            $q->whereHas('teachers', function($tQ) use ($manager) {
                $tQ->where('school_name', $manager->school_name);
            })->orWhere('manager_id', $manager->id);
        })
        ->when($searchName, function($q) use ($searchName) {
            $q->where('name', 'like', "%{$searchName}%");
        })
        ->with(['dailyChanges' => function($q) use ($searchSubject) {
            $q->whereNotNull('score');
            if ($searchSubject) {
                $q->where('subject', $searchSubject);
            }
        }])
        ->get()
        ->filter(function($student) use ($searchSubject) {
            // إذا تم البحث بالمادة، نضمن عرض الطلاب الذين لديهم سجلات مطابقة فقط
            return !$searchSubject || $student->dailyChanges->isNotEmpty();
        })
        ->unique('id');

    return view('manager.class_scores', compact('students', 'className', 'subjects', 'searchName', 'searchSubject'));
}

public function classNotes(Request $request)
{
    $this->checkManager();
    $manager = auth()->user();
    $className = $request->get('class_name');
    $searchName = $request->get('search_name');
    $searchSubject = $request->get('search_subject');

    $subjects = \App\Models\DailyChange::whereHas('child', function($q) use ($className, $manager) {
        $q->where('class', $className)
          ->where(function($subQ) use ($manager) {
              $subQ->whereHas('teachers', function($tQ) use ($manager) {
                  $tQ->where('school_name', $manager->school_name);
              })->orWhere('manager_id', $manager->id);
          });
    })->whereNotNull('note')->pluck('subject')->unique()->filter();

    $students = Child::where('class', $className)
        ->where(function($q) use ($manager) {
            $q->whereHas('teachers', function($tQ) use ($manager) {
                $tQ->where('school_name', $manager->school_name);
            })->orWhere('manager_id', $manager->id);
        })
        ->when($searchName, function($q) use ($searchName) {
            $q->where('name', 'like', "%{$searchName}%");
        })
        ->with(['dailyChanges' => function($q) use ($searchSubject) {
            $q->whereNotNull('note');
            if ($searchSubject) {
                $q->where('subject', $searchSubject);
            }
        }])
        ->get()
        ->filter(function($student) use ($searchSubject) {
            return !$searchSubject || $student->dailyChanges->isNotEmpty();
        })
        ->unique('id');

    return view('manager.class_notes', compact('students', 'className', 'subjects', 'searchName', 'searchSubject'));
}
// تحديث إعدادات مظهر المدرسة (اللوغو واللون)
public function updateBranding(Request $request)
{
    $this->checkManager();
    $manager = Auth::user();

    $request->validate([
        'logo'          => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        'primary_color' => 'required|string|max:7', // مثل #0d6efd
    ]);

    if ($request->hasFile('logo')) {
        // رفع اللوغو وتخزينه في مجلد التخزين العام
        $logoPath = $request->file('logo')->store('school_logos', 'public');
        $manager->logo = $logoPath;
    }

    $manager->primary_color = $request->primary_color;
    $manager->save();

    return redirect()->back()->with('success', 'تم تحديث مظهر المدرسة (اللوغو والألوان) بنجاح.');
}
public function approveTeacher(User $teacher)
{
    $this->checkManager();

    if ($teacher->school_name !== Auth::user()->school_name) {
        abort(403, 'غير مصرح لك بالتعامل مع هذا الأستاذ.');
    }

    // تحديث حالة الأستاذ إلى معتمد
    $teacher->update(['status' => 'approved']);

    return back()->with('success', 'تمت الموافقة على طلب انضمام الأستاذ بنجاح.');
}
public function rejectTeacher(User $teacher)
    {
        $this->checkManager();

        if ($teacher->school_name !== Auth::user()->school_name) {
            abort(403, 'غير مصرح لك بالتعامل مع هذا الأستاذ.');
        }

        // حذف الأستاذ أو رفض طلبه نهائياً
        $teacher->delete();

        return back()->with('success', 'تم رفض وحذف طلب انضمام الأستاذ بنجاح.');
    }

    private function checkManager()
    {
        if (auth()->user()->role !== 'manager' || auth()->user()->status !== 'approved') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }
}