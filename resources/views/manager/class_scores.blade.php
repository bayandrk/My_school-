<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>علامات الصف - {{ $className }}</title>
    
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

        .navbar-manager { 
            background-color: #0d6efd; 
        }

        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-control-sm, .form-select-sm, .btn-sm {
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .form-control-sm, .form-select-sm {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
        }

        .form-control-sm:focus, .form-select-sm:focus {
            background-color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
        }

        .subject-badge {
            background-color: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .score-badge {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
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

    <!-- النافبار العلوي -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-manager py-3 shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="#">
                لوحة إدارة المدرسة
            </a>
            <a href="{{ route('manager.classes') }}" class="btn btn-light btn-sm fw-bold text-primary px-3 rounded-2">
                <i class="bi bi-arrow-right me-1"></i> العودة إلى دليل الصفوف
            </a>
        </div>
    </nav>

    <div class="container pb-5">
        
        <!-- الشريط العلوي والعنوان والفلترة المجمعة -->
        <div class="main-card p-3.5 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-award-fill text-primary fs-4"></i>
                    <h4 class="fw-bold text-dark mb-0 fs-5">علامات طلاب: <span class="text-primary">{{ $className }}</span></h4>
                </div>

                <!-- نموذج البحث والتصفية المتكامل (الاسم، المادة، العام الدراسي) -->
                <form method="GET" action="{{ route('manager.class.scores') }}" class="d-flex align-items-center gap-2 flex-wrap mb-0">
                    <input type="hidden" name="class_name" value="{{ $className }}">
                    
                    <!-- تصفية العام الدراسي -->
                    <select name="year" class="form-select form-select-sm fw-semibold" style="width: 115px;" onchange="this.form.submit()">
                        @foreach($availableYears ?? [now()->year, now()->year - 1] as $y)
                            <option value="{{ $y }}" {{ (string)request('year', $selectedYear ?? now()->year) === (string)$y ? 'selected' : '' }}>
                                العام: {{ $y }}
                            </option>
                        @endforeach
                    </select>

                    <!-- بحث باسم الطالب -->
                    <input type="text" name="search_name" value="{{ request('search_name', $searchName ?? '') }}" class="form-control form-control-sm" placeholder="بحث باسم الطالب..." style="width: 160px;">

                    <!-- بحث بالمادة -->
                    <select name="search_subject" class="form-select form-select-sm" style="width: 140px;">
                        <option value="">كل المواد</option>
                        @if(isset($subjects))
                            @foreach($subjects as $subj)
                                <option value="{{ $subj }}" {{ request('search_subject', $searchSubject ?? '') == $subj ? 'selected' : '' }}>{{ $subj }}</option>
                            @endforeach
                        @endif
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">بحث</button>

                    @if(request('search_name') || request('search_subject') || request('year'))
                        <a href="{{ route('manager.class.scores', ['class_name' => $className]) }}" class="btn btn-outline-secondary btn-sm px-2">إلغاء</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- جدول عرض العلامات -->
        <div class="main-card">
            <div class="card-body p-0">
                @if($students->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="fw-bold mb-0 small">لا توجد سجلات علامات مطابقة لخيارات البحث حالياً.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">#</th>
                                    <th class="py-3 text-start ps-4">اسم الطالب</th>
                                    <th class="py-3">المادة / النشاط</th>
                                    <th class="py-3">الدرجة</th>
                                    <th class="py-3">تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp
                                @foreach($students as $student)
                                    @foreach($student->dailyChanges as $change)
                                        <tr>
                                            <td class="fw-bold text-muted">{{ $counter++ }}</td>
                                            <td class="text-start ps-4 fw-bold text-dark">{{ $student->name }}</td>
                                            <td>
                                                <span class="subject-badge">{{ $change->subject }}</span>
                                            </td>
                                            <td>
                                                <span class="score-badge">{{ $change->score }}</span>
                                            </td>
                                            <td>
                                                <span class="date-badge">
                                                    {{ \Carbon\Carbon::parse($change->recorded_at ?? $change->created_at)->format('Y-m-d') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>