<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل الصفوف</title>
    
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

        /* النافبار العلوي للمدير */
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

        .card-custom {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            background: #fff;
            overflow: hidden;
            border: 1px solid #f1f5f9;
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
        }

        .btn-sm-custom {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }

        .badge-teacher {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="text-end">

    <!-- الناڤبار العلوي (لوحة إدارة المدرسة) مع شارة اسم المدرسة -->
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
                        <a class="nav-link" href="{{ route('manager.dashboard') }}">المهام والطلبات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('manager.classes') }}">دليل الصفوف</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('manager.teachers') }}">دليل الأساتذة</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('manager.profile') }}" class="btn btn-light btn-sm rounded-2 px-3 fw-bold text-primary">
                        الملف الشخصي
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm rounded-2 px-3 fw-bold">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">

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

                <!-- فلاتر البحث والتصفية -->
                <form action="{{ route('manager.classes') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
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
                        <button class="btn btn-primary fw-bold" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    
                    @if(request('search_student') || request('class_filter'))
                        <a href="{{ route('manager.classes') }}" class="btn btn-outline-secondary btn-sm-custom">إلغاء</a>
                    @endif
                </form>
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
                    <!-- رأس بطاقة الصف بتنسيق مريح ونظيف -->
                    <div class="class-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-6">
                                <i class="bi bi-building text-primary me-1"></i> {{ Str::startsWith($className, 'الصف') ? $className : 'الصف ' . $className }}
                            </span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small fw-bold">
                                {{ $students->count() }} طالب
                            </span>
                        </div>

                        <!-- الأزرار بشكل عصري ونظيف -->
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('manager.class.scores', ['class_name' => $className]) }}" class="btn btn-sm-custom btn-outline-primary d-flex align-items-center gap-1">
                                <i class="bi bi-award-fill"></i> عرض العلامات
                            </a>
                            <a href="{{ route('manager.class.notes', ['class_name' => $className]) }}" class="btn btn-sm-custom btn-outline-warning text-dark d-flex align-items-center gap-1">
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
                                                <span class="badge bg-light text-dark border font-normal">
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
                                                @if(isset($student->average_score) && $student->average_score)
                                                    <span class="badge {{ $student->average_score >= 50 ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1">
                                                        {{ $student->average_score }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted small">لا توجد درجات</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-info btn-sm-custom py-1 px-2.5" data-bs-toggle="modal" data-bs-target="#studentModal{{ $student->id }}">
                                                    <i class="bi bi-clock-history"></i> عرض السجلات
                                                </button>

                                                <!-- Modal عرض السجلات -->
                                                <div class="modal fade text-start" id="studentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content text-end">
                                                            <div class="modal-header bg-info text-white">
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
                                                    <!-- زر الحذف فقط للمدير -->
                                                    <form action="{{ route('manager.children.destroy', $student) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف الطالب {{ $student->name }}؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm-custom py-1 px-2.5">
                                                            <i class="bi bi-trash me-1"></i> حذف
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

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>