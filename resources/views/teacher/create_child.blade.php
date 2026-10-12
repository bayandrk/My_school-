<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة طفل جديد</title>

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

        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.875rem;
            margin-bottom: 0.35rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
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

        .btn-save-child {
            background-color: #0d7a5f;
            color: white;
            font-weight: 700;
            padding: 0.65rem 2rem;
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-save-child:hover {
            background-color: #0a624c;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 122, 95, 0.2);
        }
    </style>
</head>
<body>

    <!-- Navbar مع اسم المدرسة -->
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
                        <a class="nav-link active" href="{{ Route::has('teacher.children.create') ? route('teacher.children.create') : url('/teacher/children/create') }}">إضافة طفل</a>
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
    <div class="container pb-5" style="max-width: 900px;">

        <!-- رسائل النجاح أو الأخطاء -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <span>يرجى التأكد من تصحيح البيانات المدخلة:</span>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- نموذج إضافة طفل جديد -->
        <div class="card main-card">
            <div class="p-3.5 bg-white border-bottom fw-bold d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-success fs-6">
                    <i class="bi bi-person-plus-fill fs-5"></i>
                    <span>إضافة طفل جديد وتخصيص الصف وولي الأمر</span>
                </div>
                <a href="{{ Route::has('teacher.classes') ? route('teacher.classes') : url('/teacher/classes') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
                    <i class="bi bi-arrow-right me-1"></i> العودة للصفوف
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ Route::has('teacher.children.store') ? route('teacher.children.store') : url('/teacher/children') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <!-- 1. اسم الطفل الكامل -->
                        <div class="col-md-6">
                            <label class="form-label">اسم الطفل الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="أدخل اسم الطفل الثلاثي..." value="{{ old('name') }}" required>
                        </div>

                        <!-- 2. الصف الدراسي -->
                        <div class="col-md-6">
                            <label class="form-label">الصف الدراسي <span class="text-danger">*</span></label>
                            <select name="class" class="form-select" required>
                                <option value="">-- اختر الصف الدراسي --</option>
                                @foreach($availableClasses ?? ['الصف الأول', 'الصف الثاني', 'الصف الثالث', 'الصف الرابع', 'الصف الخامس', 'الصف السادس'] as $c)
                                    <option value="{{ $c }}" {{ old('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. العمر -->
                        <div class="col-md-4">
                            <label class="form-label">العمر (بالسنوات) <span class="text-danger">*</span></label>
                            <input type="number" min="3" max="18" name="age" class="form-control" placeholder="مثال: 7" value="{{ old('age') }}" required>
                        </div>

                        <!-- 4. الجنس -->
                        <div class="col-md-4">
                            <label class="form-label">الجنس <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>

                        <!-- 5. اختيار ولي الأمر -->
                        <div class="col-md-4">
                            <label class="form-label">ولي الأمر <span class="text-danger">*</span></label>
                            <select name="parent_id" class="form-select" required>
                                <option value="">-- اختر ولي الأمر --</option>
                                @if(isset($parents))
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- 6. الحالة الصحية -->
                        <div class="col-md-12">
                            <label class="form-label">الحالة الصحية / ملاحظات طبية</label>
                            <input type="text" name="health_status" class="form-control" placeholder="سليم تماماً، أو اذكر أي حساسيات/ملاحظات خاصة..." value="{{ old('health_status', 'سليم تماماً') }}">
                        </div>

                        <!-- 7. صورة شخصية للطفل (اختياري) -->
                        <div class="col-md-12">
                            <label class="form-label">الصورة الشخصية للطفل (اختياري)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-save-child">
                            <i class="bi bi-check-lg me-1"></i> حفظ وتأكيد الإضافة
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>