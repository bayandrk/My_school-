@extends('layouts.app')

@section('title', 'صفوفي وطلابي')

@push('styles')
    <style>
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .class-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.9rem 1.25rem;
        }

        .form-select-sm, .form-control-sm {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
        }

        .form-select-sm:focus, .form-control-sm:focus {
            background-color: #fff;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        /* 1. زر البحث والفلترة */
        .btn-search-school {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 700 !important;
        }

        /* 2. أزرار الهيدر (عرض العلامات، عرض الملاحظات) */
        .btn-school-dynamic {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            padding: 0.45rem 1rem !important;
            font-size: 0.85rem !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-school-dynamic:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }

        /* 3. شارة عدد الطلاب */
        .badge-students-count {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            color: var(--primary-color) !important;
            border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;
            font-weight: 800;
        }

        /* 4. زر السجل الرئيسي (بارز ومحدد بلون المدرسة) */
        .btn-action-record {
            background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff) !important;
            border: 1.5px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.825rem !important;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 85px;
        }

        .btn-action-record:hover {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
        }

        /* 5. زر التعديل (مبتعد وشفاف بحدود رمادية واضحة) */
        .btn-action-edit {
            background-color: #ffffff !important;
            border: 1.5px solid #94a3b8 !important;
            color: #334155 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.825rem !important;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 85px;
        }

        .btn-action-edit:hover {
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
            background-color: color-mix(in srgb, var(--primary-color) 8%, #ffffff) !important;
        }

        /* 6. شارة المعدل العام */
        .badge-average-score {
            background-color: var(--primary-color) !important;
            color: var(--btn-text-color) !important;
            font-weight: 800 !important;
            padding: 0.35rem 0.75rem !important;
        }
    </style>
@endpush

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold rounded-3 shadow-sm mb-3 py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- هيدر الصفوف والفلترة المدمجة -->
    <div class="card card-custom mb-4 p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-journal-bookmark-fill text-primary"></i> الصفوف المتاحة والتابعة لك
            </h5>

            <form method="GET" action="{{ route('teacher.classes') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="ابحث باسم الطالب...">
                    <button class="btn btn-search-school" type="submit"><i class="bi bi-search"></i></button>
                </div>

                <select name="class_name" class="form-select form-select-sm fw-semibold" style="width: 130px;">
                    <option value="">كل الصفوف</option>
                    @foreach($availableClasses as $class)
                        <option value="{{ $class }}" {{ request('class_name') == $class ? 'selected' : '' }}>
                            {{ $class }}
                        </option>
                    @endforeach
                </select>

                <select name="year" id="year" class="form-select form-select-sm fw-semibold" style="width: 100px;">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ (string)$selectedYear === (string)$year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>

                @if(request('search') || request('class_name'))
                    <a href="{{ route('teacher.classes') }}" class="btn btn-outline-secondary btn-sm rounded-2">إلغاء</a>
                @endif
            </form>
        </div>
    </div>

    <!-- عرض الصفوف والطلاب -->
    @forelse($groupedClasses as $className => $students)
        <div class="card card-custom mb-4">
            <div class="class-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark fs-6">
                        <i class="bi bi-building text-primary me-1"></i> {{ $className }}
                    </span>
                    <span class="badge badge-students-count rounded-pill px-2.5 py-1 small">
                        {{ $students->count() }} طالب
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('teacher.class.scores', ['class_name' => $className]) }}" class="btn btn-school-dynamic">
                        <i class="bi bi-award-fill"></i> عرض العلامات
                    </a>

                    <a href="{{ route('teacher.class.notes', ['class_name' => $className]) }}" class="btn btn-school-dynamic">
                        <i class="bi bi-journal-text"></i> عرض الملاحظات
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2.5">#</th>
                                <th class="py-2.5 text-start ps-3">اسم الطالب</th>
                                <th class="py-2.5">العمر</th>
                                <th class="py-2.5">الجنس</th>
                                <th class="py-2.5">الحالة الصحية</th>
                                <th class="py-2.5">المعدل العام</th>
                                <th class="py-2.5">ولي الأمر</th>
                                <th class="py-2.5" style="width: 220px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-start ps-3 fw-bold text-dark">{{ $student->name }}</td>
                                    <td class="text-secondary">{{ $student->age }} سنة</td>
                                    <td>
                                        <span class="badge {{ $student->gender == 'male' ? 'bg-info' : 'bg-danger' }} bg-opacity-10 text-dark fw-semibold">
                                            {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-normal">
                                            {{ $student->health_status ?? 'سليم' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($student->average_score !== null)
                                            <span class="badge badge-average-score rounded-pill">
                                                {{ $student->average_score }}%
                                            </span>
                                        @else
                                            <span class="text-muted small">لا توجد درجات</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary">{{ $student->parent->name ?? 'غير مرتبط' }}</td>
                                    <td>
                                        <!-- تم التحكم بالتباعد بوضوح عبر gap-3 وزيادة حشو العرض -->
                                        <div class="d-flex align-items-center justify-content-center gap-3">
                                            <a href="{{ route('teacher.student.records', $student->id) }}" class="btn btn-action-record">
                                                <i class="bi bi-clock-history"></i> السجل
                                            </a>

                                            @if(Route::has('teacher.children.edit'))
                                                <a href="{{ route('teacher.children.edit', $student->id) }}" class="btn btn-action-edit">
                                                    <i class="bi bi-pencil-square"></i> تعديل
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="card card-custom p-5 text-center my-4">
            <div class="py-3">
                <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-2">لا يوجد طلاب مرتبطون في هذا الصف أو العام الدراسي</h6>
                <a href="{{ Route::has('teacher.children.create') ? route('teacher.children.create') : url('/teacher/children/create') }}" class="btn btn-search-school px-3 py-2 rounded-3 mt-2">
                    + إضافة طفل جديد
                </a>
            </div>
        </div>
    @endforelse

@endsection