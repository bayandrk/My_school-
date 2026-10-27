@extends('layouts.app')

@section('title', 'لوحة إدارة المدرسة')

@push('styles')
<style>
    .admin-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: #ffffff;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06) !important;
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
</style>
@endpush

@section('content')
    <div class="container pb-5">

        <!-- رأس الصفحة وزر الانتقال لصفحة المهام والطلبات -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold text-dark m-0">
                    <i class="bi bi-speedometer2 text-primary me-2"></i> لوحة إدارة المدرسة
                </h4>
                <p class="text-muted small m-0 mt-1">نظرة عامة ومباشرة على إحصائيات المدرسة والبيانات العامة.</p>
            </div>
            <div>
                <a href="{{ route('manager.tasks') }}" class="btn btn-sm btn-primary fw-bold px-3 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-list-check me-1"></i> الانتقال لصفحة المهام والطلبات
                </a>
            </div>
        </div>

        <!-- ================= 📊 بطاقات الإحصائيات الشاملة للمدرسة ================= -->
        <div class="row g-3 mb-4">
            <!-- عدد الطلاب -->
            <div class="col-xl-3 col-md-6">
                <div class="admin-card p-3 stat-card h-100 border-start border-success border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">الطلاب</span>
                            <h4 class="fw-extrabold text-dark m-0">{{ $studentsCount ?? 0 }}</h4>
                        </div>
                        <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- عدد الأساتذة المعتمدين -->
            <div class="col-xl-3 col-md-6">
                <div class="admin-card p-3 stat-card h-100 border-start border-warning border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">الأساتذة المعتمدون</span>
                            <h4 class="fw-extrabold text-dark m-0">{{ $teachersCount ?? 0 }}</h4>
                        </div>
                        <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- عدد أولياء الأمور -->
            <div class="col-xl-3 col-md-6">
                <div class="admin-card p-3 stat-card h-100 border-start border-info border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">أولياء الأمور</span>
                            <h4 class="fw-extrabold text-dark m-0">{{ $parentsCount ?? 0 }}</h4>
                        </div>
                        <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- عدد المتغيرات اليومية -->
            <div class="col-xl-3 col-md-6">
                <div class="admin-card p-3 stat-card h-100 border-start border-danger border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">المتغيرات اليومية</span>
                            <h4 class="fw-extrabold text-dark m-0">{{ $dailyChangesCount ?? 0 }}</h4>
                        </div>
                        <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= 📈 قسم المخططات البيانية (Charts) ================= -->
        <div class="row g-3">
            <!-- مخطط المقارنة العمودي (Bar Chart) -->
            <div class="col-lg-8">
                <div class="admin-card p-4 h-100">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-line-fill text-primary"></i> مقارنة أحجام بيانات المدرسة
                    </h6>
                    <div style="height: 320px;">
                        <canvas id="managerBarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- مخطط النسبة والتوزيع الدائري (Doughnut Chart) -->
            <div class="col-lg-4">
                <div class="admin-card p-4 h-100">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-success"></i> نسبة عناصر المدرسة
                    </h6>
                    <div style="height: 320px;" class="d-flex align-items-center justify-content-center">
                        <canvas id="managerPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chartData = {
            students: {{ $studentsCount ?? 0 }},
            teachers: {{ $teachersCount ?? 0 }},
            parents: {{ $parentsCount ?? 0 }},
            dailyChanges: {{ $dailyChangesCount ?? 0 }}
        };

        const chartLabels = ['الطلاب', 'الأساتذة', 'أولياء الأمور', 'المتغيرات اليومية'];
        const chartColors = ['#198754', '#ffc107', '#0dcaf0', '#dc3545'];

        // Bar Chart
        const ctxBar = document.getElementById('managerBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'العدد الإجمالي',
                    data: [chartData.students, chartData.teachers, chartData.parents, chartData.dailyChanges],
                    backgroundColor: chartColors,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Cairo' } } },
                    x: { ticks: { font: { family: 'Cairo', weight: 'bold' } } }
                }
            }
        });

        // Doughnut Chart
        const ctxPie = document.getElementById('managerPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: [chartData.students, chartData.teachers, chartData.parents, chartData.dailyChanges],
                    backgroundColor: chartColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Cairo', size: 11 } }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endpush