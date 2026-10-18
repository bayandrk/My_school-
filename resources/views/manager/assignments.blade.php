@extends('layouts.app')

@section('title', 'إدارة تعيين الأساتذة للصفوف')

@push('styles')
    <style>
        .dashboard-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            margin-bottom: 1.5rem;
        }
        .card-header-custom {
            background-color: #ffffff;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem;
            font-weight: 700;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .btn-assign-custom {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            border-radius: 10px;
            padding: 0.65rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .btn-assign-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: var(--btn-text-color) !important;
        }
        .btn-details-custom {
            background-color: #ffffff !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 50rem !important;
            padding: 0.35rem 1rem !important;
            font-size: 0.85rem !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .btn-details-custom:hover, .btn-details-custom:focus {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }
        .btn-action-reject {
            background-color: #ffffff !important;
            border: 1.5px solid #ef4444 !important;
            color: #ef4444 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.825rem !important;
            transition: all 0.2s ease;
            min-width: 85px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .btn-action-reject:hover {
            background-color: #ef4444 !important;
            color: #ffffff !important;
        }
        .form-control-custom {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.55rem 0.85rem;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }
        .combo-dropdown-wrapper {
            position: relative;
        }
        .combo-input-box {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%3c334155' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 0.85rem center;
            background-size: 14px 12px;
            padding-left: 2.2rem !important;
            cursor: pointer;
        }
        .combo-options-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-height: 220px;
            overflow-y: auto;
            margin-top: 4px;
            display: none;
        }
        .combo-options-menu.show {
            display: block;
        }
        .combo-option-item {
            padding: 0.6rem 0.9rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .combo-option-item:hover {
            background-color: color-mix(in srgb, var(--primary-color) 10%, #ffffff);
            color: var(--primary-color);
        }
    </style>
@endpush

@section('content')

    <!-- التنبيهات -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- العنوان العلوي -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">إدارة تعيين الأساتذة للصفوف الدراسية</h5>
        <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-secondary btn-sm fw-bold rounded-pill px-3">
            <i class="bi bi-arrow-right me-1"></i> العودة للوحة التحكم
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center gap-2 text-dark fw-bold">
                        <i class="bi bi-journal-code text-primary fs-5"></i> لوحة تعيين الأساتذة والصفوف
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill small fw-bold">
                        {{ isset($currentAssignments) ? $currentAssignments->count() : 0 }} صف له تعيينات
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <!-- نموذج التعيين الجديد -->
                    <div class="p-3 mb-4 rounded-4 bg-light border">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-plus-fill text-success"></i> تعيين أستاذ جديد لصف
                        </h6>
                        <form action="{{ route('manager.assign-teacher-class') }}" method="POST">
                            @csrf
                            <div class="row g-3 align-items-center">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold small text-secondary mb-1">الأستاذ:</label>
                                    <div class="combo-dropdown-wrapper" id="teacherComboWrapper">
                                        <input type="text" id="teacherInputDisplay" class="form-control form-control-custom combo-input-box bg-white" placeholder="-- اختر الأستاذ أو ابحث بالاسم --" autocomplete="off" required>
                                        <input type="hidden" name="teacher_id" id="hiddenTeacherId" required>

                                        <div class="combo-options-menu" id="teacherOptionsMenu">
                                            @foreach($approvedTeachers as $teacher)
                                                <div class="combo-option-item teacher-option" data-id="{{ $teacher->id }}" data-search="{{ mb_strtolower($teacher->name . ' ' . $teacher->email) }}" data-text="{{ $teacher->name }} ({{ $teacher->email }})">
                                                    <div class="fw-bold">{{ $teacher->name }}</div>
                                                    <small class="text-muted" style="font-size: 0.78rem;">{{ $teacher->email }}</small>
                                                </div>
                                            @endforeach
                                            <div class="p-3 text-center text-muted small d-none" id="noTeacherMsg">لا يوجد أستاذ مطابق للبحث</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-bold small text-secondary mb-1">الصف الدراسي:</label>
                                    <div class="combo-dropdown-wrapper" id="classComboWrapper">
                                        <input type="text" id="classInputDisplay" class="form-control form-control-custom combo-input-box bg-white" placeholder="-- اختر الصف أو ابحث عن اسمه --" autocomplete="off" required>
                                        <input type="hidden" name="class_name" id="hiddenClassName" required>

                                        <div class="combo-options-menu" id="classOptionsMenu">
                                            @isset($classesList)
                                                @foreach($classesList as $className)
                                                    <div class="combo-option-item class-option" data-value="{{ $className }}" data-search="{{ mb_strtolower($className) }}">
                                                        {{ $className }}
                                                    </div>
                                                @endforeach
                                            @endisset
                                            <div class="p-3 text-center text-muted small d-none" id="noClassMsg">لا يوجد صف مطابق للبحث</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2 mt-auto">
                                    <button type="submit" class="btn btn-assign-custom w-100 py-2 d-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-check-lg fs-5"></i> تعيين
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- جدول التعيينات الحالية -->
                    <div>
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-list-check text-primary"></i> التعيينات الحالية (اضغط على التفاصيل لعرض الأساتذة)
                        </h6>

                        @if(isset($currentAssignments) && $currentAssignments->isNotEmpty())
                            <div class="table-responsive border rounded-3 shadow-sm">
                                <table class="table table-hover align-middle mb-0 text-center small bg-white">
                                    <thead class="table-light text-secondary">
                                        <tr>
                                            <th class="py-3" style="width: 60px;">#</th>
                                            <th class="py-3 text-start ps-4">الصف الدراسي</th>
                                            <th class="py-3">عدد الأساتذة المعينين</th>
                                            <th class="py-3" style="width: 180px;">الإجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($currentAssignments as $index => $assignment)
                                            <tr>
                                                <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                                <td class="text-start ps-4">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold fs-6 border border-primary border-opacity-10">
                                                        <i class="bi bi-book-fill me-1"></i> {{ $assignment->class_name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-1 rounded-pill fw-bold">
                                                        {{ $assignment->teachers->count() }} أستاذ
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-details-custom" type="button" data-bs-toggle="collapse" data-bs-target="#teachersDetails{{ $index }}" aria-expanded="false" aria-controls="teachersDetails{{ $index }}">
                                                        <i class="bi bi-eye-fill"></i> التفاصيل
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td colspan="4" class="p-0 border-0">
                                                    <div class="collapse px-4 py-3" id="teachersDetails{{ $index }}">
                                                        <div class="card card-body bg-white border shadow-sm rounded-3">
                                                            <h6 class="fw-bold text-secondary mb-3 small text-start">
                                                                <i class="bi bi-people-fill me-1"></i> الأساتذة المعينون في ({{ $assignment->class_name }}):
                                                            </h6>
                                                            <div class="d-flex flex-column gap-2">
                                                                @foreach($assignment->teachers as $teacher)
                                                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border">
                                                                        <div class="text-start">
                                                                            <span class="fw-bold text-dark">{{ $teacher->name }}</span>
                                                                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $teacher->email }}</small>
                                                                        </div>
                                                                        <div>
                                                                            <form action="{{ route('manager.unassign-teacher-class') }}" method="POST" class="m-0">
                                                                                @csrf
                                                                                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                                                                <input type="hidden" name="class_name" value="{{ $assignment->class_name }}">
                                                                                <button type="submit" class="btn btn-action-reject py-1 px-2.5 small" onclick="return confirm('هل أنت متأكد من إلغاء تعيين الأستاذ {{ $teacher->name }} عن صف {{ $assignment->class_name }}؟')">
                                                                                    <i class="bi bi-x-circle-fill me-1"></i> إلغاء التعيين
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 border rounded-3 bg-light">
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle fs-5 d-block mb-1"></i>
                                    لا توجد أي تعيينات نشطة حالياً بين الأساتذة والصفوف. قم بتعيين أستاذ لصف باستخدام النموذج أعلاه.
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function setupComboDropdown(inputEl, hiddenEl, menuEl, itemSelector, noMsgEl, onSelectCallback) {
            if (!inputEl || !hiddenEl || !menuEl) return;
            inputEl.addEventListener('focus', () => { menuEl.classList.add('show'); });
            inputEl.addEventListener('click', () => { menuEl.classList.add('show'); });
            inputEl.addEventListener('input', function () {
                menuEl.classList.add('show');
                const query = this.value.toLowerCase().trim();
                let visibleCount = 0;
                const items = menuEl.querySelectorAll(itemSelector);
                items.forEach(item => {
                    const searchData = item.getAttribute('data-search') || '';
                    if (searchData.includes(query)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                if (noMsgEl) {
                    if (visibleCount === 0) noMsgEl.classList.remove('d-none');
                    else noMsgEl.classList.add('d-none');
                }
                if (query === '') hiddenEl.value = '';
            });
            const items = menuEl.querySelectorAll(itemSelector);
            items.forEach(item => {
                item.addEventListener('click', function () {
                    onSelectCallback(this);
                    menuEl.classList.remove('show');
                });
            });
            document.addEventListener('click', function (e) {
                if (!inputEl.parentElement.contains(e.target)) menuEl.classList.remove('show');
            });
        }
        setupComboDropdown(
            document.getElementById('teacherInputDisplay'),
            document.getElementById('hiddenTeacherId'),
            document.getElementById('teacherOptionsMenu'),
            '.teacher-option',
            document.getElementById('noTeacherMsg'),
            function (selectedItem) {
                document.getElementById('hiddenTeacherId').value = selectedItem.getAttribute('data-id');
                document.getElementById('teacherInputDisplay').value = selectedItem.getAttribute('data-text');
            }
        );
        setupComboDropdown(
            document.getElementById('classInputDisplay'),
            document.getElementById('hiddenClassName'),
            document.getElementById('classOptionsMenu'),
            '.class-option',
            document.getElementById('noClassMsg'),
            function (selectedItem) {
                val = selectedItem.getAttribute('data-value');
                document.getElementById('hiddenClassName').value = val;
                document.getElementById('classInputDisplay').value = val;
            }
        );
    });
</script>
@endpush