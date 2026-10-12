<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المدير - المهام والطلبات</title>
    
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

        /* تحسينات بصرية بأحجام أصغر وأنعم */
        .dashboard-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            overflow: hidden;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            padding: 1rem 1.25rem;
            border: 1px solid #f1f5f9;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .card-header-custom {
            background-color: #ffffff;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.85rem 1.25rem;
            font-weight: 700;
        }

        .btn-assign-custom {
            background-color: #0d6efd;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.55rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-assign-custom:hover {
            background-color: #0b5ed7;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }

        .form-select-custom, .form-control-custom {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0.85rem;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-select-custom:focus, .form-control-custom:focus {
            background-color: #ffffff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }
    </style>
</head>
<body>

    <!-- شريط النافبار الموحد للمدير مع اسم المدرسة -->
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
                        <a class="nav-link active" href="{{ route('manager.dashboard') }}">المهام والطلبات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('manager.classes') }}">دليل الصفوف</a>
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

    <!-- المحتوى الرئيسي -->
    <div class="container pb-5">

        <!-- التنبيهات -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- كروت الإحصائيات السريعة (حجم مدمج) -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">طلبات الانضمام المعلقة</span>
                        <h4 class="fw-bold text-primary mb-0">{{ $pendingTeachers->count() }}</h4>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">الأساتذة المعتمدون</span>
                        <h4 class="fw-bold text-success mb-0">{{ isset($approvedTeachers) ? $approvedTeachers->count() : 0 }}</h4>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- 1. طلبات الانضمام المعلقة -->
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                        <span class="d-flex align-items-center gap-2 small text-dark fw-bold">
                            <i class="bi bi-person-plus-fill text-primary"></i> طلبات انضمام الأساتذة المعلقة
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 rounded-pill small fw-bold">
                            {{ $pendingTeachers->count() }}
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if($pendingTeachers->isEmpty())
                            <div class="text-center py-4">
                                <div class="stat-icon bg-light text-muted mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
                                    <i class="bi bi-inbox-fill"></i>
                                </div>
                                <h6 class="fw-bold text-secondary mb-1 small">لا توجد طلبات انضمام معلقة حالياً</h6>
                                <p class="text-muted small mb-0" style="font-size: 0.8rem;">جميع طلبات الأساتذة تم التعامل معها بنجاح.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-center small">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-2">#</th>
                                            <th class="py-2 text-start ps-3">الاسم</th>
                                            <th class="py-2">البريد الإلكتروني</th>
                                            <th class="py-2">الهاتف</th>
                                            <th class="py-2">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingTeachers as $index => $teacher)
                                            <tr>
                                                <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                                <td class="text-start ps-3 fw-bold text-dark">{{ $teacher->name }}</td>
                                                <td class="text-secondary">{{ $teacher->email }}</td>
                                                <td class="text-secondary">{{ $teacher->phone ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1.5">
                                                        <form action="{{ route('manager.teachers.approve', $teacher->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm fw-bold px-2.5 py-1 rounded-2 style-btn">
                                                                <i class="bi bi-check-lg me-1"></i> موافقة
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('manager.teachers.reject', $teacher->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-2.5 py-1 rounded-2 style-btn" onclick="return confirm('هل تريد رفض هذا الطلب؟')">
                                                                <i class="bi bi-x-lg me-1"></i> رفض
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. تعيين أستاذ لصف دراسي -->
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header card-header-custom d-flex align-items-center gap-2 small text-dark fw-bold">
                        <i class="bi bi-check2-square text-primary"></i> تعيين أستاذ لصف دراسي
                    </div>
                    <div class="card-body p-3.5">
                        <form action="{{ route('manager.assign-teacher-class') }}" method="POST">
                            @csrf
                            <div class="row g-2.5">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-secondary mb-1">اختر الأستاذ:</label>
                                    <select name="teacher_id" class="form-select form-select-custom" required>
                                        <option value="">-- اختر الأستاذ --</option>
                                        @foreach($approvedTeachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-secondary mb-1">اختر الصف الدراسي:</label>
                                    <select name="class_name" class="form-select form-select-custom" required>
                                        <option value="">-- اختر الصف --</option>
                                        @isset($classesList)
                                            @foreach($classesList as $className)
                                                <option value="{{ $className }}">{{ $className }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-assign-custom w-100 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-person-check-fill"></i> تأكيد تعيين الأستاذ للصف
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