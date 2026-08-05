@extends('backend.app')

@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h1 class="page-title fw-bold mb-1">{{ $crud ? ucwords(str_replace('_', ' ', $crud)) : 'Dashboard Analytics' }}</h1>
                        <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Here's what's happening today.</p>
                    </div>
                    <div class="d-flex gap-2">
                     
                        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2" id="refreshDashboard">
                            <i class="fe fe-refresh-cw"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- ROW: SUMMARY CARDS -->
            <div class="row row-deck mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3 mb-xl-0">
                    <div class="card overflow-hidden border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="text-muted fw-medium d-block mb-1">Total Users</span>
                                    <h2 class="mb-0 fw-bold">{{ $totalUsers ?? 0 }}</h2>
                                </div>
                                <div class="bg-primary-transparent p-3 rounded-circle text-primary">
                                    <i class="fe fe-users fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3 mb-xl-0">
                    <div class="card overflow-hidden border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="text-muted fw-medium d-block mb-1">Total Properties</span>
                                    <h2 class="mb-0 fw-bold">{{ $totalProperties ?? 0 }}</h2>
                                </div>
                                <div class="bg-warning-transparent p-3 rounded-circle text-warning">
                                    <i class="fe fe-home fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3 mb-xl-0">
                    <div class="card overflow-hidden border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="text-muted fw-medium d-block mb-1">Pending Approvals</span>
                                    <h2 class="mb-0 fw-bold">{{ $pendingProperties ?? 0 }}</h2>
                                </div>
                                <div class="bg-danger-transparent p-3 rounded-circle text-danger">
                                    <i class="fe fe-clock fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3 mb-xl-0">
                    <div class="card overflow-hidden border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="text-muted fw-medium d-block mb-1">Total Income</span>
                                    <h2 class="mb-0 fw-bold">${{ number_format($totalIncome ?? 0, 2) }}</h2>
                                </div>
                                <div class="bg-success-transparent p-3 rounded-circle text-success">
                                    <i class="fe fe-dollar-sign fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW: CHARTS -->
            <div class="row mb-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center pb-0">
                            <h3 class="card-title mb-0 fw-bold">User Registrations Over Year</h3>
                        </div>
                        <div class="card-body">
                            <div id="user-analytics-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center pb-0">
                            <h3 class="card-title mb-0 fw-bold">Property Status Overview</h3>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="property-status-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- RECENT USERS -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-1 fw-bold">Recent Users</h3>
                                <p class="text-muted mb-0 fs-12">Latest registered users</p>
                            </div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border btn-sm text-muted">
                                View All
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-vcenter text-nowrap mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-top-0 text-muted fw-medium">User</th>
                                            <th class="border-top-0 text-muted fw-medium">Email</th>
                                            <th class="border-top-0 text-muted fw-medium">Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3 bg-primary-transparent text-primary rounded-circle fw-bold">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                                </div>
                                            </td>
                                            <td><div class="text-muted">{{ $user->email }}</div></td>
                                            <td>
                                                <div class="text-muted">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECENT PROPERTIES -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-1 fw-bold">Recent Properties</h3>
                                <p class="text-muted mb-0 fs-12">Latest added properties</p>
                            </div>
                            <a href="{{ route('admin.property.index') }}" class="btn btn-light border btn-sm text-muted">
                                View All
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-vcenter text-nowrap mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-top-0 text-muted fw-medium">Property</th>
                                            <th class="border-top-0 text-muted fw-medium">Price</th>
                                            <th class="border-top-0 text-muted fw-medium">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentProperties as $property)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset($property->thumbnail ?: 'default/logo.svg') }}" class="me-3 rounded" width="40" height="40" alt="img" style="object-fit: cover;">
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ \Illuminate\Support\Str::limit($property->title, 25) }}</div>
                                                        <div class="text-muted fs-11">{{ $property->category ? $property->category->name : 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold">${{ number_format($property->rent_amount, 2) }}</td>
                                            <td>
                                                @if($property->status == 'active')
                                                    <span class="badge bg-success-transparent text-success rounded-pill px-2">Active</span>
                                                @else
                                                    <span class="badge bg-warning-transparent text-warning rounded-pill px-2">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!-- ROW END -->

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection

@push('styles')
<style>
    /* Custom Dashboard Styles */
    .avatar { display: inline-flex; align-items: center; justify-content: center; }
    .avatar-text { font-weight: 600; font-size: 14px; }
    .legend-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #3B82F6; }
    .bg-gradient { background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark) 100%); }
    .shadow-sm { box-shadow: 0 2px 8px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06) !important; }
    .card { border-radius: 12px; overflow: hidden; border: 1px solid rgba(0,0,0,0.04); }
    .btn-list { display: flex; gap: 4px; }
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.36.3/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Refresh button
    document.getElementById('refreshDashboard').addEventListener('click', function() {
        const btn = this;
        btn.innerHTML = '<i class="fe fe-refresh-cw spin"></i> Refreshing...';
        btn.disabled = true;
        setTimeout(() => { window.location.reload(); }, 1500);
    });

    // Spin animation
    const style = document.createElement('style');
    style.textContent = `.spin { animation: spin 1s linear infinite; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }`;
    document.head.appendChild(style);

    // Period buttons
    document.querySelectorAll('[data-period]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });



    // Chart Data Passed from Controller
    const months = {!! json_encode(array_keys($userAnalytics->toArray())) !!};
    const registrations = {!! json_encode(array_values($userAnalytics->toArray())) !!};

    const activePropsCount = {{ $activeProperties ?? 0 }};
    const pendingPropsCount = {{ $pendingProperties ?? 0 }};

    // User Analytics Area Chart
    const userChart = new ApexCharts(document.querySelector("#user-analytics-chart"), {
        series: [{
            name: 'User Registrations',
            data: registrations
        }],
        chart: { 
            height: 320, 
            type: 'area', 
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#f97316'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { 
            categories: months,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#6b7280' } }
        },
        yaxis: { 
            labels: { style: { colors: '#6b7280' } }
        },
        grid: { 
            borderColor: '#f3f4f6', 
            strokeDashArray: 4,
            yaxis: { lines: { show: true } }
        },
        tooltip: { theme: 'light' }
    });
    userChart.render();

    // Property Status Donut Chart
    const propertyChart = new ApexCharts(document.querySelector("#property-status-chart"), {
        series: [activePropsCount, pendingPropsCount],
        chart: { 
            type: 'donut', 
            height: 320,
            fontFamily: 'inherit'
        },
        labels: ['Active Properties', 'Pending Approvals'],
        colors: ['#10b981', '#f59e0b'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: { show: true },
                        value: { show: true, fontSize: '24px', fontWeight: 600 },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total',
                            fontSize: '14px',
                            color: '#6b7280',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => { return a + b }, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: { position: 'bottom', horizontalAlign: 'center' }
    });
    propertyChart.render();
});
</script>
@endpush
