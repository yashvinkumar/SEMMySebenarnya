@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card stats-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4 class="mb-0">{{ __('Inquiry Reports & Statistics') }}</h4>
                            <div class="ms-auto">
                                <a href="{{ route('mcmc.inquiries.list') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Date Range Filter -->
                        <form method="GET" class="mb-4">
                            <div class="d-flex align-items-end flex-wrap gap-2" style="gap: 12px;">
                                <div class="d-flex flex-column">
                                    <label for="year" class="form-label mb-1"
                                        style="color: #000; white-space:nowrap;">{{ __('Year') }}</label>
                                    <select id="year" name="year" class="form-control"
                                        style="width: 120px; color: #000 !important;">
                                        <option value="">{{ __('All Years') }}</option>
                                        @for ($i = date('Y'); $i >= 2020; $i--)
                                            <option value="{{ $i }}"
                                                {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="d-flex flex-column">
                                    <label for="month" class="form-label mb-1"
                                        style="color: #000; white-space:nowrap;">{{ __('Month') }}</label>
                                    <select id="month" name="month" class="form-control"
                                        style="width: 140px; color: #000 !important;">
                                        <option value="">{{ __('All Months') }}</option>
                                        <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>
                                            {{ __('January') }}</option>
                                        <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>
                                            {{ __('February') }}</option>
                                        <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>
                                            {{ __('March') }}</option>
                                        <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>
                                            {{ __('April') }}</option>
                                        <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>
                                            {{ __('May') }}</option>
                                        <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>
                                            {{ __('June') }}</option>
                                        <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>
                                            {{ __('July') }}</option>
                                        <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>
                                            {{ __('August') }}</option>
                                        <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>
                                            {{ __('September') }}</option>
                                        <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>
                                            {{ __('October') }}</option>
                                        <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>
                                            {{ __('November') }}</option>
                                        <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>
                                            {{ __('December') }}</option>
                                    </select>
                                </div>
                                <div class="d-flex flex-column">
                                    <label for="start_date" class="form-label mb-1"
                                        style="color: #000; white-space:nowrap;">{{ __('Start Date') }}</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control"
                                        value="{{ $startDate }}" style="width: 160px; color: #000 !important;">
                                </div>
                                <div class="d-flex flex-column">
                                    <label for="end_date" class="form-label mb-1"
                                        style="color: #000; white-space:nowrap;">{{ __('End Date') }}</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                        value="{{ $endDate }}" style="width: 160px; color: #000 !important;">
                                </div>
                                <button type="submit" class="btn btn-primary" style="height: 38px;">
                                    <i class="fas fa-filter"></i> {{ __('Apply Filter') }}
                                </button>
                                <a href="{{ route('mcmc.inquiries.reports') }}" class="btn btn-secondary"
                                    style="background-color: #6c757d; border-color: #6c757d; color: #fff; height: 38px;">
                                    {{ __('Reset') }}
                                </a>
                                <button type="button" class="btn btn-info" style="height: 38px;" onclick="window.print()">
                                    <i class="fas fa-print"></i> {{ __('Print') }}
                                </button>
                                <a href="{{ route('mcmc.inquiries.reports.pdf', request()->query()) }}"
                                    class="btn btn-danger" style="height: 38px;" target="_blank">
                                    <i class="fas fa-file-pdf"></i> {{ __('Export PDF') }}
                                </a>
                                <a href="{{ route('mcmc.inquiries.reports.excel', request()->query()) }}"
                                    class="btn btn-success" style="height: 38px;">
                                    <i class="fas fa-file-excel"></i> {{ __('Export Excel') }}
                                </a>
                            </div>
                        </form>

                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-8">
                            <div
                                class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100 text-sm font-medium">{{ __('Total Inquiries') }}</p>
                                        <p class="text-3xl font-bold">{{ $totalInquiries }}</p>
                                        <p class="text-blue-100 text-xs mt-1">{{ __('All submissions') }}</p>
                                    </div>
                                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                        <i class="fas fa-clipboard-list text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="stats-card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl p-6 shadow-lg"
                                style="background: linear-gradient(to right, #eab308, #ca8a04) !important;">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-yellow-100 text-sm font-medium">{{ __('Pending') }}</p>
                                        <p class="text-3xl font-bold">{{ $pendingInquiries }}</p>
                                        <p class="text-yellow-100 text-xs mt-1">{{ __('Under review') }}</p>
                                    </div>
                                    <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                                        <i class="fas fa-clock text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100 text-sm font-medium">{{ __('In Progress') }}</p>
                                        <p class="text-3xl font-bold">{{ $inProgressInquiries }}</p>
                                        <p class="text-blue-100 text-xs mt-1">{{ __('Being processed') }}</p>
                                    </div>
                                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                        <i class="fas fa-spinner text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="stats-card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100 text-sm font-medium">{{ __('Completed') }}</p>
                                        <p class="text-3xl font-bold">{{ $completedInquiries }}</p>
                                        <p class="text-green-100 text-xs mt-1">{{ __('Finished') }}</p>
                                    </div>
                                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                                        <i class="fas fa-check-circle text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="stats-card bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-red-100 text-sm font-medium">{{ __('Rejected') }}</p>
                                        <p class="text-3xl font-bold">{{ $rejectedInquiries }}</p>
                                        <p class="text-red-100 text-xs mt-1">{{ __('Need revision') }}</p>
                                    </div>
                                    <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                                        <i class="fas fa-times-circle text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>



                            <div class="stats-card bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg"
                                style="background: linear-gradient(to right, #9333ea, #7c3aed) !important;">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-100 text-sm font-medium">{{ __('Under Review') }}</p>
                                        <p class="text-3xl font-bold">{{ $underReviewInquiries }}</p>
                                        <p class="text-purple-100 text-xs mt-1">{{ __('Under evaluation') }}</p>
                                    </div>
                                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                                        <i class="fas fa-search text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="stats-card bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl p-6 shadow-lg"
                                style="background: linear-gradient(to right, #f59e0b, #d97706) !important;">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-amber-100 text-sm font-medium">{{ __('Assign to Agency') }}</p>
                                        <p class="text-3xl font-bold">{{ $assignToAgencyInquiries }}</p>
                                        <p class="text-amber-100 text-xs mt-1">{{ __('Forwarded to agency') }}</p>
                                    </div>
                                    <div class="bg-amber-400 bg-opacity-30 rounded-full p-3">
                                        <i class="fas fa-user-tie text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Distribution Pie Chart -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                                <h5 class="text-white font-semibold mb-0">{{ __('Status Distribution') }}</h5>
                            </div>
                            <div class="p-6">
                                <div class="flex flex-col lg:flex-row items-center gap-8">
                                    <div class="w-full lg:w-1/2 flex justify-center">
                                        <div style="width: 300px; height: 300px;">
                                            <canvas id="statusPieChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="w-full lg:w-1/2">
                                        <div class="space-y-3">
                                            <div
                                                class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 bg-yellow-400 rounded-full mr-3"></div>
                                                    <span class="font-medium text-gray-700">{{ __('Pending') }}</span>
                                                </div>
                                                <span
                                                    class="text-lg font-bold text-yellow-600">{{ $pendingInquiries }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between p-3 bg-green-50 rounded-lg border-l-4 border-green-400">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 bg-green-400 rounded-full mr-3"></div>
                                                    <span class="font-medium text-gray-700">{{ __('Completed') }}</span>
                                                </div>
                                                <span
                                                    class="text-lg font-bold text-green-600">{{ $completedInquiries }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between p-3 bg-red-50 rounded-lg border-l-4 border-red-400">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 bg-red-400 rounded-full mr-3"></div>
                                                    <span class="font-medium text-gray-700">{{ __('Rejected') }}</span>
                                                </div>
                                                <span
                                                    class="text-lg font-bold text-red-600">{{ $rejectedInquiries }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 bg-blue-400 rounded-full mr-3"></div>
                                                    <span class="font-medium text-gray-700">{{ __('In Progress') }}</span>
                                                </div>
                                                <span
                                                    class="text-lg font-bold text-blue-600">{{ $inProgressInquiries }}</span>
                                            </div>

                                            <div
                                                class="flex items-center justify-between p-3 rounded-lg border-l-4"
                                                style="border-left-color: #9333EA; background-color: rgba(147, 51, 234, 0.1);">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 rounded-full mr-3"
                                                        style="background-color: #9333EA;"></div>
                                                    <span
                                                        class="font-medium text-gray-700">{{ __('Under Review') }}</span>
                                                </div>
                                                <span
                                                    class="text-lg font-bold" style="color: #9333EA;">{{ $underReviewInquiries }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between p-3 rounded-lg border-l-4"
                                                style="border-left-color: #F59E0B; background-color: rgba(245, 158, 11, 0.1);">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 rounded-full mr-3" style="background-color: #F59E0B;"></div>
                                                    <span
                                                        class="font-medium text-gray-700">{{ __('Assigned to Agency') }}</span>
                                                </div>
                                                <span
                                                    class="text-lg font-bold" style="color: #F59E0B;">{{ $assignToAgencyInquiries }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 mb-8">
                            <!-- Recent Inquiries -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                                    <h5 class="text-white font-semibold mb-0">{{ __('Recent Inquiries') }}</h5>
                                </div>
                                <div class="p-6">
                                    @if (isset($recentInquiries) && $recentInquiries->count() > 0)
                                        <div class="space-y-4">
                                            @foreach ($recentInquiries as $inquiry)
                                                <div
                                                    class="flex justify-between items-start p-4 bg-gray-50 rounded-lg border">
                                                    <div class="flex-1">
                                                        <div class="font-semibold text-gray-900 mb-1">
                                                            {{ \Illuminate\Support\Str::limit($inquiry->inquiry_Title, 40) }}
                                                        </div>
                                                        <div class="text-sm text-gray-600">
                                                            {{ $inquiry->user->name ?? 'N/A' }} •
                                                            {{ \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') }}
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                                            <p class="text-gray-500">{{ __('No recent inquiries found') }}</p>

                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Category Breakdown -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                                <h5 class="text-white font-semibold mb-0">{{ __('Category Breakdown') }}</h5>
                            </div>
                            <div class="p-6">
                                @if ($inquiriesByCategory->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Category') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Count') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($inquiriesByCategory as $category)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                                {{ ucfirst(str_replace('_', ' ', $category->inquiry_Category)) }}
                                                            </span>
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                                            {{ $category->count }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i class="fas fa-table text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-500">
                                            {{ __('No data available for the selected period') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Inquiries Lists -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-8">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                                <h5 class="text-white font-semibold mb-0">{{ __('Inquiries Lists') }}</h5>
                            </div>
                            <div class="p-6">
                                @if (isset($inquiries) && $inquiries->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Title') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Category') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Submitter') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Status') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Date') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($inquiries as $inquiry)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ \Illuminate\Support\Str::limit($inquiry->inquiry_Title, 50) }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                                {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $inquiry->user->name ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            @if (strtolower($inquiry->inquiry_Status) == 'completed') bg-green-100 text-green-800
                                                            @elseif(strtolower($inquiry->inquiry_Status) == 'in_progress') bg-blue-100 text-blue-800
                                                            @elseif(strtolower($inquiry->inquiry_Status) == 'rejected') bg-red-100 text-red-800
                                                            @else bg-yellow-100 text-yellow-800 @endif">
                                                                {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <div
                                            class="bg-indigo-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-list text-3xl text-indigo-600"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No inquiries found') }}
                                        </h3>
                                        <p class="text-indigo-600">
                                            {{ __('There are no inquiry submissions to display at this time.') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @media print {

                .btn,
                .form-control,
                .card-header .btn-group,
                .pagination {
                    display: none !important;
                }

                .card {
                    border: none !important;
                    box-shadow: none !important;
                }

                .card-body {
                    padding: 0 !important;
                }

                .container-fluid {
                    padding: 0 !important;
                }

                .row {
                    margin: 0 !important;
                }

                .col-12 {
                    padding: 0 !important;
                }

                body {
                    font-size: 12px !important;
                }

                .card-header h4 {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 10px;
                }

                .stats-card {
                    break-inside: avoid;
                }

                .table {
                    font-size: 11px !important;
                }

                .badge {
                    border: 1px solid #333 !important;
                    color: #000 !important;
                    background-color: #fff !important;
                }

                canvas {
                    display: none !important;
                }

                .chart-container {
                    display: none !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Filter interaction logic
                const yearSelect = document.getElementById('year');
                const monthSelect = document.getElementById('month');
                const startDateInput = document.getElementById('start_date');
                const endDateInput = document.getElementById('end_date');

                // Clear date range when year or month is selected
                function clearDateRange() {
                    startDateInput.value = '';
                    endDateInput.value = '';
                }

                // Clear year and month when date range is used
                function clearYearMonth() {
                    yearSelect.value = '';
                    monthSelect.value = '';
                }

                // Add event listeners
                if (yearSelect) {
                    yearSelect.addEventListener('change', function() {
                        if (this.value) {
                            clearDateRange();
                        }
                    });
                }

                if (monthSelect) {
                    monthSelect.addEventListener('change', function() {
                        if (this.value) {
                            clearDateRange();
                        }
                    });
                }

                if (startDateInput) {
                    startDateInput.addEventListener('change', function() {
                        if (this.value) {
                            clearYearMonth();
                        }
                    });
                }

                if (endDateInput) {
                    endDateInput.addEventListener('change', function() {
                        if (this.value) {
                            clearYearMonth();
                        }
                    });
                }

                // Status Pie Chart
                const ctx = document.getElementById('statusPieChart').getContext('2d');

                const statusData = {
                    labels: ['Pending', 'Completed', 'Rejected', 'In Progress', 'Under Review', 'Assign to Agency'],
                    datasets: [{
                        data: [
                            {{ $pendingInquiries }},
                            {{ $completedInquiries }},
                            {{ $rejectedInquiries }},
                            {{ $inProgressInquiries }},
                            {{ $underReviewInquiries }},
                            {{ $assignToAgencyInquiries }}
                        ],
                        backgroundColor: [
                            '#FBBF24', // Yellow for Pending
                            '#10B981', // Green for Completed
                            '#EF4444', // Red for Rejected
                            '#3B82F6', // Blue for In Progress
                            '#9333EA', // Purple for Under Review
                            '#F59E0B' // Amber for Assign to Agency
                        ],
                        borderColor: [
                            '#F59E0B',
                            '#059669',
                            '#DC2626',
                            '#2563EB',
                            '#7C3AED',
                            '#D97706'
                        ],
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                };

                const config = {
                    type: 'pie',
                    data: statusData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    usePointStyle: false
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                },
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1000
                        }
                    }
                };

                new Chart(ctx, config);
            });
        </script>
    @endpush

@endsection
