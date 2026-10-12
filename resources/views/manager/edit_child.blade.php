<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الطالب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold">لوحة إدارة المدرسة</span>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="{{ route('manager.dashboard') }}">المهام والطلبات</a>
                <a class="nav-link active" href="{{ route('manager.classes') }}">دليل الصفوف</a>
                <a class="nav-link" href="{{ route('manager.teachers') }}">دليل الأساتذة</a>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">تسجيل الخروج</button>
            </form>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-bold py-3">
                تعديل بيانات الطالب: {{ $child->name }}
            </div>
            <div class="card-body p-4">
                <form action="{{ route('manager.children.update', $child) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم الطالب</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $child->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">الجنس</label>
                            <select name="gender" class="form-select" required>
                                <option value="male" {{ $child->gender == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ $child->gender == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">العمر</label>
                            <input type="number" name="age" class="form-control" value="{{ old('age', $child->age) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">الصف</label>
                            <input type="text" name="class" class="form-control" value="{{ old('class', $child->class) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">الحالة الصحية</label>
                            <input type="text" name="health_status" class="form-control" value="{{ old('health_status', $child->health_status) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">ولي الأمر</label>
                            <select name="parent_id" class="form-select">
                                <option value="">غير محدد</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ $child->parent_id == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">صورة الطالب (اختياري)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">حفظ التغييرات</button>
                        <a href="{{ route('manager.classes') }}" class="btn btn-secondary px-4">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>