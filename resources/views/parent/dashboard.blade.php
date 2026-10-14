@extends('layouts.app')

@section('title', 'لوحة الأم - روضتي')

@push('styles')
<style>
    .welcome-section {
        margin-bottom: 35px;
    }

    .welcome-section h2 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .welcome-section p {
        color: #6b7280;
        margin: 0;
    }

    .children-title {
        font-size: 21px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
    }

    .children-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .child-card {
        position: relative;
        background: white;
        border: 1px solid #e8edf3;
        border-radius: 20px;
        padding: 24px;
        height: 100%;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.055);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .child-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto;
        height: 5px;
        background: var(--school-color, #2563eb);
    }

    .child-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.09);
    }

    .school-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 18px;
        margin-bottom: 20px;
        border-bottom: 1px solid #f0f2f5;
    }

    .child-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .child-info {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 0;
    }

    .child-avatar {
        width: 76px;
        height: 76px;
        flex: 0 0 76px;
        border: 3px solid color-mix(in srgb, var(--school-color, #2563eb) 18%, white);
        border-radius: 22px;
        background: color-mix(in srgb, var(--school-color, #2563eb) 10%, white);
        color: var(--school-color, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        overflow: hidden;
    }

    .child-name {
        font-size: 21px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
    }

    .child-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        color: #6b7280;
        font-size: 12px;
    }

    .child-meta span {
        padding: 7px 10px;
        border: 1px solid #edf0f4;
        border-radius: 9px;
        background: #fafbfc;
    }

    .child-meta i {
        color: var(--school-color, #2563eb);
        margin-left: 4px;
    }

    .school-brand {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
        color: var(--school-color, #2563eb);
        font-size: 13px;
        font-weight: 700;
    }

    .school-brand img,
    .school-brand-icon {
        width: 36px;
        height: 36px;
        border: 1px solid color-mix(in srgb, var(--school-color, #2563eb) 16%, white);
        border-radius: 10px;
        object-fit: contain;
        background: #fff;
    }

    .school-brand-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--school-color, #2563eb) 10%, white);
        font-size: 17px;
    }

    .school-caption {
        color: #94a3b8;
        font-size: 11px;
        font-weight: 500;
    }

    .details-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        flex: 0 0 auto;
        background: var(--school-color, #2563eb);
        color: white;
        border: none;
        border-radius: 11px;
        padding: 11px 17px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 4px 10px color-mix(in srgb, var(--school-color, #2563eb) 22%, transparent);
        transition: filter 0.2s ease, transform 0.2s ease;
    }

    .details-btn:hover {
        filter: brightness(0.9);
        color: white;
        transform: translateY(-1px);
    }

    .latest-report {
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #f0f2f5;
    }

    .latest-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        font-size: 14px;
        color: #374151;
        margin-bottom: 10px;
    }

    .latest-title i {
        color: var(--school-color, #2563eb);
    }

    .report-box {
        background: color-mix(in srgb, var(--school-color, #2563eb) 5%, white);
        border: 1px solid color-mix(in srgb, var(--school-color, #2563eb) 12%, white);
        border-radius: 13px;
        padding: 16px;
    }

    .report-topic {
        font-weight: 600;
        color: #1f2937;
    }

    .report-date {
        color: #6b7280;
        font-size: 12px;
        margin-top: 5px;
    }

    .result-badge {
        white-space: nowrap;
        background: color-mix(in srgb, var(--school-color, #2563eb) 10%, white);
        color: var(--school-color, #2563eb);
        border-radius: 9px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .children-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }
        .child-header {
            flex-direction: column;
            align-items: stretch;
        }
        .details-btn {
            display: block;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')

<div class="welcome-section">
    <h2>أهلاً بكِ، {{ auth()->user()->name }}</h2>
    <p>يمكنكِ متابعة أطفالكِ والاطلاع على تقاريرهم اليومية.</p>
</div>

<h3 class="children-title">
    <i class="bi bi-people"></i> أطفالي
</h3>

<div class="children-grid">
    @forelse($children as $child)
        @php
            $schoolColor = $child->manager?->primary_color;
            if (!is_string($schoolColor) || !preg_match('/^#[0-9A-Fa-f]{6}$/', $schoolColor)) {
                $schoolColor = '#2563eb';
            }
        @endphp
        <div class="child-card" style="--school-color: {{ $schoolColor }};">
            
            <div class="school-card-header">
                <div class="school-brand mb-0">
                    @if($child->manager?->logo)
                        <img src="{{ asset('storage/' . $child->manager->logo) }}" alt="شعار المدرسة">
                    @else
                        <span class="school-brand-icon"><i class="bi bi-mortarboard"></i></span>
                    @endif
                    <span>{{ $child->manager?->school_name ?? ($child->school_name ?? 'المدرسة غير محددة') }}</span>
                </div>
                <span class="school-caption">بيانات المدرسة</span>
            </div>

            <div class="child-header">
                <div class="child-info">
                    <div class="child-avatar">
                        @if(!empty($child->image))
                            <img src="{{ asset('storage/' . $child->image) }}" alt="{{ $child->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;">
                        @else
                            <i class="bi bi-person"></i>
                        @endif
                    </div>
                    <div>
                        <div class="child-name">{{ $child->name }}</div>
                        <div class="child-meta">
                            <span>
                                <i class="bi bi-calendar3"></i> العمر: {{ $child->age ?? 'غير محدد' }}
                            </span>
                            <span>
                                <i class="bi bi-mortarboard"></i> الصف: {{ $child->class ?? 'غير محدد' }}
                            </span>
                            <span>
                                <i class="bi bi-heart"></i> الحالة الصحية: {{ $child->health_status ?? ($child->Health_status ?? 'غير محددة') }}
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('parent.child.show', $child->id) }}" class="details-btn">
                    <i class="bi bi-eye"></i> عرض التفاصيل <i class="bi bi-arrow-left-short"></i>
                </a>
            </div>

            @php
                $latestReport = $child->dailyChanges?->first() ?? ($child->dailyVariables?->first() ?? null);
            @endphp

            @if($latestReport)
                <div class="latest-report">
                    <div class="latest-title">
                        <i class="bi bi-journal-text"></i> آخر تقرير يومي
                    </div>
                    <div class="report-box">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="report-topic">
                                    {{ $latestReport->topic ?? ($latestReport->subject ?? 'نشاط يومي') }}
                                </div>
                                <div class="report-date">
                                    <i class="bi bi-calendar3"></i>
                                    {{ \Carbon\Carbon::parse($latestReport->report_date ?? $latestReport->created_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            <span class="result-badge">
                                النتيجة: {{ $latestReport->result ?? ($latestReport->score ?? 'متابعة') }}
                            </span>
                        </div>
                        @if(!empty($latestReport->notes) || !empty($latestReport->note))
                            <div class="mt-3 text-muted small">
                                <strong>الملاحظات:</strong> {{ $latestReport->notes ?? $latestReport->note }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="latest-report text-muted small py-2">
                    <i class="bi bi-info-circle me-1"></i> لم يتم إضافة تقرير لهذا الطفل اليوم.
                </div>
            @endif

        </div>
    @empty
        <div class="empty-box col-12 text-center py-5">
            <i class="bi bi-people fs-1 d-block mb-3"></i>
            <h5 class="fw-bold text-dark mb-1">لا يوجد أطفال مسجلين</h5>
            <p class="text-muted mb-0">لم يتم ربط أي أطفال بحسابك حالياً. يرجى التواصل مع إدارة المدرسة.</p>
        </div>
    @endforelse
</div>

@endsection