@extends('layouts.dashboard')

@section('title', 'Assignment Reports - MySebenarnya')

@section('nav-links')
    <a href="{{ route('mcmc.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
    </a>
    <a href="{{ route('mcmc.inquiries.list') }}" class="nav-link">
        <i class="fas fa-clipboard-list mr-2"></i> Inquiries
    </a>
    <a href="{{ route('mcmc.assignments.list') }}" class="nav-link">
        <i class="fas fa-share-alt mr-2"></i> Assignments
    </a>
    <a href="{{ route('mcmc.assignments.reports') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-chart-bar mr-2"></i> Reports
    </a>
    <a href="{{ route('mcmc.users') }}" class="nav-link">
        <i class="fas fa-users mr-2"></i> Manage Users
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Assignment Reports</h1>
            <p class="text-muted">View and analyze inquiry assignment data with SLA tracking</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('mcmc.assignments.reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('mcmc.assignments.reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter by Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('mcmc.assignments.reports') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                           value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                           value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Apply
                    </button>
                    <a href="{{ route('mcmc.assignments.reports') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Total Assignments</h6>
                    <h2 class="mb-0">{{ $assignmentsByAgency->sum('total') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6 class="card-title">Pending</h6>
                    <h2 class="mb-0">{{ $assignmentsByStatus->where('assignment_Status', 'pending')->first()->total ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="card-title">In Progress</h6>
                    <h2 class="mb-0">{{ $assignmentsByStatus->where('assignment_Status', 'in_progress')->first()->total ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="card-title">Completed</h6>
                    <h2 class="mb-0">{{ $assignmentsByStatus->where('assignment_Status', 'completed')->first()->total ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments by Agency -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-building me-2"></i>Assignments by Agency</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Agency</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignmentsByAgency as $item)
                            <tr>
                                <td>{{ $item->agency->agency_Name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No data for selected period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assignments by Status -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Assignments by Status</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Status</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignmentsByStatus as $item)
                            <tr>
                                <td>
                                    @php
                                        $badgeClass = match($item->assignment_Status) {
                                            'pending' => 'bg-warning',
                                            'in_progress' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'reassigned' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->assignment_Status)) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $item->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No data for selected period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assignment Details Table with SLA -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Assignment Details with SLA Tracking</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Inquiry</th>
                            <th>Agency</th>
                            <th>Assignment Date</th>
                            <th>Due Date</th>
                            <th>SLA Status</th>
                            <th>Status</th>
                            <th>Assigned By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>#{{ $assignment->assignment_ID }}</td>
                                <td>
                                    <strong>{{ Str::limit($assignment->approval->inquiry->inquiry_Title ?? 'N/A', 40) }}</strong>
                                </td>
                                <td>{{ $assignment->agency->agency_Name ?? 'N/A' }}</td>
                                <td>
                                    @if($assignment->assignment_Date)
                                        {{ $assignment->assignment_Date->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ $assignment->assignment_Date->format('H:i') }}</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->due_date)
                                        {{ \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($assignment->due_date)->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $slaStatus = $assignment->sla_status;
                                    @endphp
                                    @if($slaStatus === 'Overdue')
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-success">On Time</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusBadge = match($assignment->assignment_Status) {
                                            'pending' => 'bg-warning',
                                            'in_progress' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'reassigned' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->assignment_Status)) }}
                                    </span>
                                </td>
                                <td>{{ $assignment->assignedByStaff->staff_Name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <br>No assignments found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
}
.table th {
    white-space: nowrap;
}
.badge {
    font-size: 0.8rem;
    padding: 0.35rem 0.65rem;
}
</style>
@endpush