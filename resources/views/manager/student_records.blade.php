@extends('layouts.app')

@section('title', 'سجلات الطالب - ' . $student->name)

@push('styles')
    <style>
        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .score-badge {
            background-color: var(--primary-color);
            color: var(--btn-text-color, #ffffff);
            border: none;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: inline-block;
        }

        .subject-badge {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .date-badge {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 0.78rem;
            border: 1px solid #e2e8f0;
            direction: ltr;
            display: inline-block;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
        }

        .form-control:focus, .form-select:focus {
            background-color: #fff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .btn-school-main {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color, #ffffff) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
        }

        .pagination .page-item .page-link {
            color: var(--primary-color);
            border-color: #e2e8f0;
            font-weight: 600;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color, #ffffff) !important;
        }
    </style>
@endpush

@section('content')
    <div class="container py-2">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- الهيدر ومعلومات الطالب -->
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm border flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 fs-4" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                    <i class="bi bi-person-vcard-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-1 fs-5">سجلات الطالب: <span style="color: var(--primary-color);">{{ $student->name }}</span></h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill px-3 py-1 fw-bold small" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                            {{ Str::startsWith($student->class, 'الصف') ? $student->class : 'الصف ' . $student->class }}
                        </span>
                        @if($student->section)
                            <span class="badge rounded-pill px-3 py-1 fw-bold small" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                                الشعبة: {{ $student->section }}
                            </span>
                        @endif
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold small">
                            المعدل العام: {{ $student->average_score ?? '-' }}%
                        </span>
                    </div>
                </div>
            </div>

            <a href="{{ route('manager.classes') }}" class="btn btn-outline-dark btn-sm fw-bold px-3">
                <i class="bi bi-arrow-right me-1"></i> العودة لدليل الصفوف
            </a>
        </div>

        <!-- جدول سجلات الطالب مع البحث والتصفية -->
        <div class="card main-card">
            <!-- شريط البحث -->
            <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-text text-primary fs-5"></i> جميع سجلات الطالب
                </h6>

                <form method="GET" action="{{ route('manager.student.records', $student->id) }}" class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="text" name="subject" class="form-control form-control-sm" placeholder="اسم المادة..." value="{{ request('subject') }}" style="width: 140px;">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" style="width: 140px;">

                    <button type="submit" class="btn btn-sm btn-school-main fw-bold px-3 py-1">
                        <i class="bi bi-search"></i> بحث
                    </button>

                    @if(request('date') || request('subject'))
                        <a href="{{ route('manager.student.records', $student->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2 fw-semibold">إعادة ضبط</a>
                    @endif
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 text-muted">#</th>
                                <th class="py-3 text-start ps-4">المادة / الموضوع</th>
                                <th class="py-3">العلامة</th>
                                <th class="py-3">الملاحظة</th>
                                <th class="py-3">الأستاذ المسجل</th>
                                <th class="py-3">تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyChanges as $index => $change)
                                <tr>
                                    <td class="fw-bold text-muted">
                                        {{ method_exists($dailyChanges, 'firstItem') && $dailyChanges->firstItem() ? $dailyChanges->firstItem() + $index : $index + 1 }}
                                    </td>
                                    <td class="text-start ps-4">
                                        <span class="subject-badge">{{ $change->subject }}</span>
                                    </td>
                                    <td>
                                        @if($change->score !== null)
                                            <span class="score-badge">{{ $change->score }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary fw-semibold">{{ $change->note ?? '-' }}</td>
                                    <td>
                                        @if($change->teacher)
                                            <span class="badge badge-teacher text-dark border">{{ $change->teacher->name }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="date-badge">
                                            {{ \Carbon\Carbon::parse($change->recorded_at ?? $change->created_at)->format('H:i | Y-m-d') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        لا توجد سجلات مطابقة لخيارات البحث.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($dailyChanges, 'hasPages') && $dailyChanges->hasPages())
                    <div class="d-flex justify-content-center p-3 border-top">
                        {{ $dailyChanges->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
