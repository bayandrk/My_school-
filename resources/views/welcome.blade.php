<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>النظام المدرسي - الصفحة الترحيبية</title>

    <!-- Bootstrap 5 RTL CSS & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- الخط العربي Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent-color: #1e3a8a;
            --accent-hover: #1e40af;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #fdfdff 50%, #eff6ff 100%);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* شريط التنقل العلوي */
        .welcome-navbar {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 0;
        }

        .welcome-navbar .brand {
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-nav-login {
            background-color: var(--accent-color);
            color: #ffffff !important;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.45rem 1.2rem;
            transition: all 0.2s ease;
            border: 1.5px solid var(--accent-color);
        }

        .btn-nav-login:hover {
            background-color: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-nav-register {
            background-color: transparent;
            color: var(--accent-color) !important;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.45rem 1.2rem;
            border: 1.5px solid var(--accent-color);
            transition: all 0.2s ease;
        }

        .btn-nav-register:hover {
            background-color: color-mix(in srgb, var(--accent-color) 10%, #ffffff);
        }

        /* البطل (Hero) */
        .hero {
            padding: 4rem 0 3rem;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: color-mix(in srgb, var(--accent-color) 10%, #ffffff);
            color: var(--accent-color);
            border: 1px solid color-mix(in srgb, var(--accent-color) 25%, transparent);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
            border-radius: 999px;
            margin-bottom: 1.25rem;
        }

        .hero h1 {
            font-weight: 800;
            font-size: clamp(1.8rem, 4.5vw, 2.8rem);
            color: #0f172a;
            margin-bottom: 1rem;
        }

        .hero h1 .accent { color: var(--accent-color); }

        .hero p.lead-text {
            color: #475569;
            font-size: 1.05rem;
            max-width: 620px;
            margin: 0 auto 2rem;
            font-weight: 500;
        }

        .hero-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero-main {
            background-color: var(--accent-color);
            color: #ffffff !important;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 6px 18px color-mix(in srgb, var(--accent-color) 30%, transparent);
        }

        .btn-hero-main:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px color-mix(in srgb, var(--accent-color) 40%, transparent);
        }

        .btn-hero-outline {
            background-color: #ffffff;
            color: var(--accent-color) !important;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            border: 1.5px solid var(--accent-color);
            transition: all 0.2s ease;
        }

        .btn-hero-outline:hover {
            background-color: color-mix(in srgb, var(--accent-color) 8%, #ffffff);
            transform: translateY(-2px);
        }

        /* أيقونة البطل */
        .hero-icon {
            width: 84px;
            height: 84px;
            border-radius: 22px;
            background-color: color-mix(in srgb, var(--accent-color) 12%, #ffffff);
            color: var(--accent-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.4rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 24px color-mix(in srgb, var(--accent-color) 15%, transparent);
        }

        /* بطاقات الميزات */
        .feature-card {
            background: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 16px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            height: 100%;
            transition: all 0.25s ease;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 26px rgba(30, 58, 138, 0.10);
            border-color: color-mix(in srgb, var(--accent-color) 25%, transparent);
        }

        .feature-card .feature-icon {
            width: 58px;
            height: 58px;
            border-radius: 15px;
            background-color: color-mix(in srgb, var(--accent-color) 10%, #ffffff);
            color: var(--accent-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            margin-bottom: 1rem;
        }

        .feature-card h5 {
            font-weight: 700;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: #64748b;
            font-size: 0.875rem;
            margin: 0;
            font-weight: 500;
        }

        /* شريط الأدوار */
        .roles-strip {
            background-color: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }

        .role-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 12px;
            transition: background-color 0.2s ease;
        }

        .role-item:hover { background-color: #f8fafc; }

        .role-item .role-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background-color: color-mix(in srgb, var(--accent-color) 10%, #ffffff);
            color: var(--accent-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .role-item h6 {
            font-weight: 700;
            font-size: 0.9rem;
            color: #0f172a;
            margin: 0;
        }

        .role-item small {
            color: #64748b;
            font-weight: 500;
        }

        /* التذييل */
        .welcome-footer {
            margin-top: auto;
            border-top: 1px solid #e2e8f0;
            padding: 1.25rem 0;
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            background-color: #ffffff;
        }

        .section-title {
            font-weight: 800;
            color: #0f172a;
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-title .accent { color: var(--accent-color); }
    </style>
</head>
<body>

    <!-- شريط التنقل العلوي -->
    <nav class="navbar navbar-expand-lg welcome-navbar sticky-top">
        <div class="container">
            <span class="brand">
                <i class="bi bi-mortarboard-fill"></i> النظام المدرسي
            </span>

            <div class="ms-auto d-flex align-items-center gap-2">
                @auth
                    {{-- المستخدم مسجل: زر للدخول للنظام مباشرة --}}
                    <a href="{{ auth()->user()->role === 'manager' ? route('manager.dashboard') : (auth()->user()->role === 'teacher' ? route('teacher.dashboard') : (Route::has('parent.dashboard') ? route('parent.dashboard') : url('/'))) }}"
                       class="btn btn-nav-login d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-in-left"></i> الدخول للنظام
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-nav-register d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-nav-login d-flex align-items-center gap-1">
                        <i class="bi bi-person-plus-fill"></i> إنشاء حساب
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- البطل (Hero) -->
    <section class="hero">
        <div class="container">
            <div class="hero-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <span class="hero-badge">
                <i class="bi bi-stars"></i> منصة إدارة مدرسية متكاملة
            </span>
            <h1>نظام إدارة <span class="accent">المدرسة</span> الحديث</h1>
            <p class="lead-text">
                منصة شاملة تربط المدير والمعلمة وولي الأمر في مكان واحد — إدارة الصفوف والشعب،
                تسجيل العلامات والملاحظات اليومية، ومتابعة ملاحظات أولياء الأمور وردود المعلمات عليها،
                كل ذلك بواجهة سهلة وواضحة.
            </p>
            <div class="hero-actions">
                @auth
                    <a href="{{ auth()->user()->role === 'manager' ? route('manager.dashboard') : (auth()->user()->role === 'teacher' ? route('teacher.dashboard') : (Route::has('parent.dashboard') ? route('parent.dashboard') : url('/'))) }}"
                       class="btn btn-hero-main d-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-in-left"></i> الدخول للنظام
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-hero-main d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill"></i> أنشئ حسابك الآن
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-hero-outline d-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- الميزات -->
    <section class="container pb-4">
        <h2 class="section-title fs-3">كل ما تحتاجه مدرستك <span class="accent">في منصة واحدة</span></h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-diagram-3-fill"></i></div>
                    <h5>دليل الصفوف والشعب</h5>
                    <p>إدارة الصفوف والشعب والمواد، وتنظيم الطلاب في كل شعبة بسهولة.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-award-fill"></i></div>
                    <h5>العلامات والتقييم</h5>
                    <p>تسجيل درجات الطلاب لكل مادة مع حساب المعدل العام تلقائياً.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-journal-text"></i></div>
                    <h5>السجلات اليومية</h5>
                    <p>توثيق الأنشطة والملاحظات اليومية لكل طالب مع إمكانية البحث والتصفية.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-chat-heart"></i></div>
                    <h5>تواصل مع الأهل</h5>
                    <p>ملاحظات أولياء الأمور وردود المعلمات عليها مع إشعارات فورية.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- الأدوار -->
    <section class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="roles-strip">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="role-item">
                                <span class="role-icon"><i class="bi bi-person-badge-fill"></i></span>
                                <div>
                                    <h6>المدير</h6>
                                    <small>إدارة المدرسة والأساتذة والطلاب</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="role-item">
                                <span class="role-icon"><i class="bi bi-person-video3"></i></span>
                                <div>
                                    <h6>المعلمة</h6>
                                    <small>السجلات والعلامات وملاحظات الأهل</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="role-item">
                                <span class="role-icon"><i class="bi bi-people-fill"></i></span>
                                <div>
                                    <h6>ولي الأمر</h6>
                                    <small>متابعة الطالب واستلام الإشعارات</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- التذييل -->
    <footer class="welcome-footer">
        <div class="container">
            النظام المدرسي &copy; {{ date('Y') }} — جميع الحقوق محفوظة
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
