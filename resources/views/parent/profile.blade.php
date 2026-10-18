@extends('layouts.app')

@section('title', 'تعديل الملف الشخصي لولي الأمر')

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

        .btn-back-custom {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            color: var(--primary-color) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.6rem 1.25rem;
            font-size: 0.925rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back-custom:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }
    </style>
@endpush

@section('content')

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- زر العودة للرئيسية أعلى الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-dark mb-0 fs-5">إعدادات الحساب الشخصي</h4>
                <a href="{{ route('parent.dashboard') }}" class="btn-back-custom">
                    <i class="bi bi-arrow-right"></i> العودة للوحة الرئيسية
                </a>
            </div>

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
                    <i class="bi bi-person-heart text-primary"></i>
                    <span>تعديل الملف الشخصي لولي الأمر</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('parent.profile.update') ?? route('parent.profile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- اسم المستخدم (للقراءة فقط) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-custom">اسم المستخدم (المعرّف في النظام)</label>
                                <input type="text" class="form-control form-control-custom bg-light" value="{{ auth()->user()->username ?? auth()->user()->name }}" disabled>
                                <small class="text-muted" style="font-size: 0.75rem;">اسم الحساب المعرف المسجل بالنظام.</small>
                            </div>

                            <!-- الاسم الكامل -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label form-label-custom">الاسم الكامل *</label>
                                <input type="text" name="name" id="name" class="form-control form-control-custom" value="{{ old('name', auth()->user()->name) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label form-label-custom">البريد الإلكتروني *</label>
                                <input type="email" name="email" id="email" class="form-control form-control-custom" value="{{ old('email', auth()->user()->email) }}" required>
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label form-label-custom">رقم الهاتف *</label>
                                <input type="text" name="phone" id="phone" class="form-control form-control-custom" value="{{ old('phone', auth()->user()->phone) }}" placeholder="أدخل رقم الهاتف..." required>
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

                        <div class="d-flex justify-content-end align-items-center mt-4 pt-2">
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

@endsection