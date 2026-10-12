<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد</title>
    <!-- رابط Bootstrap للتنسيق السريع -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>إنشاء حساب جديد في النظام المدرسي</h4>
                </div>
                <div class="card-body p-4">

                    <!-- عرض الأخطاء في حال وجودها -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <!-- الاسم الكامل -->
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <!-- اسم المستخدم -->
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم (Username)</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <!-- رقم الهاتف -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>

                        <!-- نوع الحساب (الـ Role) -->
                        <div class="mb-3">
                            <label for="role" class="form-label">نوع الحساب</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="" selected disabled>اختر نوع الحساب...</option>
                                <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>ولي أمر (تفعيل فورياً)</option>
                                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>أستاذ (بانتظار موافقة المدير)</option>
                                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>مدير مدرسة (بانتظار موافقة الأدمن)</option>
                            </select>
                        </div>

                        <!-- اسم المدرسة -->
                        <div class="mb-3">
                            <label for="school_name" class="form-label">اسم المدرسة (اختياري للمدراء والأساتذة)</label>
                            <input type="text" name="school_name" id="school_name" class="form-control" value="{{ old('school_name') }}">
                        </div>

                        <!-- كلمة المرور -->
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">إنشاء الحساب</button>
                    </form>

                </div>
                <div class="card-footer text-center">
                    <small>لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>