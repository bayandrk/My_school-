@extends('layouts.app')

@section('title', 'سجل الطالب - ' . $student->name)

@push('styles')
    <style>
        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
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
@endpush

@section('content')
    <div class="container py-2">
        <!-- الهيدر ومعلومات الطالب -->
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3.5 rounded-4 shadow-sm border flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                    <i class="bi bi-person-vcard-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-1 fs-5">سجل الطالب: <span class="text-primary">{{ $student->name }}</span></h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold small">
                        الصف: {{ $student->class }}
                    </span>
                </div>
            </div>

            <a href="{{ route('teacher.classes') }}" class="btn btn-outline-dark btn-sm fw-bold px-3">
                <i class="bi bi-arrow-right me-1"></i> العودة للصفوف
            </a>
        </div>

        <!-- جدول جميع سجلات الطالب -->
        <div class="card main-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 text-muted">#</th>
                                <th class="py-3 text-start ps-4">المادة / الموضوع</th>
                                <th class="py-3">العلامة</th>
                                <th class="py-3">الملاحظة</th>
                                <th class="py-3">تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyChanges as $index => $change)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-start ps-4">
                                        <span class="subject-badge">{{ $change->subject }}</span>
                                    </td>
                                    <td>
                                        @if($change->score !== null)
                                            <span class="score-badge">{{ $change->score }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
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
                                    <td colspan="5" class="py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        لا توجد سجلات مدخلة لهذا الطالب حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection