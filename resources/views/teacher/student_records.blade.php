<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الطالب - {{ $student->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; background-color: #f4f7f6; }</style>
</head>
<body>
    <div class="container py-5">
        <!-- الهيدر ومعلومات الطالب -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">سجل الطالب: <span class="text-primary">{{ $student->name }}</span></h3>
                <span class="badge bg-secondary fs-6">الصف: {{ $student->class }}</span>
            </div>
            <a href="{{ route('teacher.classes') }}" class="btn btn-secondary btn-sm">العودة للصفوف</a>
        </div>

        <!-- جدول جميع سجلات الطالب -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المادة / الموضوع</th>
                            <th>العلامة</th>
                            <th>الملاحظة</th>
                            <th>تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyChanges as $index => $change)
                            <tr>
                                <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $change->subject }}</td>
                                <td>
                                    @if($change->score !== null)
                                        <span class="badge bg-success fs-6">{{ $change->score }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $change->note ?? '-' }}</td>
                                <td>{{ $change->recorded_at ?? $change->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-muted">لا توجد سجلات مدخلة لهذا الطالب حتى الآن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>