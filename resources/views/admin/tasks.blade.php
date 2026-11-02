@extends('layouts.app')

@section('title', 'صفحة المهام - لوحة تحكم الأدمن')

@push('styles')
<style>
    :root {
        --admin-navy: #0f172a;
        --admin-navy-light: #1e293b;
    }

    body {
        background-color: #f8fafc;
        font-family: 'Cairo', sans-serif;
    }

    .admin-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: #ffffff;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    /* تقليل ارتفاع الترويسات العلوية وتعديل الحشوات الداخلية */
    .admin-card-header {
        background-color: var(--admin-navy) !important;
        color: #ffffff !important;
        padding: 0.6rem 1rem !important;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .btn-action-sm {
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    /* تنسيق زر الرفض والحذف */
    .btn-reject-custom {
        background-color: #ffffff !important;
        border: 1.5px solid #ef4444 !important;
        color: #ef4444 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
        padding: 0.35rem 0.75rem !important;
        transition: all 0.2s ease;
    }

    .btn-reject-custom:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
    }

    .badge-count {
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
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
        background-color: rgba(15, 23, 42, 0.08);
        color: var(--admin-navy);
        border: 1px solid rgba(15, 23, 42, 0.15);
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        display: inline-block;
    }

    .table > :not(caption) > * > * {
        padding: 0.85rem 0.75rem;
    }

    .btn-primary, .btn-success {
        background-color: var(--admin-navy) !important;
        border-color: var(--admin-navy) !important;
        color: #ffffff !important;
    }
    .btn-primary:hover, .btn-success:hover {
        background-color: var(--admin-navy-light) !important;
        border-color: var(--admin-navy-light) !important;
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

        <!-- ================= 1. طلبات تسجيل المدراء المعلقة ================= -->
        <div class="admin-card mb-4">
            <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 fw-bold">
                    <i class="bi bi-hourglass-split fs-5"></i>
                    <span class="fs-6">طلبات تسجيل المدراء المعلقة</span>
                </div>
                
                <!-- بحث خاص بطلبات المدراء المعلقة -->
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 250px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-dark border-start-0" style="border-radius: 0 8px 8px 0;"><i class="bi bi-search"></i></span>
                            <input type="text" id="pendingSearchInput" class="form-control border-end-0" placeholder="ابحث باسم المدير أو المدرسة..." style="border-radius: 8px 0 0 8px;">
                        </div>
                    </div>
                    <span class="badge bg-white text-dark badge-count">{{ $pendingManagers->count() }}</span>
                </div>
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
                                    <th class="py-3" style="width: 200px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingManagers as $index => $manager)
                                    @php $rowNum = $index + 1; @endphp
                                    <tr class="pending-manager-row {{ $rowNum > 5 ? 'd-none' : '' }}" data-index="{{ $rowNum }}">
                                        <td class="fw-bold text-muted">{{ $rowNum }}</td>
                                        <td class="text-start ps-4 fw-bold text-dark">{{ $manager->name }}</td>
                                        <td>
                                            <span class="school-badge">{{ $manager->school?->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td class="text-secondary">{{ $manager->username }}</td>
                                        <td class="text-secondary">{{ $manager->email }}</td>
                                        <td class="text-secondary">{{ $manager->phone ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <form action="{{ route('admin.managers.approve', $manager->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-action-sm">
                                                        <i class="bi bi-check-lg me-1"></i> موافقة
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.managers.reject', $manager->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-reject-custom btn-action-sm" onclick="return confirm('هل أنت متأكد من رفض هذا الطلب وحذفه؟')">
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

                    <!-- زر عرض المزيد (5 بـ 5) -->
                    @if($pendingManagers->count() > 5)
                        <div class="p-3 text-center border-top" id="load-more-pending-container">
                            <button type="button" onclick="loadMorePending()" class="btn btn-primary btn-sm px-4 fw-bold">
                                <i class="bi bi-chevron-down me-1"></i> عرض المزيد
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- ================= 2. قائمة المدراء المفعلين ================= -->
        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 fw-bold">
                    <i class="bi bi-person-check-fill fs-5"></i>
                    <span class="fs-6">قائمة المدراء المفعلين</span>
                </div>
                
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- زر المدراء غير المفعلين -->
                    @php
                        $deactCount = \App\Models\User::where('role', 'manager')->where('status', 'deactivated')->count();
                    @endphp
                    <a href="{{ route('admin.managers.deactivated') }}" class="btn btn-warning btn-sm fw-bold rounded-2 px-3 py-1.5 text-dark shadow-sm d-flex align-items-center gap-1">
                        <i class="bi bi-person-x-fill"></i> المدراء غير المفعلين
                        @if($deactCount > 0)
                            <span class="badge rounded-pill bg-danger ms-1" style="font-size: 0.65rem;">
                                {{ $deactCount }}
                            </span>
                        @endif
                    </a>

                    <!-- بحث خاص بالمدراء المفعلين -->
                    <div style="width: 250px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-dark border-start-0" style="border-radius: 0 8px 8px 0;"><i class="bi bi-search"></i></span>
                            <input type="text" id="approvedSearchInput" class="form-control border-end-0" placeholder="ابحث باسم المدير أو المدرسة..." style="border-radius: 8px 0 0 8px;">
                        </div>
                    </div>

                    <span class="badge bg-white text-dark badge-count">
                        {{ method_exists($approvedManagers, 'total') ? $approvedManagers->total() : $approvedManagers->count() }}
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                @if($approvedManagers->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>
                        <p class="mb-0 small fw-semibold">لا يوجد مدراء مفعلون حالياً.</p>
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
                                    @php $rowNum = $index + 1; @endphp
                                    <tr class="approved-manager-row {{ $rowNum > 5 ? 'd-none' : '' }}" data-index="{{ $rowNum }}">
                                        <td class="fw-bold text-muted">{{ $rowNum }}</td>
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

                    <!-- زر عرض المزيد (5 بـ 5) -->
                    @if($approvedManagers->count() > 5)
                        <div class="p-3 text-center border-top" id="load-more-approved-container">
                            <button type="button" onclick="loadMoreApproved()" class="btn btn-success btn-sm px-4 fw-bold">
                                <i class="bi bi-chevron-down me-1"></i> عرض المزيد
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    let visiblePending = 5;

    function loadMorePending() {
        visiblePending += 5;
        const query = document.getElementById('pendingSearchInput').value.trim().toLowerCase();
        
        const rows = document.querySelectorAll('.pending-manager-row');
        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            const text = row.textContent.toLowerCase();
            const matchesSearch = query === '' || text.includes(query);
            
            if (matchesSearch && index <= visiblePending) {
                row.classList.remove('d-none');
            } else if (!matchesSearch) {
                row.classList.add('d-none');
            }
        });

        if (visiblePending >= rows.length || query !== '') {
            const btnContainer = document.getElementById('load-more-pending-container');
            if (btnContainer) btnContainer.style.display = 'none';
        }
    }

    let visibleApproved = 5;

    function loadMoreApproved() {
        visibleApproved += 5;
        const query = document.getElementById('approvedSearchInput').value.trim().toLowerCase();

        const rows = document.querySelectorAll('.approved-manager-row');
        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            const text = row.textContent.toLowerCase();
            const matchesSearch = query === '' || text.includes(query);

            if (matchesSearch && index <= visibleApproved) {
                row.classList.remove('d-none');
            } else if (!matchesSearch) {
                row.classList.add('d-none');
            }
        });

        if (visibleApproved >= rows.length || query !== '') {
            const btnContainer = document.getElementById('load-more-approved-container');
            if (btnContainer) btnContainer.style.display = 'none';
        }
    }

    // تفعيل البحث الفوري المستقل لكل جدول
    document.addEventListener('DOMContentLoaded', function () {
        const pendingSearch = document.getElementById('pendingSearchInput');
        if (pendingSearch) {
            pendingSearch.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.pending-manager-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const index = parseInt(row.getAttribute('data-index'));
                    const matches = text.includes(query);
                    if (query === '') {
                        row.classList.toggle('d-none', index > visiblePending);
                    } else {
                        row.classList.toggle('d-none', !matches);
                    }
                });
                const loadMoreContainer = document.getElementById('load-more-pending-container');
                if (loadMoreContainer) {
                    loadMoreContainer.style.display = query !== '' ? 'none' : (visiblePending >= rows.length ? 'none' : 'block');
                }
            });
        }

        const approvedSearch = document.getElementById('approvedSearchInput');
        if (approvedSearch) {
            approvedSearch.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.approved-manager-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const index = parseInt(row.getAttribute('data-index'));
                    const matches = text.includes(query);
                    if (query === '') {
                        row.classList.toggle('d-none', index > visibleApproved);
                    } else {
                        row.classList.toggle('d-none', !matches);
                    }
                });
                const loadMoreContainer = document.getElementById('load-more-approved-container');
                if (loadMoreContainer) {
                    loadMoreContainer.style.display = query !== '' ? 'none' : (visibleApproved >= rows.length ? 'none' : 'block');
                }
            });
        }
    });
</script>
@endpush