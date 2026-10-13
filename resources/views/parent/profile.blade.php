@php
    // جلب اللون من الجلسة بأسلوب آمن تماماً وبدون التسبب بأي خطأ
    $themeColor = session('primary_color') ?? session('school_color') ?? '#0d6efd';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي لولي الأمر</title>

    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- الخط العربي (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: {{ $themeColor }};
            --btn-text-color: #ffffff;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        /* نافبار موحد يطابق تصميم الصفحة الرئيسية */
        .navbar-parent {
            background-color: var(--primary-color) !important;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .profile-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .profile-card-header {
            background-color: #ffffff;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .form-label-custom {
            font-weight: 700;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        .form-control-custom {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.95rem;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .btn-save-custom {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-size: 0.925rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-save-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: var(--btn-text-color) !important;
        }

        .btn-cancel-custom {
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-size: 0.925rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- شريط الملاحة الأعلى (طابق نمط وشكل الصفحة الرئيسية) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-parent shadow-sm py-3 mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white text-decoration-none" href="{{ route('parent.dashboard') }}">
                <i class="bi bi-mortarboard-fill fs-4"></i>
                <span>متابعة الأولياء</span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small d-none d-md-inline fw-semibold">
                    مرحباً بك، {{ auth()->user()->name ?? 'ولي الأمر' }}
                </span>
                
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-house-door me-1"></i> الرئيسية
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">
                            <i class="bi bi-box-arrow-left me-1"></i> خروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- المحتوى الرئيسي -->
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card profile-card">
                    <div class="profile-card-header d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge-fill" style="color: var(--primary-color);"></i>
                        <span>تعديل الملف الشخصي (ولي الأمر)</span>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('parent.profile') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- الاسم -->
                            <div class="mb-3">
                                <label for="name" class="form-label form-label-custom">الاسم الكامل</label>
                                <input type="text" name="name" id="name" class="form-control form-control-custom" value="{{ old('name', $parent->name ?? auth()->user()->name) }}" required>
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="mb-3">
                                <label for="email" class="form-label form-label-custom">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" class="form-control form-control-custom" value="{{ old('email', $parent->email ?? auth()->user()->email) }}" required>
                            </div>

                            <hr class="my-4 text-muted opacity-25">
                            <p class="text-muted small fw-semibold mb-3">اترك حقول كلمة المرور فارغة إذا كنت لا ترغب في تغييرها.</p>

                            <!-- كلمة المرور الجديدة -->
                            <div class="mb-3">
                                <label for="password" class="form-label form-label-custom">كلمة المرور الجديدة</label>
                                <input type="password" name="password" id="password" class="form-control form-control-custom">
                            </div>

                            <!-- تأكيد كلمة المرور -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label form-label-custom">تأكيد كلمة المرور الجديدة</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-custom">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                                <a href="{{ route('parent.dashboard') }}" class="btn btn-light btn-cancel-custom border">إلغاء</a>
                                
                                <div class="d-flex gap-2">
                                    <!-- زر حذف الحساب الشخصي -->
                                    <button type="button" class="btn btn-outline-danger btn-cancel-custom fw-bold" data-bs-toggle="modal" data-bs-target="#deleteParentModal">
                                        حذف حسابي الشخصي
                                    </button>

                                    <!-- زر الحفظ -->
                                    <button type="submit" class="btn btn-save-custom">حفظ التعديلات</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- نافذة تأكيد حذف حساب ولي الأمر (Modal) -->
    <div class="modal fade" id="deleteParentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 p-2">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark">تأكيد حذف الحساب</h6>
                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger fw-bold mb-1">هل أنت متأكد من رغبتك في حذف حسابك الشخصي كولي أمر؟</p>
                    <p class="text-muted small mb-0">ملاحظة: سيتم حذف حسابك فقط دون أن يتأثر وجود أو بيانات الطلاب المرتبطين.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light btn-sm px-3 rounded-2" data-bs-dismiss="modal">إلغاء</button>
                    
                    <form action="{{ route('parent.profile.destroy') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm px-3 rounded-2 fw-bold">نعم، احذف حسابي</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>