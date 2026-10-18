@extends('layouts.app')

@section('title', 'إضافة طفل جديد')

@push('styles')
    <style>
        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.875rem;
            margin-bottom: 0.35rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(13, 122, 95, 0.12);
        }

        .btn-save-child {
            background-color: var(--primary-color);
            color: white;
            font-weight: 700;
            padding: 0.65rem 2rem;
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-save-child:hover {
            opacity: 0.9;
            color: white;
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')

    <div class="container pb-5" style="max-width: 900px;">

        <!-- رسائل النجاح أو الأخطاء -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 mb-3 py-2 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 fw-bold py-2 shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <span>يرجى التأكد من تصحيح البيانات المدخلة:</span>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- نموذج إضافة طفل جديد -->
        <div class="card main-card">
            <div class="p-3.5 bg-white border-bottom fw-bold d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-primary fs-6">
                    <i class="bi bi-person-plus-fill fs-5"></i>
                    <span>إضافة طفل جديد وتخصيص الصف وولي الأمر</span>
                </div>
                <a href="{{ Route::has('teacher.classes') ? route('teacher.classes') : url('/teacher/classes') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
                    <i class="bi bi-arrow-right me-1"></i> العودة للصفوف
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ Route::has('teacher.children.store') ? route('teacher.children.store') : url('/teacher/children') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <!-- 1. اسم الطفل الكامل -->
                        <div class="col-md-6">
                            <label class="form-label">اسم الطفل الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="أدخل اسم الطفل الثلاثي..." value="{{ old('name') }}" required>
                        </div>

                        <!-- 2. الصف الدراسي (تم تحويله إلى حقل نصي كتابة حرة) -->
                        <div class="col-md-6">
                            <label class="form-label">الصف الدراسي <span class="text-danger">*</span></label>
                            <input type="text" name="class" class="form-control" placeholder="مثال: الصف الأول، أو الأول أ..." value="{{ old('class') }}" required>
                        </div>

                        <!-- 3. العمر -->
                        <div class="col-md-4">
                            <label class="form-label">العمر (بالسنوات) <span class="text-danger">*</span></label>
                            <input type="number" min="3" max="18" name="age" class="form-control" placeholder="مثال: 7" value="{{ old('age') }}" required>
                        </div>

                        <!-- 4. الجنس -->
                        <div class="col-md-4">
                            <label class="form-label">الجنس <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>

                        <!-- 5. اختيار ولي الأمر -->
                        <div class="col-md-4">
                            <label class="form-label">ولي الأمر <span class="text-danger">*</span></label>
                            <select name="parent_id" class="form-select" required>
                                <option value="">-- اختر ولي الأمر --</option>
                                @if(isset($parents))
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- 6. الحالة الصحية -->
                        <div class="col-md-12">
                            <label class="form-label">الحالة الصحية / ملاحظات طبية</label>
                            <input type="text" name="health_status" class="form-control" placeholder="سليم تماماً، أو اذكر أي حساسيات/ملاحظات خاصة..." value="{{ old('health_status', 'سليم تماماً') }}">
                        </div>

                        <!-- 7. صورة شخصية للطفل (اختياري) -->
                        <div class="col-md-12">
                            <label class="form-label">الصورة الشخصية للطفل (اختياري)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-save-child">
                            <i class="bi bi-check-lg me-1"></i> حفظ وتأكيد الإضافة
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection