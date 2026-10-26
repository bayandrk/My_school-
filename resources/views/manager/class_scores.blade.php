@extends('layouts.app')

@section('title', 'علامات الصف - ' . $className)

@push('styles')
    <style>
        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-control-sm, .form-select-sm {
            border-radius: 8px;
            font-size: 0.875rem;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
        }

        .form-control-sm:focus, .form-select-sm:focus {
            background-color: #fff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        /* زر البحث بلون المدرسة Dynamic */
        .btn-search-school {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        /* شارة المادة متناسقة مع لون المدرسة */
        .subject-badge {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            border: 1px solid color-mix(in srgb, var(--primary-color) 25%, transparent);
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .score-badge {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
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

        .table > :not(caption) > * > * {
            padding: 0.85rem 0.75rem;
        }

        .btn-back-custom {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px;
            font-size: 0.85rem;
            padding: 0.35rem 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-back-custom:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }
    </style>
@endpush

@section('content')

    <!-- الشريط العلوي والعنوان والفلترة المجمعة -->
    <div class="main-card p-3.5 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('manager.classes') }}" class="btn btn-back-custom d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-right"></i> العودة لدليل الصفوف
                </a>
                <h4 class="fw-bold text-dark mb-0 fs-5 d-flex align-items-center gap-2">
                    <i class="bi bi-award-fill text-primary"></i> علامات طلاب: <span class="text-primary">{{ $className }}</span>
                </h4>
            </div>

            <!-- نموذج البحث والتصفية المتكامل (الاسم، المادة، العام الدراسي) -->
            <form method="GET" action="{{ route('manager.class.scores') }}" class="d-flex align-items-center gap-2 flex-wrap mb-0">
                <input type="hidden" name="class_name" value="{{ $className }}">
                
                <!-- تصفية العام الدراسي -->
                <select name="year" class="form-select form-select-sm fw-semibold" style="width: 115px;" onchange="this.form.submit()">
                    @foreach($availableYears ?? [now()->year, now()->year - 1] as $y)
                        <option value="{{ $y }}" {{ (string)request('year', $selectedYear ?? now()->year) === (string)$y ? 'selected' : '' }}>
                            العام: {{ $y }}
                        </option>
                    @endforeach
                </select>

                <!-- بحث باسم الطالب -->
                <input type="text" name="search_name" value="{{ request('search_name', $searchName ?? '') }}" class="form-control form-control-sm" placeholder="بحث باسم الطالب..." style="width: 160px;">

                <!-- بحث بالمادة -->
                <select name="search_subject" class="form-select form-select-sm" style="width: 140px;">
                    <option value="">كل المواد</option>
                    @if(isset($subjects))
                        @foreach($subjects as $subj)
                            <option value="{{ $subj }}" {{ request('search_subject', $searchSubject ?? '') == $subj ? 'selected' : '' }}>{{ $subj }}</option>
                        @endforeach
                    @endif
                </select>

                <button type="submit" class="btn btn-search-school fw-bold px-3 py-1">بحث</button>

                @if(request('search_name') || request('search_subject') || request('year'))
                    <a href="{{ route('manager.class.scores', ['class_name' => $className]) }}" class="btn btn-outline-secondary btn-sm rounded-2 px-2">إلغاء</a>
                @endif
            </form>
        </div>
    </div>

    <!-- جدول عرض العلامات -->
    <div class="main-card">
        <div class="card-body p-0">
            @if($students->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="fw-bold mb-0 small">لا توجد سجلات علامات مطابقة لخيارات البحث حالياً.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">#</th>
                                <th class="py-3 text-start ps-4">اسم الطالب</th>
                                <th class="py-3">المادة / النشاط</th>
                                <th class="py-3">الدرجة</th>
                                <th class="py-3">ملاحظة مرافقة</th>
                                <th class="py-3">تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @foreach($students as $student)
                                @foreach($student->dailyChanges as $change)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $counter++ }}</td>
                                        <td class="text-start ps-4 fw-bold text-dark">{{ $student->name }}</td>
                                        <td>
                                            <span class="subject-badge">{{ $change->subject }}</span>
                                        </td>
                                        <td>
                                            <span class="score-badge">{{ $change->score }}</span>
                                        </td>
                                        <td class="text-secondary fw-semibold">
                                            {{ $change->note ?? '-' }}
                                        </td>
                                        <td>
                                            <span class="date-badge">
                                                {{ \Carbon\Carbon::parse($change->recorded_at ?? $change->created_at)->format('Y-m-d') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection