@extends('layouts.app')

@section('title', 'السجلات المدخلة')

@push('styles')
    <style>
        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.875rem;
            margin-bottom: 0.35rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.55rem 0.85rem;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .subject-badge {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 8px;
            font-size: 0.825rem;
            display: inline-block;
        }

        .date-badge {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.78rem;
            border: 1px solid #e2e8f0;
            direction: ltr;
            display: inline-block;
        }

        .score-badge {
            background-color: var(--primary-color);
            color: var(--btn-text-color);
            border: none;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: inline-block;
        }

        .class-section-badge {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.78rem;
        }

        .btn-school-main {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            transition: all 0.2s ease;
        }

        .btn-school-main:hover {
            opacity: 0.9;
            color: var(--btn-text-color) !important;
        }

        .btn-action-edit {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.3rem 0.85rem !important;
            font-size: 0.8rem !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            min-width: 75px;
            justify-content: center;
        }

        .btn-action-edit:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }

        .btn-action-delete {
            background-color: #ffffff !important;
            border: 1.5px solid #ef4444 !important;
            color: #ef4444 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.3rem 0.85rem !important;
            font-size: 0.8rem !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            min-width: 75px;
            justify-content: center;
        }

        .btn-action-delete:hover {
            background-color: #ef4444 !important;
            color: #ffffff !important;
        }

        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            color: var(--primary-color);
            border-color: #e2e8f0;
            font-weight: 600;
            padding: 0.4rem 0.75rem;
            transition: all 0.2s ease;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color, #ffffff) !important;
        }

        .pagination .page-item .page-link:hover {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 0.65rem;
        }

        .modal-rtl-fix {
            text-align: right !important;
            direction: rtl !important;
        }

        .modal-rtl-fix .form-label,
        .modal-rtl-fix .form-control,
        .modal-rtl-fix .modal-title {
            text-align: right !important;
            direction: rtl !important;
        }
    </style>
@endpush

@section('content')

    <!-- رسائل النجاح أو الأخطاء -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- نموذج إضافة سجل جديد -->
    <div class="card main-card mb-4">
        <div class="p-3 bg-white border-bottom fw-bold d-flex align-items-center gap-2 text-primary">
            <i class="bi bi-plus-circle-fill fs-5"></i> <span>إضافة سجل جديد (علامة / ملاحظة / نشاط)</span>
        </div>
        <div class="card-body p-3.5">
            <form action="{{ route('teacher.daily-changes.store') }}" method="POST">
                @csrf
                
                <div class="row g-2.5">
                    <div class="col-md-3">
                        <label class="form-label">اختر الصف:</label>
                        <select id="class_select" class="form-select" required>
                            <option value="">-- اختر الصف أولاً --</option>
                            @foreach(array_unique(array_map(fn($k) => explode('||', $k)[0], array_keys($studentsGroupedByClassSection->all()))) as $className)
                                <option value="{{ $className }}">{{ $className }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">اختر الشعبة:</label>
                        <select id="section_select" class="form-select" required disabled>
                            <option value="">-- اختر الصف أولاً --</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">اختر الطالب:</label>
                        <select name="child_id" id="student_select" class="form-select" required disabled>
                            <option value="">-- اختر الشعبة أولاً --</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">المادة / النشاط:</label>
                        <select id="subjectSelect" class="form-select">
                            <option value="">-- اختر المادة --</option>
                            <option value="__custom__">✏️ مخصص (كتابة حرة)...</option>
                            @foreach($schoolSubjects as $subj)
                                <option value="{{ $subj }}">{{ $subj }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="subjectCustom" name="subject" class="form-control mt-2 d-none" placeholder="اكتب المادة أو النشاط..." maxlength="255">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">التاريخ والوقت:</label>
                        <input type="datetime-local" name="recorded_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">الدرجة (اختياري):</label>
                        <input type="number" step="0.01" name="score" class="form-control" placeholder="100">
                    </div>

                    <div class="col-md-9">
                        <label class="form-label">الملاحظة:</label>
                        <input type="text" name="note" class="form-control" placeholder="ملاحظات الأداء...">
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-school-main fw-bold px-4 py-2 rounded-3">
                        <i class="bi bi-save me-1"></i> حفظ السجل
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- كرت جدول السجلات -->
    <div class="card main-card">
        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-primary fs-5"></i> سجل التقييمات مرتبة بالتاريخ والوقت
            </h6>

            <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', $selectedDate ?? date('Y-m-d')) }}" style="width: 145px;">
                <button type="submit" class="btn btn-sm btn-school-main fw-bold px-3 py-1">
                    <i class="bi bi-filter"></i> تصفية
                </button>
                @if(request('date'))
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary py-1 px-2 fw-semibold">إظهار الكل</a>
                @endif
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2.5 text-muted">#</th>
                            <th class="py-2.5 text-start ps-3">اسم الطالب</th>
                            <th class="py-2.5">الصف والشعبة</th> <!-- تم إضافة عمود الصف والشعبة هنا -->
                            <th class="py-2.5">المادة</th>
                            <th class="py-2.5">الدرجة</th>
                            <th class="py-2.5">الملاحظات</th>
                            <th class="py-2.5">التاريخ والوقت</th>
                            <th class="py-2.5" style="width: 200px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recordsList = $dailyChanges ?? $records ?? [];
                        @endphp

                        @forelse($recordsList as $index => $record)
                            <tr>
                                <td class="fw-bold text-muted">
                                    {{ method_exists($recordsList, 'firstItem') ? $recordsList->firstItem() + $index : $index + 1 }}
                                </td>
                                <td class="text-start ps-3 fw-bold text-dark">
                                    {{ $record->child->name ?? 'غير محدد' }}
                                </td>
                                <td>
                                    <span class="class-section-badge">
                                        {{ $record->child->class ?? '-' }} {{ $record->child->section ? ' - ' . $record->child->section : '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="subject-badge">
                                        {{ $record->subject ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($record->score !== null)
                                        <span class="score-badge">
                                            {{ $record->score }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-secondary fw-semibold">
                                    {{ $record->note ?? '-' }}
                                </td>
                                <td>
                                    <span class="date-badge">
                                        {{ \Carbon\Carbon::parse($record->recorded_at ?? $record->created_at)->format('H:i | Y-m-d') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <button type="button" class="btn btn-action-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $record->id }}">
                                            <i class="bi bi-pencil-square"></i> تعديل
                                        </button>

                                        <form action="{{ Route::has('teacher.daily-changes.destroy') ? route('teacher.daily-changes.destroy', $record->id) : url('/teacher/daily-changes/' . $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action-delete">
                                                <i class="bi bi-trash3-fill"></i> حذف
                                            </button>
                                        </form>
                                    </div>

                                    <!-- مودال التعديل -->
                                    <div class="modal fade modal-rtl-fix" id="editModal{{ $record->id }}" tabindex="-1" aria-hidden="true" dir="rtl">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 modal-rtl-fix p-2">
                                                <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                                                    <h6 class="modal-title fw-bold text-dark fs-6 mb-0">تعديل السجل</h6>
                                                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ Route::has('teacher.daily-changes.update') ? route('teacher.daily-changes.update', $record->id) : url('/teacher/daily-changes/' . $record->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body text-end modal-rtl-fix">
                                                        <div class="mb-3">
                                                            <label class="form-label">المادة / النشاط:</label>
                                                            <input type="text" name="subject" class="form-control" value="{{ $record->subject }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">التاريخ والوقت:</label>
                                                            <input type="datetime-local" name="recorded_at" class="form-control" value="{{ $record->recorded_at ? \Carbon\Carbon::parse($record->recorded_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">الدرجة:</label>
                                                            <input type="number" step="0.1" min="0" max="100" name="score" class="form-control" value="{{ $record->score }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">الملاحظة:</label>
                                                            <input type="text" name="note" class="form-control" value="{{ $record->note }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-light rounded-3 btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-school-main rounded-3 btn-sm px-3">حفظ التغييرات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    لا توجد سجلات مدخلة لهذا اليوم.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($recordsList, 'hasPages') && $recordsList->hasPages())
                <div class="d-flex justify-content-center p-3 border-top">
                    {{ $recordsList->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // سكربت اختيار المادة المخصصة
            const select = document.getElementById('subjectSelect');
            const custom = document.getElementById('subjectCustom');
            if (select && custom) {
                select.addEventListener('change', function () {
                    if (this.value === '__custom__') {
                        custom.classList.remove('d-none');
                        custom.focus();
                    } else {
                        custom.classList.add('d-none');
                        custom.value = '';
                    }
                });

                const form = custom.closest('form');
                form.addEventListener('submit', function (event) {
                    if (select.value && select.value !== '__custom__') {
                        custom.value = select.value;
                    }
                    if (!custom.value.trim()) {
                        select.required = true;
                        if (select.value === '__custom__' || !select.value) {
                            alert('اختر مادة من القائمة أو اكتب موضوع مخصص.');
                            event.preventDefault();
                        }
                    }
                });
            }

            // سكربت الربط التفاعلي (الصف ← الشعبة ← الطالب)
            const studentsGrouped = @json($studentsGroupedByClassSection ?? []);
            const classSelect   = document.getElementById('class_select');
            const sectionSelect = document.getElementById('section_select');
            const studentSelect = document.getElementById('student_select');

            if (classSelect && sectionSelect && studentSelect) {
                classSelect.addEventListener('change', function () {
                    const selectedClass = this.value;
                    sectionSelect.innerHTML = '<option value="">-- اختر الشعبة --</option>';
                    studentSelect.disabled = true;
                    studentSelect.innerHTML = '<option value="">-- اختر الشعبة أولاً --</option>';

                    if (selectedClass) {
                        const sections = Object.keys(studentsGrouped)
                            .map(k => k.split('||'))
                            .filter(([cls]) => cls === selectedClass)
                            .map(([, sec]) => sec)
                            .filter(s => s !== '')
                            .filter((v, i, arr) => arr.indexOf(v) === i);

                        if (sections.length > 0) {
                            sectionSelect.disabled = false;
                            sections.forEach(sec => {
                                const option = document.createElement('option');
                                option.value = sec;
                                option.textContent = sec;
                                sectionSelect.appendChild(option);
                            });
                        } else {
                            sectionSelect.disabled = true;
                            sectionSelect.innerHTML = '<option value="">-- لا توجد شعب --</option>';
                        }
                    } else {
                        sectionSelect.disabled = true;
                        sectionSelect.innerHTML = '<option value="">-- اختر الصف أولاً --</option>';
                    }
                });

                sectionSelect.addEventListener('change', function () {
                    const selectedClass = classSelect.value;
                    const selectedSection = this.value;
                    studentSelect.innerHTML = '<option value="">-- اختر الطالب --</option>';

                    const key = selectedClass + '||' + selectedSection;
                    if (selectedSection && studentsGrouped[key]) {
                        studentSelect.disabled = false;
                        studentsGrouped[key].forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.textContent = student.name;
                            studentSelect.appendChild(option);
                        });
                    } else {
                        studentSelect.disabled = true;
                        studentSelect.innerHTML = '<option value="">-- اختر الشعبة أولاً --</option>';
                    }
                });
            }
        });
    </script>
@endpush