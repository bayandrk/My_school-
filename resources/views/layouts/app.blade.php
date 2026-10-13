<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام إدارة المدرسة')</title>

    <!-- Bootstrap 5 RTL CSS & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- الخط العربي Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    @php
        // جلب لون المدرسة والشعار ديناميكياً بحسب المستخدم المسجل
        $user = auth()->user();
        if ($user && $user->role === 'manager') {
            $schoolColor = $user->primary_color ?? '#0d7a5f';
            $schoolLogo  = $user->logo;
            $schoolName  = $user->school_name ?? 'سعدالله ونوس';
        } else {
            $manager = \App\Models\User::where('role', 'manager')
                ->where('school_name', $user->school_name ?? '')
                ->first();
            $schoolColor = $manager->primary_color ?? '#0d7a5f';
            $schoolLogo  = $manager->logo ?? null;
            $schoolName  = $user->school_name ?? 'سعدالله ونوس';
        }

        // دالة لحساب درجة سطوع اللون واختيار لون النص التلقائي (أسود أو أبيض)
        function getContrastingTextColor($hexColor) {
            $hex = str_replace('#', '', $hexColor);
            if (strlen($hex) == 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));

            $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
            return ($yiq >= 150) ? '#0f172a' : '#ffffff';
        }

        $textColor = getContrastingTextColor($schoolColor);
    @endphp

    <style>
        :root {
            --primary-color: {{ $schoolColor }};
            --btn-text-color: {{ $textColor }};
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* النافبار المطور بتصميم عصري وأنيق */
        .navbar-custom {
            background-color: var(--primary-color) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            padding: 0.65rem 0;
            transition: all 0.3s ease;
        }

        .app-brand-title {
            color: var(--btn-text-color) !important;
            font-weight: 800;
            font-size: 1.05rem;
            text-decoration: none;
        }

        /* شارة اسم المستخدم واسم المدرسة والشعار داخل النافبار */
        .school-navbar-brand {
            background-color: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.3rem 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .school-navbar-brand span {
            color: var(--btn-text-color) !important;
            font-weight: 800;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .school-navbar-logo {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 7px;
            background-color: #ffffff;
            flex-shrink: 0;
        }

        /* روابط التنقل الأنيقة المطابقة للمدير والمعلم */
        .navbar-custom .nav-link {
            color: var(--btn-text-color) !important;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.45rem 0.85rem !important;
            border-radius: 10px;
            opacity: 0.88;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .navbar-custom .nav-link:hover {
            opacity: 1;
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* التبويب النشط */
        .navbar-custom .nav-link.active {
            opacity: 1;
            background-color: rgba(255, 255, 255, 0.22) !important;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        /* أزرار النافبار الجانبية */
        .btn-nav-profile {
            background-color: rgba(255, 255, 255, 0.18) !important;
            color: var(--btn-text-color) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            font-weight: 700;
            border-radius: 9px;
            font-size: 0.85rem;
            padding: 0.35rem 0.85rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-nav-profile:hover {
            background-color: rgba(255, 255, 255, 0.28) !important;
        }

        .btn-nav-logout {
            background-color: transparent !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700;
            border-radius: 9px;
            font-size: 0.85rem;
            padding: 0.35rem 0.85rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-nav-logout:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.8) !important;
        }

        /* النظام الموحد لكافة أزرار وشارات الموقع ديناميكياً */
        .btn-primary, 
        .btn-success, 
        .btn-custom-primary, 
        .btn-search-school,
        button[type="submit"]:not(.btn-close):not(.btn-outline-danger):not(.btn-light) {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
        }

        .btn-primary:hover, 
        .btn-success:hover, 
        .btn-custom-primary:hover {
            opacity: 0.9;
            color: var(--btn-text-color) !important;
        }

        .badge.bg-info,
        .badge.bg-primary,
        .badge.bg-success,
        .subject-badge,
        .badge-record-type {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 35%, transparent) !important;
            color: color-mix(in srgb, var(--primary-color) 85%, #000000) !important;
            font-weight: 800 !important;
        }

        .badge-average-score,
        .score-badge {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 800 !important;
        }

        .text-primary, .text-success, .text-info, .title-theme-icon {
            color: var(--primary-color) !important;
        }

        .icon-box, .stat-icon-box {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            color: var(--primary-color) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 25%, transparent) !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- النافبار الموحد للموقع كاملاً -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <!-- عنوان اللوحة -->
                <a class="app-brand-title d-flex align-items-center gap-2 m-0" href="#">
                    @if(auth()->user()->role === 'manager')
                        لوحة إدارة المدرسة
                    @else
                        <i class="bi bi-person-badge-fill"></i> لوحة تحكم المعلم
                    @endif
                </a>

                <!-- شارة اسم المستخدم/المدرسة وشعار المدرسة (مطابق للمدير والمعلم) -->
                <div class="school-navbar-brand ms-2">
                    @if(!empty($schoolLogo))
                        <img src="{{ asset('storage/' . $schoolLogo) }}" alt="لوغو" class="school-navbar-logo">
                    @else
                        <i class="bi bi-person-circle fs-6"></i>
                    @endif
                    <span>{{ auth()->user()->name }}</span>
                </div>
            </div>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- روابط النافبار بنمط وتنسيق موحد -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold gap-1 ms-lg-3">
                    @if(auth()->user()->role === 'manager')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}" href="{{ route('manager.dashboard') }}">المهام والطلبات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manager.classes') ? 'active' : '' }}" href="{{ route('manager.classes') }}">دليل الصفوف</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manager.teachers') ? 'active' : '' }}" href="{{ route('manager.teachers') }}">دليل الأساتذة</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">الرئيسية والمهام</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.children.create') ? 'active' : '' }}" href="{{ route('teacher.children.create') }}">إضافة طفل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.classes') ? 'active' : '' }}" href="{{ route('teacher.classes') }}">صفوفي وطلابي</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.daily-changes') ? 'active' : '' }}" href="{{ route('teacher.daily-changes') }}">السجلات المدخلة</a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ auth()->user()->role === 'manager' ? route('manager.profile') : route('teacher.profile') }}" class="btn btn-nav-profile">
                        <i class="bi bi-person-circle me-1"></i> الملف الشخصي
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-nav-logout">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- المحتوى المتغير لكل صفحة -->
    <main class="container pb-5">
        @yield('content')
    </main>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>