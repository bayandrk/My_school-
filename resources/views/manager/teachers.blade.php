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

        /* زر الحذف باللون الديناميكي للمدرسة */
        .btn-delete-dynamic {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
        }

        .btn-delete-dynamic:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
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

            <form method="GET" action="{{ route('manager.teachers') }}" class="d-flex align-items-center gap-2 flex-wrap">
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
                                <!-- زر عرض التفاصيل باللون الديناميكي -->
                                <button class="btn btn-sm-custom btn-school-dynamic d-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#teacherDetails{{ $teacher->id }}" aria-expanded="false">
                                    <i class="bi bi-eye"></i> عرض التفاصيل
                                </button>

                                <!-- زر الحذف بلون المدرسة الديناميكي -->
                                <button type="button" class="btn btn-sm-custom btn-delete-dynamic d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $teacher->id }}">
                                    <i class="bi bi-trash"></i> حذف
                                </button>
                            </div>
                        </div>

                        <!-- تفاصيل الصفوف والطلاب -->
                        <div class="collapse mt-3" id="teacherDetails{{ $teacher->id }}">
                            <hr class="text-muted opacity-25 my-2.5">

                            <div class="mb-3">
                                <h6 class="fw-bold text-dark small mb-2">
                                    <i class="bi bi-journal-check text-primary me-1"></i> الصفوف المسؤول عنها ({{ $teacherClasses->count() }}):
                                </h6>
                                <div class="d-flex flex-wrap gap-1.5">
                                    @forelse($teacherClasses as $class)
                                        <span class="badge badge-class px-2.5 py-1.5 rounded-2">{{ $class }}</span>
                                    @empty
                                        <span class="text-muted small">لا توجد صفوف مخصصة حالياً</span>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <h6 class="fw-bold text-dark small mb-2">
                                    <i class="bi bi-person-video3 text-success me-1"></i> الطلاب التابعين له حسب الصفوف ({{ $teacher->students->count() }}):
                                </h6>

                                @if($teacher->students->count() > 0)
                                    <div class="mt-2.5">
                                        @foreach($studentsByClass as $className => $classStudents)
                                            <div class="mb-2.5 border rounded-3 p-2.5 bg-light">
                                                <div class="fw-bold text-primary small mb-1.5 border-bottom pb-1 d-flex justify-content-between align-items-center">
                                                    <span><i class="bi bi-building me-1"></i> {{ $className }} ({{ $classStudents->count() }} طلاب)</span>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold">العام الدراسي: {{ request('year', $selectedYear ?? now()->year) }}</span>
                                                </div>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($classStudents as $st)
                                                        <span class="badge bg-white text-dark border px-2 py-1">{{ $st->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted small mb-0">لا يوجد طلاب مرتبطين بهذا الأستاذ في العام الحالي.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal الحذف للأستاذ -->
            <div class="modal fade" id="deleteModal{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 p-2">
                        <div class="modal-header border-0 pb-0">
                            <h6 class="modal-title fw-bold text-dark">تأكيد الحذف</h6>
                            <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            هل أنت متأكد من رغبتك في حذف الأستاذ {{ $teacher->name }}؟
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light btn-sm px-3 rounded-2" data-bs-dismiss="modal">إلغاء</button>
                            <form action="{{ route('manager.teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <!-- زر التأكيد داخل المودال بلون المدرسة الديناميكي -->
                                <button type="submit" class="btn btn-sm-custom btn-delete-dynamic px-3">تأكيد الحذف</button>
                            </form>
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