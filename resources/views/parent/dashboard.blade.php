<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة أولي الأمر - سجلات اليوم</title>

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
            background-color: #f8f9fa;
            color: #333;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .card-child {
            border: none;
            border-radius: 16px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-child:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
        }

        .child-avatar {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .avatar-placeholder {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            font-size: 1.5rem;
            border: 3px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .note-card {
            border-right: 4px solid #0d6efd;
            background-color: #f8fafc;
            border-radius: 8px;
        }

        .badge-date {
            background-color: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
        }
    </style>
</head>
<body>

    <!-- شريط الملاحة الأعلى -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <i class="bi bi-mortarboard-fill fs-4"></i>
                <span>متابعة الأولياء</span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small d-none d-md-inline">
                    مرحباً بك، {{ auth()->user()->name ?? 'ولي الأمر' }}
                </span>
                
                <div class="d-flex align-items-center gap-2">
                    <!-- زر الملف الشخصي -->
                    <a href="{{ route('parent.profile') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="bi bi-person-circle me-1"></i> الملف الشخصي
                    </a>

                    <!-- زر تسجيل الخروج -->
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-left me-1"></i> خروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- المحتوى الرئيسي -->
    <main class="container py-5">
        
        <!-- الهيدر والترحيب -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
            <div>
                <h2 class="fw-bold text-dark mb-1">لوحة متابعة الأبناء</h2>
                <p class="text-muted mb-0">عرض السجلات اليومية المضافة من قِبل المعلمين</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge-date">
                    <i class="bi bi-calendar3 me-1"></i> اليوم: {{ \Carbon\Carbon::now()->format('Y-m-d') }}
                </span>
            </div>
        </div>

        <!-- قائمة الأطفال -->
        <div class="row g-4">
            @forelse($children as $child)
                <div class="col-lg-6">
                    <div class="card card-child shadow-sm bg-white h-100">
                        
                        <!-- كارت الطفل الأعلى ( الاسم والصورة ) -->
                        <div class="card-header bg-white border-0 p-4 pb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                
                                <div class="d-flex align-items-center gap-3">
                                    <!-- الصورة الشخصية -->
                                    @if(!empty($child->image))
                                        <img src="{{ asset('storage/' . $child->image) }}" alt="{{ $child->name }}" class="child-avatar">
                                    @else
                                        <div class="avatar-placeholder">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    @endif

                                    <!-- تفاصيل اسم الطفل -->
                                    <div>
                                        <h5 class="fw-bold mb-1 text-dark">{{ $child->name }}</h5>
                                        <small class="text-muted">
                                            الصف: {{ $child->class ?? 'غير محدد' }} 
                                            @if($child->age) | العمر: {{ $child->age }} سنة @endif
                                        </small>
                                    </div>
                                </div>

                                <!-- زر الانتقال للتفاصيل الكاملة -->
                                <a href="{{ route('parent.child.show', $child->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    التفاصيل <i class="bi bi-arrow-left ms-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- جسم الكارت: سجلات اليوم -->
                        <div class="card-body p-4 pt-3">
                            <h6 class="fw-bold text-secondary mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-journal-text text-primary"></i> سجل المعلم لهذا اليوم:
                            </h6>

                            @forelse($child->dailyChanges as $change)
                                <div class="note-card p-3 mb-2 shadow-2xs">
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                                        <span class="fw-bold text-dark">
                                            <i class="bi bi-person-badge me-1"></i> {{ $change->teacher->name ?? 'المعلم' }}
                                        </span>
                                        <span>
                                            <i class="bi bi-clock me-1"></i> {{ $change->created_at->format('h:i A') }}
                                        </span>
                                    </div>
                                    <p class="mb-0 text-dark fs-6 lh-base">
                                        {{ $change->notes ?? $change->description ?? $change->details ?? 'تم تسجيل نشاط جديد اليوم.' }}
                                    </p>
                                </div>
                            @empty
                                <!-- حالة عدم وجود سجل اليوم -->
                                <div class="text-center py-4 bg-light rounded-3 border border-dashed">
                                    <i class="bi bi-chat-square-dots fs-2 text-muted d-block mb-2"></i>
                                    <p class="text-muted small mb-0">لم يقم المعلم بإضافة أي ملاحظة أو سجل لهذا اليوم حتى الآن.</p>
                                </div>
                            @endforelse

                        </div>

                    </div>
                </div>
            @empty
                <!-- حالة عدم وجود أطفال مربوطين بحساب الأم -->
                <div class="col-12 text-center py-5">
                    <div class="bg-white p-5 rounded-4 shadow-sm d-inline-block">
                        <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                        <h5 class="fw-bold text-dark">لا يوجد أطفال مسجلين</h5>
                        <p class="text-muted mb-0">لم يتم ربط أي أطفال بحسابك حالياً. يرجى التواصل مع إدارة المدرسة.</p>
                    </div>
                </div>
            @endforelse
        </div>

    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>