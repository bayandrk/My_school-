@extends('layouts.app')

@section('title', 'تعديل بيانات الطالب - ' . $child->name)

@push('styles')
    <style>
        .edit-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: #ffffff;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.1rem 1.3rem;
            font-weight: 700;
        }

        .form-label-custom {
            font-weight: 700;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        .form-control-custom, .form-select-custom {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.6rem 0.9rem;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background-color: #ffffff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        /* زر الحفظ الملون ديناميكياً حسب لون المدرسة */
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

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card edit-card">
                <div class="card-header card-header-custom d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-primary fs-5"></i>
                    <span>تعديل بيانات الطالب: <strong class="text-primary">{{ $child->name }}</strong></span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('manager.children.update', $child) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3.5">
                            <div class="col-md-6">
                                <label class="form-label form-label-custom">اسم الطالب</label>
                                <input type="text" name="name" class="form-control form-control-custom" value="{{ old('name', $child->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label form-label-custom">الجنس</label>
                                <select name="gender" class="form-select form-select-custom" required>
                                    <option value="male" {{ $child->gender == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ $child->gender == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label form-label-custom">العمر (بالسنوات)</label>
                                <input type="number" name="age" class="form-control form-control-custom" value="{{ old('age', $child->age) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label form-label-custom">الصف الدراسي</label>
                                <input type="text" name="class" class="form-control form-control-custom" value="{{ old('class', $child->class) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label form-label-custom">الحالة الصحية</label>
                                <input type="text" name="health_status" class="form-control form-control-custom" value="{{ old('health_status', $child->health_status) }}" placeholder="مثال: سليم، حك للعيون...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label form-label-custom">ولي الأمر</label>
                                <select name="parent_id" class="form-select form-select-custom">
                                    <option value="">-- غير محدد --</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ $child->parent_id == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label form-label-custom">صورة الطالب (اختياري)</label>
                                <input type="file" name="image" class="form-control form-control-custom">
                            </div>
                        </div>

                        <div class="mt-4 pt-2 d-flex align-items-center justify-content-end gap-2">
                            <a href="{{ route('manager.classes') }}" class="btn btn-light btn-cancel-custom border">إلغاء</a>
                            <button type="submit" class="btn btn-save-custom d-inline-flex align-items-center gap-1.5">
                                <i class="bi bi-check-lg"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection