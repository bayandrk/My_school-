@extends('layouts.app')

@section('title', 'صفوفي وطلابي')

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
            cursor: pointer;
            user-select: none;
        }

        .class-header:hover {
            background-color: color-mix(in srgb, var(--primary-color) 8%, #f8fafc);
        }

        .section-header {
            background-color: #f1f5f9;
            border-bottom: 1px solid #cbd5e1;
            padding: 0.75rem 1rem;
        }

        .section-header-clickable {
            cursor: pointer;
            user-select: none;
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

        .btn-search-school {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
        }

        .btn-school-dynamic {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.75rem !important;
            font-size: 0.8rem !important;
            white-space: nowrap;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-school-dynamic:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }

        .badge-students-count {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            color: var(--primary-color) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;
            font-weight: 800;
        }

        .btn-action-record {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.825rem !important;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 85px;
        }

        .btn-action-record:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }

        .btn-action-edit {
            background-color: #ffffff !important;
            border: 1.5px solid #94a3b8 !important;
            color: #334155 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.825rem !important;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 85px;
        }

        .btn-action-edit:hover {
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
            background-color: color-mix(in srgb, var(--primary-color) 8%, #ffffff) !important;
        }

        .badge-average-score {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 800 !important;
            padding: 0.35rem 0.75rem !important;
        }

        .class-toggle-icon, .section-toggle-icon {
            transition: transform 0.25s ease;
        }

        .class-toggle-icon.collapsed, .section-toggle-icon.collapsed {
            transform: rotate(-90deg);
        }
    </style>
@endpush

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- هيدر الصفوف والفلترة المدمجة -->
    <div class="card card-custom mb-4 p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-journal-bookmark-fill text-primary"></i> الصفوف المتاحة والتابعة لك
            </h5>

            <form method="GET" action="{{ route('teacher.classes') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="ابحث باسم الطالب...">
                    <button class="btn btn-search-school" type="submit"><i class="bi bi-search"></i></button>
                </div>

                <select name="class_name" class="form-select form-select-sm fw-semibold" style="width: 130px;">
                    <option value="">كل الصفوف</option>
                    @foreach($availableClasses as $class)
                        <option value="{{ $class }}" {{ request('class_name') == $class ? 'selected' : '' }}>
                            {{ $class }}
                        </option>
                    @endforeach
                </select>

                <select name="section_name" class="form-select form-select-sm fw-semibold" style="width: 130px;">
                    <option value="">كل الشعب</option>
                    @foreach($availableSections ?? [] as $section)
                        <option value="{{ $section }}" {{ request('section_name') == $section ? 'selected' : '' }}>
                            {{ $section }}
                        </option>
                    @endforeach
                </select>

                <select name="year" id="year" class="form-select form-select-sm fw-semibold" style="width: 100px;">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ (string)$selectedYear === (string)$year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>

                @if(request('search') || request('class_name') || request('section_name'))
                    <a href="{{ route('teacher.classes') }}" class="btn btn-outline-secondary btn-sm rounded-2">إلغاء</a>
                @endif
            </form>
        </div>
    </div>

    <!-- بطاقات ملخصة لقوائم المدرسة -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom p-3 h-100">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-collection text-primary me-1"></i> الصفوف ({{ count($availableClasses) }})</h6>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($availableClasses as $class)
                        <span class="badge rounded-pill" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color); border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);">{{ $class }}</span>
                    @empty
                        <span class="text-muted small">لا توجد صفوف</span>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 h-100">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-diagram-3 text-primary me-1"></i> الشعب ({{ count($availableSections ?? []) }})</h6>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($availableSections ?? [] as $section)
                        <span class="badge rounded-pill" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color); border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);">{{ $section }}</span>
                    @empty
                        <span class="text-muted small">لا توجد شعب</span>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 h-100">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-book text-primary me-1"></i> المواد ({{ count($availableSubjects ?? []) }})</h6>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($availableSubjects ?? [] as $subject)
                        <span class="badge rounded-pill" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color); border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);">{{ $subject }}</span>
                    @empty
                        <span class="text-muted small">لا توجد مواد</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- عرض الصفوف والشعب والطلاب بشكل هرمي -->
    @forelse($groupedClasses as $className => $sections)
        <div class="card card-custom mb-4">
            <!-- 1. هيدر الصف (يفتح الشعب بداخله عند النقر) -->
            <div class="class-header d-flex justify-content-between align-items-center flex-wrap gap-2"
                 data-bs-toggle="collapse" data-bs-target="#classCollapse{{ $loop->index }}"
                 aria-expanded="false" aria-controls="classCollapse{{ $loop->index }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-chevron-down class-toggle-icon collapsed text-primary" style="font-size: 0.85rem;"></i>
                    <span class="fw-bold text-dark fs-6">
                        <i class="bi bi-building text-primary me-1"></i> {{ $className }}
                    </span>
                    <span class="badge badge-students-count rounded-pill px-2.5 py-1 small">
                        {{ $sections->flatten()->count() }} طالب
                    </span>
                </div>
            </div>

            <!-- محتوى الصف (الشعب) - مغلق افتراضياً -->
            <div class="collapse" id="classCollapse{{ $loop->index }}">
                <div class="card-body p-3 bg-light">
                    @foreach($sections as $sectionName => $students)
                        <div class="card border mb-3 shadow-none rounded-3 overflow-hidden">
                            <!-- 2. هيدر الشعبة (يحتوي على أزرار عرض العلامات وعرض الملاحظات الخاصة بالشعبة) -->
                            <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="section-header-clickable d-flex align-items-center gap-2 flex-grow-1"
                                     data-bs-toggle="collapse" data-bs-target="#sectionCollapse{{ $loop->parent->index }}_{{ $loop->index }}"
                                     aria-expanded="false" aria-controls="sectionCollapse{{ $loop->parent->index }}_{{ $loop->index }}">
                                    <i class="bi bi-chevron-down section-toggle-icon collapsed text-secondary" style="font-size: 0.75rem;"></i>
                                    <span class="fw-bold text-secondary">
                                        <i class="bi bi-diagram-3 me-1 text-primary"></i> الشعبة: {{ $sectionName }}
                                    </span>
                                    <span class="badge bg-secondary bg-opacity-15 text-dark rounded-pill px-2 py-0.5 small fw-bold">
                                        {{ $students->count() }} طالب
                                    </span>
                                </div>

                                <!-- أزرار عرض العلامات والملاحظات الخاصة بهذه الشعبة تحديداً -->
                                <div class="d-flex align-items-center gap-2 flex-wrap" onclick="event.stopPropagation();">
                                    <a href="{{ route('teacher.class.scores', ['class_name' => $className . ' - الشعبة ' . $sectionName]) }}" class="btn btn-school-dynamic">
    <i class="bi bi-award-fill"></i> عرض العلامات
</a>
                                    <a href="{{ route('teacher.class.notes', ['class_name' => $className . ' - الشعبة ' . $sectionName]) }}" class="btn btn-school-dynamic">
        <i class="bi bi-journal-text"></i> عرض الملاحظات
    </a>
                                </div>
                            </div>

                            <!-- 3. جدول طلاب الشعبة - مغلق افتراضياً -->
                            <div class="collapse" id="sectionCollapse{{ $loop->parent->index }}_{{ $loop->index }}">
                                <div class="card-body p-0 bg-white">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle text-center mb-0 small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="py-2.5">#</th>
                                                    <th class="py-2.5 text-start ps-3">اسم الطالب</th>
                                                    <th class="py-2.5">العمر</th>
                                                    <th class="py-2.5">الجنس</th>
                                                    <th class="py-2.5">الحالة الصحية</th>
                                                    <th class="py-2.5">المعدل العام</th>
                                                    <th class="py-2.5">ولي الأمر</th>
                                                    <th class="py-2.5" style="width: 220px;">الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students as $index => $student)
                                                    <tr>
                                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                                        <td class="text-start ps-3 fw-bold text-dark">{{ $student->name }}</td>
                                                        <td class="text-secondary">{{ $student->age }} سنة</td>
                                                        <td>
                                                            <span class="badge {{ $student->gender == 'male' ? 'bg-info' : 'bg-danger' }} bg-opacity-10 text-dark fw-semibold">
                                                                {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border font-normal">
                                                                {{ $student->health_status ?? 'سليم' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($student->average_score !== null)
                                                                <span class="badge badge-average-score rounded-pill">
                                                                    {{ $student->average_score }}%
                                                                </span>
                                                            @else
                                                                <span class="text-muted small">لا توجد درجات</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-secondary">{{ $student->parent->name ?? 'غير مرتبط' }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center justify-content-center gap-3">
                                                                <a href="{{ route('teacher.student.records', $student->id) }}" class="btn btn-action-record">
                                                                    <i class="bi bi-clock-history"></i> السجل
                                                                </a>

                                                                @if(Route::has('teacher.children.edit'))
                                                                    <a href="{{ route('teacher.children.edit', $student->id) }}" class="btn btn-action-edit">
                                                                        <i class="bi bi-pencil-square"></i> تعديل
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="card card-custom p-5 text-center my-4">
            <div class="py-3">
                <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-2">لا يوجد طلاب مرتبطون في هذا الصف أو العام الدراسي</h6>
                <a href="{{ Route::has('teacher.children.create') ? route('teacher.children.create') : url('/teacher/children/create') }}" class="btn btn-search-school px-3 py-2 rounded-3 mt-2">
                    + إضافة طفل جديد
                </a>
            </div>
        </div>
    @endforelse

@endsection

@push('scripts')
<script>
    // تدوير الأسهم بسلاسة عند فتح وإغلاق الصفوف والشعب
    document.addEventListener('DOMContentLoaded', function () {
        const collapses = document.querySelectorAll('.collapse');
        collapses.forEach(collapse => {
            collapse.addEventListener('show.bs.collapse', function (e) {
                e.stopPropagation();
                const header = document.querySelector(`[data-bs-target="#${this.id}"]`);
                if (header) {
                    const icon = header.querySelector('.class-toggle-icon, .section-toggle-icon');
                    if (icon) icon.classList.remove('collapsed');
                }
            });
            collapse.addEventListener('hide.bs.collapse', function (e) {
                e.stopPropagation();
                const header = document.querySelector(`[data-bs-target="#${this.id}"]`);
                if (header) {
                    const icon = header.querySelector('.class-toggle-icon, .section-toggle-icon');
                    if (icon) icon.classList.add('collapsed');
                }
            });
        });
    });
</script>
@endpush