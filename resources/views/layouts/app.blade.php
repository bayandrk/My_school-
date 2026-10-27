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
        // جلب لون المدرسة والشعار واسمها من جدول schools (المصدر الوحيد لهذه الإعدادات)
        $user = auth()->user();
        $schoolColor = '#6b21a8';
        $schoolLogo  = null;
        $schoolName  = 'النظام المدرسي';

        if ($user && $user->role !== 'admin') {
            $school = $user->school;

            if (!$school) {
                $schoolId = \App\Models\Child::where('parent_id', $user->id)->value('school_id');
                $school = $schoolId ? \App\Models\School::find($schoolId) : null;
            }

            if ($school) {
                $schoolColor = $school->color ?? '#1e3a8a';
                $schoolLogo  = $school->logo;
                $schoolName  = $school->name;
            }
        } elseif ($user && $user->role === 'admin') {
            $schoolName = 'لوحة تحكم الأدمن';
            $schoolColor = '#0f172a';
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

        /* شارة اسم المدرسة والشعار داخل النافبار */
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

        /* روابط التنقل الأنيقة */
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
                    @if(auth()->user()->role === 'admin')
                        <i class="bi bi-shield-lock-fill text-warning"></i> لوحة تحكم الأدمن
                    @elseif(auth()->user()->role === 'manager')
                        لوحة إدارة المدرسة
                    @elseif(auth()->user()->role === 'teacher')
                        <i class="bi bi-person-badge-fill"></i> لوحة تحكم المعلم
                    @else
                        <i class="bi bi-mortarboard-fill"></i> متابعة الأولياء
                    @endif
                </a>

                <!-- شارة اسم المدرسة والشعار داخل النافبار (لغير الأدمن) -->
                @if(auth()->user()->role !== 'admin')
                    <div class="school-navbar-brand ms-2">
                        @if(!empty($schoolLogo))
                            <img src="{{ asset('storage/' . $schoolLogo) }}" alt="لوغو" class="school-navbar-logo">
                        @else
                            <i class="bi bi-building fs-6"></i>
                        @endif
                        <span>{{ $schoolName }}</span>
                    </div>
                @endif
            </div>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- روابط التنقل حسب دور المستخدم -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold gap-1 ms-lg-3">
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.tasks') ? 'active' : '' }}" href="{{ route('admin.tasks') }}">المهام</a>
                        </li>
                    @elseif(auth()->user()->role === 'manager')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}" href="{{ route('manager.dashboard') }}">الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manager.tasks') ? 'active' : '' }}" href="{{ route('manager.tasks') }}">المهام</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manager.classes') ? 'active' : '' }}" href="{{ route('manager.classes') }}">دليل الصفوف</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manager.teachers') ? 'active' : '' }}" href="{{ route('manager.teachers') }}">دليل الأساتذة</a>
                        </li>
                    @elseif(auth()->user()->role === 'teacher')
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
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.parent-notes') ? 'active' : '' }}" href="{{ route('teacher.parent-notes') }}">ملاحظات الأهل</a>
                        </li>
                    @else
                        <!-- ولي الأمر -->
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-2">
                    
                    <!-- 🔔 إشعارات الأدمن -->
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        @php
                            $pendingManagersList = \App\Models\User::where('role', 'manager')->where('status', 'pending')->latest()->take(5)->get();
                            $pendingCount = \App\Models\User::where('role', 'manager')->where('status', 'pending')->count();
                        @endphp
                        <div class="dropdown me-1">
                            <button class="btn btn-nav-profile position-relative px-2.5 py-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="الطلبات المعلقة">
                                <i class="bi bi-bell-fill fs-6"></i>
                                @if($pendingCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2 p-2" style="width: 310px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header fw-bold text-dark border-bottom pb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-person-plus text-primary me-1"></i> طلبات انضمام المدراء</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $pendingCount }} طلب</span>
                                </li>
                                
                                @forelse($pendingManagersList as $managerReq)
                                    <li>
                                        <a class="dropdown-item p-2 rounded-2 mt-1 border-bottom bg-light text-wrap" href="{{ route('admin.tasks') }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-hourglass-split text-warning fs-5"></i>
                                                <div>
                                                    <div class="small fw-bold text-dark">طلب انضمام مدير جديد</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">المدير: {{ $managerReq->name }}</div>
                                                    <small class="text-secondary opacity-75" style="font-size: 0.68rem;">{{ $managerReq->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center text-muted py-3 small">لا توجد طلبات معلقة</li>
                                @endforelse

                                @if($pendingCount > 0)
                                    <li class="text-center pt-2">
                                        <a href="{{ route('admin.tasks') }}" class="btn btn-sm btn-primary w-100 fw-bold py-1" style="font-size: 0.8rem;">الانتقال لصفحة المهام</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <!-- 🔔 إشعارات المدير المُعدلة (تستدعي دالة قراءة إشعارات المدير وتوجه لصفحة المهام مباشرة) -->
                    @if(auth()->check() && auth()->user()->role === 'manager')
                        @php
                            $unreadManagerNotifications = auth()->user()->unreadNotifications;
                        @endphp
                        <div class="dropdown me-1">
                            <button class="btn btn-nav-profile position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="إشعارات المدير">
                                <i class="bi bi-bell-fill fs-6"></i>
                                @if($unreadManagerNotifications->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        {{ $unreadManagerNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2 p-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header fw-bold text-dark border-bottom pb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-bell text-primary me-1"></i> إشعارات المدير</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $unreadManagerNotifications->count() }} غير مقروء</span>
                                </li>
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <li>
                                        <!-- تم تعديل الرابط ليستدعي مسار manager.notifications.read ليعلمه كمقروء ويفتح صفحة المهام مباشرة دون 404 -->
                                        <a class="dropdown-item p-2 rounded-2 mt-1 border-bottom text-wrap {{ $notification->read_at ? 'bg-white opacity-75' : 'bg-light fw-bold' }}" href="{{ Route::has('manager.notifications.read') ? route('manager.notifications.read', $notification->id) : route('manager.tasks') }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi {{ $notification->data['icon'] ?? 'bi-person-plus-fill' }} text-primary fs-5"></i>
                                                <div>
                                                    <div class="small fw-bold text-dark">{{ $notification->data['title'] ?? 'إشعار جديد' }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $notification->data['message'] ?? '' }}</div>
                                                    <small class="text-secondary opacity-75" style="font-size: 0.68rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center text-muted py-4 small">
                                        <i class="bi bi-bell-slash d-block fs-3 mb-1 opacity-50"></i>
                                        لا توجد إشعارات حالياً
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    @endif

                    <!-- 🔔 إشعارات المعلم -->
                    @if(auth()->check() && auth()->user()->role === 'teacher')
                        @php
                            $unreadTeacherNotifications = auth()->user()->unreadNotifications;
                        @endphp
                        <div class="dropdown me-1">
                            <button class="btn btn-nav-profile position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="إشعارات المعلم">
                                <i class="bi bi-bell-fill fs-6"></i>
                                @if($unreadTeacherNotifications->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        {{ $unreadTeacherNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2 p-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header fw-bold text-dark border-bottom pb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-bell text-primary me-1"></i> إشعارات المعلم</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $unreadTeacherNotifications->count() }} غير مقروء</span>
                                </li>
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <li>
                                        <a class="dropdown-item p-2 rounded-2 mt-1 border-bottom text-wrap {{ $notification->read_at ? 'bg-white opacity-75' : 'bg-light fw-bold' }}" href="{{ route('teacher.notification.read', $notification->id) }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi {{ $notification->data['icon'] ?? 'bi-journal-check' }} text-primary fs-5"></i>
                                                <div>
                                                    <div class="small fw-bold text-dark">{{ $notification->data['title'] ?? 'إشعار جديد' }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $notification->data['message'] ?? '' }}</div>
                                                    <small class="text-secondary opacity-75" style="font-size: 0.68rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center text-muted py-4 small">
                                        <i class="bi bi-bell-slash d-block fs-3 mb-1 opacity-50"></i>
                                        لا توجد إشعارات حالياً
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    @endif

                    <!-- 🔔 إشعارات ولي الأمر -->
                    @if(auth()->check() && auth()->user()->role === 'parent')
                        @php
                            $unreadNotifications = auth()->user()->unreadNotifications;
                        @endphp
                        <div class="dropdown me-1">
                            <button class="btn btn-nav-profile position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="الإشعارات">
                                <i class="bi bi-bell-fill fs-6"></i>
                                @if($unreadNotifications->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        {{ $unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2 p-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header fw-bold text-dark border-bottom pb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-bell text-primary me-1"></i> الإشعارات</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $unreadNotifications->count() }} غير مقروء</span>
                                </li>
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <li>
                                        <a class="dropdown-item p-2 rounded-2 mt-1 border-bottom text-wrap {{ $notification->read_at ? 'bg-white opacity-75' : 'bg-light fw-bold' }}" href="{{ route('parent.notification.read', $notification->id) }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi {{ $notification->data['icon'] ?? 'bi-info-circle' }} text-primary fs-5"></i>
                                                <div>
                                                    <div class="small fw-bold text-dark">{{ $notification->data['title'] ?? 'إشعار جديد' }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $notification->data['message'] ?? '' }}</div>
                                                    <small class="text-secondary opacity-75" style="font-size: 0.68rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center text-muted py-4 small">
                                        <i class="bi bi-bell-slash d-block fs-3 mb-1 opacity-50"></i>
                                        لا توجد إشعارات حالياً
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    @endif

                    @if(auth()->user()->role !== 'admin')
                        <a href="{{ auth()->user()->role === 'manager' ? route('manager.profile') : (auth()->user()->role === 'teacher' ? route('teacher.profile') : route('parent.profile')) }}" class="btn btn-nav-profile">
                            <i class="bi bi-person-circle me-1"></i> الملف الشخصي
                        </a>
                    @endif

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