@extends('layouts.app')

@section('title', 'دليل الكادر التدريسي')

@push('styles')
    <style>
        .teacher-card { 
            border: none; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease; 
            background: #ffffff;
        }

        .badge-class { 
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); 
            color: var(--primary-color); 
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
            font-weight: 700; 
            font-size: 0.8rem;
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

        /* زر إلغاء التفعيل */
        .btn-deactivate-dynamic {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
        }

        .btn-deactivate-dynamic:hover {
            opacity: 0.9;
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- الهيدر والبحث والتصفية بالعام الدراسي -->
    <div class="card teacher-card bg-white p-3.5 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i> دليل الكادر التدريسي والصفوف
                </h5>
                <p class="text-muted small mb-0">إدارة المعلمين ومتابعة توزيع الطلاب والصفوف المدرسية</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('manager.teachers') }}" class="d-flex align-items-center gap-2 flex-wrap m-0">
                    <!-- القائمة المنسدلة للعام الدراسي -->
                    <select name="year" class="form-select form-select-sm fw-semibold" style="width: 125px;" onchange="this.form.submit()">
                    @foreach($availableYears ?? [now()->year, now()->year - 1] as $y)
                        <option value="{{ $y }}" {{ (string)request('year', $selectedYear ?? now()->year) === (string)$y ? 'selected' : '' }}>
                                العام: {{ $y }}
                            </option>
                        @endforeach
                    </select>

                    <!-- حقل البحث باسم الأستاذ أو البريد -->
                    <div class="input-group input-group-sm" style="width: 210px;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="ابحث باسم الأستاذ...">
                        <button type="submit" class="btn btn-search-school fw-bold"><i class="bi bi-search"></i></button>
                    </div>

                    @if(request('search') || request('year'))
                        <a href="{{ route('manager.teachers') }}" class="btn btn-outline-secondary btn-sm-custom">إلغاء</a>
                    @endif
                </form>

                <!-- زر الأساتذة غير المفعلين -->
                <a href="{{ route('manager.teachers.deactivated') }}" class="btn btn-sm-custom btn-school-dynamic d-flex align-items-center gap-1">
    <i class="bi bi-person-x-fill"></i> الأساتذة غير المفعلين
</a>
            </div>
        </div>
    </div>

    <!-- عرض بطاقات الأساتذة -->
    <div class="row g-3">
        @forelse($teachers as $tIndex => $teacher)
            @php $teacherRowNum = $tIndex + 1; @endphp
            @php
                // هيكل هرمي: الصف -> الشعبة -> الطلاب
                $teacherClasses = $teacher->students->pluck('class')->unique()->filter()->sort();
                $studentsByClass = $teacher->students->groupBy('class');
            @endphp
            <div class="col-12 teacher-row" data-index="{{ $teacherRowNum }}" style="{{ $teacherRowNum > 10 ? 'display: none;' : '' }}">
                <div class="card teacher-card bg-white">
                    <div class="card-body p-3.5">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h5 class="fw-bold text-dark mb-1 fs-6">{{ $teacher->name }}</h5>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span><i class="bi bi-envelope me-1"></i> {{ $teacher->email }}</span>
                                    @if($teacher->phone)
                                        <span><i class="bi bi-telephone me-1"></i> {{ $teacher->phone }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <!-- زر إلغاء التفعيل مع رسالة تأكيد -->
                                <form action="{{ route('manager.teachers.deactivate', $teacher->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm-custom btn-deactivate-dynamic d-flex align-items-center gap-1" onclick="return confirm('هل أنت متأكد من رغبتك في إلغاء تفعيل حساب هذا الأستاذ؟')">
                                        <i class="bi bi-person-x-fill"></i> إلغاء التفعيل
                                    </button>
                                </form>

                                <!-- زر عرض التفاصيل -->
                                <button class="btn btn-sm-custom btn-school-dynamic d-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#teacherDetails{{ $teacher->id }}" aria-expanded="false" aria-controls="teacherDetails{{ $teacher->id }}">
                                    <i class="bi bi-eye"></i> عرض التفاصيل
                                </button>
                            </div>
                        </div>

                        <!-- تفاصيل الصفوف والطلاب المخفية -->
                        <div class="collapse mt-3" id="teacherDetails{{ $teacher->id }}">
                            <hr class="text-muted opacity-25 my-2.5">

                            <div class="mb-2">
                                <h6 class="fw-bold text-dark small mb-2">
                                    <i class="bi bi-journal-check text-primary me-1"></i> الصفوف المسؤول عنها ({{ $teacherClasses->count() }}): انقر على الصف لعرض شعبه ثم طلابه
                                </h6>
                                <div class="accordion" id="teacherClassesAcc{{ $teacher->id }}">
    @forelse($teacherClasses as $class)
        @php
            $classStudents = $studentsByClass[$class] ?? collect();
            $classCount = $classStudents->count();
            // استخدام md5 بدلاً من Str::slug لدعم الأسماء العربية بشكل صحيح وعزلها فريداً
            $classSlug = $teacher->id . '_' . md5($class);
            $studentsBySection = $classStudents->groupBy(fn($s) => $s->section ?: 'عام');
        @endphp

        <!-- الصف -->
        <div class="accordion-item border rounded-3 mb-2 bg-light shadow-sm">
            <h2 class="accordion-header" id="classHead{{ $classSlug }}">
                <button class="accordion-button collapsed bg-white fw-bold text-dark rounded-3" type="button"
                        data-bs-toggle="collapse" data-bs-target="#classBody{{ $classSlug }}"
                        aria-expanded="false" aria-controls="classBody{{ $classSlug }}">
                    <i class="bi bi-building me-2 text-primary"></i>
                    الصف: {{ $class }}
                    <span class="badge bg-primary text-white ms-2">{{ $classCount }} طلاب</span>
                </button>
            </h2>
            <div id="classBody{{ $classSlug }}" class="accordion-collapse collapse" aria-labelledby="classHead{{ $classSlug }}"
                 data-bs-parent="#teacherClassesAcc{{ $teacher->id }}">
                <div class="accordion-body py-2">
                    @if($classCount > 0)
                        <!-- شعب الصف -->
                        <div class="accordion accordion-flush" id="sectionsAcc{{ $classSlug }}">
                            @foreach($studentsBySection as $sectionName => $sectionStudents)
                                @php
                                    $sectionSlug = $classSlug . '_' . md5($sectionName);
                                    $sectionCount = $sectionStudents->count();
                                @endphp

                                <!-- الشعبة -->
                                <div class="accordion-item border rounded-3 mb-2 bg-white">
                                    <h2 class="accordion-header" id="secHead{{ $sectionSlug }}">
                                        <button class="accordion-button collapsed fw-bold text-secondary bg-light rounded-3 py-2" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#secBody{{ $sectionSlug }}"
                                                aria-expanded="false" aria-controls="secBody{{ $sectionSlug }}">
                                            <i class="bi bi-diagram-3 me-2 text-info"></i>
                                            الشعبة: {{ $sectionName }}
                                            <span class="badge bg-secondary bg-opacity-10 text-dark ms-2">{{ $sectionCount }} طالب</span>
                                        </button>
                                    </h2>
                                    <div id="secBody{{ $sectionSlug }}" class="accordion-collapse collapse" aria-labelledby="secHead{{ $sectionSlug }}"
                                         data-bs-parent="#sectionsAcc{{ $classSlug }}">
                                        <div class="accordion-body py-2">
                                            @php $secId = $sectionSlug; @endphp
                                            <div class="list-group rounded-3 shadow-sm">
    @foreach($sectionStudents as $student)
        <div class="modal-student-row-{{ $secId }} list-group-item d-flex justify-content-between align-items-center py-2 px-3" data-index="{{ $loop->iteration }}" style="{{ $loop->iteration > 10 ? 'display: none !important;' : '' }}">
            <span class="fw-bold text-dark small"><i class="bi bi-person-badge me-2 text-muted"></i>{{ $student->name }}</span>
            <span class="badge bg-light text-secondary border small">العمر: {{ $student->age }}</span>
        </div>
    @endforeach
</div>

                                            @if($sectionCount > 10)
                                                <div class="text-center py-3 border-top" id="load-more-modal-{{ $secId }}">
                                                    <button type="button" onclick="loadMoreModalStudents('{{ $secId }}', {{ $sectionCount }})" class="btn btn-sm-custom btn-school-dynamic btn-sm px-4 fw-bold rounded-3">
                                                        عرض المزيد <i class="bi bi-chevron-down ms-1"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small text-center py-2 mb-0">لا يوجد طلاب مسجلين في هذا الصف لهذا المعلم.</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <span class="text-muted small">لا توجد صفوف مخصصة حالياً</span>
    @endforelse
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="card teacher-card text-center text-muted py-5 bg-white">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <h6 class="fw-bold">لا يوجد أساتذة مضافون حالياً.</h6>
                </div>
            </div>
        @endforelse

        @if($teachers->count() > 10)
            <div class="col-12 text-center py-3" id="load-more-teachers-container">
                <button type="button" onclick="loadMoreTeachers()" class="btn btn-sm-custom btn-school-dynamic px-4 fw-bold rounded-3">
                    عرض المزيد <i class="bi bi-chevron-down ms-1"></i>
                </button>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    // خريطة لتخزين العدادات لكل عنصر
    const visibleTeachersMap = {};

    function loadMoreTeachers() {
        if (!visibleTeachersMap.main) visibleTeachersMap.main = 10;
        visibleTeachersMap.main += 10;

        const rows = document.querySelectorAll('.teacher-row');
        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            if (index <= visibleTeachersMap.main) row.style.display = '';
        });

        if (visibleTeachersMap.main >= rows.length) {
            const btnContainer = document.getElementById('load-more-teachers-container');
            if (btnContainer) btnContainer.style.display = 'none';
        }
    }

    function loadMoreModalStudents(modalId, totalCount) {
        // إذا لم يتم النقر من قبل، نبدأ من 10 (لأن أول 10 تكون ظاهرة افتراضياً عبر style="display: none;")
        if (!visibleTeachersMap['modal_' + modalId]) {
            visibleTeachersMap['modal_' + modalId] = 10;
        }
        
        // في كل ضغطة نضيف 10 طلاب إضافيين
        visibleTeachersMap['modal_' + modalId] += 10;

        const currentLimit = visibleTeachersMap['modal_' + modalId];
        const rows = document.querySelectorAll(`.modal-student-row-${modalId}`);
        
        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            if (index <= currentLimit) {
                row.style.display = ''; // إظهار الطالب
            }
        });

        // إذا تجاوزنا أو وصلنا للعدد الكلي، نقوم بإخفاء زر "عرض المزيد"
        if (currentLimit >= totalCount) {
            const btnContainer = document.getElementById(`load-more-modal-${modalId}`);
            if (btnContainer) {
                btnContainer.style.display = 'none';
            }
        }
    }
</script>
@endpush