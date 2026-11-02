@extends('layouts.app')

@section('title', 'ملاحظات الطالب: ' . $child->name)

@php
    $schoolColor = $child->school?->color;
    if (!is_string($schoolColor) || !preg_match('/^#[0-9A-Fa-f]{6}$/', $schoolColor)) {
        $schoolColor = '#1e3a8a';
    }
@endphp

@push('styles')
<style>
    :root {
        --primary-color: {{ $schoolColor }} !important;
    }
    body { background-color: #f8fafc; font-family: 'Cairo', sans-serif; }
    .subject-badge {
        background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
        color: var(--primary-color) !important;
        border: 1px solid color-mix(in srgb, var(--primary-color) 25%, transparent) !important;
        font-weight: 700; padding: 4px 12px; border-radius: 8px; font-size: 0.8rem; display: inline-block;
    }
    .btn-custom-theme {
        background-color: var(--primary-color) !important;
        color: white !important;
        font-weight: 700;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-journal-richtext me-2" style="color: var(--primary-color);"></i> ملاحظات ومواضيع المعلمة للطالب: {{ $child->name }}
        </h4>
        <a href="{{ route('parent.child.show', $child->id) }}" class="btn btn-sm fw-bold rounded-3 text-white shadow-sm" style="background-color: var(--primary-color); border-color: var(--primary-color);">
    <i class="bi bi-arrow-right me-1"></i> العودة لملف الطالب
</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        @if($notes->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 opacity-50 mb-2" style="color: var(--primary-color);"></i>
                <p class="fw-semibold mb-0">لا توجد ملاحظات مسجلة من المعلمة لهذا الطالب حتى الآن.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3">الموضوع / المادة</th>
                            <th class="py-3 text-start ps-4">الملاحظة التفصيلية</th>
                            <th class="py-3">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notes as $index => $n)
                            <tr class="note-row-item {{ $index >= 10 ? 'd-none' : '' }}" data-index="{{ $index + 1 }}">
                                <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                <td><span class="subject-badge">{{ $n->subject ?? 'موضوع عام' }}</span></td>
                                <td class="text-start fw-semibold text-dark ps-4" style="white-space: pre-line;">{{ $n->note }}</td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($n->recorded_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($notes->count() > 10)
                <div class="text-center mt-4" id="loadMoreNotesContainer">
                    <button type="button" class="btn btn-custom-theme px-5 py-2 shadow-sm" onclick="loadMoreNotes()">
                        <i class="bi bi-chevron-down me-1"></i> عرض المزيد (10 بـ 10)
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let visibleNotes = 10;
    function loadMoreNotes() {
        visibleNotes += 10;
        const rows = document.querySelectorAll('.note-row-item');
        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            if (index <= visibleNotes) row.classList.remove('d-none');
        });

        if (visibleNotes >= rows.length) {
            const container = document.getElementById('loadMoreNotesContainer');
            if (container) container.style.display = 'none';
        }
    }
</script>
@endpush