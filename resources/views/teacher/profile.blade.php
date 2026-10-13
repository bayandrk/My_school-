@extends('layouts.app')

@section('title', 'تعديل الملف الشخصي للمعلم')

@push('styles')
    <style>
        .profile-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .profile-card-header {
            background-color: #ffffff;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .form-label-custom {
            font-weight: 700;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        .form-control-custom {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.95rem;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .btn-save-custom {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-size: 0.925rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-save-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: var(--btn-text-color) !important;
        }

        .btn-cancel-custom {
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-size: 0.925rem;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card profile-card">
                <div class="profile-card-header d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge-fill text-primary"></i>
                    <span>تعديل الملف الشخصي للمعلم</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('teacher.profile.update') ?? route('teacher.profile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- اسم المستخدم (للقراءة فقط أو العرض) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-custom">اسم المستخدم (المعرّف في النظام)</label>
                                <input type="text" class="form-control form-control-custom bg-light" value="{{ $teacher->username ?? auth()->user()->username ?? auth()->user()->name }}" disabled>
                                <small class="text-muted" style="font-size: 0.75rem;">هذا الاسم يظهر في شريط التنقل العلوي.</small>
                            </div>

                            <!-- الاسم الكامل -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label form-label-custom">الاسم الكامل *</label>
                                <input type="text" name="name" id="name" class="form-control form-control-custom" value="{{ old('name', $teacher->name ?? auth()->user()->name) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label form-label-custom">البريد الإلكتروني *</label>
                                <input type="email" name="email" id="email" class="form-control form-control-custom" value="{{ old('email', $teacher->email ?? auth()->user()->email) }}" required>
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label form-label-custom">رقم الهاتف</label>
                                <input type="text" name="phone" id="phone" class="form-control form-control-custom" value="{{ old('phone', $teacher->phone ?? auth()->user()->phone) }}" placeholder="أدخل رقم الهاتف...">
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">
                        <p class="text-muted small fw-semibold mb-3">
                            <i class="bi bi-info-circle me-1"></i> اترك حقول كلمة المرور فارغة إذا كنت لا ترغب في تغييرها.
                        </p>

                        <div class="row">
                            <!-- كلمة المرور الجديدة -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label form-label-custom">كلمة المرور الجديدة</label>
                                <input type="password" name="password" id="password" class="form-control form-control-custom" placeholder="********">
                            </div>

                            <!-- تأكيد كلمة المرور -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label form-label-custom">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-custom" placeholder="********">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                            <!-- زر حذف الحساب الشخصي -->
                            <button type="button" class="btn btn-outline-danger btn-cancel-custom fw-bold" data-bs-toggle="modal" data-bs-target="#deleteTeacherModal">
                                <i class="bi bi-trash me-1"></i> حذف حسابي الشخصي
                            </button>

                            <!-- زر الحفظ -->
                            <button type="submit" class="btn btn-save-custom">
                                <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- نافذة تأكيد حذف حساب المعلم (Modal) -->
<div class="modal fade" id="deleteTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 p-2">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-dark">تأكيد حذف الحساب</h6>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger fw-bold mb-1">هل أنت متأكد من رغبتك في حذف حسابك الشخصي كمعلم؟</p>
                <p class="text-muted small mb-0">ملاحظة: سيتم حذف حسابك نهائياً من النظام.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm px-3 rounded-2" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('teacher.profile.destroy') ?? '#' }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-3 rounded-2 fw-bold">نعم، احذف حسابي</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection