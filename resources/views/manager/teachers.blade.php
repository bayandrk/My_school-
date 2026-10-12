<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل الكادر التدريسي</title>
    
    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- الخط العربي (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Cairo', sans-serif; 
            background-color: #f8fafc; 
            color: #1e293b; 
        }

        /* النافبار الخاصة بالمدير */
        .navbar-manager {
            background-color: #0d6efd;
        }

        /* شارة واسم المدرسة بداخل النافبار */
        .school-navbar-brand {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 0.35rem 0.85rem;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .teacher-card { 
            border: none; 
            border-radius: 14px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease; 
        }

        .badge-class { 
            background-color: #eff6ff; 
            color: #2563eb; 
            border: 1px solid #bfdbfe;
            font-weight: 600; 
            font-size: 0.8rem;
        }

        .form-select-sm, .form-control-sm {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
        }

        .btn-sm-custom {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }
    </style>
</head>
<body>

    <!-- الناڤبار العلوي لمعلم المدير مع شارة اسم المدرسة -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-manager py-3 shadow-sm mb-4">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2 m-0" href="#">
                    لوحة إدارة المدرسة
                </a>

                <!-- شارة اسم المدرسة (سعدالله ونوس) -->
                <div class="school-navbar-brand d-flex align-items-center gap-2 ms-2">
                    <i class="bi bi-building-check text-warning fs-5"></i>
                    <span>
                        @if(isset($manager->school_name) && $manager->school_name)
                            {{ $manager->school_name }}
                        @elseif(auth()->user()->school_name ?? false)
                            {{ auth()->user()->school_name }}
                        @elseif(auth()->user()->school && auth()->user()->school->name)
                            {{ auth()->user()->school->name }}
                        @else
                            سعدالله ونوس
                        @endif
                    </span>
                </div>
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#managerNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="managerNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('manager.dashboard') ? route('manager.dashboard') : url('/manager/dashboard') }}">المهام والطلبات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('manager.classes') ? route('manager.classes') : url('/manager/classes') }}">دليل الصفوف</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ Route::has('manager.teachers') ? route('manager.teachers') : url('/manager/teachers') }}">دليل الأساتذة</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ Route::has('manager.profile') ? route('manager.profile') : url('/manager/profile') }}" class="btn btn-light btn-sm rounded-2 px-3 fw-bold text-primary">
                        الملف الشخصي
                    </a>
                    <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm rounded-2 px-3 fw-bold">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- المحتوى الرئيسي -->
    <div class="container mb-5">

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

                <!-- نموذج البحث والتصفية بحسب العام الدراسي -->
                <form method="GET" action="{{ route('manager.teachers') }}" class="d-flex align-items-center gap-2 flex-wrap">
                    
                    <!-- القائمة المنسدلة للعام الدراسي -->
                    <select name="year" class="form-select form-select-sm fw-semibold" style="width: 110px;" onchange="this.form.submit()">
                        @foreach($availableYears ?? [now()->year, now()->year - 1] as $y)
                            <option value="{{ $y }}" {{ (string)request('year', $selectedYear ?? now()->year) === (string)$y ? 'selected' : '' }}>
                                العام: {{ $y }}
                            </option>
                        @endforeach
                    </select>

                    <!-- حقل البحث باسم الأستاذ أو البريد -->
                    <div class="input-group input-group-sm" style="width: 210px;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="ابحث باسم الأستاذ...">
                        <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-search"></i></button>
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
                            <!-- الجزء الأساسي: الاسم، البريد، الهاتف، وأزرار التحكم -->
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

                                <!-- الأزرار (عرض التفاصيل + الحذف) -->
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-outline-primary btn-sm-custom d-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#teacherDetails{{ $teacher->id }}" aria-expanded="false">
                                        <i class="bi bi-eye"></i> عرض التفاصيل
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm-custom d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $teacher->id }}">
                                        <i class="bi bi-trash"></i> حذف
                                    </button>
                                </div>
                            </div>

                            <!-- تفاصيل الصفوف والطلاب -->
                            <div class="collapse mt-3" id="teacherDetails{{ $teacher->id }}">
                                <hr class="text-muted opacity-25 my-2.5">

                                <!-- الصفوف المسؤول عنها -->
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

                                <!-- الطلاب التابعين له حسب الصفوف -->
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
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold fs-7">العام الدراسي: {{ request('year', $selectedYear ?? now()->year) }}</span>
                                                    </div>
                                                    <ul class="list-group list-group-flush bg-transparent small">
                                                        @foreach($classStudents as $student)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center py-1.5 px-1 bg-transparent border-0">
                                                                <span class="fw-semibold text-secondary"><i class="bi bi-dash me-1"></i> {{ $student->name }}</span>
                                                                <small class="text-muted">{{ $student->health_status ?? 'سليم' }}</small>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted small mb-0">لا يوجد طلاب مرتبطون بهذا المعلم.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- مودال تأكيد الحذف -->
                <div class="modal fade" id="deleteModal{{ $teacher->id }}" tabindex="-1" aria-hidden="true" dir="rtl">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 text-end">
                            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                                <h6 class="modal-title fw-bold text-dark mb-0">تأكيد حذف الأستاذ</h6>
                                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body small text-secondary">
                                هل أنت متأكد من رغبتك في حذف الأستاذ <strong class="text-dark">{{ $teacher->name }}</strong>؟ 
                                <br>
                                <span class="text-muted fs-7">ملاحظة: سيتم فك ارتباط الأستاذ بالطلاب مع الحفاظ على كافة بيانات الطلاب السليمة.</span>
                            </div>
                            <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light rounded-2 btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                                <form action="{{ route('manager.teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger fw-bold btn-sm rounded-2 px-3">تأكيد الحذف</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-person-x fs-1 text-muted d-block mb-2"></i>
                    <h6 class="fw-bold text-muted mb-0">لا يوجد أساتذة متاحون حالياً في المدرسة لهذا العام الدراسي</h6>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>