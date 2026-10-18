<?php

namespace App\Http\Controllers;
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

        // جلب أطفال ولي الأمر النشطين فقط (استبعاد المؤرشفين) مع سجل اليوم
        $children = Child::where('parent_id', auth()->id())
            ->where(function($q) {
                $q->where('status', 'active')
                  ->orWhereNull('status');
            })
            ->with(['dailyChanges' => function ($query) {
                // جلب السجلات التابعة لتاريخ اليوم فقط
                $query->whereDate('created_at', Carbon::today());
            }])
            ->get();

        return view('parent.dashboard', compact('children'));
    }

    public function showChild(Child $child)
    {
        $this->checkParent();

        // التأكد من أن الطفل يخص ولي الأمر الحالي وأنه غير مؤرشف
        if ($child->parent_id !== auth()->id() || $child->status === 'archived') {
            abort(403, 'غير مصرح لك بعرض بيانات هذا الطفل.');
        }

        // جلب العلاقات المرتبطة بالطفل (الأساتذة والملاحظات والدرجات)
        $child->load(['teachers', 'dailyChanges']);

        return view('parent.child_show', compact('child'));
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

    $child->update([
        'parent_note' => $request->parent_note,
    ]);

    return back()->with('success', 'تم حفظ وتحديث ملاحظة ولي الأمر بنجاح.');
}
public function sendTeacherNote(Request $request, Child $child)
{
    $request->validate([
        'teacher_ids'   => 'required|array|min:1',
        'teacher_ids.*' => 'exists:users,id',
        'note'          => 'required|string|max:1000',
    ]);

    // دمج الملاحظة الجديدة مع الملاحظات السابقة إن وجدت لكي لا تُحذف القديمة
    $newNoteText = "• [" . now()->format('Y-m-d H:i') . "] " . $request->note;
    $finalNotes = $child->parent_note ? $child->parent_note . "\n\n" . $newNoteText : $newNoteText;

    // حفظ الملاحظة المدمجة
    $child->update([
        'parent_note' => $finalNotes,
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