<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inquiry Assignment Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        .header .subtitle {
            color: #666;
            margin: 5px 0;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .filter-info h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
        }

        .filter-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
        }

        .stat-box {
            text-align: center;
            flex: 1;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .chart-section {
            margin: 30px 0;
            page-break-inside: avoid;
        }

        .chart-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .chart-data {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .section {
            margin: 30px 0;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }

        .group-header {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
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

        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>INQUIRY ASSIGNMENT REPORT</h1>
        <div class="subtitle">Malaysian Communications and Multimedia Commission (MCMC)</div>
        <div class="subtitle">Generated on: {{ \Carbon\Carbon::now()->format('F d, Y \a\t H:i:s') }}</div>
    </div>

    <!-- Filter Information -->
    <div class="filter-info">
        <h3>Report Parameters</h3>
        <div class="filter-row">
            <strong>Date Range:</strong>
            <span>{{ $filters['date_from'] }} to {{ $filters['date_to'] }}</span>
        </div>
        <div class="filter-row">
            <strong>Agency Filter:</strong>
            <span>{{ $filters['agency_id'] ? 'Specific Agency' : 'All Agencies' }}</span>
        </div>
        <div class="filter-row">
            <strong>Status Filter:</strong>
            <span>{{ $filters['status'] ? ucfirst($filters['status']) : 'All Statuses' }}</span>
        </div>
        <div class="filter-row">
            <strong>Group By:</strong>
            <span>{{ ucfirst($filters['group_by']) }}</span>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-box">
            <div class="stat-number">{{ $chartData['status_distribution']->sum('total_assignments') }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $chartData['status_distribution']->where('assignment_Status', 'pending')->first()->total_assignments ?? 0 }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $chartData['status_distribution']->where('assignment_Status', 'in_progress')->first()->total_assignments ?? 0 }}</div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $chartData['status_distribution']->where('assignment_Status', 'completed')->first()->total_assignments ?? 0 }}</div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $chartData['status_distribution']->where('assignment_Status', 'rejected')->first()->total_assignments ?? 0 }}</div>
            <div class="stat-label">Rejected</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $chartData['status_distribution']->where('assignment_Status', 'under_review')->first()->total_assignments ?? 0 }}</div>
            <div class="stat-label">Under Review</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $chartData['status_distribution']->where('assignment_Status', 'assign_to_agency')->first()->total_assignments ?? 0 }}</div>
            <div class="stat-label">Assigned to Agency</div>
        </div>
    </div>

    <!-- Agency Distribution Chart Data -->
    <div class="chart-section">
        <div class="chart-title">Top 10 Agencies by Assignment Count</div>
        <div class="chart-data">
            @foreach($chartData['agency_distribution']->take(10) as $agency)
            <div class="data-row">
                <span>{{ $agency->agency_Name }}</span>
                <span><strong>{{ $agency->total_assignments }}</strong></span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Monthly Trend Data -->
    @if($chartData['monthly_trend']->count() > 0)
    <div class="chart-section">
        <div class="chart-title">Monthly Assignment Trend</div>
        <div class="chart-data">
            @foreach($chartData['monthly_trend'] as $month)
            <div class="data-row">
                <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('F Y') }}</span>
                <span>
                    <strong>{{ $month->total_assignments }}</strong>
                    ({{ $month->completed }} completed, {{ $month->pending }} pending)
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Category Distribution -->
    @if($chartData['category_distribution']->count() > 0)
    <div class="chart-section">
        <div class="chart-title">Inquiry Categories Distribution</div>
        <div class="chart-data">
            @foreach($chartData['category_distribution'] as $category)
            <div class="data-row">
                <span>{{ ucfirst($category->inquiry_Category) }}</span>
                <span><strong>{{ $category->total_assignments }}</strong></span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Detailed Report Data -->
    <div class="page-break"></div>
    <div class="section">
        <div class="section-title">Detailed Assignment Records</div>

        @foreach($reportData as $groupKey => $assignments)
            <div class="group-header">{{ strtoupper($groupKey) }}</div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 8%">ID</th>
                        <th style="width: 20%">Inquiry Title</th>
                        <th style="width: 15%">User</th>
                        <th style="width: 18%">Agency</th>
                        <th style="width: 10%">Category</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 12%">Assignment Date</th>
                        <th style="width: 7%">Assigned By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->assignment_ID }}</td>
                        <td>{{ $assignment->inquiry_Title }}</td>
                        <td>
                            {{ $assignment->user_name }}<br>
                            <small>{{ $assignment->user_email }}</small>
                        </td>
                        <td>
                            {{ $assignment->agency_Name }}<br>
                            <small>{{ $assignment->agency_Type }}</small>
                        </td>
                        <td>{{ ucfirst($assignment->inquiry_Category) }}</td>
                        <td>
                            <span class="status-badge status-{{ $assignment->assignment_Status }}">
                                {{ str_replace('_', ' ', $assignment->assignment_Status) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($assignment->assignment_Date)->format('M d, Y') }}</td>
                        <td>{{ $assignment->assigned_by_name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Generated by MCMC Inquiry Management System | Page <span class="pagenum"></span></div>
        <div>This report contains confidential information. Handle with care.</div>
    </div>
</body>
</html>
