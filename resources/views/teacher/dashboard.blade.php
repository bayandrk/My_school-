@extends('layouts.app')

@section('title', 'لوحة تحكم المعلم')

@push('styles')
    <style>
        .stat-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 0.75rem;
        }

        .badge-record-type {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')

    <!-- الترحيب باسم المعلم المسجل حالياً -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">
            مرحباً، {{ auth()->user()->name }}
        </h3>
    </div>

    <!-- الإحصائيات السريعة -->
   <!-- كروت الإحصائيات بتصميم منسق ومضبوط لليمين واليسار -->
<div class="row g-3 mb-4">
    <!-- إجمالي السجلات والتقييمات -->
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center justify-content-between p-3 bg-white rounded-4 shadow-sm border">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon d-flex align-items-center justify-content-center rounded-3 fs-4" style="width: 50px; height: 50px; background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">إجمالي السجلات والتقييمات المدخلة</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalRecords ?? 0 }} <span class="fs-6 text-muted fw-normal">سجل</span></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- إجمالي الصفوف المسؤول عنها -->
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center justify-content-between p-3 bg-white rounded-4 shadow-sm border">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon d-flex align-items-center justify-content-center rounded-3 fs-4" style="width: 50px; height: 50px; background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                    <i class="bi bi-building-gear"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">إجمالي الصفوف المسؤول عنها</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalClasses ?? 0 }} <span class="fs-6 text-muted fw-normal">صفوف</span></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- إجمالي الطلاب المربوطين بحسابك -->
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center justify-content-between p-3 bg-white rounded-4 shadow-sm border">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon d-flex align-items-center justify-content-center rounded-3 fs-4" style="width: 50px; height: 50px; background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">إجمالي الطلاب المربوطين بحسابك</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalStudents ?? 0 }} <span class="fs-6 text-muted fw-normal">طالب</span></h4>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- جدول آخر السجلات المضافة -->
    <div class="stat-card overflow-hidden">
        <div class="p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-success fs-5"></i> تقارير اليوم
            </h6>
            <a href="{{ Route::has('teacher.daily-changes') ? route('teacher.daily-changes') : url('/teacher/daily-changes') }}" class="btn btn-success btn-sm fw-bold px-3 rounded-2">
                عرض كافة السجلات
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2.5 text-muted">#</th>
                            <th class="py-2.5 text-start ps-4">اسم الطالب</th>
                            <th class="py-2.5">الصف والشعبة</th>
                            <th class="py-2.5">نوع السجل / التقييم</th>
                            <th class="py-2.5">التاريخ</th>
                            <th class="py-2.5">الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $latestRecords = $recentRecords ?? $dailyChanges ?? [];
                        @endphp

                        @forelse($latestRecords as $index => $record)
                            <tr>
                                <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                <td class="text-start ps-4 fw-bold text-dark">
                                    {{ $record->child->name ?? $record->student->name ?? 'طالب' }}
                                </td>
                                <td>
                                    @php
                                        $child = $record->child ?? $record->student ?? null;
                                    @endphp
                                    <span class="text-secondary">
                                        {{ $child->class ?? '-' }} {{ $child->section ? ' - ' . $child->section : '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-record-type">
                                        {{ $record->subject ?? 'سجل يومي' }}
                                    </span>
                                </td>
                                <td class="text-secondary dir-ltr">
                                    {{ \Carbon\Carbon::parse($record->recorded_at ?? $record->created_at)->format('H:i Y-m-d') }}
                                </td>
                                <td class="text-secondary">
                                    {{ $record->note ?? $record->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-muted">
                                    لا توجد سجلات مضافة اليوم.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($latestRecords, 'hasPages') && $latestRecords->hasPages())
                <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top small text-muted">
                    <span>عرض {{ $latestRecords->firstItem() }} - {{ $latestRecords->lastItem() }} من {{ $latestRecords->total() }} سجل</span>
                    <span class="badge bg-success-subtle text-success fw-bold">10 سجلات في كل صفحة</span>
                </div>
                <div class="d-flex justify-content-center pb-3">
                    {{ $latestRecords->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

@endsection