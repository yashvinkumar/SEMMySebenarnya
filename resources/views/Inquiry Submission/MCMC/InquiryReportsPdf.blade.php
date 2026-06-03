<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inquiry Reports & Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }

        .date-range {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }

        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            flex: 1;
            min-width: 150px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card.total {
            background-color: #e3f2fd;
        }

        .stat-card.pending {
            background-color: #fff3e0;
        }

        .stat-card.progress {
            background-color: #e8f5e8;
        }

        .stat-card.completed {
            background-color: #e8f5e8;
        }

        .stat-card.rejected {
            background-color: #ffebee;
        }

        .stat-card.submitted {
            background-color: #f3e8ff;
        }

        .stat-card.under-review {
            background-color: #fed7aa;
        }

        .stat-card.assign-to-agency {
            background-color: #f0fdfa;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .recent-item {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .recent-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .recent-meta {
            font-size: 12px;
            color: #666;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in_progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .category-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .category-technical,
        .category-billing,
        .category-general,
        .category-complaint,
        .category-feedback,
        .category-support,
        .category-other {
            background-color: #d1f2eb;
            color: #0c5460;
        }

        /* Status Distribution Chart Styles */
        .chart-container {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .chart-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #6f42c1;
        }

        .status-chart {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .status-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-label {
            min-width: 100px;
            font-weight: bold;
            font-size: 12px;
        }

        .status-bar {
            flex: 1;
            height: 25px;
            border-radius: 12px;
            position: relative;
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        .status-bar.pending {
            background-color: #FBBF24;
        }

        .status-bar.in-progress {
            background-color: #3B82F6;
        }

        .status-bar.completed {
            background-color: #10B981;
        }

        .status-bar.rejected {
            background-color: #EF4444;
        }

        .status-bar.submitted {
            background-color: #8B5CF6;
        }

        .status-bar.under-review {
            background-color: #F97316;
        }

        .status-bar.assign-to-agency {
            background-color: #14B8A6;
        }

        .status-count {
            min-width: 60px;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }

        .percentage-text {
            font-size: 10px;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ __('Inquiry Reports & Statistics') }}</h1>
    </div>

    <div class="date-range">
        <strong>{{ __('Report Period') }}:</strong>
        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
    </div>

    <!-- Statistics Cards -->
    <div class="section">
        <div class="section-title">{{ __('Summary Statistics') }}</div>
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-number">{{ $totalInquiries }}</div>
                <div class="stat-label">{{ __('Total Inquiries') }}</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number">{{ $pendingInquiries }}</div>
                <div class="stat-label">{{ __('Pending') }}</div>
            </div>
            <div class="stat-card progress">
                <div class="stat-number">{{ $inProgressInquiries }}</div>
                <div class="stat-label">{{ __('In Progress') }}</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-number">{{ $completedInquiries }}</div>
                <div class="stat-label">{{ __('Completed') }}</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-number">{{ $rejectedInquiries }}</div>
                <div class="stat-label">{{ __('Rejected') }}</div>
            </div>

            <div class="stat-card under-review">
                <div class="stat-number">{{ $underReviewInquiries }}</div>
                <div class="stat-label">{{ __('Under Review') }}</div>
            </div>
            <div class="stat-card assign-to-agency">
                <div class="stat-number">{{ $assignToAgencyInquiries }}</div>
                <div class="stat-label">{{ __('Assign to Agency') }}</div>
            </div>
        </div>
    </div>

    <!-- Status Distribution Chart -->
    <div class="section">
        <div class="chart-container">
            <div class="chart-title">{{ __('Status Distribution') }}</div>
            <div class="status-chart">
                @php
                    $maxCount = max(
                        $pendingInquiries,
                        $inProgressInquiries,
                        $completedInquiries,
                        $rejectedInquiries,
                        $underReviewInquiries,
                        $assignToAgencyInquiries,
                    );
                    $maxCount = $maxCount > 0 ? $maxCount : 1; // Prevent division by zero
                @endphp

                <div class="status-row">
                    <div class="status-label">{{ __('Pending') }}</div>
                    <div class="status-bar pending"
                        style="width: {{ $maxCount > 0 ? ($pendingInquiries / $maxCount) * 100 : 0 }}%;">
                        {{ $pendingInquiries }}
                        <span class="percentage-text">
                            ({{ $totalInquiries > 0 ? round(($pendingInquiries / $totalInquiries) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="status-count">{{ $pendingInquiries }}</div>
                </div>

                <div class="status-row">
                    <div class="status-label">{{ __('In Progress') }}</div>
                    <div class="status-bar in-progress"
                        style="width: {{ $maxCount > 0 ? ($inProgressInquiries / $maxCount) * 100 : 0 }}%;">
                        {{ $inProgressInquiries }}
                        <span class="percentage-text">
                            ({{ $totalInquiries > 0 ? round(($inProgressInquiries / $totalInquiries) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="status-count">{{ $inProgressInquiries }}</div>
                </div>

                <div class="status-row">
                    <div class="status-label">{{ __('Completed') }}</div>
                    <div class="status-bar completed"
                        style="width: {{ $maxCount > 0 ? ($completedInquiries / $maxCount) * 100 : 0 }}%;">
                        {{ $completedInquiries }}
                        <span class="percentage-text">
                            ({{ $totalInquiries > 0 ? round(($completedInquiries / $totalInquiries) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="status-count">{{ $completedInquiries }}</div>
                </div>

                <div class="status-row">
                    <div class="status-label">{{ __('Rejected') }}</div>
                    <div class="status-bar rejected"
                        style="width: {{ $maxCount > 0 ? ($rejectedInquiries / $maxCount) * 100 : 0 }}%;">
                        {{ $rejectedInquiries }}
                        <span class="percentage-text">
                            ({{ $totalInquiries > 0 ? round(($rejectedInquiries / $totalInquiries) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="status-count">{{ $rejectedInquiries }}</div>
                </div>



                <div class="status-row">
                    <div class="status-label">{{ __('Under Review') }}</div>
                    <div class="status-bar under-review"
                        style="width: {{ $maxCount > 0 ? ($underReviewInquiries / $maxCount) * 100 : 0 }}%;">
                        {{ $underReviewInquiries }}
                        <span class="percentage-text">
                            ({{ $totalInquiries > 0 ? round(($underReviewInquiries / $totalInquiries) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="status-count">{{ $underReviewInquiries }}</div>
                </div>

                <div class="status-row">
                    <div class="status-label">{{ __('Assign to Agency') }}</div>
                    <div class="status-bar assign-to-agency"
                        style="width: {{ $maxCount > 0 ? ($assignToAgencyInquiries / $maxCount) * 100 : 0 }}%;">
                        {{ $assignToAgencyInquiries }}
                        <span class="percentage-text">
                            ({{ $totalInquiries > 0 ? round(($assignToAgencyInquiries / $totalInquiries) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="status-count">{{ $assignToAgencyInquiries }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Inquiries (Top 3) -->
    <div class="section">
        <div class="section-title">{{ __('Recent Inquiries') }} ({{ __('Top 3') }})</div>
        @if (isset($recentInquiries) && $recentInquiries->count() > 0)
            @foreach ($recentInquiries as $inquiry)
                <div class="recent-item">
                    <div class="recent-title">{{ \Illuminate\Support\Str::limit($inquiry->inquiry_Title, 60) }}</div>
                    <div class="recent-meta">
                        <strong>{{ __('User') }}:</strong> {{ $inquiry->user->name ?? 'N/A' }} |
                        <strong>{{ __('Date') }}:</strong>
                        {{ \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') }} |
                        <strong>{{ __('Status') }}:</strong>
                        <span class="status-badge status-{{ strtolower($inquiry->inquiry_Status) }}">
                            {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                        </span> |
                        <strong>{{ __('Category') }}:</strong>
                        <span class="category-badge category-{{ strtolower($inquiry->inquiry_Category) }}">
                            {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}
                        </span>
                    </div>
                </div>
            @endforeach
        @else
            <p>{{ __('No recent inquiries found') }}</p>
        @endif
    </div>

    <!-- Category Breakdown -->
    <div class="section">
        <div class="section-title">{{ __('Category Breakdown') }}</div>
        @if ($inquiriesByCategory->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Count') }}</th>
                        <th>{{ __('Percentage') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inquiriesByCategory as $category)
                        <tr>
                            <td>
                                <span class="category-badge category-{{ strtolower($category->inquiry_Category) }}">
                                    {{ ucfirst(str_replace('_', ' ', $category->inquiry_Category)) }}
                                </span>
                            </td>
                            <td>{{ $category->count }}</td>
                            <td>{{ $totalInquiries > 0 ? round(($category->count / $totalInquiries) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>{{ __('No category data available') }}</p>
        @endif
    </div>

    <!-- All Inquiries List -->
    <div class="section">
        <div class="section-title">{{ __('All Inquiries in Selected Period') }}</div>
        @if ($inquiries->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Created At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inquiries as $inquiry)
                        <tr>
                            <td>{{ $inquiry->inquiry_ID }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($inquiry->inquiry_Title, 40) }}</td>
                            <td>
                                <span class="category-badge category-{{ strtolower($inquiry->inquiry_Category) }}">
                                    {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($inquiry->inquiry_Status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                                </span>
                            </td>
                            <td>{{ $inquiry->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>{{ __('No inquiries found for the selected period') }}</p>
        @endif
    </div>

    <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
        {{ __('Generated on') }}: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>

</html>
