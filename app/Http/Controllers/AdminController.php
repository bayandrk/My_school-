<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // عرض لوحة تحكم الأدمن (الطلبات المعلقة + المفعلون + البحث + الترقيم 10 بـ 10)
    public function dashboard(Request $request)
    {
        $this->checkAdmin();

        $search = $request->input('search');

        // 1. طلبات تسجيل المدراء المعلقة
        $pendingManagersQuery = User::where('role', 'manager')
            ->where('status', 'pending');

        if ($search) {
            $pendingManagersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%");
            });
        }

        $pendingManagers = $pendingManagersQuery->latest()->get();

        // 2. قائمة المدراء المفعلين (مع البحث والترقيم 10 بـ 10)
        $approvedManagersQuery = User::where('role', 'manager')
            ->where('status', 'approved');

        if ($search) {
            $approvedManagersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%");
            });
        }

        $approvedManagers = $approvedManagersQuery->latest()
            ->paginate(10)
            ->appends($request->all());

        // 3. عداد المدراء غير المفعلين
        $deactivatedCount = User::where('role', 'manager')
            ->where('status', 'deactivated')
            ->count();

        return view('admin.dashboard', compact('pendingManagers', 'approvedManagers', 'deactivatedCount', 'search'));
    }

    // الموافقة على طلب مدير
    public function approveManager(User $manager)
    {
        $this->checkAdmin();

        $manager->update(['status' => 'approved']);

        return back()->with('success', 'تمت الموافقة على حساب المدير بنجاح.');
    }

    // رفض طلب مدير (حذفه فوراً من قاعدة البيانات)
    public function rejectManager(User $manager)
    {
        $this->checkAdmin();

        $manager->delete();

        return back()->with('success', 'تم رفض طلب المدير وحذفه نهائياً من قاعدة البيانات.');
    }

    // إلغاء تفعيل حساب مدير (نقل حالته إلى deactivated)
    public function deactivateManager(User $manager)
    {
        $this->checkAdmin();

        $manager->update(['status' => 'deactivated']);

        return back()->with('success', 'تم إلغاء تفعيل حساب المدير ونقله لقائمة الحسابات غير المفعلة.');
    }

    // عرض صفحة المدراء غير المفعلين
    public function deactivatedManagers()
    {
        $this->checkAdmin();

        $deactivatedManagers = User::where('role', 'manager')
            ->where('status', 'deactivated')
            ->latest()
            ->get();

        return view('admin.deactivated', compact('deactivatedManagers'));
    }

    // إعادة تفعيل حساب مدير
    public function reactivateManager(User $manager)
    {
        $this->checkAdmin();

        $manager->update(['status' => 'approved']);

        return redirect()->route('admin.managers.deactivated')->with('success', 'تمت إعادة تفعيل حساب المدير بنجاح.');
    }

    // حذف حساب المدير نهائياً
    public function destroyManager(User $manager)
    {
        $this->checkAdmin();

        $manager->delete();

        return back()->with('success', 'تم حذف حساب المدير نهائياً من قاعدة البيانات.');
    }

    // فحص صلاحيات الأدمن
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }
    }
}