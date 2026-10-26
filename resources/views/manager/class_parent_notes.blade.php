@extends('layouts.app')

@section('title', 'ملاحظات الأهل - ' . $className)

@push('styles')
    <style>
        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-control-sm, .form-select-sm {
            border-radius: 8px;
            font-size: 0.875rem;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
        }

        .form-control-sm:focus, .form-select-sm:focus {
            background-color: #fff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .btn-search-school {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .subject-badge {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            border: 1px solid color-mix(in srgb, var(--primary-color) 25%, transparent);
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

        .badge-teacher {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* مربع ملاحظة ولي الأمر */
        .parent-note-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        /* مربع رد الأنسة */
        .teacher-reply-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 0.75rem;
        }

        /* منع تلف الأعمدة: عرض أدنى للجدول وعدم التفاف الشارات */
        .table {
            min-width: 850px;
        }

        .table td,
        .table th {
            vertical-align: top;
        }

        .subject-badge,
        .date-badge {
            white-space: nowrap;
        }

        .student-cell {
            min-width: 130px;
            white-space: nowrap;
        }

        /* دليل بصري أنو الجدول قابل للسحب أفقياً */
        .table-responsive {
            position: relative;
        }

        .table-responsive::after {
            content: "⇠ اسحب لعرض كل الأعمدة ⇢";
            display: block;
            text-align: center;
            font-size: 0.72rem;
            color: #94a3b8;
            padding: 6px 0;
        }

        .btn-back-custom {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px;
            font-size: 0.85rem;
            padding: 0.35rem 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-back-custom:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }
    </style>
@endpush

@section('content')

    <!-- الشريط العلوي والعنوان والفلترة -->
    <div class="main-card p-3.5 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('manager.classes') }}" class="btn btn-back-custom d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-right"></i> العودة لدليل الصفوف
                </a>
                <h4 class="fw-bold text-dark mb-0 fs-5 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-heart text-primary"></i> ملاحظات الأهل لطلاب: <span class="text-primary">{{ $className }}</span>
                </h4>
            </div>

            <!-- نموذج البحث والتصفية -->
            <form method="GET" action="{{ route('manager.class.parent.notes') }}" class="d-flex align-items-center gap-2 flex-wrap mb-0">
                <input type="hidden" name="class_name" value="{{ $className }}">

                <input type="text" name="search_name" value="{{ request('search_name', $searchName ?? '') }}" class="form-control form-control-sm" placeholder="بحث باسم الطالب..." style="width: 160px;">

                <select name="search_subject" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">كل المواضيع</option>
                    @foreach($subjects ?? [] as $subj)
                        <option value="{{ $subj }}" {{ request('search_subject', $searchSubject ?? '') == $subj ? 'selected' : '' }}>{{ $subj }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-search-school fw-bold px-3 py-1">بحث</button>

                @if(request('search_name') || request('search_subject'))
                    <a href="{{ route('manager.class.parent.notes', ['class_name' => $className]) }}" class="btn btn-outline-secondary btn-sm rounded-2 px-2">إلغاء</a>
                @endif
            </form>
        </div>
    </div>

    <!-- جدول ملاحظات الأهل وردود الأنسات -->
    <div class="main-card">
        <div class="card-body p-0">
            @if($notes->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="fw-bold mb-0 small">لا توجد ملاحظات من أولياء الأمور مطابقة لخيارات البحث حالياً.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">#</th>
                                <th class="py-3 text-start ps-4">اسم الطالب</th>
                                <th class="py-3">الموضوع</th>
                                <th class="py-3 text-start">ملاحظة ولي الأمر</th>
                                <th class="py-3 text-start">رد الأنسة</th>
                                <th class="py-3">تاريخ الملاحظة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @foreach($notes as $note)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $counter++ }}</td>
                                    <td class="text-start ps-4 fw-bold text-dark">
                                        <div class="student-cell">
                                        {{ $note->child->name }}
                                        <div class="small text-muted fw-normal">
                                            ولي الأمر: {{ $note->child->parent->name ?? ($note->parent->name ?? 'غير محدد') }}
                                        </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="subject-badge">{{ $note->subject }}</span>
                                    </td>
                                    <td class="text-start">
                                        <div class="parent-note-box">
                                            <p class="mb-0 fw-semibold text-dark" style="white-space: pre-line; font-size: 0.82rem;">
                                                {{ $note->note }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="text-start">
                                        @forelse($note->replies->sortBy('recorded_at') as $reply)
                                            <div class="teacher-reply-box mb-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge-teacher badge rounded-pill px-2 py-1 small">
                                                        <i class="bi bi-reply-fill me-1"></i> {{ $reply->teacher->name ?? 'الأنسة' }}
                                                    </span>
                                                    <span class="text-muted" style="font-size: 0.7rem;">
                                                        {{ \Carbon\Carbon::parse($reply->recorded_at)->format('Y-m-d') }}
                                                    </span>
                                                </div>
                                                <p class="mb-0 fw-semibold text-dark" style="white-space: pre-line; font-size: 0.82rem;">
                                                    {{ $reply->note }}
                                                </p>
                                            </div>
                                        @empty
                                            <span class="text-muted small fst-italic">لم يتم الرد بعد</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <span class="date-badge">
                                            {{ \Carbon\Carbon::parse($note->recorded_at)->format('Y-m-d') }}
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

@endsection
