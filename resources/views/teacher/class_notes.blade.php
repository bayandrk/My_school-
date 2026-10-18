@extends('layouts.app')

@section('title', 'ملاحظات طلاب: ' . $className)

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

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.875rem;
            margin-bottom: 0.35rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.55rem 0.85rem;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .subject-badge {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
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

        /* أزرار الإجراءات */
        .btn-action-edit {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.3rem 0.85rem !important;
            font-size: 0.8rem !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            min-width: 75px;
            justify-content: center;
        }

        .btn-action-edit:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color, #ffffff) !important;
        }

        .btn-action-delete {
            background-color: #ffffff !important;
            border: 1.5px solid #ef4444 !important;
            color: #ef4444 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.3rem 0.85rem !important;
            font-size: 0.8rem !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            min-width: 75px;
            justify-content: center;
        }

        .btn-action-delete:hover {
            background-color: #ef4444 !important;
            color: #ffffff !important;
        }

        .btn-school-main {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color, #ffffff) !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            transition: all 0.2s ease;
        }

        .btn-school-main:hover {
            opacity: 0.9;
            color: var(--btn-text-color, #ffffff) !important;
        }

        /* أزرار التنقل بين الصفحات Pagination */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            color: var(--primary-color);
            border-color: #e2e8f0;
            font-weight: 600;
            padding: 0.4rem 0.75rem;
            transition: all 0.2s ease;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color, #ffffff) !important;
        }

        .pagination .page-item .page-link:hover {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 0.75rem;
        }

        .modal-rtl-fix {
            text-align: right !important;
            direction: rtl !important;
        }

        .modal-rtl-fix .form-label,
        .modal-rtl-fix .form-control,
        .modal-rtl-fix .modal-title {
            text-align: right !important;
            direction: rtl !important;
        }
    </style>
@endpush

@section('content')
    <div class="container py-2">

        <!-- رسائل النجاح أو الأخطاء -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- الهيدر ونموذج البحث -->
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3.5 rounded-4 shadow-sm border flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 fs-4" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-1 fs-5">ملاحظات طلاب: <span style="color: var(--primary-color);">{{ $className }}</span></h4>
                    <span class="badge rounded-pill px-3 py-1 fw-bold small" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                        مرتبة أبجديّاً حسب أسماء الطلاب
                    </span>
                </div>
            </div>

            <!-- نموذج البحث والتصفية -->
            <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2 flex-wrap">
                <input type="text" name="student_name" class="form-control form-control-sm" placeholder="اسم الطالب..." value="{{ $studentName ?? request('student_name') }}" style="width: 140px;">
                <input type="text" name="subject" class="form-control form-control-sm" placeholder="الموضوع / المادة..." value="{{ $subject ?? request('subject') }}" style="width: 140px;">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date ?? request('date') }}" style="width: 140px;">
                
                <button type="submit" class="btn btn-sm btn-school-main fw-bold px-3 py-1">
                    <i class="bi bi-search"></i> بحث
                </button>

                @if(request('student_name') || request('subject') || request('date'))
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary py-1 px-2 fw-semibold">إعادة ضبط</a>
                @endif

                <a href="{{ route('teacher.classes') }}" class="btn btn-outline-dark btn-sm fw-bold px-3 py-1">
                    <i class="bi bi-arrow-right me-1"></i> العودة للصفوف
                </a>
            </form>
        </div>

        <!-- جدول ملاحظات الصف -->
        <div class="card main-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 text-muted">#</th>
                                <th class="py-3 text-start ps-4">اسم الطالب</th>
                                <th class="py-3">الموضوع / المادة</th>
                                <th class="py-3">الملاحظة</th>
                                <th class="py-3">تاريخ التسجيل</th>
                                <th class="py-3" style="width: 200px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyChanges as $index => $change)
                                <tr>
                                    <td class="fw-bold text-muted">
                                        {{ method_exists($dailyChanges, 'firstItem') ? $dailyChanges->firstItem() + $index : $index + 1 }}
                                    </td>
                                    <td class="text-start ps-4 fw-bold text-dark">
                                        {{ $change->child->name ?? 'غير محدد' }}
                                    </td>
                                    <td>
                                        <span class="subject-badge">{{ $change->subject }}</span>
                                    </td>
                                    <td class="text-secondary fw-semibold">{{ $change->note }}</td>
                                    <td>
                                        <span class="date-badge">
                                            {{ \Carbon\Carbon::parse($change->recorded_at ?? $change->created_at)->format('H:i | Y-m-d') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                            <!-- زر التعديل -->
                                            <button type="button" class="btn btn-action-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $change->id }}">
                                                <i class="bi bi-pencil-square"></i> تعديل
                                            </button>

                                            <!-- زر الحذف -->
                                            <form action="{{ Route::has('teacher.daily-changes.destroy') ? route('teacher.daily-changes.destroy', $change->id) : url('/teacher/daily-changes/' . $change->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action-delete">
                                                    <i class="bi bi-trash3-fill"></i> حذف
                                                </button>
                                            </form>
                                        </div>

                                        <!-- مودال التعديل -->
                                        <div class="modal fade modal-rtl-fix" id="editModal{{ $change->id }}" tabindex="-1" aria-hidden="true" dir="rtl">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 modal-rtl-fix p-2">
                                                    <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                                                        <h6 class="modal-title fw-bold text-dark fs-6 mb-0">تعديل الملاحظة</h6>
                                                        <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ Route::has('teacher.daily-changes.update') ? route('teacher.daily-changes.update', $change->id) : url('/teacher/daily-changes/' . $change->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body text-end modal-rtl-fix">
                                                            <div class="mb-3">
                                                                <label class="form-label">الموضوع / المادة:</label>
                                                                <input type="text" name="subject" class="form-control" value="{{ $change->subject }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">التاريخ والوقت:</label>
                                                                <input type="datetime-local" name="recorded_at" class="form-control" value="{{ $change->recorded_at ? \Carbon\Carbon::parse($change->recorded_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">الدرجة / العلامة (إن وجدت):</label>
                                                                <input type="number" step="0.1" min="0" max="100" name="score" class="form-control" value="{{ $change->score }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">الملاحظة:</label>
                                                                <input type="text" name="note" class="form-control" value="{{ $change->note }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-light rounded-3 btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-school-main rounded-3 btn-sm px-3">حفظ التغييرات</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        لا توجد ملاحظات مسجلة لهذا الصف حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- أزرار التنقل بين الصفحات (Pagination) 10 بـ 10 -->
                @if(method_exists($dailyChanges, 'hasPages') && $dailyChanges->hasPages())
                    <div class="d-flex justify-content-center p-3 border-top">
                        {{ $dailyChanges->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection