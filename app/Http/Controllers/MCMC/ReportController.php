<?php

namespace App\Http\Controllers\MCMC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InquiryAssignment;
use App\Models\Agency;
use App\Models\InquirySubmissionRecord;
use App\Models\Approval;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InquiryAssignmentReportExport;

class ReportController extends Controller
{
    /**
     * Display the main reporting dashboard
     */
    public function index(Request $request)
    {
        $agencies = Agency::orderBy('agency_Name')->get();

        // Get filter parameters
        $filters = $this->getFilters($request);

        // Get dashboard data
        $dashboardData = $this->getDashboardData($filters);

        // Get chart data
        $chartData = $this->getChartData($filters);

        return view('mcmc.reports.index', compact(
            'agencies',
            'filters',
            'dashboardData',
            'chartData'
        ));
    }

    /**
     * Get inquiry assignment report data
     */
    public function getReportData(Request $request)
    {
        $filters = $this->getFilters($request);
        $reportData = $this->getInquiryAssignmentReport($filters);

        return response()->json($reportData);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = $this->getFilters($request);
        $reportData = $this->getInquiryAssignmentReport($filters);

        $filename = 'inquiry_assignment_report_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new InquiryAssignmentReportExport($reportData, $filters),
            $filename
        );
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = $this->getFilters($request);
        $reportData = $this->getInquiryAssignmentReport($filters);
        $chartData = $this->getChartData($filters);

