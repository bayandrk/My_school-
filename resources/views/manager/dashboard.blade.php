@extends('layouts.app')

@section('title', 'لوحة تحكم المدير - المهام والطلبات')

@push('styles')
    <style>
        .dashboard-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .stat-card {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            padding: 1.1rem 1.3rem;
            border: 1px solid #f1f5f9;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        /* أيقونات الإحصائيات تتكيف ديناميكياً مع لون المدرسة */
        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            color: var(--primary-color) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 25%, transparent) !important;
        }

        .card-header-custom {
            background-color: #ffffff;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem;
            font-weight: 700;
        }

        /* زر التأكيد الرئيسي */
        .btn-assign-custom {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            border-radius: 10px;
            padding: 0.65rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-assign-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: var(--btn-text-color) !important;
        }

        /* زر الموافقة الملون ديناميكياً */
        .btn-action-approve {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.825rem !important;
            transition: all 0.2s ease;
            min-width: 85px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-action-approve:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }

        /* زر الرفض */
        .btn-action-reject {
            background-color: #ffffff !important;
            border: 1.5px solid #ef4444 !important;
            color: #ef4444 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.825rem !important;
            transition: all 0.2s ease;
            min-width: 85px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-action-reject:hover {
            background-color: #ef4444 !important;
            color: #ffffff !important;
        }

        .form-select-custom, .form-control-custom {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.55rem 0.85rem;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-select-custom:focus, .form-control-custom:focus {
            background-color: #ffffff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }
    </style>
@endpush

@section('content')

    <!-- التنبيهات -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- كروت الإحصائيات السريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">طلبات الانضمام المعلقة</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $pendingTeachers->count() }}</h4>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold d-block mb-1">الأساتذة المعتمدون</span>
                    <h4 class="fw-bold text-dark mb-0">{{ isset($approvedTeachers) ? $approvedTeachers->count() : 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- 1. طلبات الانضمام المعلقة -->
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center gap-2 text-dark fw-bold">
                        <i class="bi bi-person-plus-fill text-primary"></i> طلبات انضمام الأساتذة المعلقة
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill small fw-bold">
                        {{ $pendingTeachers->count() }}
                    </span>
                </div>
                <div class="card-body p-3">
                    @if($pendingTeachers->isEmpty())
                        <div class="text-center py-4">
                            <div class="stat-icon mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
                                <i class="bi bi-inbox-fill"></i>
                            </div>
                            <h6 class="fw-bold text-secondary mb-1 small">لا توجد طلبات انضمام معلقة حالياً</h6>
                            <p class="text-muted small mb-0">جميع طلبات الأساتذة تم التعامل معها بنجاح.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2.5">#</th>
                                        <th class="py-2.5 text-start ps-3">الاسم</th>
                                        <th class="py-2.5">البريد الإلكتروني</th>
                                        <th class="py-2.5">الهاتف</th>
                                        <th class="py-2.5" style="width: 220px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingTeachers as $index => $teacher)
                                        <tr>
                                            <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                            <td class="text-start ps-3 fw-bold text-dark">{{ $teacher->name }}</td>
                                            <td class="text-secondary">{{ $teacher->email }}</td>
                                            <td class="text-secondary">{{ $teacher->phone ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center align-items-center gap-3">
                                                    <form action="{{ route('manager.teachers.approve', $teacher->id) }}" method="POST" class="m-0">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-action-approve">
                                                            <i class="bi bi-check-lg"></i> موافقة
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('manager.teachers.reject', $teacher->id) }}" method="POST" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-action-reject" onclick="return confirm('هل تريد رفض هذا الطلب؟')">
                                                            <i class="bi bi-x-lg"></i> رفض
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

        <!-- 2. تعيين أستاذ لصف دراسي -->
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header card-header-custom d-flex align-items-center gap-2 text-dark fw-bold">
                    <i class="bi bi-check2-square text-primary"></i> تعيين أستاذ لصف دراسي
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('manager.assign-teacher-class') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary mb-1">اختر الأستاذ:</label>
                                <select name="teacher_id" class="form-select form-select-custom" required>
                                    <option value="">-- اختر الأستاذ --</option>
                                    @foreach($approvedTeachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary mb-1">اختر الصف الدراسي:</label>
                                <select name="class_name" class="form-select form-select-custom" required>
                                    <option value="">-- اختر الصف --</option>
                                    @isset($classesList)
                                        @foreach($classesList as $className)
                                            <option value="{{ $className }}">{{ $className }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-assign-custom w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-person-check-fill"></i> تأكيد تعيين الأستاذ للصف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection