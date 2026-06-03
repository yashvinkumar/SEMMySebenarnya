<?php

namespace App\Http\Controllers;

use App\Models\InquiryAssignment;
use App\Models\Agency;
use App\Models\Approval;
use App\Models\InquirySubmissionRecord;
use App\Models\McmcStaff;
use App\Notifications\AssignmentStatusUpdatedNotification;
use App\Notifications\AssignmentRejectedNotification;
use App\Notifications\InquiryReassignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class AgencyJurisdictionController extends Controller
{
    /**
     * Get current agency ID from authenticated user
     */
    private function getAgencyId()
    {
        return Auth::guard('agency')->id();
    }

    /**
     * Display enhanced dashboard for agencies
     */
    public function enhancedDashboard()
    {
        $agencyId = $this->getAgencyId();

        // Get assignment statistics
        $stats = [
            'total_assignments' => InquiryAssignment::where('agency_ID', $agencyId)->count(),
            'pending_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'pending')->count(),
            'in_progress_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'in_progress')->count(),
            'completed_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'completed')->count(),
            'rejected_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'rejected')->count(),
        ];

        // This month statistics
        $stats['this_month_assignments'] = InquiryAssignment::where('agency_ID', $agencyId)
            ->whereMonth('assignment_Date', now()->month)
            ->whereYear('assignment_Date', now()->year)
            ->count();

        // Completion rate
        $stats['completion_rate'] = $stats['total_assignments'] > 0
            ? round(($stats['completed_assignments'] / $stats['total_assignments']) * 100, 1)
            : 0;

        // Average response time (in hours)
        $avgResponseTime = InquiryAssignment::where('agency_ID', $agencyId)
            ->whereNotNull('completed_At')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assignment_Date, completed_At)) as avg_hours')
            ->value('avg_hours') ?? 0;

        $stats['avg_response_time'] = round($avgResponseTime, 1);

        // Recent assignments
        $recentAssignments = InquiryAssignment::with([
            'approval.inquiry.user',
            'assignedByStaff'
        ])
        ->where('agency_ID', $agencyId)
        ->orderBy('assignment_Date', 'desc')
        ->limit(10)
        ->get();

        // Overdue assignments (pending for more than 48 hours)
        $overdueCount = InquiryAssignment::where('agency_ID', $agencyId)
            ->where('assignment_Status', 'pending')
            ->where('assignment_Date', '<', now()->subHours(48))
            ->count();

        return view('agency.enhanced-dashboard', compact('stats', 'recentAssignments', 'overdueCount'));
    }

    /**
     * Display enhanced assignments list
     */
    public function enhancedList(Request $request)
    {
        $agencyId = $this->getAgencyId();

        $query = InquiryAssignment::with(['approval.inquiry.user', 'assignedByStaff'])
            ->where('agency_ID', $agencyId);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('assignment_Status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('assignment_Date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('assignment_Date', '<=', $request->end_date);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('approval.inquiry', function($q) use ($search) {
                $q->where('inquiry_Title', 'like', "%{$search}%")
                  ->orWhere('inquiry_Description', 'like', "%{$search}%");
            });
        }

        $assignments = $query->orderBy('assignment_Date', 'desc')->paginate(15);

        return view('agency.assignments.enhanced-list', compact('assignments'));
    }

    /**
     * Show jurisdiction review interface
     */
    public function jurisdictionReview($assignmentId)
    {
        $agencyId = $this->getAgencyId();

        $assignment = InquiryAssignment::with([
            'agency',
            'approval.inquiry.user',
            'assignedByStaff'
        ])
        ->where('agency_ID', $agencyId)
        ->where('assignment_Status', 'pending')
        ->findOrFail($assignmentId);

        return view('agency.assignments.jurisdiction-review', compact('assignment'));
    }

    /**
     * Accept assignment with jurisdiction confirmation
     */
    public function acceptJurisdiction(Request $request, $assignmentId)
    {
        $request->validate([
            'jurisdiction_confirmation' => 'required|boolean',
            'comments' => 'nullable|string|max:1000'
        ]);

        if (!$request->jurisdiction_confirmation) {
            return back()->with('error', 'You must confirm that this inquiry falls within your jurisdiction.');
        }

        $agencyId = $this->getAgencyId();

        $assignment = InquiryAssignment::where('agency_ID', $agencyId)
            ->where('assignment_Status', 'pending')
            ->findOrFail($assignmentId);

        DB::beginTransaction();

        try {
            // Update assignment status
            $assignment->assignment_Status = 'in_progress';
            $assignment->assignment_Comments = "Jurisdiction confirmed by agency. " . ($request->comments ?? '');
            $assignment->save();

            // Update inquiry status
            $inquiry = $assignment->approval->inquiry;
            $inquiry->inquiry_Status = 'agency_review_in_progress';
            $inquiry->save();

            // Send notifications
            $user = $inquiry->user;
            $mcmcStaff = $assignment->assignedByStaff;

            if ($user) {
                Notification::send($user, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
            }

            if ($mcmcStaff) {
                Notification::send($mcmcStaff, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
            }

            DB::commit();

            return redirect()->route('agency.assignments.list')
                ->with('success', 'Assignment accepted successfully. You can now proceed with the verification process.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to accept assignment. Please try again.');
        }
    }

    /**
     * Reject assignment with jurisdiction reason
     */
    public function rejectJurisdiction(Request $request, $assignmentId)
    {
        $request->validate([
            'jurisdiction_reason' => 'required|string|max:2000',
            'suggested_agency' => 'nullable|exists:agencies,agency_ID'
        ]);

        $agencyId = $this->getAgencyId();

        $assignment = InquiryAssignment::where('agency_ID', $agencyId)
            ->whereIn('assignment_Status', ['pending', 'in_progress'])
            ->findOrFail($assignmentId);

        DB::beginTransaction();

        try {
            // Update assignment with rejection details
            $assignment->assignment_Status = 'rejected';
            $assignment->rejection_Reason = $request->jurisdiction_reason;

            // Add suggested agency information if provided
            if ($request->suggested_agency) {
                $suggestedAgency = Agency::find($request->suggested_agency);
                $assignment->assignment_Comments = "Jurisdiction rejected. Suggested agency: " . $suggestedAgency->agency_Name . ". Reason: " . $request->jurisdiction_reason;
            } else {
                $assignment->assignment_Comments = "Jurisdiction rejected. Reason: " . $request->jurisdiction_reason;
            }

            $assignment->save();

            // Update inquiry status
            $inquiry = $assignment->approval->inquiry;
            $inquiry->inquiry_Status = 'agency_rejected';
            $inquiry->save();

            // Send notifications
            $user = $inquiry->user;
            $mcmcStaff = $assignment->assignedByStaff;

            if ($user) {
                Notification::send($user, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
            }

            if ($mcmcStaff) {
                Notification::send($mcmcStaff, new AssignmentRejectedNotification($assignment, $inquiry));
            }

            DB::commit();

            return redirect()->route('agency.assignments.list')
                ->with('success', 'Assignment rejected and returned to MCMC. They will reassign it to an appropriate agency.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject assignment. Please try again.');
        }
    }

    /**
     * Bulk accept assignments
     */
    public function bulkAccept(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'exists:inquiry_assignments,assignment_ID',
            'comments' => 'nullable|string|max:1000'
        ]);

        $agencyId = $this->getAgencyId();
        $assignmentIds = $request->assignment_ids;
        $comments = $request->comments ?? '';

        $assignments = InquiryAssignment::where('agency_ID', $agencyId)
            ->where('assignment_Status', 'pending')
            ->whereIn('assignment_ID', $assignmentIds)
            ->get();

        if ($assignments->count() !== count($assignmentIds)) {
            return back()->with('error', 'Some assignments could not be found or are not eligible for bulk acceptance.');
        }

        DB::beginTransaction();

        try {
            $successCount = 0;

            foreach ($assignments as $assignment) {
                // Update assignment status
                $assignment->assignment_Status = 'in_progress';
                $assignment->assignment_Comments = "Bulk jurisdiction confirmation by agency. " . $comments;
                $assignment->save();

                // Update inquiry status
                $inquiry = $assignment->approval->inquiry;
                $inquiry->inquiry_Status = 'agency_review_in_progress';
                $inquiry->save();

                // Send notifications
                $user = $inquiry->user;
                $mcmcStaff = $assignment->assignedByStaff;

                if ($user) {
                    Notification::send($user, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
                }

                if ($mcmcStaff) {
                    Notification::send($mcmcStaff, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
                }

                $successCount++;
            }

            DB::commit();

            return redirect()->route('agency.assignments.list')
                ->with('success', "Successfully accepted {$successCount} assignment" . ($successCount > 1 ? 's' : '') . ".");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process bulk acceptance. Please try again.');
        }
    }

    /**
     * Bulk reject assignments
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'exists:inquiry_assignments,assignment_ID',
            'jurisdiction_reason' => 'required|string|max:2000'
        ]);

        $agencyId = $this->getAgencyId();
        $assignmentIds = $request->assignment_ids;
        $reason = $request->jurisdiction_reason;

        $assignments = InquiryAssignment::where('agency_ID', $agencyId)
            ->where('assignment_Status', 'pending')
            ->whereIn('assignment_ID', $assignmentIds)
            ->get();

        if ($assignments->count() !== count($assignmentIds)) {
            return back()->with('error', 'Some assignments could not be found or are not eligible for bulk rejection.');
        }

        DB::beginTransaction();

        try {
            $successCount = 0;

            foreach ($assignments as $assignment) {
                // Update assignment with rejection details
                $assignment->assignment_Status = 'rejected';
                $assignment->rejection_Reason = $reason;
                $assignment->assignment_Comments = "Bulk jurisdiction rejection by agency. Reason: " . $reason;
                $assignment->save();

                // Update inquiry status
                $inquiry = $assignment->approval->inquiry;
                $inquiry->inquiry_Status = 'agency_rejected';
                $inquiry->save();

                // Send notifications
                $user = $inquiry->user;
                $mcmcStaff = $assignment->assignedByStaff;

                if ($user) {
                    Notification::send($user, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
                }

                if ($mcmcStaff) {
                    Notification::send($mcmcStaff, new AssignmentRejectedNotification($assignment, $inquiry));
                }

                $successCount++;
            }

            DB::commit();

            return redirect()->route('agency.assignments.list')
                ->with('success', "Successfully rejected {$successCount} assignment" . ($successCount > 1 ? 's' : '') . " and returned them to MCMC for reassignment.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process bulk rejection. Please try again.');
        }
    }

    /**
     * Show notifications interface
     */
    public function notifications(Request $request)
    {
        $agencyId = $this->getAgencyId();

        // Mock notification data - in a real implementation, you would fetch from a notifications table
        $notifications = collect([
            [
                'id' => 1,
                'type' => 'assignment',
                'title' => 'New Urgent Assignment',
                'message' => 'You have been assigned a new inquiry: "Telecommunication Service Disruption Report"',
                'created_at' => now()->subHours(2),
                'read_at' => null,
                'priority' => 'urgent',
                'data' => ['assignment_id' => 1]
            ],
            [
                'id' => 2,
                'type' => 'status',
                'title' => 'Assignment Deadline Reminder',
                'message' => 'Assignment "Internet Service Quality Issues" is due for review in 6 hours.',
                'created_at' => now()->subHours(4),
                'read_at' => null,
                'priority' => 'high',
                'data' => ['assignment_id' => 2]
            ],
            // Add more mock notifications as needed
        ]);

        return view('agency.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        // Implementation for marking notification as read
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        // Implementation for marking all notifications as read
        return response()->json(['success' => true]);
    }

    /**
     * Get assignment statistics for API
     */
    public function getStats()
    {
        $agencyId = $this->getAgencyId();

        $stats = [
            'total_assignments' => InquiryAssignment::where('agency_ID', $agencyId)->count(),
            'pending_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'pending')->count(),
            'in_progress_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'in_progress')->count(),
            'completed_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'completed')->count(),
            'overdue_assignments' => InquiryAssignment::where('agency_ID', $agencyId)
                ->where('assignment_Status', 'pending')
                ->where('assignment_Date', '<', now()->subHours(48))
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Generate assignment report for agency
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel'
        ]);

        $agencyId = $this->getAgencyId();
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;

        $assignments = InquiryAssignment::with(['approval.inquiry.user', 'assignedByStaff'])
            ->where('agency_ID', $agencyId)
            ->whereDate('assignment_Date', '>=', $startDate)
            ->whereDate('assignment_Date', '<=', $endDate)
            ->orderBy('assignment_Date', 'desc')
            ->get();

        if ($format === 'pdf') {
            // Generate PDF report
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('agency.reports.assignments-pdf', compact('assignments', 'startDate', 'endDate'));
            return $pdf->download('agency-assignments-report.pdf');
        } else {
            // Generate Excel report
            return \Excel::download(
                new \App\Exports\AgencyAssignmentReportExport($assignments, $startDate, $endDate),
                'agency-assignments-report.xlsx'
            );
        }
    }
}
