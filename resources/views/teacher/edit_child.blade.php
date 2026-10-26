@extends('layouts.app')

@section('title', 'تعديل بيانات الطالب')

@push('styles')
    <style>
        .form-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .form-card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.75rem;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(13, 122, 95, 0.12);
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            font-weight: 700;
            padding: 0.65rem 1.75rem;
            border-radius: 10px;
            border: none;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            opacity: 0.9;
            color: white;
            transform: translateY(-1px);
        }

        .btn-cancel {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.65rem 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- عرض أخطاء الـ Validation إن وجدت -->
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                    <ul class="mb-0 small fw-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="card form-card">
                <!-- Form Header -->
                <div class="form-card-header d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-primary fs-4"></i>
                    <h5 class="fw-bold mb-0 text-dark">
                        تعديل بيانات الطالب: {{ $child->name ?? $child->student_name ?? '' }}
                    </h5>
                </div>

                <!-- Form Body -->
                <div class="card-body p-4">
                    <form action="{{ Route::has('teacher.children.update') ? route('teacher.children.update', $child->id) : url('/teacher/children/' . $child->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- اسم الطالب -->
                        <div class="mb-3.5">
                            <label for="name" class="form-label">اسم الطالب الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="مثال: أحمد محمد" value="{{ old('name', $child->name ?? '') }}" required>
                        </div>

                        <!-- الجنس والعمر -->
                        <div class="row g-3 mb-3.5">
                            <div class="col-md-6">
                                <label for="gender" class="form-label">الجنس <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-select" required>
                                    <option value="male" {{ old('gender', $child->gender ?? '') == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ old('gender', $child->gender ?? '') == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="age" class="form-label">العمر <span class="text-danger">*</span></label>
                                <input type="number" name="age" id="age" class="form-control" placeholder="مثال: 7" min="3" max="18" value="{{ old('age', $child->age ?? '') }}" required>
                            </div>
                        </div>

                        <!-- الصف الدراسي وولي الأمر -->
                        <div class="row g-3 mb-3.5">
                            <div class="col-md-6">
                                <label for="class" class="form-label">الصف الدراسي <span class="text-danger">*</span></label>
                                <select name="class" id="class" class="form-select" required>
                                    <option value="">-- اختر الصف --</option>
                                    @foreach(($school?->classes ?? []) as $cls)
                                        <option value="{{ $cls }}" {{ old('class', $child->class ?? '') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="section" class="form-label">الشعبة <span class="text-danger">*</span></label>
                                <select name="section" id="section" class="form-select" required>
                                    <option value="">-- اختر الشعبة --</option>
                                    @foreach(($school?->sections ?? []) as $sec)
                                        <option value="{{ $sec }}" {{ old('section', $child->section ?? '') == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="parent_id" class="form-label">ولي الأمر <small class="text-muted fw-normal">(اختياري)</small></label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">-- اختر ولي الأمر --</option>
                                    @if(isset($parents))
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id', $child->parent_id ?? '') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- صورة الطالب -->
                        <div class="mb-3.5">
                            <label for="image" class="form-label">تغيير صورة الطالب <small class="text-muted fw-normal">(اختياري)</small></label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>

                        <!-- الحالة الصحية / ملاحظات -->
                        <div class="mb-4">
                            <label for="health_status" class="form-label">الحالة الصحية / ملاحظات خاصة</label>
                            <textarea name="health_status" id="health_status" class="form-control" rows="3" placeholder="سليم، أو يرجى ذكر أي حساسية أو ملاحظات صحية...">{{ old('health_status', $child->health_status ?? '') }}</textarea>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('teacher.classes') }}" class="btn btn-outline-secondary btn-cancel">
                                إلغاء والعودة
                            </a>
                            <button type="submit" class="btn btn-submit">
                                <i class="bi bi-check-circle-fill me-1"></i> حفظ التعديلات
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection