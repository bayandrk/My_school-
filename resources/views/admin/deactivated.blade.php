@extends('layouts.app')

@section('title', 'قائمة المدراء غير المفعلين')

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

    .btn-success {
        background-color: var(--admin-navy) !important;
        border-color: var(--admin-navy) !important;
        color: #ffffff !important;
    }
    .btn-success:hover {
        background-color: var(--admin-navy-light) !important;
        border-color: var(--admin-navy-light) !important;
    }
</style>
@endpush

@section('content')
    <div class="container pb-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bold m-0" style="color: var(--admin-navy);">
                <i class="bi bi-person-x-fill text-warning me-2"></i> قائمة المدراء غير المفعلين
            </h4>
            <a href="{{ route('admin.tasks') }}" class="btn btn-sm px-4 py-2 rounded-3 shadow-sm" style="background-color: var(--admin-navy); color: #fff; font-weight: 700;">
                <i class="bi bi-arrow-right me-1"></i> العودة لصفحة المهام
            </a>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 fw-bold">
                    <i class="bi bi-shield-exclamation fs-5"></i>
                    <span class="fs-6">الحسابات المعطلة</span>
                </div>
                
                <!-- بحث خاص بالحسابات المعطلة -->
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 250px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-dark border-start-0" style="border-radius: 0 8px 8px 0;"><i class="bi bi-search"></i></span>
                            <input type="text" id="deactivatedSearchInput" class="form-control border-end-0" placeholder="ابحث باسم المدير أو المدرسة..." style="border-radius: 8px 0 0 8px;">
                        </div>
                    </div>
                    <span class="badge bg-white text-dark badge-count">{{ $deactivatedManagers->count() }}</span>
                </div>
            </div>

            <div class="card-body p-0">
                @if($deactivatedManagers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                        <p class="mb-0 fw-semibold">لا يوجد مدراء ملغى تفعيلهم حالياً.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center small">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">#</th>
                                    <th class="py-3 text-start ps-4">الاسم</th>
                                    <th class="py-3">اسم المدرسة المسؤول عنها</th>
                                    <th class="py-3">اسم المستخدم</th>
                                    <th class="py-3">البريد الإلكتروني</th>
                                    <th class="py-3">الحالة</th>
                                    <th class="py-3" style="width: 210px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deactivatedManagers as $index => $manager)
                                    @php $rowNum = $index + 1; @endphp
                                    <tr class="deactivated-manager-row {{ $rowNum > 10 ? 'd-none' : '' }}" data-index="{{ $rowNum }}">
                                        <td class="fw-bold text-muted">{{ $rowNum }}</td>
                                        <td class="text-start ps-4 fw-bold text-dark">{{ $manager->name }}</td>
                                        <td>
                                            <span class="school-badge">{{ $manager->school?->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td class="text-secondary">{{ $manager->username }}</td>
                                        <td class="text-secondary">{{ $manager->email }}</td>
                                        <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 fw-bold">معطل</span></td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <!-- إعادة التفعيل -->
                                                <form action="{{ route('admin.managers.reactivate', $manager->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-action-sm">
                                                        <i class="bi bi-check-circle me-1"></i> إعادة تفعيل
                                                    </button>
                                                </form>

                                                <!-- الحذف النهائي -->
                                                <form action="{{ route('admin.managers.destroy', $manager->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-reject-custom btn-action-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الحساب نهائياً من قاعدة البيانات؟')">
                                                        <i class="bi bi-trash me-1"></i> حذف نهائي
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- زر عرض المزيد (10 بـ 10) -->
                    @if($deactivatedManagers->count() > 10)
                        <div class="p-3 text-center border-top" id="load-more-deactivated-container">
                            <button type="button" onclick="loadMoreDeactivated()" class="btn btn-primary btn-sm px-4 fw-bold" style="background-color: var(--admin-navy); border-color: var(--admin-navy);">
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
    let visibleDeactivated = 10;

    function loadMoreDeactivated() {
        visibleDeactivated += 10;
        const query = document.getElementById('deactivatedSearchInput').value.trim().toLowerCase();

        const rows = document.querySelectorAll('.deactivated-manager-row');
        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            const text = row.textContent.toLowerCase();
            const matchesSearch = query === '' || text.includes(query);

            if (matchesSearch && index <= visibleDeactivated) {
                row.classList.remove('d-none');
            } else if (!matchesSearch) {
                row.classList.add('d-none');
            }
        });

        if (visibleDeactivated >= rows.length || query !== '') {
            const btnContainer = document.getElementById('load-more-deactivated-container');
            if (btnContainer) btnContainer.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const deactivatedSearch = document.getElementById('deactivatedSearchInput');
        if (deactivatedSearch) {
            deactivatedSearch.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.deactivated-manager-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const index = parseInt(row.getAttribute('data-index'));
                    const matches = text.includes(query);
                    if (query === '') {
                        row.classList.toggle('d-none', index > visibleDeactivated);
                    } else {
                        row.classList.toggle('d-none', !matches);
                    }
                });
                const loadMoreContainer = document.getElementById('load-more-deactivated-container');
                if (loadMoreContainer) {
                    loadMoreContainer.style.display = query !== '' ? 'none' : (visibleDeactivated >= rows.length ? 'none' : 'block');
                }
            });
        }
    });
</script>
@endpush