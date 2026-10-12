<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي للمدير</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- شريط النافبار -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold">لوحة إدارة المدرسة</span>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="{{ route('manager.dashboard') }}">المهام والطلبات</a>
                <a class="nav-link" href="{{ route('manager.classes') }}">دليل الصفوف</a>
                <a class="nav-link" href="{{ route('manager.teachers') }}">دليل الأساتذة</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('manager.profile') }}" class="btn btn-light btn-sm text-primary fw-bold">الملف الشخصي</a>
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

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white fw-bold">
                        تعديل الملف الشخصي (المدير)
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manager.profile') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- الاسم -->
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم الكامل</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $manager->name) }}" required>
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $manager->email) }}" required>
                            </div>

                            <hr class="my-4">
                            <p class="text-muted small">اترك حقول كلمة المرور فارغة إذا كنت لا ترغب في تغييرها.</p>

                            <!-- كلمة المرور الجديدة -->
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>

                            <!-- تأكيد كلمة المرور -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary">إلغاء</a>
                                
                                <div class="d-flex gap-2">
                                    <!-- زر حذف الحساب الشخصي للمدير -->
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteManagerModal">
                                        حذف حسابي الشخصي
                                    </button>

                                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- نافذة تأكيد حذف حساب المدير (Modal) -->
    <div class="modal fade" id="deleteManagerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">تأكيد حذف الحساب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger fw-bold">هل أنت متأكد من رغبتك في حذف حسابك الشخصي كمدير؟</p>
                    <p class="text-muted small">ملاحظة: سيتم حذف حسابك فقط، ولن يتم حذف بيانات المدرسة أو الطلاب أو الأساتذة التابعين لها.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    
                    <form action="{{ route('manager.profile.destroy') }}" method="POST">
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