@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.pending {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.in-progress {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stat-card.completed {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .jurisdiction-alert {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-left: 5px solid #ff6b6b;
            animation: glow 2s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { box-shadow: 0 0 5px rgba(255, 107, 107, 0.5); }
            to { box-shadow: 0 0 20px rgba(255, 107, 107, 0.8); }
        }
        .priority-urgent {
            border-left: 4px solid #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }
        .priority-high {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(135deg, #fefbf2 0%, #fef3c7 100%);
        }
        .priority-normal {
            border-left: 4px solid #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        .action-button {
            transition: all 0.3s ease;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .metric-trend {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
        }
        .trend-up {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
        }
        .trend-down {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        .assignment-timeline {
            position: relative;
        }
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            padding-bottom: 1rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            width: 2px;
            height: 100%;
            background: #e5e7eb;
        }
        .timeline-dot {
            position: absolute;
            left: 0.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            z-index: 10;
        }
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
        @if(isset($stats['pending_assignments']) && $stats['pending_assignments'] > 0)
            <span class="badge bg-danger ms-2">{{ $stats['pending_assignments'] }}</span>
        @endif
    </a>
    <a href="{{ route('agency.progress.inquiry-list') }}" class="nav-link">
        <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
        Progress
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-bell mr-2" aria-hidden="true"></i>
        Notifications
        <span class="badge bg-warning ms-2">3</span>
    </a>
    <a href="{{ route('agency.profile') }}" class="nav-link">
        <i class="fas fa-user mr-2" aria-hidden="true"></i>
        Profile
    </a>
@endsection

@section('user-menu-items')
    <a href="{{ route('agency.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('agency.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
<div class="py-8" style="color:#000 !important;">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Welcome Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::guard('agency')->user()->agency_Name }}</h1>
                    <p class="text-gray-600 mt-2">{{ now()->format('l, F j, Y') }} • {{ now()->format('g:i A') }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Last login</div>
                    <div class="font-semibold">{{ Auth::guard('agency')->user()->agency_Updated_At ? Auth::guard('agency')->user()->agency_Updated_At->diffForHumans() : 'First time' }}</div>
                </div>
            </div>
        </div>

        <!-- Urgent Jurisdiction Reviews Alert -->
        @if(isset($stats['pending_assignments']) && $stats['pending_assignments'] > 0)
        <div class="jurisdiction-alert dashboard-card p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-gavel text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $stats['pending_assignments'] }} Jurisdiction Review{{ $stats['pending_assignments'] > 1 ? 's' : '' }} Required</h2>
                        <p class="text-white text-opacity-90 mt-1">
                            You have {{ $stats['pending_assignments'] }} assignment{{ $stats['pending_assignments'] > 1 ? 's' : '' }} awaiting jurisdiction review.
                            @if($overdueCount > 0)
                                <span class="font-semibold">{{ $overdueCount }} overdue (>48 hours)</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('agency.assignments.list', ['status' => 'pending']) }}"
                       class="action-button bg-white text-gray-800 px-6 py-3 rounded-lg font-semibold inline-flex items-center">
                        <i class="fas fa-list mr-2"></i>View All Pending
                    </a>
                    @if($recentAssignments->where('assignment_Status', 'pending')->first())
                        <a href="{{ route('agency.assignments.jurisdiction-review', $recentAssignments->where('assignment_Status', 'pending')->first()->assignment_ID) }}"
                           class="action-button bg-red-600 text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center">
                            <i class="fas fa-gavel mr-2"></i>Review Next
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card stat-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white text-opacity-80 text-sm font-medium">Total Assignments</h3>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['total_assignments'] ?? 0 }}</p>
                        <div class="flex items-center mt-2">
                            <span class="metric-trend trend-up">
                                <i class="fas fa-arrow-up mr-1"></i>+12%
                            </span>
                            <span class="text-white text-opacity-70 text-xs ml-2">vs last month</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-tasks text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card stat-card pending p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white text-opacity-80 text-sm font-medium">Pending Review</h3>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['pending_assignments'] ?? 0 }}</p>
                        <div class="flex items-center mt-2">
                            @if($overdueCount > 0)
                                <span class="metric-trend" style="background-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $overdueCount }} overdue
                                </span>
                            @else
                                <span class="text-white text-opacity-70 text-xs">All current</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card stat-card in-progress p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white text-opacity-80 text-sm font-medium">In Progress</h3>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['in_progress_assignments'] ?? 0 }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-white text-opacity-70 text-xs">Active reviews</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-spinner text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card stat-card completed p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white text-opacity-80 text-sm font-medium">Completed</h3>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['completed_assignments'] ?? 0 }}</p>
                        <div class="flex items-center mt-2">
                            <span class="metric-trend trend-up">
                                <i class="fas fa-arrow-up mr-1"></i>{{ $stats['completion_rate'] ?? 0 }}%
                            </span>
                            <span class="text-white text-opacity-70 text-xs ml-2">completion rate</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Assignments -->
            <div class="lg:col-span-2">
                <div class="dashboard-card bg-white shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Assignments</h2>
                        <a href="{{ route('agency.assignments.list') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @if($recentAssignments && $recentAssignments->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentAssignments->take(5) as $assignment)
                                <div class="border rounded-lg p-4 {{
                                    $assignment->assignment_Status === 'pending' ? 'priority-urgent' :
                                    ($assignment->assignment_Date->diffInHours(now()) > 24 ? 'priority-high' : 'priority-normal')
                                }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 mb-1">
                                                {{ $assignment->approval->inquiry->inquiry_Title }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600 mb-2">
                                                <span><i class="fas fa-calendar mr-1"></i>{{ $assignment->assignment_Date->format('d M Y') }}</span>
                                                <span><i class="fas fa-user mr-1"></i>{{ $assignment->approval->inquiry->user->name ?? 'Anonymous' }}</span>
                                                <span><i class="fas fa-tag mr-1"></i>{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Category)) }}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm">{{ Str::limit($assignment->approval->inquiry->inquiry_Description, 100) }}</p>
                                        </div>
                                        <div class="ml-4 flex flex-col items-end space-y-2">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800'
                                                ];
                                            @endphp
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$assignment->assignment_Status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $assignment->formatted_status }}
                                            </span>
                                            @if($assignment->assignment_Status === 'pending')
                                                <a href="{{ route('agency.assignments.jurisdiction-review', $assignment->assignment_ID) }}"
                                                   class="action-button bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                                    <i class="fas fa-gavel mr-1"></i>Review
                                                </a>
                                            @else
                                                <a href="{{ route('agency.assignments.details', $assignment->assignment_ID) }}"
                                                   class="action-button bg-blue-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No recent assignments</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Performance Metrics -->
                <div class="dashboard-card bg-white shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance This Month</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Assignments Handled</span>
                            <span class="font-semibold">{{ $stats['this_month_assignments'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Completion Rate</span>
                            <span class="font-semibold text-green-600">{{ $stats['completion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Avg Response Time</span>
                            <span class="font-semibold">{{ $stats['avg_response_time'] ?? 0 }}h</span>
                        </div>
                        <div class="pt-2 border-t">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($stats['completion_rate'] ?? 0, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Overall Performance Score</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card bg-white shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($stats['pending_assignments'] > 0)
                            <a href="{{ route('agency.assignments.list', ['status' => 'pending']) }}"
                               class="action-button w-full bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg inline-flex items-center justify-center hover:bg-red-100">
                                <i class="fas fa-gavel mr-2"></i>Review Pending ({{ $stats['pending_assignments'] }})
                            </a>
                        @endif
                        <a href="{{ route('agency.assignments.list') }}"
                           class="action-button w-full bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg inline-flex items-center justify-center hover:bg-blue-100">
                            <i class="fas fa-tasks mr-2"></i>View All Assignments
                        </a>
                        <a href="#"
                           class="action-button w-full bg-gray-50 border border-gray-200 text-gray-700 px-4 py-3 rounded-lg inline-flex items-center justify-center hover:bg-gray-100">
                            <i class="fas fa-bell mr-2"></i>Notifications (3)
                        </a>
                        <a href="{{ route('agency.profile') }}"
                           class="action-button w-full bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg inline-flex items-center justify-center hover:bg-green-100">
                            <i class="fas fa-user mr-2"></i>Update Profile
                        </a>
                    </div>
                </div>

                <!-- Recent Activity Timeline -->
                <div class="dashboard-card bg-white shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="assignment-timeline space-y-3">
                        <div class="timeline-item">
                            <div class="timeline-dot bg-blue-500"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">Assignment received</p>
                                <p class="text-gray-600 text-xs">2 hours ago</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot bg-green-500"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">Review completed</p>
                                <p class="text-gray-600 text-xs">1 day ago</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot bg-yellow-500"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">Assignment accepted</p>
                                <p class="text-gray-600 text-xs">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    // You can implement AJAX refresh here
    console.log('Refreshing dashboard data...');
}, 300000);

// Add click tracking for quick actions
document.querySelectorAll('.action-button').forEach(button => {
    button.addEventListener('click', function() {
        // Track button clicks for analytics
        console.log('Quick action clicked:', this.textContent.trim());
    });
});

// Add hover effects for assignment cards
document.querySelectorAll('.priority-urgent, .priority-high, .priority-normal').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.1)';
    });

    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '';
    });
});
</script>
@endpush
