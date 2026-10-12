<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // عرض لوحة تحكم الأدمن (الطلبات المعلقة + المفعلون + عداد غير المفعلين)
    public function dashboard()
    {
        $this->checkAdmin();

        $pendingManagers = User::where('role', 'manager')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $approvedManagers = User::where('role', 'manager')
            ->where('status', 'approved')
            ->latest()
            ->get();

        $deactivatedCount = User::where('role', 'manager')
            ->where('status', 'deactivated')
            ->count();

        return view('admin.dashboard', compact('pendingManagers', 'approvedManagers', 'deactivatedCount'));
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