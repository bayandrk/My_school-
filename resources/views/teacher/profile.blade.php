<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي للمعلم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- شريط النافبار -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold">لوحة المعلم</span>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="{{ route('teacher.dashboard') }}">الرئيسية</a>
                <a class="nav-link" href="{{ route('teacher.classes') }}">الصفوف والطلاب</a>
                <a class="nav-link" href="{{ route('teacher.daily-changes') }}">السجلات</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('teacher.profile') }}" class="btn btn-outline-light btn-sm active">الملف الشخصي</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        تعديل الملف الشخصي
                    </div>
                    <div class="card-body">
                        <!-- فورم تحديث البيانات -->
                        <form action="{{ route('teacher.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">الاسم:</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">البريد الإلكتروني:</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->email) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">كلمة المرور الجديدة (اتركها فارغة إن لمغن ترغب بتغييرها):</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تأكيد كلمة المرور:</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                
                                <!-- زر حذف الحساب موجود ضمن صفحة الملف الشخصي -->
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                    حذف حسابي الشخصي
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- نافذة تأكيد الحذف (Modal) لتكون الأزرار مرتبة وفي نفس الصفحة -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">تأكيد حذف الحساب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger fw-bold">هل أنت متأكد من رغبتك في حذف حسابك الشخصي؟</p>
                    <p class="text-muted small">ملاحظة: سيتم حذف حسابك فقط، ولن يتم حذف الطلاب أو سجلاتهم الدراسية.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    
                    <form action="{{ route('teacher.profile.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">نعم، احذف حسابي</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>