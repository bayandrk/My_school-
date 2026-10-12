<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المعلم</title>

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

        /* النافبار الخضراء للمعلم */
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

        .school-navbar-logo {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 6px;
            background-color: #ffffff;
        }

        .stat-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 0.75rem;
        }

        .badge-record-type {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

    <!-- Navbar المعلم يضم اسم المدرسة المدخل من قبل المعلم (سعدالله ونوس) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-teacher py-3 shadow-sm mb-4">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2 m-0" href="#">
                    <i class="bi bi-person-badge-fill"></i> لوحة تحكم المعلم
                </a>

                <!-- جلب اسم المدرسة الذي أدخله المعلم أو المرتبط بحسابه -->
                <div class="school-navbar-brand d-flex align-items-center gap-2 ms-2">
                    <i class="bi bi-building-check text-warning fs-5"></i>
                    <span>
                        @if(isset($teacher->school_name) && $teacher->school_name)
                            {{ $teacher->school_name }}
                        @elseif(auth()->user()->school_name ?? false)
                            {{ auth()->user()->school_name }}
                        @else
                        (auth()->user()->school && auth()->user()->school->name)
                            {{ auth()->user()->school->name }}
                       
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
                        <a class="nav-link active" href="{{ Route::has('teacher.dashboard') ? route('teacher.dashboard') : url('/teacher/dashboard') }}">الرئيسية والمهام</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('teacher.children.create') ? route('teacher.children.create') : url('/teacher/children/create') }}">إضافة طفل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('teacher.classes') ? route('teacher.classes') : url('/teacher/classes') }}">صفوفي وطلابي</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('teacher.daily-changes') ? route('teacher.daily-changes') : url('/teacher/daily-changes') }}">السجلات المدخلة</a>
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

        <!-- الترحيب باسم المعلم المسجل حالياً -->
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1">
                مرحباً، {{ auth()->user()->name }}
            </h3>
        </div>

        <!-- الإحصائيات السريعة -->
        <div class="row g-3 mb-4">
            <!-- 1. إجمالي الطلاب -->
            <div class="col-md-4">
                <div class="stat-card p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي الطلاب المربوطين بحسابك</span>
                        <h3 class="fw-bold text-dark mb-0">
                            {{ $studentsCount ?? $totalStudentsCount ?? (isset($students) ? count($students) : 0) }} <small class="fs-6 text-muted font-normal">طالب</small>
                        </h3>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>

            <!-- 2. إجمالي الصفوف -->
            <div class="col-md-4">
                <div class="stat-card p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي الصفوف المسؤول عنها</span>
                        <h3 class="fw-bold text-dark mb-0">
                            {{ $classesCount ?? $totalClassesCount ?? (isset($classes) ? count($classes) : 0) }} <small class="fs-6 text-muted font-normal">صفوف</small>
                        </h3>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-building-gear"></i>
                    </div>
                </div>
            </div>

            <!-- 3. إجمالي السجلات -->
            <div class="col-md-4">
                <div class="stat-card p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي السجلات والتقييمات المدخلة</span>
                        <h3 class="fw-bold text-dark mb-0">
                            {{ $recordsCount ?? $totalRecordsCount ?? (isset($dailyChanges) ? count($dailyChanges) : 0) }} <small class="fs-6 text-muted font-normal">سجل</small>
                        </h3>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-journal-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول آخر السجلات المضافة -->
        <div class="stat-card overflow-hidden">
            <div class="p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-success fs-5"></i> آخر السجلات المضافة
                </h6>
                <a href="{{ Route::has('teacher.daily-changes') ? route('teacher.daily-changes') : url('/teacher/daily-changes') }}" class="btn btn-success btn-sm fw-bold px-3 rounded-2">
                    عرض كافة السجلات
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2.5 text-muted">#</th>
                                <th class="py-2.5 text-start ps-4">اسم الطالب</th>
                                <th class="py-2.5">نوع السجل / التقييم</th>
                                <th class="py-2.5">التاريخ</th>
                                <th class="py-2.5">الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $latestRecords = $recentRecords ?? $dailyChanges ?? [];
                            @endphp

                            @forelse($latestRecords as $index => $record)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-start ps-4 fw-bold text-dark">
                                        {{ $record->child->name ?? $record->student->name ?? 'طالب' }}
                                    </td>
                                    <td>
                                        <span class="badge-record-type">
                                            {{ $record->subject ?? 'سجل يومي' }}
                                        </span>
                                    </td>
                                    <td class="text-secondary dir-ltr">
                                        {{ \Carbon\Carbon::parse($record->recorded_at ?? $record->created_at)->format('H:i Y-m-d') }}
                                    </td>
                                    <td class="text-secondary">
                                        {{ $record->note ?? $record->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-muted">
                                        لا توجد سجلات مضافة حديثاً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>