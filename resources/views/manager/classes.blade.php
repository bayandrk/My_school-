@extends('layouts.app')

@section('title', 'دليل الصفوف')

@push('styles')
    <style>
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .class-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.9rem 1.25rem;
        }

        .form-select-sm, .form-control-sm {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
        }

        .form-select-sm:focus, .form-control-sm:focus {
            background-color: #fff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .btn-sm-custom {
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.35rem 0.85rem;
            transition: all 0.2s ease;
        }

        /* أزرار مخصصة بلون المدرسة الديناميكي */
        .btn-school-dynamic {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
        }

        .btn-school-dynamic:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }

        .badge-teacher {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-search-school {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
        }
    </style>
@endpush

@section('content')

    <!-- رسالة النجاح -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold mb-3 rounded-3 shadow-sm py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- هيدر الصفوف مع التصفية السريعة -->
    <div class="card card-custom mb-4 p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-journal-bookmark-fill text-primary"></i> دليل الصفوف والطلاب
            </h5>

            <!-- فلاتر البحث والتصفية مع زر الأرشيف -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form action="{{ route('manager.classes') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap m-0">
                    <!-- العام الدراسي -->
                    <select name="year" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()" style="width: 110px;">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ ($selectedYear ?? '') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <!-- تصفية حسب الصف -->
                    <select name="class_filter" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()" style="width: 130px;">
                        <option value="">كل الصفوف</option>
                        @foreach($allClassesList ?? [] as $cls)
                            <option value="{{ $cls }}" {{ (request('class_filter') == $cls) ? 'selected' : '' }}>
                                {{ $cls }}
                            </option>
                        @endforeach
                    </select>

                    <!-- تصفية حسب اسم الطالب -->
                    <div class="input-group input-group-sm" style="width: 210px;">
                        <input type="text" name="search_student" class="form-control form-control-sm" placeholder="بحث باسم الطالب..." value="{{ request('search_student') }}">
                        <button class="btn btn-search-school fw-bold" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    
                    @if(request('search_student') || request('class_filter'))
                        <a href="{{ route('manager.classes') }}" class="btn btn-outline-secondary btn-sm-custom">إلغاء</a>
                    @endif
                </form>

                <!-- زر الانتقال لصفحة أرشيف الطلاب -->
                <a href="{{ route('manager.children.archived') }}" class="btn btn-outline-danger btn-sm-custom d-flex align-items-center gap-1">
                    <i class="bi bi-box-archive-fill"></i> أرشيف الطلاب
                </a>
            </div>
        </div>
    </div>

    <!-- عرض الجداول لكل صف -->
    @if($childrenGroupedByClass->isEmpty())
        <div class="card card-custom text-center text-muted py-5">
            <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
            <h6 class="fw-bold">لا توجد صفوف أو طلاب مطابقة لخيارات البحث الحالية.</h6>
        </div>
    @else
        @foreach($childrenGroupedByClass as $className => $students)
            <div class="card card-custom mb-4">
                <div class="class-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-6">
                            <i class="bi bi-building text-primary me-1"></i> {{ Str::startsWith($className, 'الصف') ? $className : 'الصف ' . $className }}
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small fw-bold">
                            {{ $students->count() }} طالب
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('manager.class.scores', ['class_name' => $className]) }}" class="btn btn-sm-custom btn-school-dynamic d-flex align-items-center gap-1">
                            <i class="bi bi-award-fill"></i> عرض العلامات
                        </a>
                        <a href="{{ route('manager.class.notes', ['class_name' => $className]) }}" class="btn btn-sm-custom btn-school-dynamic d-flex align-items-center gap-1">
                            <i class="bi bi-journal-text"></i> عرض الملاحظات
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2.5">#</th>
                                    <th class="py-2.5 text-start ps-3">اسم الطالب</th>
                                    <th class="py-2.5">العمر</th>
                                    <th class="py-2.5">الحالة الصحية</th>
                                    <th class="py-2.5">الأساتذة المدرسون</th>
                                    <th class="py-2.5">المعدل العام</th>
                                    <th class="py-2.5">تفاصيل السجلات</th>
                                    <th class="py-2.5">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="text-start ps-3 fw-bold text-dark">{{ $student->name }}</td>
                                        <td class="text-secondary">{{ $student->age }} سنة</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $student->health_status ?? 'سليم' }}
                                            </span>
                                        </td>
                                        <td>
                                            @forelse($student->teachers as $t)
                                                <span class="badge badge-teacher me-1">{{ $t->name }}</span>
                                            @empty
                                                <span class="text-muted small">غير معين</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if(isset($student->average_score) && $student->average_score !== null)
                                                <span class="badge bg-success rounded-pill px-2.5 py-1">
                                                    {{ $student->average_score }}%
                                                </span>
                                            @else
                                                <span class="text-muted small">لا توجد درجات</span>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- زر عرض السجلات باللون الديناميكي -->
                                            <button type="button" class="btn btn-sm-custom btn-school-dynamic py-1 px-2.5 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#studentModal{{ $student->id }}">
                                                <i class="bi bi-clock-history"></i> عرض السجلات
                                            </button>

                                            <!-- Modal عرض السجلات -->
                                            <div class="modal fade text-start" id="studentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content text-end rounded-4">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h6 class="modal-title fw-bold">سجلات الطالب: {{ $student->name }} - {{ $className }}</h6>
                                                            <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @if($student->dailyChanges->isEmpty())
                                                                <p class="text-center text-muted py-3 small mb-0">لا توجد سجلات يومية أو علامات للطفل بعد.</p>
                                                            @else
                                                                <table class="table table-sm table-striped text-center align-middle mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>المادة / النشاط</th>
                                                                            <th>الدرجة</th>
                                                                            <th>الملاحظة</th>
                                                                            <th>التاريخ</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($student->dailyChanges as $change)
                                                                            <tr>
                                                                                <td>{{ $change->subject }}</td>
                                                                                <td>
                                                                                    @if($change->score !== null)
                                                                                        <span class="badge bg-success">{{ $change->score }}</span>
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $change->note ?? '-' }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($change->recorded_at)->format('Y-m-d') }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <!-- تم استبدال الحذف بزر الأرشفة الآمن -->
                                                <form action="{{ route('manager.children.archive', $student->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في أرشفة ونقل الطالب {{ $student->name }} إلى الأرشيف؟');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm-custom btn-school-dynamic py-1 px-2.5">
                                                        <i class="bi bi-box-arrow-down me-1"></i> أرشفة
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection