<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الطالب: {{ $child->name ?? '' }}</title>
    
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

        .navbar-parent {
            background-color: #0d6efd;
        }

        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .avatar-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .info-badge {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
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

    <!-- النافبار العلوي (مثال لوحة ولي الأمر أو زر العودة) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-parent py-3 shadow-sm mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold fs-5">
                سجل الطالب: {{ $child->name ?? '' }}
            </span>
            <div class="d-flex align-items-center">
                <a href="{{ route('parent.dashboard') ?? url('/parent/dashboard') }}" class="btn btn-light btn-sm fw-bold text-primary px-3 rounded-2">
                    <i class="bi bi-arrow-right me-1"></i> العودة للوحة الرئيسية
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">

        <!-- كرت معلومات الطالب الشاملة -->
        <div class="card card-custom mb-4 p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    @if(isset($child->image) && $child->image)
                        <img src="{{ asset('storage/' . $child->image) }}" alt="{{ $child->name }}" class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;">
                    @else
                        <div class="avatar-circle">
                            {{ mb_substr($child->name ?? 'ط', 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $child->name }}</h4>
                        <p class="text-muted small mb-0 fw-semibold">
                            الصف: <span class="text-primary">{{ $child->class ?? '-' }}</span> | العمر: <span class="text-primary">{{ $child->age ?? '-' }} سنوات</span>
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                        الجنس: {{ isset($child->gender) ? ($child->gender == 'male' ? 'ذكر' : 'أنثى') : '-' }}
                    </span>
                </div>
            </div>

            <!-- تفاصيل إضافية (الحالة الصحية، ولي الأمر، والأساتذة) -->
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-badge h-100">
                        <span class="text-muted small fw-bold d-block mb-1">
                            <i class="bi bi-heart-pulse text-danger me-1"></i> الحالة الصحية / ملاحظات:
                        </span>
                        <span class="fw-bold text-dark small">
                            {{ $child->health_status ?? 'سليم تماماً' }}
                        </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-badge h-100">
                        <span class="text-muted small fw-bold d-block mb-1">
                            <i class="bi bi-person-fill text-primary me-1"></i> ولي الأمر:
                        </span>
                        <span class="fw-bold text-dark small">
                            {{ $child->parent->name ?? 'غير محدد' }}
                        </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-badge h-100">
                        <span class="text-muted small fw-bold d-block mb-1">
                            <i class="bi bi-person-badge text-success me-1"></i> الأساتذة المدرسون:
                        </span>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @forelse($child->teachers ?? [] as $t)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fw-bold" style="font-size: 0.75rem;">
                                    {{ $t->name }}
                                </span>
                            @empty
                                <span class="text-muted small">لا يوجد أساتذة معينين بعد</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- سجل الدرجات والملاحظات اليومية الشامل -->
        <div class="card card-custom">
            <div class="p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0 fs-6 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-text text-primary"></i> سجل الدرجات والملاحظات اليومية الشامل
                </h5>
            </div>

            <div class="card-body p-0">
                @php
                    $recordsList = $child->dailyChanges ?? $dailyChanges ?? [];
                @endphp

                @if($recordsList->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="fw-bold mb-0 small">لا توجد ملاحظات أو درجات مسجلة لهذا الطالب حتى الآن.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">#</th>
                                    <th class="py-3">المادة / الموضوع</th>
                                    <th class="py-3">الدرجة</th>
                                    <th class="py-3">الملاحظة</th>
                                    <th class="py-3">تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recordsList as $index => $record)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="subject-badge">{{ $record->subject ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if(($record->score) !== null)
                                                <span class="score-badge">{{ $record->score }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-secondary fw-semibold">{{ $record->note ?? '-' }}</td>
                                        <td>
                                            <span class="date-badge">
                                                {{ \Carbon\Carbon::parse($record->recorded_at ?? $record->created_at)->format('H:i | Y-m-d') }}
                                            </span>
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

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>