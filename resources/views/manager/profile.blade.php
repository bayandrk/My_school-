<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي للمدير</title>

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
            background-color: {{ $manager->primary_color ?? '#0d6efd' }};
            transition: background-color 0.3s ease;
        }

        /* شارة واسم المدرسة بداخل النافبار */
        .school-navbar-brand {
            background-color: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
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

        /* أزرار النافبار العلوي شفافة وأنيقة مثل المدير المعين */
        .btn-nav-profile-custom {
            background-color: rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            font-weight: 700;
            border-radius: 10px;
            font-size: 0.85rem;
            padding: 0.4rem 0.95rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-nav-profile-custom:hover {
            background-color: rgba(255, 255, 255, 0.32) !important;
            color: #ffffff !important;
        }

        .btn-nav-logout-custom {
            background-color: transparent !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            color: #ffffff !important;
            font-weight: 700;
            border-radius: 10px;
            font-size: 0.85rem;
            padding: 0.4rem 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-nav-logout-custom:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }

        .profile-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header-custom {
            background-color: #ffffff;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem;
            font-weight: 700;
        }

        /* الأيقونات الديناميكية بلون المدرسة */
        .theme-icon-dynamic {
            color: {{ $manager->primary_color ?? '#0d6efd' }} !important;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.875rem;
            margin-bottom: 0.35rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.55rem 0.85rem;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: {{ $manager->primary_color ?? '#0d6efd' }};
            box-shadow: 0 0 0 3px color-mix(in srgb, {{ $manager->primary_color ?? '#0d6efd' }} 20%, transparent);
        }

        .btn-custom-primary {
            background-color: {{ $manager->primary_color ?? '#0d6efd' }};
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.55rem 1.25rem;
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn-custom-primary:hover {
            color: #ffffff;
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <!-- شريط النافبار الموحد للمدير -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-manager py-3 shadow-sm mb-4">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2 m-0" href="#">
                    لوحة إدارة المدرسة
                </a>

                <!-- شارة اسم المدرسة والشعار -->
                <div class="school-navbar-brand d-flex align-items-center gap-2 ms-2">
                    @if($manager->logo)
                        <img src="{{ asset('storage/' . $manager->logo) }}" alt="لوغو" class="school-navbar-logo">
                    @else
                        <i class="bi bi-building-check text-warning fs-5"></i>
                    @endif
                    <span>
                        {{ $manager->school_name ?? 'سعدالله ونوس' }}
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
                        <a class="nav-link" href="{{ route('manager.classes') }}">دليل الصفوف</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('manager.teachers') }}">دليل الأساتذة</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <!-- زر الملف الشخصي الشفاف الموحد -->
                    <a href="{{ route('manager.profile') }}" class="btn btn-nav-profile-custom">
                        <i class="bi bi-person-circle me-1"></i> الملف الشخصي
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-nav-logout-custom">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5" style="max-width: 900px;">

        <!-- رسائل النجاح أو الأخطاء -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <span>يرجى التأكد من الحقول التالية:</span>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 1. كرت تخصيص مظهر المدرسة (اللوغو والألوان) -->
        <div class="profile-card">
            <div class="card-header-custom d-flex align-items-center gap-2">
                <i class="bi bi-palette-fill fs-5 theme-icon-dynamic"></i>
                <span>تخصيص مظهر المدرسة (شعار المدرسة واللون الأساسي)</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('manager.update-branding') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row align-items-center g-4">
                        <!-- عرض الشعار الحالي -->
                        <div class="col-md-3 text-center border-end-md">
                            @if($manager->logo)
                                <img src="{{ asset('storage/' . $manager->logo) }}" alt="شعار المدرسة" class="rounded-3 shadow-sm border mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mx-auto mb-2 border text-muted" style="width: 80px; height: 80px;">
                                    <i class="bi bi-building fs-2 text-secondary"></i>
                                </div>
                            @endif
                            <span class="d-block small fw-bold text-muted">الشعار الحالي</span>
                        </div>

                        <!-- رفع شعار جديد -->
                        <div class="col-md-5">
                            <label class="form-label">تغيير شعار المدرسة (لوغو):</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">صيغ مسموحة: JPG, PNG, SVG (بحد أقصى 2MB)</small>
                        </div>

                        <!-- اختيار لون الهوية -->
                        <div class="col-md-4">
                            <label class="form-label">لون الهوية المفضل للمدرسة:</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="primary_color" class="form-control form-control-color p-1" value="{{ $manager->primary_color ?? '#0d6efd' }}" title="اختر اللون" style="width: 50px; height: 38px; cursor: pointer;">
                                <span class="text-secondary small fw-semibold">انقر لاختيار اللون</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-custom-primary">
                            <i class="bi bi-brush me-1"></i> حفظ إعدادات المظهر
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. كرت تعديل البيانات الشخصية والحساب -->
        <div class="profile-card">
            <div class="card-header-custom d-flex align-items-center gap-2">
                <i class="bi bi-person-gear fs-5 theme-icon-dynamic"></i>
                <span>تعديل الملف الشخصي للمدير (البيانات والحساب)</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('manager.profile') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- اسم المستخدم -->
                        <div class="col-md-6">
                            <label class="form-label">اسم المستخدم (المعرّف في النظام)</label>
                            <input type="text" class="form-control bg-light" value="{{ $manager->username ?? $manager->name }}" disabled>
                            <small class="text-muted" style="font-size: 0.75rem;">هذا الاسم المعرّف للحساب في النظام.</small>
                        </div>

                        <!-- الاسم الكامل -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $manager->name) }}" required>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $manager->email) }}" required>
                        </div>

                        <!-- رقم الهاتف -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $manager->phone) }}" placeholder="أدخل رقم الهاتف...">
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">
                    <p class="text-muted small fw-bold mb-3"><i class="bi bi-info-circle me-1"></i> اترك حقول كلمة المرور فارغة إذا كنت لا ترغب في تغييرها:</p>

                    <div class="row g-3">
                        <!-- كلمة المرور الجديدة -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                        <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-secondary rounded-2 fw-semibold btn-sm px-3">
                            <i class="bi bi-arrow-right me-1"></i> إلغاء
                        </a>

                        <div class="d-flex align-items-center gap-2">
                            <!-- زر حذف الحساب الشخصي -->
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-2 fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#deleteManagerModal">
                                <i class="bi bi-trash3 me-1"></i> حذف حسابي الشخصي
                            </button>

                            <button type="submit" class="btn btn-custom-primary">
                                <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- نافذة تأكيد حذف حساب المدير (Modal) -->
    <div class="modal fade" id="deleteManagerModal" tabindex="-1" aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 text-end">
                <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="modal-title fw-bold text-danger mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> تأكيد حذف الحساب</h6>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body small text-secondary">
                    <p class="text-dark fw-bold mb-1">هل أنت متأكد من رغبتك في حذف حسابك الشخصي كمدير؟</p>
                    <span class="text-muted fs-7">ملاحظة: سيتم حذف حسابك فقط، ولن يتم حذف بيانات المدرسة أو الطلاب أو الأساتذة التابعين لها.</span>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-2 btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                    
                    <form action="{{ route('manager.profile.destroy') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger fw-bold btn-sm rounded-2 px-3">نعم، احذف حسابي</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>