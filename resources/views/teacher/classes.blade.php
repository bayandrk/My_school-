<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفوفي وطلابي</title>

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

        /* النافبار الخضراء الخاصة بالمعلم */
        .navbar-teacher {
            background-color: #0d7a5f;
        }

        /* شارة واسم المدرسة بداخل النافبار */
        .school-navbar-brand {
            background-color: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            padding: 0.35rem 0.85rem;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 700;
        }

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
            padding: 0.85rem 1.25rem;
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

    <!-- Navbar مع اسم المدرسة في النافبار -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-teacher py-3 shadow-sm mb-4">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2 m-0" href="#">
                    <i class="bi bi-person-badge-fill"></i> لوحة تحكم المعلم
                </a>
                
                <!-- عرض اسم المدرسة الذي أدخله المعلم (سعدالله ونوس) -->
                <div class="school-navbar-brand d-flex align-items-center gap-2 ms-2">
                    <i class="bi bi-building-check text-warning fs-5"></i>
                    <span>
                        @if(isset($teacher->school_name) && $teacher->school_name)
                            {{ $teacher->school_name }}
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
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#teacherNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="teacherNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('teacher.dashboard') ? route('teacher.dashboard') : url('/teacher/dashboard') }}">الرئيسية والمهام</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('teacher.children.create') ? route('teacher.children.create') : url('/teacher/children/create') }}">إضافة طفل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ Route::has('teacher.classes') ? route('teacher.classes') : url('/teacher/classes') }}">صفوفي وطلابي</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('teacher.daily-changes') ? route('teacher.daily-changes') : (Route::has('teacher.records') ? route('teacher.records') : url('/teacher/daily-changes')) }}">السجلات المدخلة</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ Route::has('teacher.profile') ? route('teacher.profile') : url('/teacher/profile') }}" class="btn btn-light btn-sm rounded-2 px-3 fw-bold text-success d-flex align-items-center gap-1">
                        <i class="bi bi-person-circle"></i> الملف الشخصي
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

    <!-- Main Content -->
    <div class="container pb-5">

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
                    <i class="bi bi-journal-bookmark-fill text-success"></i> الصفوف المتاحة والتابعة لك
                </h5>

                <form method="GET" action="{{ route('teacher.classes') }}" class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- 1. البحث باسم الطالب -->
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="ابحث باسم الطالب...">
                        <button class="btn btn-success fw-bold" type="submit"><i class="bi bi-search"></i></button>
                    </div>

                    <!-- 2. القائمة المنسدلة لاختيار الصف -->
                    <select name="class_name" class="form-select form-select-sm fw-semibold" style="width: 130px;">
                        <option value="">كل الصفوف</option>
                        @foreach($availableClasses as $class)
                            <option value="{{ $class }}" {{ request('class_name') == $class ? 'selected' : '' }}>
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>

                    <!-- 3. العام الدراسي -->
                    <select name="year" id="year" class="form-select form-select-sm fw-semibold" style="width: 100px;">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ (string)$selectedYear === (string)$year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    @if(request('search') || request('class_name'))
                        <a href="{{ route('teacher.classes') }}" class="btn btn-outline-secondary btn-sm-custom">إلغاء</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- عرض الصفوف والطلاب -->
        @forelse($groupedClasses as $className => $students)
            <div class="card card-custom mb-4">
                <div class="class-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-6">
                            <i class="bi bi-building text-success me-1"></i> {{ $className }}
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small fw-bold">
                            {{ $students->count() }} طالب
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('teacher.class.scores', ['class_name' => $className]) }}" class="btn btn-sm-custom btn-outline-primary d-flex align-items-center gap-1">
                            <i class="bi bi-award-fill"></i> عرض العلامات
                        </a>

                        <a href="{{ route('teacher.class.notes', ['class_name' => $className]) }}" class="btn btn-sm-custom btn-outline-warning text-dark d-flex align-items-center gap-1">
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
                                    <th class="py-2.5">الجنس</th>
                                    <th class="py-2.5">الحالة الصحية</th>
                                    <th class="py-2.5">المعدل العام</th>
                                    <th class="py-2.5">ولي الأمر</th>
                                    <th class="py-2.5">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="text-start ps-3 fw-bold text-dark">
                                            {{ $student->name }}
                                        </td>
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
                                        
                                        <!-- خانة المعدل العام -->
                                        <td>
                                            @if($student->average_score !== null)
                                                <span class="badge {{ $student->average_score >= 50 ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1">
                                                    {{ $student->average_score }}%
                                                </span>
                                            @else
                                                <span class="text-muted small">لا توجد درجات</span>
                                            @endif
                                        </td>

                                        <td class="text-secondary">{{ $student->parent->name ?? 'غير مرتبط' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <a href="{{ route('teacher.student.records', $student->id) }}" class="btn btn-sm-custom btn-outline-info py-1 px-2.5">
                                                    <i class="bi bi-clock-history me-1"></i> السجل
                                                </a>

                                                @if(Route::has('teacher.children.edit'))
                                                    <a href="{{ route('teacher.children.edit', $student->id) }}" class="btn btn-sm-custom btn-outline-primary py-1 px-2.5">
                                                        <i class="bi bi-pencil-square me-1"></i> تعديل
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
        @empty
            <div class="card card-custom p-5 text-center my-4">
                <div class="py-3">
                    <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                    <h6 class="fw-bold text-dark mb-2">لا يوجد طلاب مرتبطون في هذا الصف أو العام الدراسي</h6>
                    <p class="text-muted small mb-3">يمكنك إضافة طلاب جديد من خلال صفحة إضافة طفل.</p>
                    <a href="{{ Route::has('teacher.children.create') ? route('teacher.children.create') : url('/teacher/children/create') }}" class="btn btn-success btn-sm-custom px-3 py-2">
                        + إضافة طفل جديد
                    </a>
                </div>
            </div>
        @endforelse

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>