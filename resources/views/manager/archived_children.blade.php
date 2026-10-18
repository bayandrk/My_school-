@extends('layouts.app')

@section('title', 'أرشيف الطلاب')

@section('content')
<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-box-archive-fill text-danger me-2"></i> أرشيف الطلاب غير المفعلين</h4>
            <p class="text-muted small mb-0">قائمة الطلاب الذين تم نقلهم للأرشيف ويمكنك إعادة تفعيلهم في أي وقت</p>
        </div>
        <a href="{{ route('manager.classes') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-bold px-3">
            <i class="bi bi-arrow-right me-1"></i> العودة لدليل الصفوف
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($children->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox d-block fs-1 text-muted mb-2"></i>
                    <h6 class="fw-bold text-secondary">لا يوجد طلاب في الأرشيف حالياً</h6>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center small">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th class="text-start ps-3">اسم الطالب</th>
                                <th>الصف</th>
                                <th>العمر</th>
                                <th>الحالة الصحية</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($children as $index => $child)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-start ps-3 fw-bold text-dark">{{ $child->name }}</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">{{ $child->class }}</span></td>
                                    <td>{{ $child->age }} سنة</td>
                                    <td>{{ $child->health_status ?? 'سليم' }}</td>
                                    <td>
                                        <form action="{{ route('manager.children.restore', $child->id) }}" method="POST" class="d-inline">
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