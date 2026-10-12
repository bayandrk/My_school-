<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملاحظات - {{ $className }}</title>
    
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

        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-control-sm, .btn-sm {
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .form-control-sm {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
        }

        .form-control-sm:focus {
            background-color: #fff;
            border-color: #0d7a5f;
            box-shadow: 0 0 0 3px rgba(13, 122, 95, 0.12);
        }

        .subject-badge {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .date-badge {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 0.78rem;
            border: 1px solid #e2e8f0;
            direction: ltr;
            display: inline-block;
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        
        <!-- الشريط العلوي والعنوان والفلترة مرتبة من اليمين -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 bg-white p-3.5 rounded-4 shadow-sm border">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-warning fs-4"></i>
                <h4 class="fw-bold text-dark mb-0 fs-5">ملاحظات طلاب: <span class="text-success">{{ $className }}</span></h4>
            </div>
            
            <!-- نموذج البحث المنفصل -->
            <form method="GET" action="{{ route('teacher.class.notes', $className) }}" class="d-flex align-items-center gap-2 flex-wrap mb-0">
                <!-- بحث اسم الطالب -->
                <input type="text" name="student_name" value="{{ request('student_name') }}" class="form-control form-control-sm" placeholder="اسم الطالب..." style="width: 160px;">
                
                <!-- بحث الموضوع / المادة -->
                <input type="text" name="subject" value="{{ request('subject') }}" class="form-control form-control-sm" placeholder="الموضوع / المادة..." style="width: 150px;">
                
                <!-- بحث التاريخ -->
                <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" style="width: 140px;">

                <button type="submit" class="btn btn-success btn-sm fw-bold px-3">بحث</button>
                @if(request('student_name') || request('subject') || request('date'))
                    <a href="{{ route('teacher.class.notes', $className) }}" class="btn btn-outline-secondary btn-sm px-2">إلغاء</a>
                @endif
            </form>

            <a href="{{ route('teacher.classes') }}" class="btn btn-outline-dark btn-sm fw-bold px-3">
                <i class="bi bi-arrow-right me-1"></i> العودة للصفوف
            </a>
        </div>

        <!-- جدول العرض المطور للملاحظات -->
        <div class="card main-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 text-start ps-4">اسم الطالب</th>
                                <th class="py-3">الموضوع / المادة</th>
                                <th class="py-3">الملاحظة</th>
                                <th class="py-3">تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyChanges as $change)
                                <tr>
                                    <td class="text-start ps-4 fw-bold text-dark">{{ $change->child->name ?? 'غير معروف' }}</td>
                                    <td>
                                        <span class="subject-badge">{{ $change->subject }}</span>
                                    </td>
                                    <td class="text-secondary fw-semibold">{{ $change->note ?? '-' }}</td>
                                    <td>
                                        <span class="date-badge">
                                            {{ \Carbon\Carbon::parse($change->recorded_at ?? $change->created_at)->format('H:i | Y-m-d') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        لا توجد ملاحظات مطابقة لخيارات البحث المحددة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>