        $pdf = Pdf::loadView('mcmc.reports.pdf', compact('reportData', 'filters', 'chartData'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'inquiry_assignment_report_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get filters from request
     */
    private function getFilters(Request $request)
    {
        return [
            'date_from' => $request->get('date_from', Carbon::now()->startOfYear()->format('Y-m-d')),
            'date_to' => $request->get('date_to', Carbon::now()->format('Y-m-d')),
            'agency_id' => $request->get('agency_id'),
            'month' => $request->get('month'),
            'year' => $request->get('year', Carbon::now()->year),
            'status' => $request->get('status'),
            'group_by' => $request->get('group_by', 'agency'), // agency, month, status
        ];
    }

    /**
     * Get dashboard summary data
     */
    private function getDashboardData($filters)
    {
        $query = InquiryAssignment::query()
            ->join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
            ->join('approvals', 'inquiry_assignments.approval_ID', '=', 'approvals.approval_ID')
            ->join('inquiries', 'approvals.inquiry_ID', '=', 'inquiries.inquiry_ID');

        // Apply filters
        $this->applyFilters($query, $filters);

        $totalAssignments = $query->count();
        $pendingAssignments = (clone $query)->where('inquiry_assignments.assignment_Status', 'pending')->count();
        $inProgressAssignments = (clone $query)->where('inquiry_assignments.assignment_Status', 'in_progress')->count();
        $completedAssignments = (clone $query)->where('inquiry_assignments.assignment_Status', 'completed')->count();
        $rejectedAssignments = (clone $query)->where('inquiry_assignments.assignment_Status', 'rejected')->count();

        // Get agency stats
        $agencyStats = (clone $query)
            ->select('agencies.agency_Name', DB::raw('COUNT(*) as total_assignments'))
            ->groupBy('agencies.agency_ID', 'agencies.agency_Name')
            ->orderBy('total_assignments', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_assignments' => $totalAssignments,
            'pending_assignments' => $pendingAssignments,
            'in_progress_assignments' => $inProgressAssignments,
            'completed_assignments' => $completedAssignments,
            'rejected_assignments' => $rejectedAssignments,
            'top_agencies' => $agencyStats,
        ];
    }

    /**
     * Get chart data for visualizations
     */
    private function getChartData($filters)
    {
        // Monthly trend data
        $monthlyTrend = $this->getMonthlyTrendData($filters);

        // Agency distribution data
        $agencyDistribution = $this->getAgencyDistributionData($filters);

        // Status distribution data
        $statusDistribution = $this->getStatusDistributionData($filters);

        // Inquiry category data
        $categoryDistribution = $this->getCategoryDistributionData($filters);

        return [
            'monthly_trend' => $monthlyTrend,
            'agency_distribution' => $agencyDistribution,
            'status_distribution' => $statusDistribution,
            'category_distribution' => $categoryDistribution,
        ];
    }

    /**
     * Get detailed inquiry assignment report
     */
    private function getInquiryAssignmentReport($filters)
    {
        $query = InquiryAssignment::query()
            ->join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
            ->join('approvals', 'inquiry_assignments.approval_ID', '=', 'approvals.approval_ID')
            ->join('inquiries', 'approvals.inquiry_ID', '=', 'inquiries.inquiry_ID')
            ->join('public_users', 'inquiries.user_ID', '=', 'public_users.user_ID')
            ->leftJoin('mcmc_staff', 'inquiry_assignments.assigned_By', '=', 'mcmc_staff.staff_ID')
            ->select(
                'inquiry_assignments.*',
                'agencies.agency_Name',
                'agencies.agency_Type',
                'inquiries.inquiry_Title',
                'inquiries.inquiry_Category',
                'inquiries.inquiry_Status as inquiry_status',
                'inquiries.inquiry_Created_At',
                'public_users.user_Name as user_name',
                'public_users.user_Email as user_email',
                'mcmc_staff.staff_Name as assigned_by_name',
                'approvals.approval_Date'
            );

        // Apply filters
        $this->applyFilters($query, $filters);

        // Group by based on filter
        if ($filters['group_by'] === 'agency') {
            return $query->orderBy('agencies.agency_Name')
                ->orderBy('inquiry_assignments.assignment_Date', 'desc')
                ->get()
                ->groupBy('agency_Name');
        } elseif ($filters['group_by'] === 'month') {
            return $query->orderBy('inquiry_assignments.assignment_Date', 'desc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->assignment_Date)->format('Y-m');
                });
        } else {
            return $query->orderBy('inquiry_assignments.assignment_Date', 'desc')
                ->get()
                ->groupBy('assignment_Status');
        }
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $filters)
    {
        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->where('inquiry_assignments.assignment_Date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('inquiry_assignments.assignment_Date', '<=', $filters['date_to'] . ' 23:59:59');
        }

        // Agency filter
        if (!empty($filters['agency_id'])) {
            $query->where('agencies.agency_ID', $filters['agency_id']);
        }

        // Month filter
        if (!empty($filters['month'])) {
            $query->whereMonth('inquiry_assignments.assignment_Date', $filters['month']);
        }

        // Year filter
        if (!empty($filters['year'])) {
            $query->whereYear('inquiry_assignments.assignment_Date', $filters['year']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('inquiry_assignments.assignment_Status', $filters['status']);
        }
    }

    /**
     * Get monthly trend data
     */
    private function getMonthlyTrendData($filters)
    {
        $query = InquiryAssignment::query()
            ->join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
            ->select(
                DB::raw('DATE_FORMAT(assignment_Date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_assignments'),
                DB::raw('SUM(CASE WHEN assignment_Status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN assignment_Status = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN assignment_Status = "in_progress" THEN 1 ELSE 0 END) as in_progress')
            );

        // Apply basic filters (excluding month filter)
        $tempFilters = $filters;
        unset($tempFilters['month']);
        $this->applyFilters($query, $tempFilters);

        return $query->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get agency distribution data
     */
    private function getAgencyDistributionData($filters)
    {
        $query = InquiryAssignment::query()
            ->join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
            ->select(
                'agencies.agency_Name',
                'agencies.agency_Type',
                DB::raw('COUNT(*) as total_assignments')
            );

        $this->applyFilters($query, $filters);

        return $query->groupBy('agencies.agency_ID', 'agencies.agency_Name', 'agencies.agency_Type')
            ->orderBy('total_assignments', 'desc')
            ->get();
    }

    /**
     * Get status distribution data
     */
    private function getStatusDistributionData($filters)
    {
        $query = InquiryAssignment::query()
            ->join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
            ->select(
                'inquiry_assignments.assignment_Status',
                DB::raw('COUNT(*) as total_assignments')
            );

        $this->applyFilters($query, $filters);

        return $query->groupBy('inquiry_assignments.assignment_Status')
            ->orderBy('total_assignments', 'desc')
            ->get();
    }

    /**
     * Get category distribution data
     */
    private function getCategoryDistributionData($filters)
    {
        $query = InquiryAssignment::query()
            ->join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
            ->join('approvals', 'inquiry_assignments.approval_ID', '=', 'approvals.approval_ID')
            ->join('inquiries', 'approvals.inquiry_ID', '=', 'inquiries.inquiry_ID')
            ->select(
                'inquiries.inquiry_Category',
                DB::raw('COUNT(*) as total_assignments')
            );

        $this->applyFilters($query, $filters);

        return $query->groupBy('inquiries.inquiry_Category')
            ->orderBy('total_assignments', 'desc')
            ->get();
    }
}
