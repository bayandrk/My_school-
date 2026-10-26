<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ManagerLifecycleService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly ManagerLifecycleService $managers,
    ) {}

    // عرض لوحة تحكم الأدمن الفارغة حالياً (تحتوي على النافبار فقط)
    public function dashboard(Request $request)
    {
        return view('admin.dashboard', [
            'pendingCount'     => $this->managers->pendingManagers(null)->count(),
            'deactivatedCount' => $this->managers->deactivatedManagersCount(),
        ]);
    }

    // عرض صفحة المهام (تحتوي على جدول الطلبات المعلقة والمفعلين والبحث)
    public function tasks(Request $request)
    {
        $search =$request->input('search');

        return view('admin.tasks', [
            'pendingManagers'  => $this->managers->pendingManagers($search),
            'approvedManagers' => $this->managers->approvedManagers($search),
            'deactivatedCount' => $this->managers->deactivatedManagersCount(),
            'search'           => $search,
        ]);
    }

    // الموافقة على طلب مدير
    public function approveManager(User $manager)
    {
        $this->managers->approve($manager);

        return back()->with('success', 'تمت الموافقة على حساب المدير بنجاح.');
    }

    // رفض طلب مدير (حذفه فوراً من قاعدة البيانات)
    public function rejectManager(User $manager)
    {
        $this->managers->reject($manager);

        return back()->with('success', 'تم رفض طلب المدير وحذفه نهائياً من قاعدة البيانات.');
    }

    // إلغاء تفعيل حساب مدير
    public function deactivateManager(User $manager)
    {
        $this->managers->deactivate($manager);

        return back()->with('success', 'تم إلغاء تفعيل حساب المدير ونقله لقائمة الحسابات غير المفعلة.');
    }

    // عرض صفحة المدراء غير المفعلين
    public function deactivatedManagers()
    {
        return view('admin.deactivated', [
            'deactivatedManagers' => $this->managers->deactivatedManagers(),
        ]);
    }

    // إعادة تفعيل حساب مدير
    public function reactivateManager(User $manager)
    {
        $this->managers->reactivate($manager);

        return redirect()->route('admin.managers.deactivated')
            ->with('success', 'تمت إعادة تفعيل حساب المدير بنجاح.');
    }

    // حذف حساب المدير نهائياً
    public function destroyManager(User $manager)
    {
        $this->managers->destroy($manager);

        return back()->with('success', 'تم حذف حساب المدير نهائياً من قاعدة البيانات.');
    }
}