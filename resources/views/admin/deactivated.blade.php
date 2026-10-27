@extends('layouts.app')

@section('title', 'قائمة المدراء غير المفعلين')

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark m-0">
                <i class="bi bi-person-x-fill text-warning me-2"></i> قائمة المدراء غير المفعلين
            </h4>
            <a href="{{ route('admin.tasks') }}" class="btn btn-sm btn-outline-secondary fw-bold rounded-2">
                <i class="bi bi-arrow-right me-1"></i> العودة لصفحة المهام
            </a>
        </div>

        <div class="admin-card card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold fs-6">الحسابات المعطلة</h5>
                <span class="badge bg-light text-dark px-2.5 py-1">{{ $deactivatedManagers->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($deactivatedManagers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                        <p class="mb-0 fw-semibold">لا يوجد مدراء ملغى تفعيلهم حالياً.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center small">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">#</th>
                                    <th class="py-3 text-start ps-4">الاسم</th>
                                    <th class="py-3">اسم المستخدم</th>
                                    <th class="py-3">البريد الإلكتروني</th>
                                    <th class="py-3">الحالة</th>
                                    <th class="py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deactivatedManagers as $index => $manager)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="text-start ps-4 fw-bold text-dark">{{ $manager->name }}</td>
                                        <td class="text-secondary">{{ $manager->username }}</td>
                                        <td class="text-secondary">{{ $manager->email }}</td>
                                        <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 fw-bold">معطل</span></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1.5">
                                                <!-- إعادة التفعيل -->
                                                <form action="{{ route('admin.managers.reactivate', $manager->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm fw-bold px-2.5 py-1" style="font-size: 0.8rem; border-radius: 8px;">
                                                        <i class="bi bi-check-circle me-1"></i> إعادة تفعيل
                                                    </button>
                                                </form>

                                                <!-- الحذف النهائي -->
                                                <form action="{{ route('admin.managers.destroy', $manager->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-2.5 py-1" style="font-size: 0.8rem; border-radius: 8px;" onclick="return confirm('هل أنت متأكد من حذف هذا الحساب نهائياً من قاعدة البيانات؟')">
                                                        <i class="bi bi-trash me-1"></i> حذف نهائي
                                                    </button>
                                                </form>
                                            </div>
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