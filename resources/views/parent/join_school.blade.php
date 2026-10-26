@extends('layouts.app')

@section('title', 'الانضمام لمدرسة - روضتي')

@push('styles')
<style>
    .school-card {
        background: white;
        border: 1px solid #e8edf3;
        border-radius: 16px;
        padding: 18px;
        height: 100%;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .school-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .school-logo {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        object-fit: contain;
        background: color-mix(in srgb, var(--primary-color, #2563eb) 8%, white);
        border: 1px solid color-mix(in srgb, var(--primary-color, #2563eb) 15%, white);
    }

    .school-logo-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--primary-color, #2563eb) 10%, white);
        color: var(--primary-color, #2563eb);
        font-size: 22px;
    }

    .join-btn {
        background: var(--primary-color, #2563eb);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: filter 0.2s ease;
    }

    .join-btn:hover {
        filter: brightness(0.9);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    <!-- التنبيهات -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- الهيدر مع البحث -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 rounded-3 fs-4" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-color);">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-0">الانضمام لمدرسة</h4>
                <small class="text-muted">ابحثي عن المدرسة وأرسلي طلب انضمام — تُضاف بعد موافقة المدير</small>
            </div>
        </div>

        <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-dark btn-sm fw-bold px-3 py-2 rounded-3">
            <i class="bi bi-arrow-right me-1"></i> العودة للوحة الرئيسية
        </a>
    </div>

    <!-- البحث عن مدرسة -->
    <form action="{{ route('parent.schools.join.page') }}" method="GET" class="d-flex gap-2 mb-4" style="max-width: 480px;">
        <div class="input-group">
            <input type="text" name="search" class="form-control rounded-start-3" placeholder="ابحثي باسم المدرسة..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary rounded-end-3 fw-bold">
                <i class="bi bi-search me-1"></i> بحث
            </button>
        </div>
        @if(!empty($search))
            <a href="{{ route('parent.schools.join.page') }}" class="btn btn-light border rounded-3" title="إلغاء البحث">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </form>

    <!-- مدارس ولي الأمر الحالية (مع زر المغادرة) -->
    <h6 class="fw-bold text-dark mb-3">
        <i class="bi bi-mortarboard-fill me-1" style="color: var(--primary-color);"></i>
        مدارسي الحالية ({{ $mySchools->count() }})
    </h6>

    @if($mySchools->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted bg-white mb-4">
            <p class="mb-0">لم تنضمي لأي مدرسة بعد.</p>
        </div>
    @else
        <div class="row g-3 mb-5">
            @foreach($mySchools as $school)
                <div class="col-md-6 col-lg-4">
                    <div class="school-card border-success border-opacity-25">
                        <div class="d-flex align-items-start gap-3">
                            @if($school->logo)
                                <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="school-logo">
                            @else
                                <div class="school-logo-icon"><i class="bi bi-building"></i></div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-1">{{ $school->name }}</h6>
                                @if(!empty($school->address))
                                    <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i>{{ $school->address }}</small>
                                @endif
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 mt-1">منضمة</span>
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <form action="{{ route('parent.schools.leave') }}" method="POST"
                                  onsubmit="return confirm('هل تريدين مغادرة مدرسة {{ $school->name }}؟');">
                                @csrf
                                <input type="hidden" name="school_id" value="{{ $school->id }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">
                                    <i class="bi bi-box-arrow-right me-1"></i> مغادرة المدرسة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- قائمة المدارس المتاحة -->
    <h6 class="fw-bold text-dark mb-3">
        <i class="bi bi-buildings me-1" style="color: var(--primary-color);"></i>
        {{ !empty($search) ? 'نتائج البحث' : 'المدارس المتاحة' }} ({{ $availableSchools->count() }})
    </h6>

    @if($availableSchools->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-white mb-4">
            <i class="bi bi-buildings fs-1 opacity-50 mb-2" style="color: var(--primary-color);"></i>
            <h6 class="fw-bold mb-0">
                {{ !empty($search) ? 'لا توجد مدارس تطابق البحث.' : 'لا توجد مدارس متاحة للانضمام حالياً.' }}
            </h6>
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach($availableSchools as $school)
                @php
                    $pendingRequest = $myRequests->firstWhere('school_id', $school->id);
                    $isPending = $pendingRequest && $pendingRequest->status === 'pending';
                    $isRejected = $pendingRequest && $pendingRequest->status === 'rejected';
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="school-card">
                        <div class="d-flex align-items-start gap-3">
                            @if($school->logo)
                                <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="school-logo">
                            @else
                                <div class="school-logo-icon"><i class="bi bi-building"></i></div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-1">{{ $school->name }}</h6>
                                @if(!empty($school->address))
                                    <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i>{{ $school->address }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            @if($isPending)
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                    <i class="bi bi-hourglass-split me-1"></i> طلبك معلق — بانتظار موافقة المدير
                                </span>
                            @elseif($isRejected)
                                <form action="{{ route('parent.schools.join') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="school_id" value="{{ $school->id }}">
                                    <button type="submit" class="join-btn">
                                        <i class="bi bi-arrow-repeat me-1"></i> إعادة إرسال الطلب
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('parent.schools.join') }}" method="POST"
                                      onsubmit="return confirm('إرسال طلب انضمام إلى {{ $school->name }}؟ سيُضاف المدرسة بعد موافقة المدير.');">
                                    @csrf
                                    <input type="hidden" name="school_id" value="{{ $school->id }}">
                                    <button type="submit" class="join-btn">
                                        <i class="bi bi-send-plus me-1"></i> طلب انضمام
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- طلباتي السابقة -->
    @if($myRequests->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="p-3 border-bottom d-flex align-items-center gap-2">
                <i class="bi bi-clock-history" style="color: var(--primary-color);"></i>
                <h6 class="fw-bold text-dark mb-0">طلباتي السابقة</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2.5">المدرسة</th>
                            <th class="py-2.5">الحالة</th>
                            <th class="py-2.5">تاريخ الطلب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myRequests as $req)
                            <tr>
                                <td class="fw-bold text-dark">{{ $req->school?->name ?? '-' }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 fw-bold">معلق</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge bg-success fw-bold">مقبول ✓</span>
                                    @else
                                        <span class="badge bg-danger fw-bold">مرفوض</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
