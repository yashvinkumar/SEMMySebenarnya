@extends('layouts.mcmc')

@section('title', 'Inquiry Assignment Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Inquiry Assignment Reports</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('mcmc.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Report Filters</h5>
                    <form id="reportFilters" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_from">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from"
                                           value="{{ $filters['date_from'] }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_to">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to"
                                           value="{{ $filters['date_to'] }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="agency_id">Agency</label>
                                    <select class="form-control" id="agency_id" name="agency_id">
                                        <option value="">All Agencies</option>
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency->agency_ID }}"
                                                {{ $filters['agency_id'] == $agency->agency_ID ? 'selected' : '' }}>
                                                {{ $agency->agency_Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $filters['status'] == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $filters['status'] == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="rejected" {{ $filters['status'] == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <select class="form-control" id="year" name="year">
                                        @for($i = 2020; $i <= date('Y') + 1; $i++)
                                            <option value="{{ $i }}" {{ $filters['year'] == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <select class="form-control" id="month" name="month">
                                        <option value="">All Months</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $filters['month'] == $i ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="group_by">Group By</label>
                                    <select class="form-control" id="group_by" name="group_by">
                                        <option value="agency" {{ $filters['group_by'] == 'agency' ? 'selected' : '' }}>Agency</option>
                                        <option value="month" {{ $filters['group_by'] == 'month' ? 'selected' : '' }}>Month</option>
                                        <option value="status" {{ $filters['group_by'] == 'status' ? 'selected' : '' }}>Status</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-block">
                                        <button type="submit" class="btn btn-primary mr-2">
                                            <i class="fas fa-search"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Assignments</h6>
                            <h3 class="mb-0">{{ $dashboardData['total_assignments'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Pending</h6>
                            <h3 class="mb-0 text-warning">{{ $dashboardData['pending_assignments'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">In Progress</h6>
                            <h3 class="mb-0 text-info">{{ $dashboardData['in_progress_assignments'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-spinner fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Completed</h6>
                            <h3 class="mb-0 text-success">{{ $dashboardData['completed_assignments'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Rejected</h6>
                            <h3 class="mb-0 text-danger">{{ $dashboardData['rejected_assignments'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="btn-group-vertical" role="group">
                            <a href="{{ route('mcmc.reports.export.excel', request()->query()) }}"
                               class="btn btn-success btn-sm mb-1">
                                <i class="fas fa-file-excel"></i> Excel
                            </a>
                            <a href="{{ route('mcmc.reports.export.pdf', request()->query()) }}"
                               class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Monthly Trend</h5>
                    <canvas id="monthlyTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Agency Distribution</h5>
                    <canvas id="agencyDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Status Distribution</h5>
                    <canvas id="statusDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Category Distribution</h5>
                    <canvas id="categoryDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Agencies Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top 5 Agencies by Assignment Count</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Agency Name</th>
                                    <th>Total Assignments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dashboardData['top_agencies'] as $index => $agency)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $agency->agency_Name }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $agency->total_assignments }}</span>
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

    <!-- Detailed Report Data -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detailed Report Data</h5>
                    <div id="reportData">
                        <!-- Report data will be loaded here via AJAX -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Loading report data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize charts
    initializeCharts();

    // Load report data
    loadReportData();

    // Auto-refresh on filter change
    $('#reportFilters').on('submit', function(e) {
        e.preventDefault();
        location.href = '{{ route('mcmc.reports.index') }}?' + $(this).serialize();
    });
});

function resetFilters() {
    location.href = '{{ route('mcmc.reports.index') }}';
}

function initializeCharts() {
    // Monthly Trend Chart
    const monthlyData = @json($chartData['monthly_trend']);
    if (monthlyData.length > 0) {
        const ctx1 = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Total Assignments',
                    data: monthlyData.map(item => item.total_assignments),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Completed',
                    data: monthlyData.map(item => item.completed),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Agency Distribution Chart
    const agencyData = @json($chartData['agency_distribution']);
    if (agencyData.length > 0) {
        const ctx2 = document.getElementById('agencyDistributionChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: agencyData.slice(0, 10).map(item => item.agency_Name.substring(0, 20) + '...'),
                datasets: [{
                    label: 'Total Assignments',
                    data: agencyData.slice(0, 10).map(item => item.total_assignments),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(255, 99, 255, 0.8)',
                        'rgba(99, 255, 132, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Status Distribution Chart
    const statusData = @json($chartData['status_distribution']);
    if (statusData.length > 0) {
        const ctx3 = document.getElementById('statusDistributionChart').getContext('2d');
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.assignment_Status.charAt(0).toUpperCase() + item.assignment_Status.slice(1).replace('_', ' ')),
                datasets: [{
                    data: statusData.map(item => item.total_assignments),
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',   // Warning for pending
                        'rgba(23, 162, 184, 0.8)',  // Info for in_progress
                        'rgba(40, 167, 69, 0.8)',   // Success for completed
                        'rgba(220, 53, 69, 0.8)',   // Danger for rejected
                        'rgba(255, 152, 0, 0.8)',   // Orange for under_review
                        'rgba(0, 150, 136, 0.8)'    // Teal for assign_to_agency
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Category Distribution Chart
    const categoryData = @json($chartData['category_distribution']);
    if (categoryData.length > 0) {
        const ctx4 = document.getElementById('categoryDistributionChart').getContext('2d');
        new Chart(ctx4, {
            type: 'pie',
            data: {
                labels: categoryData.map(item => item.inquiry_Category.charAt(0).toUpperCase() + item.inquiry_Category.slice(1)),
                datasets: [{
                    data: categoryData.map(item => item.total_assignments),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}

function loadReportData() {
    const params = new URLSearchParams(window.location.search);

    $.ajax({
        url: '{{ route('mcmc.reports.data') }}',
        method: 'GET',
        data: params.toString(),
        success: function(data) {
            renderReportData(data);
        },
        error: function() {
            $('#reportData').html('<div class="alert alert-danger">Error loading report data.</div>');
        }
    });
}

function renderReportData(data) {
    let html = '';

    $.each(data, function(groupKey, assignments) {
        html += `
            <div class="mb-4">
                <h6 class="text-primary font-weight-bold">${groupKey.toUpperCase()}</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Assignment ID</th>
                                <th>Inquiry Title</th>
                                <th>User</th>
                                <th>Agency</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Assignment Date</th>
                                <th>Assigned By</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        $.each(assignments, function(index, assignment) {
            const statusClass = {
                'pending': 'warning',
                'in_progress': 'info',
                'completed': 'success',
                'rejected': 'danger'
            }[assignment.assignment_Status] || 'secondary';

            html += `
                <tr>
                    <td>${assignment.assignment_ID}</td>
                    <td>${assignment.inquiry_Title}</td>
                    <td>${assignment.user_name}<br><small class="text-muted">${assignment.user_email}</small></td>
                    <td>${assignment.agency_Name}<br><small class="text-muted">${assignment.agency_Type}</small></td>
                    <td>${assignment.inquiry_Category}</td>
                    <td><span class="badge badge-${statusClass}">${assignment.assignment_Status.replace('_', ' ')}</span></td>
                    <td>${new Date(assignment.assignment_Date).toLocaleDateString()}</td>
                    <td>${assignment.assigned_by_name || 'N/A'}</td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    });

    $('#reportData').html(html);
}
</script>
@endpush
@endsection
