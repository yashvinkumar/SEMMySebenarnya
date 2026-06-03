@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
    <style>
        .notification-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .notification-unread {
            border-left-color: #3b82f6;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }
        .notification-read {
            border-left-color: #e5e7eb;
            background: #ffffff;
        }
        .notification-priority-high {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }
        .notification-priority-urgent {
            border-left-color: #dc2626;
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            animation: pulse 2s infinite;
        }
        .notification-type-assignment {
            border-top: 3px solid #10b981;
        }
        .notification-type-status {
            border-top: 3px solid #f59e0b;
        }
        .notification-type-rejection {
            border-top: 3px solid #ef4444;
        }
        .notification-type-system {
            border-top: 3px solid #6366f1;
        }
        .filter-tab {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-tab.active {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        .filter-tab:hover:not(.active) {
            background-color: #f3f4f6;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
    </style>
@endpush

@section('title')
    Notifications - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('agency.dashboard') }}" class="nav-link">
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
    <a href="#" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-bell mr-2" aria-hidden="true"></i>
        Notifications
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
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-2">Stay updated with assignment notifications and system messages</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="markAllAsRead()" class="btn btn-outline-primary">
                    <i class="fas fa-eye mr-2"></i>Mark All as Read
                </button>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Auto-refresh:</span>
                    <label class="switch">
                        <input type="checkbox" id="autoRefresh" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-bell text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">24</h3>
                        <p class="text-sm text-gray-600">Total Notifications</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">3</h3>
                        <p class="text-sm text-gray-600">Unread</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">2</h3>
                        <p class="text-sm text-gray-600">Urgent</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-tasks text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">5</h3>
                        <p class="text-sm text-gray-600">Assignment Updates</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-md mb-8">
            <div class="flex border-b">
                <button class="filter-tab px-6 py-4 text-sm font-medium active" data-filter="all">
                    <i class="fas fa-list mr-2"></i>All Notifications
                </button>
                <button class="filter-tab px-6 py-4 text-sm font-medium" data-filter="unread">
                    <i class="fas fa-exclamation-circle mr-2"></i>Unread (3)
                </button>
                <button class="filter-tab px-6 py-4 text-sm font-medium" data-filter="assignments">
                    <i class="fas fa-tasks mr-2"></i>Assignments
                </button>
                <button class="filter-tab px-6 py-4 text-sm font-medium" data-filter="status-updates">
                    <i class="fas fa-info-circle mr-2"></i>Status Updates
                </button>
                <button class="filter-tab px-6 py-4 text-sm font-medium" data-filter="system">
                    <i class="fas fa-cog mr-2"></i>System
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            <!-- Urgent New Assignment -->
            <div class="notification-card notification-unread notification-priority-urgent notification-type-assignment rounded-lg shadow-md p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">New Urgent Assignment</h3>
                                <p class="text-gray-700 mt-1">
                                    You have been assigned a new inquiry: <strong>"Telecommunication Service Disruption Report"</strong>
                                </p>
                                <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar mr-1"></i>2 hours ago</span>
                                    <span><i class="fas fa-user mr-1"></i>MCMC Staff</span>
                                    <span class="text-red-600"><i class="fas fa-fire mr-1"></i>Urgent Priority</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                    URGENT
                                </span>
                                <button class="text-gray-400 hover:text-gray-600" onclick="markAsRead(1)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-gavel mr-2"></i>Review Assignment
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="markAsRead(1)">
                                <i class="fas fa-check mr-2"></i>Mark as Read
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Status Update -->
            <div class="notification-card notification-unread notification-type-status rounded-lg shadow-md p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-info-circle text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Assignment Deadline Reminder</h3>
                                <p class="text-gray-700 mt-1">
                                    Assignment <strong>"Internet Service Quality Issues"</strong> is due for review in 6 hours.
                                </p>
                                <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar mr-1"></i>4 hours ago</span>
                                    <span><i class="fas fa-clock mr-1"></i>Due: Today 6:00 PM</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                    REMINDER
                                </span>
                                <button class="text-gray-400 hover:text-gray-600" onclick="markAsRead(2)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-2"></i>View Assignment
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="markAsRead(2)">
                                <i class="fas fa-check mr-2"></i>Mark as Read
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Accepted Confirmation -->
            <div class="notification-card notification-read notification-type-assignment rounded-lg shadow-md p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Assignment Accepted Confirmation</h3>
                                <p class="text-gray-700 mt-1">
                                    Your acceptance of assignment <strong>"Mobile Network Coverage Issues"</strong> has been confirmed by MCMC.
                                </p>
                                <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar mr-1"></i>1 day ago</span>
                                    <span><i class="fas fa-user mr-1"></i>MCMC Staff</span>
                                    <span class="text-green-600"><i class="fas fa-check mr-1"></i>Accepted</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                    CONFIRMED
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reassignment Notification -->
            <div class="notification-card notification-unread notification-type-rejection rounded-lg shadow-md p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-red-600"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Assignment Reassignment Notice</h3>
                                <p class="text-gray-700 mt-1">
                                    Assignment <strong>"Broadcasting License Violation"</strong> has been reassigned to another agency as per your jurisdiction rejection.
                                </p>
                                <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar mr-1"></i>2 days ago</span>
                                    <span><i class="fas fa-user mr-1"></i>MCMC Staff</span>
                                    <span class="text-blue-600"><i class="fas fa-info-circle mr-1"></i>Reassigned</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                    INFO
                                </span>
                                <button class="text-gray-400 hover:text-gray-600" onclick="markAsRead(3)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <button class="btn btn-outline-secondary btn-sm" onclick="markAsRead(3)">
                                <i class="fas fa-check mr-2"></i>Mark as Read
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Notification -->
            <div class="notification-card notification-read notification-type-system rounded-lg shadow-md p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-cog text-purple-600"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">System Maintenance Notice</h3>
                                <p class="text-gray-700 mt-1">
                                    The system will undergo scheduled maintenance on Sunday, December 15th from 2:00 AM to 6:00 AM.
                                </p>
                                <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar mr-1"></i>3 days ago</span>
                                    <span><i class="fas fa-cog mr-1"></i>System Administrator</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full">
                                    SYSTEM
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More -->
        <div class="text-center mt-8">
            <button class="btn btn-outline-primary" onclick="loadMoreNotifications()">
                <i class="fas fa-plus mr-2"></i>Load More Notifications
            </button>
        </div>
    </div>
</div>

<!-- Custom Switch Styles -->
<style>
.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: #3b82f6;
}

input:checked + .slider:before {
    transform: translateX(20px);
}

.slider.round {
    border-radius: 20px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>
@endsection

@push('scripts')
<script>
// Filter functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active class from all tabs
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        // Add active class to clicked tab
        this.classList.add('active');

        // Filter notifications (implement filtering logic here)
        const filter = this.dataset.filter;
        filterNotifications(filter);
    });
});

function filterNotifications(filter) {
    const notifications = document.querySelectorAll('.notification-card');

    notifications.forEach(notification => {
        switch(filter) {
            case 'all':
                notification.style.display = 'block';
                break;
            case 'unread':
                if (notification.classList.contains('notification-unread')) {
                    notification.style.display = 'block';
                } else {
                    notification.style.display = 'none';
                }
                break;
            case 'assignments':
                if (notification.classList.contains('notification-type-assignment')) {
                    notification.style.display = 'block';
                } else {
                    notification.style.display = 'none';
                }
                break;
            case 'status-updates':
                if (notification.classList.contains('notification-type-status')) {
                    notification.style.display = 'block';
                } else {
                    notification.style.display = 'none';
                }
                break;
            case 'system':
                if (notification.classList.contains('notification-type-system')) {
                    notification.style.display = 'block';
                } else {
                    notification.style.display = 'none';
                }
                break;
        }
    });
}

function markAsRead(notificationId) {
    // Implement mark as read functionality
    console.log('Marking notification as read:', notificationId);

    // Find the notification element
    const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notification) {
        notification.classList.remove('notification-unread');
        notification.classList.add('notification-read');
    }

    // Update unread count
    updateUnreadCount();
}

function markAllAsRead() {
    // Implement mark all as read functionality
    document.querySelectorAll('.notification-unread').forEach(notification => {
        notification.classList.remove('notification-unread');
        notification.classList.add('notification-read');
    });

    updateUnreadCount();

    // Show confirmation
    showToast('All notifications marked as read', 'success');
}

function updateUnreadCount() {
    const unreadCount = document.querySelectorAll('.notification-unread').length;
    // Update UI elements that show unread count
    document.querySelector('[data-filter="unread"]').textContent = `Unread (${unreadCount})`;
}

function loadMoreNotifications() {
    // Implement load more functionality
    console.log('Loading more notifications...');
    showToast('Loading more notifications...', 'info');
}

function showToast(message, type = 'info') {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Auto-refresh functionality
let autoRefreshInterval;

document.getElementById('autoRefresh').addEventListener('change', function() {
    if (this.checked) {
        // Start auto-refresh every 30 seconds
        autoRefreshInterval = setInterval(() => {
            console.log('Auto-refreshing notifications...');
            // Implement refresh logic here
        }, 30000);
    } else {
        // Stop auto-refresh
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    }
});

// Initialize auto-refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('autoRefresh').checked) {
        autoRefreshInterval = setInterval(() => {
            console.log('Auto-refreshing notifications...');
            // Implement refresh logic here
        }, 30000);
    }
});
</script>
@endpush
