@extends('layouts.app')

@section('title', 'الأساتذة غير المفعلين')

@section('content')
<div class="container pb-5">
    
    <!-- التنبيهات -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-person-x-fill text-danger me-2"></i> الأساتذة غير المفعلين</h4>
            <p class="text-muted small mb-0">قائمة الحسابات المعطلة ويمكنك إعادة تفعيلها في أي وقت</p>
        </div>
        <a href="{{ route('manager.teachers') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-bold px-3">
            <i class="bi bi-arrow-right me-1"></i> العودة لدليل الأساتذة
        </a>
    </div>

    <!-- كارت الفلترة والبحث -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('manager.teachers.deactivated') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control form-control-custom" placeholder="ابحث باسم الأستاذ، البريد، أو رقم الهاتف..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول الأساتذة المعطلين -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($teachers->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-person-check d-block fs-1 text-muted mb-2"></i>
                    <h6 class="fw-bold text-secondary">لا يوجد أساتذة غير مفعلين حالياً</h6>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th class="text-start ps-3">الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>رقم الهاتف</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $index => $teacher)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-start ps-3 fw-bold text-dark">{{ $teacher->name }}</td>
                                    <td class="text-secondary">{{ $teacher->email }}</td>
                                    <td class="text-secondary">{{ $teacher->phone ?? '-' }}</td>
                                    <td>
                                        <!-- زر إعادة التفعيل -->
                                        <form action="{{ route('manager.teachers.reactivate', $teacher->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm fw-bold rounded-2 px-3">
                                                <i class="bi bi-person-check-fill me-1"></i> إعادة التفعيل
                                            </button>
                                        </form>
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
@endsection