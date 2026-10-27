@extends('layouts.app')

@section('title', 'صفحة المهام - لوحة تحكم الأدمن')

@push('styles')
<style>
    .admin-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: #ffffff;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .card-header-pending {
        background-color: #fffbeb;
        color: #b45309;
        border-bottom: 1px solid #fef3c7;
    }

    .card-header-active {
        background-color: #f0fdf4;
        color: #15803d;
        border-bottom: 1px solid #dcfce7;
    }

    .btn-action-sm {
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
    }

    .badge-count {
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.35rem 0.65rem;
    }

    .status-badge-active {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.78rem;
    }

    .school-badge {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        display: inline-block;
    }

    .table > :not(caption) > * > * {
        padding: 0.85rem 0.75rem;
    }
</style>
@endpush

@section('content')
    <div class="container pb-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 mb-4 py-2.5 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- شريط البحث السريع -->
        <div class="admin-card p-3 mb-4 bg-white">
            <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="fw-bold text-dark d-flex align-items-center gap-2 fs-6">
                    <i class="bi bi-search text-primary"></i> <span>البحث في المهام والطلبات</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="ابحث باسم المدير أو اسم المدرسة..." value="{{ request('search') }}" style="width: 260px; border-radius: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-3 py-1.5" style="border-radius: 8px;">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    @if(request('search'))
                        <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary py-1.5 px-3 fw-semibold" style="border-radius: 8px;">إعادة ضبط</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- الطلبات المعلقة -->
        <div class="admin-card mb-4">
            <div class="p-3.5 card-header-pending d-flex justify-content-between align-items-center fw-bold">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-hourglass-split fs-5"></i>
                    <span class="fs-6">طلبات تسجيل المدراء المعلقة</span>
                </div>
                <span class="badge bg-warning text-dark badge-count">{{ $pendingManagers->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($pendingManagers->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check2-circle fs-3 d-block mb-1 text-success opacity-75"></i>
                        <p class="mb-0 small fw-semibold">لا توجد طلبات مدراء معلقة حالياً.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">#</th>
                                    <th class="py-3 text-start ps-4">الاسم</th>
                                    <th class="py-3">اسم المدرسة</th>
                                    <th class="py-3">اسم المستخدم</th>
                                    <th class="py-3">البريد الإلكتروني</th>
                                    <th class="py-3">الهاتف</th>
                                    <th class="py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingManagers as $index => $manager)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="text-start ps-4 fw-bold text-dark">{{ $manager->name }}</td>
                                        <td>
                                            <span class="school-badge">{{ $manager->school?->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td class="text-secondary">{{ $manager->username }}</td>
                                        <td class="text-secondary">{{ $manager->email }}</td>
                                        <td class="text-secondary">{{ $manager->phone ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1.5">
                                                <form action="{{ route('admin.managers.approve', $manager->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-action-sm">
                                                        <i class="bi bi-check-lg me-1"></i> موافقة
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.managers.reject', $manager->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-action-sm" onclick="return confirm('هل أنت متأكد من رفض هذا الطلب وحذفه؟')">
                                                        <i class="bi bi-x-lg me-1"></i> رفض وحذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- قائمة المدراء المفعلين -->
        <div class="admin-card">
            <div class="p-3.5 card-header-active d-flex justify-content-between align-items-center fw-bold flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-check-fill fs-5"></i>
                    <span class="fs-6">قائمة المدراء المفعلين</span>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <!-- 🟡 زر المدراء غير المفعلين بجانب عنوان الجدول -->
                    @php
                        $deactCount = \App\Models\User::where('role', 'manager')->where('status', 'deactivated')->count();
                    @endphp
                    <a href="{{ route('admin.managers.deactivated') }}" class="btn btn-warning btn-sm fw-bold rounded-2 px-3 py-1.5 position-relative text-dark shadow-sm d-flex align-items-center gap-1" title="المدراء غير المفعلين">
                        <i class="bi bi-person-x-fill"></i> المدراء غير المفعلين
                        @if($deactCount > 0)
                            <span class="badge rounded-pill bg-danger ms-1" style="font-size: 0.65rem;">
                                {{ $deactCount }}
                            </span>
                        @endif
                    </a>

                    <span class="badge bg-success badge-count">
                        {{ method_exists($approvedManagers, 'total') ? $approvedManagers->total() : $approvedManagers->count() }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($approvedManagers->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>
                        <p class="mb-0 small fw-semibold">لا يوجد مدراء مفعلون مطابقون لخيارات البحث حالياً.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">#</th>
                                    <th class="py-3 text-start ps-4">الاسم</th>
                                    <th class="py-3">اسم المدرسة المسؤول عنها</th>
                                    <th class="py-3">اسم المستخدم</th>
                                    <th class="py-3">البريد الإلكتروني</th>
                                    <th class="py-3">الحالة</th>
                                    <th class="py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedManagers as $index => $manager)
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            {{ method_exists($approvedManagers, 'firstItem') ? $approvedManagers->firstItem() + $index : $index + 1 }}
                                        </td>
                                        <td class="text-start ps-4 fw-bold text-dark">{{ $manager->name }}</td>
                                        <td>
                                            <span class="school-badge">{{ $manager->school?->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td class="text-secondary">{{ $manager->username }}</td>
                                        <td class="text-secondary">{{ $manager->email }}</td>
                                        <td>
                                            <span class="status-badge-active">نشط</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.managers.deactivate', $manager->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-warning text-dark btn-action-sm" onclick="return confirm('هل أنت متأكد من إلغاء تفعيل هذا الحساب؟')">
                                                    <i class="bi bi-slash-circle me-1"></i> إلغاء التفعيل
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($approvedManagers, 'hasPages') && $approvedManagers->hasPages())
                        <div class="d-flex justify-content-center p-3 border-top">
                            {{ $approvedManagers->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>
@endsection