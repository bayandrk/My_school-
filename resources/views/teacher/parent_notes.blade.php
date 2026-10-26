@extends('layouts.app')

@section('title', 'ملاحظات أولياء الأمور')

@section('content')
<div class="container py-4">

    <!-- التنبيهات -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- العنوان وشريط البحث -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-chat-heart-fill me-2" style="color: var(--primary-color);"></i> ملاحظات وتوجيهات أولياء الأمور
            </h4>
            <span class="badge px-3 py-2 rounded-pill fs-6" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);">
                عدد الطلاب: {{ $studentsWithNotes->count() }}
            </span>
        </div>

        <!-- شريط البحث باسم الطالب -->
        <form action="{{ route('teacher.parent-notes') }}" method="GET" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
            <div class="input-group">
                <input type="text" name="search" class="form-control rounded-start-3" placeholder="ابحث باسم الطالب..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary rounded-end-3">
                    <i class="bi bi-search me-1"></i> بحث
                </button>
            </div>
            @if(!empty($search))
                <a href="{{ route('teacher.parent-notes') }}" class="btn btn-light border rounded-3" title="إلغاء البحث">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>

    @if($studentsWithNotes->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-white">
            <i class="bi bi-inbox fs-1 opacity-50 mb-2" style="color: var(--primary-color);"></i>
            <h6 class="fw-bold mb-0">
                {{ !empty($search) ? 'لا توجد نتائج بحث تطابق اسم الطالب المكتوب.' : 'لا توجد ملاحظات مضافة من أولياء الأمور للطلاب حالياً.' }}
            </h6>
        </div>
    @else
        <div class="row g-4">
            @foreach($studentsWithNotes as $childId => $notesGroup)
                @php
                    $student = $notesGroup->first()->child;
                    $latestNoteModel = $notesGroup->first();
                    $latestNote = $latestNoteModel->note;
                    $hasMoreNotes = $notesGroup->count() > 1;
                    $latestReply = $latestNoteModel->replies->sortByDesc('created_at')->first();
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white d-flex flex-column" style="border-right: 5px solid var(--primary-color) !important;">

                        <!-- معلومات الطالب -->
                        <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 text-white" style="width: 48px; height: 48px; background-color: var(--primary-color);">
                                {{ mb_substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $student->name }}</h6>
                                <small class="text-muted">الصف: <span class="fw-semibold" style="color: var(--primary-color);">{{ $student->class ?? '-' }}</span> | ولي الأمر: {{ $student->parent->name ?? '-' }}</small>
                            </div>
                        </div>

                        <!-- 📌 أحدث ملاحظة من الأم (من حقل الملاحظة في جدول التغيرات اليومية) -->
                        <div class="p-3 rounded-3 mb-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-primary">
                                    <i class="bi bi-star-fill text-warning me-1"></i> أحدث ملاحظة من ولي الأمر:
                                </span>
                                <span class="text-muted" style="font-size: 0.7rem;">
                                    <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($latestNoteModel->recorded_at)->format('Y-m-d H:i') }}
                                </span>
                            </div>
                            <p class="fw-bold text-dark mb-2 small" style="white-space: pre-line;">
                                {{ $latestNote }}
                            </p>

                            @if($hasMoreNotes)
                                <button type="button" class="btn btn-sm btn-primary w-100 rounded-3 fw-bold mt-1" data-bs-toggle="modal" data-bs-target="#historyModal{{ $student->id }}">
                                    <i class="bi bi-journal-text me-1"></i> عرض سجل الملاحظات الكامل والردود ({{ $notesGroup->count() }})
                                </button>
                            @endif
                        </div>

                        <!-- رد المعلمة إن وجد (من جدول التغيرات اليومية source = teacher_reply) -->
                        @if($latestReply)
                            <div class="p-3 rounded-3 mb-3 bg-light border-start border-3 border-success">
                                <span class="small fw-bold d-block mb-1 text-success">
                                    <i class="bi bi-reply-fill me-1"></i> ردكِ الحالي:
                                </span>
                                <p class="fw-semibold text-dark mb-0 small" style="white-space: pre-line;">
                                    {{ $latestReply->note }}
                                </p>
                            </div>
                        @endif

                        <!-- أزرار الرد ووقت التحديث -->
                        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-2 px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#replyModal{{ $latestNoteModel->id }}">
                                <i class="bi bi-reply-fill me-1"></i> {{ $latestReply ? 'إضافة رد جديد' : 'الرد على الملاحظة' }}
                            </button>

                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($latestNoteModel->recorded_at)->diffForHumans() }}
                            </span>
                        </div>

                    </div>
                </div>

                <!-- Modal عرض سجل الملاحظات بالكامل (ملاحظات الأم + ردود المعلمة من الأحدث) -->
                <div class="modal fade" id="historyModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <div class="modal-header bg-light border-bottom">
                                <h6 class="modal-title fw-bold text-dark">
                                    <i class="bi bi-clock-history me-1 text-primary"></i> سجل ملاحظات وردود الطالب: {{ $student->name }} (من الأحدث للأقدم)
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                @php
                                    $allRecords = $notesGroup->flatMap(function($note) {
                                        return collect([$note])->merge($note->replies);
                                    })->sortByDesc('recorded_at');
                                @endphp
                                @foreach($allRecords as $index => $record)
                                    @if($record->source === 'parent_note')
                                        <div class="p-3 rounded-3 mb-3 {{ $index === 0 ? 'bg-primary bg-opacity-10 border border-primary' : 'bg-light border' }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge {{ $index === 0 ? 'bg-primary' : 'bg-secondary' }}">
                                                    <i class="bi bi-person-fill me-1"></i> ملاحظة ولي الأمر
                                                </span>
                                                <span class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($record->recorded_at)->format('Y-m-d H:i') }}</span>
                                            </div>
                                            <p class="mb-2 small fw-semibold text-dark" style="white-space: pre-line;">{{ $record->note }}</p>
                                        </div>
                                    @else
                                        <div class="p-3 rounded-3 mb-3 bg-success bg-opacity-10 border border-success border-opacity-25 me-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-success">
                                                    <i class="bi bi-reply-fill me-1"></i> رد المعلمة
                                                </span>
                                                <span class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($record->recorded_at)->format('Y-m-d H:i') }}</span>
                                            </div>
                                            <p class="mb-0 small fw-semibold text-dark" style="white-space: pre-line;">{{ $record->note }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary btn-sm rounded-3 px-4" data-bs-dismiss="modal">إغلاق</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal نموذج الرد على الملاحظة (حقل الملاحظة فقط - يُسجل تلقائياً كرد المعلمة) -->
                <div class="modal fade" id="replyModal{{ $latestNoteModel->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <div class="modal-header text-white" style="background-color: var(--primary-color);">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-reply-fill me-1"></i> الرد على ملاحظة ولي أمر: {{ $student->name }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('teacher.parent-notes.reply', $latestNoteModel->id) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3 p-3 rounded-3 bg-light border">
                                        <label class="form-label text-muted small fw-bold mb-1">الملاحظة الأخيرة الواردة من ولي الأمر:</label>
                                        <p class="mb-0 small fw-bold text-dark" style="white-space: pre-line;">{{ $latestNote }}</p>
                                    </div>

                                    <div class="mb-2">
                                        <label for="note_{{ $latestNoteModel->id }}" class="form-label fw-bold text-dark small">اكتبي ردكِ لولي الأمر:</label>
                                        <textarea name="note" id="note_{{ $latestNoteModel->id }}" rows="4" class="form-control rounded-3" placeholder="أكتبي إجابتك أو توضيحك هنا..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">إرسال الرد</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection