<?php

namespace App\Http\Controllers;

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

        // جلب أطفال ولي الأمر الحالي مع سجل اليوم فقط
        $children = Child::where('parent_id', auth()->id())
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

        // التأكد من أن الطفل يخص ولي الأمر المسجل حالياً للحماية
        if ($child->parent_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بعرض بيانات هذا الطفل');
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

    private function checkParent()
    {
        if (auth()->user()->role !== 'parent' || auth()->user()->status !== 'approved') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }
}