<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Child;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
public function dashboard()
{
    $this->checkManager();
    $manager = Auth::user();

    // طلبات الأساتذة المعلقة التابعين لنفس مدرسة المدير فقط
    $pendingTeachers = User::where('role', 'teacher')
        ->where('status', 'pending')
        ->where('school_name', $manager->school_name)
        ->latest()
        ->get();

    // الأساتذة المفعلون التابعون لنفس مدرسة المدير فقط
    $approvedTeachers = User::where('role', 'teacher')
        ->where('status', 'approved')
        ->where('school_name', $manager->school_name)
        ->get();

    // طلاب مدرسة المدير فقط
    $children = Child::where('manager_id', $manager->id)
        ->orderBy('class')
        ->get();

    // جلب قائمة الصفوف الفريدة الخاصة بطلاب هذا المدير لنموذج التعيين
    $classesList = Child::where('manager_id', $manager->id)
        ->pluck('class')
        ->unique()
        ->sort()
        ->values();

    return view('manager.dashboard', compact('pendingTeachers', 'approvedTeachers', 'children', 'classesList'));
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
    public function approveTeacher(User $teacher)
    {
        $this->checkManager();

        // التأكد من أن الأستاذ يتبع لنفس مدرسة المدير
        if ($teacher->school_name !== Auth::user()->school_name) {
            abort(403, 'غير مصرح لك بالتعامل مع هذا الأستاذ.');
        }

        $teacher->update(['status' => 'approved']);

        return back()->with('success', 'تمت الموافقة على حساب الأستاذ بنجاح.');
    }

    // 3. رفض الأستاذ
    public function rejectTeacher(User $teacher)
    {
        $this->checkManager();

        if ($teacher->school_name !== Auth::user()->school_name) {
            abort(403, 'غير مصرح لك بالتعامل مع هذا الأستاذ.');
        }

        $teacher->delete();

        return back()->with('success', 'تم رفض طلب الأستاذ وحذفه بنجاح.');
    }

    // 4. تعيين أستاذ لطالب
    // public function assignTeacherToChild(Request $request)
    // {
    //     $this->checkManager();
    //     $managerId = Auth::id();

    //     $request->validate([
    //         'teacher_id' => 'required|exists:users,id',
    //         'child_id'   => 'required|exists:children,id',
    //     ]);

    //     // التأكد من أن الطالب يتبع لهذا المدير وأن الأستاذ يتبع لنفس المدرسة
    //     $child = Child::where('id', $request->child_id)->where('manager_id', $managerId)->firstOrFail();
    //     $teacher = User::where('id', $request->teacher_id)->where('school_name', Auth::user()->school_name)->firstOrFail();
        
    //     // ربط الأستاذ بالطفل بدون تكرار في جدول child_teacher
    //     $child->teachers()->syncWithoutDetaching([$teacher->id]);

    //     return back()->with('success', 'تم تعيين الأستاذ للطالب بنجاح.');
    // }
// تعيين أستاذ لصف كامل
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
        ->firstOrFail();

    // جلب كل طلاب هذا المدير الذين ينتمون إلى الصف المحدد
    $children = Child::where('manager_id', $managerId)
        ->where('class', $request->class_name)
        ->get();

    if ($children->isEmpty()) {
        return back()->with('error', 'لا يوجد طلاب في هذا الصف ليتم ربطهم بالأستاذ.');
    }

    // ربط كل طلاب الصف بهذا الأستاذ بدون تكرار
    foreach ($children as $child) {
        $child->teachers()->syncWithoutDetaching([$teacher->id]);
    }

    return back()->with('success', 'تم تعيين الأستاذ لجميع طلاب صف (' . $request->class_name . ') بنجاح.');
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

        // 4. بناء استعلام الطلاب مع تطبيق فلاتر العام، الصف، والبحث بالاسم
        $query = Child::with(['dailyChanges', 'teachers'])
            ->where('manager_id', $managerId)
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

        // جلب قائمة الصفوف الفريدة لملء خيارات فلتر الصفوف في الواجهة الخاصة بطلاب المدير
        $allClassesList = Child::where('manager_id', $managerId)
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

    // 6. صفحة الأساتذة والصفوف المسؤولة عنها
    // public function teachers()
    // {
    //     $this->checkManager();

    //     // جلب جميع الأساتذة المفعلين التابعين لنفس مدرسة المدير فقط مع الطلاب التابعين لهم
    //     $teachers = User::where('role', 'teacher')
    //         ->where('status', 'approved')
    //         ->where('school_name', Auth::user()->school_name)
    //         ->with('students')
    //         ->get();

    //     return view('manager.teachers', compact('teachers'));
    // }

    // public function editChild(Child $child)
    // {
    //     $this->checkManager();

    //     // التأكد من أن الطالب يتبع لهذا المدير
    //     if ($child->manager_id !== Auth::id()) {
    //         abort(403, 'غير مصرح لك بالوصول إلى هذا الطالب.');
    //     }

    //     $parents = User::where('role', 'parent')
    //         ->where('status', 'approved')
    //         ->get();

    //     return view('manager.edit_child', compact('child', 'parents'));
    // }
// صفحة الأساتذة والصفوف المسؤولة عنها
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

    private function checkManager()
    {
        if (auth()->user()->role !== 'manager' || auth()->user()->status !== 'approved') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }
}