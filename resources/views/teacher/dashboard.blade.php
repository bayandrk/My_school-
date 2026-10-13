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
    <div class="row g-3 mb-4">
        <!-- 1. إجمالي الطلاب -->
        <div class="col-md-4">
            <div class="stat-card p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">إجمالي الطلاب المربوطين بحسابك</span>
                    <h3 class="fw-bold text-dark mb-0">
                        {{ $studentsCount ?? $totalStudentsCount ?? (isset($students) ? count($students) : 0) }} <small class="fs-6 text-muted font-normal">طالب</small>
                    </h3>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <!-- 2. إجمالي الصفوف -->
        <div class="col-md-4">
            <div class="stat-card p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">إجمالي الصفوف المسؤول عنها</span>
                    <h3 class="fw-bold text-dark mb-0">
                        {{ $classesCount ?? $totalClassesCount ?? (isset($classes) ? count($classes) : 0) }} <small class="fs-6 text-muted font-normal">صفوف</small>
                    </h3>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-building-gear"></i>
                </div>
            </div>
        </div>

        <!-- 3. إجمالي السجلات -->
        <div class="col-md-4">
            <div class="stat-card p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">إجمالي السجلات والتقييمات المدخلة</span>
                    <h3 class="fw-bold text-dark mb-0">
                        {{ $recordsCount ?? $totalRecordsCount ?? (isset($dailyChanges) ? count($dailyChanges) : 0) }} <small class="fs-6 text-muted font-normal">سجل</small>
                    </h3>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-journal-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول آخر السجلات المضافة -->
    <div class="stat-card overflow-hidden">
        <div class="p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-success fs-5"></i> آخر السجلات المضافة
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
                                <td colspan="5" class="py-4 text-muted">
                                    لا توجد سجلات مضافة حديثاً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection