@extends('layouts.app')

@section('title', 'سجل الطالب: ' . ($child->name ?? ''))

@php
    $schoolColor = $child->manager?->primary_color;
    if (!is_string($schoolColor) || !preg_match('/^#[0-9A-Fa-f]{6}$/', $schoolColor)) {
        $schoolColor = '#0d7a5f';
    }
@endphp

@push('styles')
    <style>
        :root {
            --primary-color: {{ $schoolColor }} !important;
            --school-color: {{ $schoolColor }} !important;
            --btn-text-color: #ffffff;
        }

        .navbar-custom, .navbar-parent, nav.navbar {
            background-color: var(--primary-color) !important;
        }

        body { 
            font-family: 'Cairo', sans-serif; 
            background-color: #f8fafc; 
            color: #1e293b;
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
            background-color: var(--primary-color);
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
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            color: var(--primary-color) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 25%, transparent) !important;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .score-badge {
            background-color: var(--primary-color) !important;
            color: white !important;
            font-weight: 800;
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

        .btn-back-custom {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            color: var(--primary-color) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.4rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-back-custom:hover {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        .btn-send-custom {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700;
            border-radius: 8px;
        }

        .btn-send-custom:hover {
            opacity: 0.9;
        }
    </style>
@endpush

@section('content')

<div class="container pb-5" style="--primary-color: {{ $schoolColor }};">

    <!-- تنبيهات النجاح والأخطاء -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- شريط العودة العلوي -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-person-badge-fill me-1" style="color: var(--primary-color);"></i> سجل الطالب: {{ $child->name ?? '' }}
        </h4>
        <a href="{{ route('parent.dashboard') ?? url('/parent/dashboard') }}" class="btn btn-back-custom">
            <i class="bi bi-arrow-right me-1"></i> العودة للوحة الرئيسية
        </a>
    </div>

    <!-- كرت معلومات الطالب الشاملة -->
    <div class="card card-custom mb-4 p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                @if(isset($child->image) && $child->image)
                    <img src="{{ asset('storage/' . $child->image) }}" alt="{{ $child->name }}" class="rounded-circle shadow-sm border" style="width: 64px; height: 64px; object-fit: cover;">
                @else
                    <div class="avatar-circle">
                        {{ mb_substr($child->name ?? 'ط', 0, 1) }}
                    </div>
                @endif
                <div>
                    <h4 class="fw-bold text-dark mb-1">{{ $child->name }}</h4>
                    <p class="text-muted small mb-0 fw-semibold">
                        الصف: <span style="color: var(--primary-color);">{{ $child->class ?? '-' }}</span> | العمر: <span style="color: var(--primary-color);">{{ $child->age ?? '-' }} سنوات</span>
                        @if(!empty($child->manager?->school_name))
                            | المدرسة: <span class="fw-bold" style="color: var(--primary-color);">{{ $child->manager->school_name }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important; color: var(--primary-color) !important; border: 1px solid color-mix(in srgb, var(--primary-color) 25%, transparent) !important;">
                    الجنس: {{ isset($child->gender) ? ($child->gender == 'male' ? 'ذكر' : 'أنثى') : '-' }}
                </span>

                <!-- زر إرسال ملاحظة للأستاذ -->
                <button type="button" class="btn btn-send-custom btn-sm px-3 py-2 rounded-pill d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#sendNoteModal">
                    <i class="bi bi-send-plus-fill"></i> إرسال ملاحظة لأستاذ
                </button>
            </div>
        </div>

        <!-- تفاصيل إضافية -->
        <div class="row g-3">
            <div class="col-md-4">
                <div class="info-badge h-100">
                    <span class="text-muted small fw-bold d-block mb-1">
                        <i class="bi bi-heart-pulse text-danger me-1"></i> الحالة الصحية / ملاحظات:
                    </span>
                    <span class="fw-bold text-dark small">
                        {{ $child->health_status ?? ($child->Health_status ?? 'سليم تماماً') }}
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-badge h-100">
                    <span class="text-muted small fw-bold d-block mb-1">
                        <i class="bi bi-person-fill me-1" style="color: var(--primary-color);"></i> ولي الأمر:
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

    <!-- كرت ملاحظات ولي الأمر وردود المعلمة -->
    <div class="card card-custom mb-4 p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-chat-left-quote-fill" style="color: var(--primary-color);"></i> ملاحظتك الخاصة وتوجيهاتك للمدرسة
            </h6>
            @if(!empty($child->parent_note))
                <span class="text-muted" style="font-size: 0.75rem;">
                    <i class="bi bi-clock me-1"></i> آخر تحديث: {{ $child->updated_at?->diffForHumans() }}
                </span>
            @endif
        </div>

        @if(!empty($child->parent_note))
            <div class="p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                <span class="small fw-bold d-block mb-1" style="color: var(--primary-color);">نص الملاحظة المرسلة:</span>
                <p class="fw-semibold text-dark mb-0 small" style="white-space: pre-line;">
                    {{ $child->parent_note }}
                </p>
            </div>
        @else
            <p class="text-muted small mb-3">لم تقم بإضافة ملاحظة خاصة لهذا الطالب بعد. يمكنك إرسال ملاحظة عبر زر "إرسال ملاحظة لأستاذ" أعلاه.</p>
        @endif

        <!-- رد المعلمة إن وجد -->
        @if(!empty($child->teacher_reply))
            <div class="p-3 rounded-3 bg-white border-start border-4 border-success shadow-sm">
                <h6 class="fw-bold text-success mb-2 d-flex align-items-center gap-2 small">
                    <i class="bi bi-reply-fill fs-5"></i> رد المعلمة:
                </h6>
                <p class="mb-0 text-dark small fw-semibold" style="white-space: pre-line;">
                    {{ $child->teacher_reply }}
                </p>
            </div>
        @endif
    </div>

    <!-- Modal إرسال ملاحظة للأستاذ -->
    <div class="modal fade" id="sendNoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-chat-left-dots-fill text-primary"></i> إرسال ملاحظة لكادر التدريس
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('parent.send-teacher-note', $child->id) }}" method="POST">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">اختر الأستاذ المستهدف (أو أكثر):</label>
                            <div class="border rounded-3 p-2 bg-light" style="max-height: 120px; overflow-y: auto;">
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" id="selectAllTeachers" onclick="toggleAllTeachers(this)">
                                    <label class="form-check-label small fw-bold text-primary" for="selectAllTeachers">
                                        تحديد جميع الأساتذة المدرسين
                                    </label>
                                </div>
                                <hr class="my-1 text-muted opacity-25">
                                @forelse($child->teachers ?? [] as $teacher)
                                    <div class="form-check">
                                        <input class="form-check-input teacher-checkbox" type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}">
                                        <label class="form-check-label small fw-semibold text-dark" for="teacher_{{ $teacher->id }}">
                                            {{ $teacher->name }}
                                        </label>
                                    </div>
                                @empty
                                    <span class="text-muted small d-block py-1">لا يوجد أساتذة معينين لهذا الطالب.</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="mb-2">
    <label class="form-label small fw-bold text-muted mb-1">نص الملاحظة أو التنبيه:</label>
    <textarea name="note" class="form-control form-control-sm rounded-3" rows="3" placeholder="اكتب الملاحظة الجديدة التي ترغب بإرسالها للأستاذ هنا..." required></textarea>
</div>
                    </div>

                    <div class="modal-footer border-top pt-2">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-send-custom btn-sm rounded-pill px-4">إرسال الملاحظة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- سجل الدرجات والملاحظات اليومية الشامل -->
    <div class="card card-custom">
        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-journal-text fs-5" style="color: var(--primary-color);"></i> سجل الدرجات والملاحظات اليومية الشامل
            </h5>
        </div>

        <div class="card-body p-0">
            @php
                $recordsList = $child->dailyChanges ?? ($child->dailyVariables ?? []);
            @endphp

            @if(empty($recordsList) || (is_object($recordsList) && $recordsList->isEmpty()))
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
                                <th class="py-3">الدرجة / النتيجة</th>
                                <th class="py-3">الملاحظة</th>
                                <th class="py-3">تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recordsList as $index => $record)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="subject-badge">{{ $record->subject ?? ($record->topic ?? '-') }}</span>
                                    </td>
                                    <td>
                                        @if(($record->score ?? $record->result) !== null)
                                            <span class="score-badge">{{ $record->score ?? $record->result }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary fw-semibold">{{ $record->note ?? ($record->notes ?? ($record->description ?? '-')) }}</td>
                                    <td>
                                        <span class="date-badge">
                                            {{ \Carbon\Carbon::parse($record->recorded_at ?? ($record->report_date ?? $record->created_at))->format('H:i | Y-m-d') }}
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

@push('scripts')
<script>
    function toggleAllTeachers(source) {
        var checkboxes = document.getElementsByClassName('teacher-checkbox');
        for(var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
@endpush

@endsection