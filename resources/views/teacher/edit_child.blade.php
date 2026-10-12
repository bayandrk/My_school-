<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الطالب</title>

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

        /* النافبار الخضراء الخاصة بالمعلم (بدون أي تغيير) */
        .navbar-teacher {
            background-color: #0d7a5f;
        }

        /* تصميم الكارد والنموذج الناعم الموحد */
        .form-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.75rem;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #0d7a5f;
            box-shadow: 0 0 0 3px rgba(13, 122, 95, 0.12);
        }

        .btn-submit {
            background-color: #0d7a5f;
            color: white;
            font-weight: 700;
            padding: 0.65rem 1.75rem;
            border-radius: 10px;
            border: none;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background-color: #0a5c47;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 122, 95, 0.2);
        }

        .btn-cancel {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.65rem 1.5rem;
        }
    </style>
</head>
<body>

    <!-- Navbar المعلم (بدون أي تغيير) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-teacher py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="{{ url('/teacher/dashboard') }}">
                <i class="bi bi-person-badge-fill"></i> لوحة تحكم المعلم
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#teacherNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="teacherNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/teacher/dashboard') }}">الرئيسية والمهام</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/teacher/children/create') }}">إضافة طفل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ url('/teacher/classes') }}">صفوفي وطلابي</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/teacher/records') }}">السجلات المدخلة</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ Route::has('teacher.profile') ? route('teacher.profile') : url('/teacher/profile') }}" class="btn btn-light btn-sm rounded-2 px-3 fw-bold text-success d-flex align-items-center gap-1">
                        <i class="bi bi-person-circle"></i> الملف الشخصي
                    </a>

                    <form action="{{ url('/logout') }}" method="POST" class="d-inline">
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
    <div class="container py-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- عرض أخطاء الـ Validation إن وجدت -->
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                        <ul class="mb-0 small fw-semibold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="card form-card">
                    <!-- Form Header -->
                    <div class="form-card-header d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-success fs-4"></i>
                        <h5 class="fw-bold mb-0 text-dark">
                            تعديل بيانات الطالب: {{ $child->name ?? $child->student_name ?? '' }}
                        </h5>
                    </div>

                    <!-- Form Body -->
                    <div class="card-body p-4">
                        <form action="{{ Route::has('teacher.children.update') ? route('teacher.children.update', $child->id) : url('/teacher/children/' . $child->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- اسم الطالب -->
                            <div class="mb-3.5">
                                <label for="name" class="form-label">اسم الطالب الكامل <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="مثال: أحمد محمد" value="{{ old('name', $child->name ?? '') }}" required>
                            </div>

                            <!-- الجنس والعمر -->
                            <div class="row g-3 mb-3.5">
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">الجنس <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-select" required>
                                        <option value="male" {{ old('gender', $child->gender ?? '') == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender', $child->gender ?? '') == 'female' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="age" class="form-label">العمر <span class="text-danger">*</span></label>
                                    <input type="number" name="age" id="age" class="form-control" placeholder="مثال: 7" min="3" max="18" value="{{ old('age', $child->age ?? '') }}" required>
                                </div>
                            </div>

                            <!-- الصف الدراسي وولي الأمر -->
                            <div class="row g-3 mb-3.5">
                                <div class="col-md-6">
                                    <label for="class" class="form-label">الصف والعام الدراسي <span class="text-danger">*</span></label>
                                    <input type="text" name="class" id="class" class="form-control" placeholder="مثال: الصف الأول 2026" value="{{ old('class', $child->class ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="parent_id" class="form-label">ولي الأمر <small class="text-muted fw-normal">(اختياري)</small></label>
                                    <select name="parent_id" id="parent_id" class="form-select">
                                        <option value="">-- اختر ولي الأمر --</option>
                                        @if(isset($parents))
                                            @foreach($parents as $parent)
                                                <option value="{{ $parent->id }}" {{ old('parent_id', $child->parent_id ?? '') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- صورة الطالب -->
                            <div class="mb-3.5">
                                <label for="image" class="form-label">تغيير صورة الطالب <small class="text-muted fw-normal">(اختياري)</small></label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            </div>

                            <!-- الحالة الصحية / ملاحظات -->
                            <div class="mb-4">
                                <label for="health_status" class="form-label">الحالة الصحية / ملاحظات خاصة</label>
                                <textarea name="health_status" id="health_status" class="form-control" rows="3" placeholder="سليم، أو يرجى ذكر أي حساسية أو ملاحظات صحية...">{{ old('health_status', $child->health_status ?? '') }}</textarea>
                            </div>

                            <!-- أزرار الإجراءات -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ url('/teacher/classes') }}" class="btn btn-outline-secondary btn-cancel">
                                    إلغاء والعودة
                                </a>
                                <button type="submit" class="btn btn-submit">
                                    <i class="bi bi-check-circle-fill me-1"></i> حفظ التعديلات
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>