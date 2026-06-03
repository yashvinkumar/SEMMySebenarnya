@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
    <style>
        .stats-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .stats-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .overdue-badge {
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .recent-assignments {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .assignment-item {
            border-bottom: 1px solid #ecf0f1;
            padding: 15px 0;
        }
        .assignment-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
    </style>
@endpush

@section('title')
    Agency Dashboard - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('agency.dashboard') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('agency.assignments.list') }}" class="nav-link">
        <i class="fas fa-tasks mr-2" aria-hidden="true"></i>
        My Assignments
    </a>
    <a href="{{ route('agency.progress.inquiry-list') }}" class="nav-link">
        <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
        Progress
    </a>
    <a href="{{ route('agency.profile') }}" class="nav-link">
        <i class="fas fa-user mr-2" aria-hidden="true"></i>
        Profile
    </a>
@endsection

@section('user-menu-items')
    <a href="{{ route('agency.profile') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('agency.settings') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Agency Dashboard</h2>
            <p class="text-muted">Overview of your inquiry assignments and performance metrics</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-primary">{{ $stats['total_assignments'] }}</div>
                <div class="stats-label">Total Assignments</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-warning">{{ $stats['pending_assignments'] }}</div>
                <div class="stats-label">Pending</div>
                @if($overdueCount > 0)
                    <div class="mt-2">
                        <span class="overdue-badge">{{ $overdueCount }} Overdue</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-info">{{ $stats['in_progress_assignments'] }}</div>
                <div class="stats-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-success">{{ $stats['completed_assignments'] }}</div>
                <div class="stats-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-secondary">{{ $stats['this_month_assignments'] }}</div>
                <div class="stats-label">This Month</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-success">{{ $stats['completion_rate'] }}%</div>
                <div class="stats-label">Completion Rate</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-info">{{ $stats['avg_response_time'] }}h</div>
                <div class="stats-label">Avg Response Time</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center">
                <div class="stats-number text-danger">{{ $stats['rejected_assignments'] }}</div>
                <div class="stats-label">Rejected</div>
            </div>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="row">
        <div class="col-12">
            <div class="recent-assignments">
                <h4 class="mb-3">Recent Assignments</h4>
                @if($recentAssignments->count() > 0)
                    @foreach($recentAssignments as $assignment)
                        <div class="assignment-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        Assignment #{{ $assignment->assignment_ID }}
                                    </h6>
                                    <p class="mb-0 text-muted small">
                                        @if($assignment->approval && $assignment->approval->inquiry)
                                            {{ Str::limit($assignment->approval->inquiry->inquiry_Title, 50) }}
                                        @else
                                            No title available
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-2">
                                    <span class="status-badge status-{{ $assignment->assignment_Status }}">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->assignment_Status)) }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($assignment->assignment_Date)->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('agency.assignments.details', $assignment->assignment_ID) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('agency.assignments.list') }}" class="btn btn-primary">
                            View All Assignments
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No assignments yet. You will be notified when new assignments are available.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($overdueCount > 0)
        <!-- Overdue Alert -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i>
                        Attention Required
                    </h5>
                    <p>You have {{ $overdueCount }} assignment(s) that are overdue (pending for more than 48 hours).</p>
                    <hr>
                    <p class="mb-0">
                        <a href="{{ route('agency.assignments.list', ['status' => 'pending']) }}" class="btn btn-warning">
                            View Overdue Assignments
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh the page every 5 minutes to keep stats updated
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 minutes
</script>
@endpush
