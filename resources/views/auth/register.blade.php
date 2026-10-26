<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - النظام المدرسي</title>
    
    <!-- Bootstrap 5 RTL CSS & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- الخط العربي (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent-color: #1e3a8a; /* اللون الكحلي — مناسب للمدارس */
            --accent-hover: #1e40af;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 0;
        }

        .register-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(30, 58, 138, 0.08);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 2rem 1.5rem 1rem;
            text-align: center;
        }

        .card-header-custom h4 {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.35rem;
            margin-bottom: 0.3rem;
        }

        .icon-circle {
            background-color: color-mix(in srgb, var(--accent-color) 12%, #ffffff);
            color: var(--accent-color);
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .form-label-custom {
            font-weight: 700;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        .form-control-custom, .form-select-custom {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 1rem;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background-color: #ffffff;
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent-color) 18%, transparent) !important;
        }

        /* زر إظهار/إخفاء كلمة المرور */
        .password-toggle-btn {
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
            border: 1px solid #cbd5e1;
            border-right: none;
            background-color: #f8fafc;
            color: #64748b;
            padding: 0.65rem 0.9rem;
            transition: all 0.2s ease;
        }

        .password-toggle-btn:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .btn-submit-custom {
            background-color: var(--accent-color);
            border: none;
            color: #ffffff;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px color-mix(in srgb, var(--accent-color) 30%, transparent);
        }

        .btn-submit-custom:hover {
            background-color: var(--accent-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px color-mix(in srgb, var(--accent-color) 40%, transparent);
        }

        .card-footer-custom {
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .card-footer-custom a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 700;
        }

        .card-footer-custom a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card register-card">
                
                <div class="card-header-custom">
                    <div class="icon-circle mb-2">
                        <i class="bi bi-person-plus-fill fs-4"></i>
                    </div>
                    <h4>إنشاء حساب جديد</h4>
                    <p class="text-muted small mb-0">يرجى ملء البيانات التالية للانضمام للنظام المدرسي</p>
                </div>

                <div class="card-body p-4">

                    <!-- عرض الأخطاء إن وجدت -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 py-2 fw-bold" role="alert">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <span>يرجى التأكد من الحقول التالية:</span>
                            </div>
                            <ul class="mb-0 ps-3 small fw-normal">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- الاسم الكامل -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label form-label-custom">الاسم الكامل *</label>
                                <input type="text" name="name" id="name" class="form-control form-control-custom" value="{{ old('name') }}" placeholder="أدخل الاسم الكامل..." required>
                            </div>

                            <!-- اسم المستخدم -->
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label form-label-custom">اسم المستخدم (Username) *</label>
                                <input type="text" name="username" id="username" class="form-control form-control-custom" value="{{ old('username') }}" placeholder="مثال: rima_2026" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label form-label-custom">البريد الإلكتروني *</label>
                                <input type="email" name="email" id="email" class="form-control form-control-custom" value="{{ old('email') }}" placeholder="example@domain.com" required>
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label form-label-custom">رقم الهاتف *</label>
                                <input type="text" name="phone" id="phone" class="form-control form-control-custom" value="{{ old('phone') }}" placeholder="09xxxxxxxx" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- نوع الحساب -->
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label form-label-custom">نوع الحساب *</label>
                                <select name="role" id="role" class="form-select form-select-custom" required>
                                    <option value="" selected disabled>اختر نوع الحساب...</option>
                                    <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>ولي أمر (تفعيل فورياً)</option>
                                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>أستاذ (بانتظار موافقة المدير)</option>
                                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>مدير مدرسة (بانتظار موافقة الأدمن)</option>
                                </select>
                            </div>

                            <!-- اسم المدرسة -->
                            <div class="col-md-6 mb-3">
                                <label for="school_name" class="form-label form-label-custom">اسم المدرسة (للمدراء والأساتذة)</label>
                                <input type="text" name="school_name" id="school_name" class="form-control form-control-custom" value="{{ old('school_name') }}" placeholder="أدخل اسم المدرسة...">
                            </div>
                        </div>

                        <div class="row">
                            <!-- كلمة المرور مع زر إظهار/إخفاء -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label form-label-custom">كلمة المرور *</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control form-control-custom border-end-0" placeholder="••••••••" required>
                                    <button class="btn password-toggle-btn" type="button" onclick="togglePasswordVisibility('password', 'passwordIcon')">
                                        <i class="bi bi-eye-slash" id="passwordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- تأكيد كلمة المرور مع زر إظهار/إخفاء -->
                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label form-label-custom">تأكيد كلمة المرور *</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-custom border-end-0" placeholder="••••••••" required>
                                    <button class="btn password-toggle-btn" type="button" onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordIcon')">
                                        <i class="bi bi-eye-slash" id="confirmPasswordIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-submit-custom w-100">
                            <i class="bi bi-check-lg me-1"></i> إنشاء الحساب
                        </button>
                    </form>

                </div>
                
                <div class="card-footer-custom">
                    <span>لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // دالة التبديل بين إظهار وإخفاء كلمة المرور
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }
</script>

</body>
</html>