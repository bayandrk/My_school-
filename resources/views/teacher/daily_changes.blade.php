<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>السجلات المدخلة</title>

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
            border-color: #0d7a5f;
            box-shadow: 0 0 0 3px rgba(13, 122, 95, 0.12);
        }

        .btn-save-record {
            background-color: #0d7a5f;
            color: white;
            font-weight: 700;
            padding: 0.6rem 1.75rem;
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-save-record:hover {
            background-color: #0a624c;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 122, 95, 0.2);
        }

        /* شارات المادة والتاريخ والدرجة الناعمة */
        .subject-badge {
            background-color: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .date-badge {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 0.78rem;
            border: 1px solid #e2e8f0;
            direction: ltr;
            display: inline-block;
        }

        .score-badge {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: inline-block;
        }

        /* أزرار إجراءات الجدول الفرعية الناعمة */
        .btn-table-action {
            border-radius: 8px;
            padding: 0.3rem 0.65rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .table > :not(caption) > * > * {
            padding: 0.75rem 0.65rem;
        }

        /* تعديلات للمودال ليكون محاذاته لليمين 100% */
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
</head>
<body>

    <!-- Navbar مع اسم المدرسة -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-teacher py-3 shadow-sm">
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
                        <a class="nav-link" href="{{ Route::has('teacher.classes') ? route('teacher.classes') : url('/teacher/classes') }}">صفوفي وطلابي</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ Route::has('teacher.daily-changes') ? route('teacher.daily-changes') : (Route::has('teacher.records') ? route('teacher.records') : url('/teacher/daily-changes')) }}">السجلات المدخلة</a>
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
    <div class="container py-4">

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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <span>يرجى التأكد من تصحيح الحقول التالية:</span>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- نموذج إضافة سجل جديد -->
        <div class="card main-card mb-4">
            <div class="p-3 bg-white border-bottom fw-bold d-flex align-items-center gap-2 text-success">
                <i class="bi bi-plus-circle-fill fs-5"></i> <span>إضافة سجل جديد (علامة / ملاحظة / نشاط)</span>
            </div>
            <div class="card-body p-3.5">
                <form action="{{ route('teacher.daily-changes.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-2.5">
                        <!-- 1. اختيار الصف -->
                        <div class="col-md-3">
                            <label class="form-label">اختر الصف:</label>
                            <select id="class_select" class="form-select" required>
                                <option value="">-- اختر الصف أولاً --</option>
                                @foreach($studentsGroupedByClass as $className => $classStudents)
                                    <option value="{{ $className }}">{{ $className }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. اختيار الطالب -->
                        <div class="col-md-3">
                            <label class="form-label">اختر الطالب:</label>
                            <select name="child_id" id="student_select" class="form-select" required disabled>
                                <option value="">-- اختر الصف أولاً --</option>
                            </select>
                        </div>

                        <!-- 3. المادة / النشاط -->
                        <div class="col-md-3">
                            <label class="form-label">المادة / النشاط:</label>
                            <input type="text" name="subject" class="form-control" placeholder="مثال: الرياضيات" required>
                        </div>

                        <!-- 4. التاريخ والوقت -->
                        <div class="col-md-3">
                            <label class="form-label">التاريخ والوقت:</label>
                            <input type="datetime-local" name="recorded_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <!-- 5. الدرجة -->
                        <div class="col-md-3">
                            <label class="form-label">الدرجة (اختياري):</label>
                            <input type="number" step="0.01" name="score" class="form-control" placeholder="100">
                        </div>

                        <!-- 6. الملاحظة -->
                        <div class="col-md-9">
                            <label class="form-label">الملاحظة:</label>
                            <input type="text" name="note" class="form-control" placeholder="ملاحظات الأداء...">
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-save-record">
                            <i class="bi bi-save me-1"></i> حفظ السجل
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- كود JavaScript لتصفية قائمة الطلاب بناءً على الصف المختار -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const studentsGrouped = @json($studentsGroupedByClass);
                const classSelect = document.getElementById('class_select');
                const studentSelect = document.getElementById('student_select');

                classSelect.addEventListener('change', function () {
                    const selectedClass = this.value;
                    studentSelect.innerHTML = '<option value="">-- اختر الطالب --</option>';

                    if (selectedClass && studentsGrouped[selectedClass]) {
                        studentSelect.disabled = false;
                        studentsGrouped[selectedClass].forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.textContent = student.name;
                            studentSelect.appendChild(option);
                        });
                    } else {
                        studentSelect.disabled = true;
                        studentSelect.innerHTML = '<option value="">-- اختر الصف أولاً --</option>';
                    }
                });
            });
        </script>

        <!-- كرت جدول السجلات -->
        <div class="card main-card">
            <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-text text-success fs-5"></i> سجل التقييمات مرتبة بالتاريخ والوقت
                </h6>

                <!-- فلترة التاريخ بشكل ناعم -->
                <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" style="width: 140px;">
                    <button type="submit" class="btn btn-sm btn-outline-secondary fw-bold px-2.5 py-1">
                        <i class="bi bi-filter"></i> تصفية
                    </button>
                    @if(request('date'))
                        <a href="{{ url()->current() }}" class="btn btn-sm btn-link text-danger text-decoration-none p-0 small">إلغاء</a>
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
                                <th class="py-2.5">المادة</th>
                                <th class="py-2.5">الدرجة</th>
                                <th class="py-2.5">الملاحظات</th>
                                <th class="py-2.5">التاريخ والوقت</th>
                                <th class="py-2.5">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $recordsList = $dailyChanges ?? $records ?? [];
                            @endphp

                            @forelse($recordsList as $index => $record)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-start ps-3 fw-bold text-dark">
                                        {{ $record->child->name ?? $record->student->name ?? $record->student_name ?? 'غير محدد' }}
                                    </td>
                                    <td>
                                        <span class="subject-badge">
                                            {{ $record->subject ?? $record->title ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(($record->score ?? $record->grade) !== null)
                                            <span class="score-badge">
                                                {{ $record->score ?? $record->grade }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary fw-semibold">
                                        {{ $record->note ?? $record->notes ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="date-badge">
                                            {{ \Carbon\Carbon::parse($record->recorded_at ?? $record->created_at)->format('H:i | Y-m-d') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center gap-1.5">
                                            <!-- زر التعديل -->
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark btn-table-action" data-bs-toggle="modal" data-bs-target="#editModal{{ $record->id }}">
                                                <i class="bi bi-pencil-square me-1"></i> تعديل
                                            </button>

                                            <!-- زر الحذف -->
                                            <form action="{{ Route::has('teacher.daily-changes.destroy') ? route('teacher.daily-changes.destroy', $record->id) : url('/teacher/daily-changes/' . $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من حذف هذا السجل؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-table-action">
                                                    <i class="bi bi-trash3-fill me-1"></i> حذف
                                                </button>
                                            </form>
                                        </div>

                                        <!-- مودال التعديل المحاذي لليمين تماماً -->
                                        <div class="modal fade modal-rtl-fix" id="editModal{{ $record->id }}" tabindex="-1" aria-hidden="true" dir="rtl">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 modal-rtl-fix">
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
                                                                <input type="text" name="subject" class="form-control" value="{{ $record->subject ?? $record->title }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">التاريخ والوقت:</label>
                                                                <input type="datetime-local" name="recorded_at" class="form-control" value="{{ $record->recorded_at ? \Carbon\Carbon::parse($record->recorded_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">الدرجة:</label>
                                                                <input type="number" step="0.1" min="0" max="100" name="score" class="form-control" value="{{ $record->score ?? $record->grade }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">الملاحظة:</label>
                                                                <input type="text" name="note" class="form-control" value="{{ $record->note ?? $record->notes }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-light rounded-2 btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-save-record rounded-2 btn-sm px-3">حفظ التغييرات</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        لا توجد سجلات مدخلة حالياً.
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