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
                <a href="{{ route('manager.teachers.deactivated') }}" class="btn btn-outline-danger btn-sm-custom d-flex align-items-center gap-1">
                    <i class="bi bi-person-x-fill"></i> الأساتذة غير المفعلين
                </a>
            </div>
        </div>
    </div>

    <!-- عرض بطاقات الأساتذة -->
    <div class="row g-3">
        @forelse($teachers as $teacher)
            @php
                $teacherClasses = $teacher->students->pluck('class')->unique()->filter();
                $studentsByClass = $teacher->students->groupBy('class');
            @endphp
            <div class="col-12">
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
                                    <i class="bi bi-journal-check text-primary me-1"></i> الصفوف المسؤول عنها ({{ $teacherClasses->count() }}): انقر على الصف لعرض طلابه
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse($teacherClasses as $class)
                                        @php
                                            $classCount = isset($studentsByClass[$class]) ? $studentsByClass[$class]->count() : 0;
                                        @endphp

                                        <!-- زر يفتح نافذة منبثقة خاصة بطلاب هذا الصف -->
                                        <button type="button" class="btn badge-class px-3 py-2 rounded-2 border shadow-sm text-start" data-bs-toggle="modal" data-bs-target="#classModal{{ $teacher->id }}_{{ Str::slug($class) }}">
                                            <i class="bi bi-building me-1"></i> {{ $class }} <span class="badge bg-primary text-white ms-1">{{ $classCount }} طلاب</span>
                                        </button>

                                        <!-- نافذة الـ Modal لكل صف -->
                                        <div class="modal fade" id="classModal{{ $teacher->id }}_{{ Str::slug($class) }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content border-0 shadow rounded-4">
                                                    <div class="modal-header border-bottom pb-2">
                                                        <h5 class="fw-bold text-dark mb-0 fs-6">
                                                            <i class="bi bi-people-fill text-primary me-2"></i> طلاب صف: {{ $class }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2 rounded-2 small text-muted">
                                                            <span>المعلم المسؤول: <strong class="text-dark">{{ $teacher->name }}</strong></span>
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">العام: {{ request('year', $selectedYear ?? now()->year) }}</span>
                                                        </div>

                                                        @if(isset($studentsByClass[$class]) && $studentsByClass[$class]->count() > 0)
                                                            <div class="list-group rounded-3 shadow-sm">
                                                                @foreach($studentsByClass[$class] as $student)
                                                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                                                        <span class="fw-bold text-dark small"><i class="bi bi-person-badge me-2 text-muted"></i>{{ $student->name }}</span>
                                                                        <span class="badge bg-light text-secondary border small">العمر: {{ $student->age }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <p class="text-muted small text-center py-3 mb-0">لا يوجد طلاب مسجلين في هذا الصف لهذا المعلم.</p>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer border-top pt-2">
                                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                                                    </div>
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
    </div>

@endsection