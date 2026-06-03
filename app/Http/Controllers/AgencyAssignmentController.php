<?php

namespace App\Http\Controllers;

use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\InquiryAssignment;
use App\Models\Approval;
use App\Models\McmcStaff;
use App\Models\UserRecord;
use App\Notifications\InquiryAssignedNotification;
use App\Notifications\AssignmentStatusUpdatedNotification;
use App\Notifications\AssignmentRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssignmentReportsExport;
use Illuminate\Support\Facades\Log;

class AgencyAssignmentController extends Controller
{
    /**
     * Display inquiries list for MCMC staff to manage assignments
     */
    public function mcmcInquiriesList(Request $request)
    {
        $query = InquirySubmissionRecord::with(['user']);

        // Add relationship for latest assignment
        $query->with(['assignments' => function ($q) {
            $q->latest('assignment_Date')->with('agency');
        }]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('inquiry_Status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('inquiry_Category', $request->category);
        }

        // Filter by assignment status
        if ($request->filled('assignment_filter')) {
            switch ($request->assignment_filter) {
                case 'assigned':
                    $query->whereHas('assignments');
                    break;
                case 'unassigned':
                    $query->whereDoesntHave('assignments');
                    break;
            }
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inquiry_Title', 'like', "%{$search}%")
                    ->orWhere('inquiry_Description', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('inquiry_Created_At', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('inquiry_Created_At', '<=', $request->end_date);
        }

        $inquiries = $query->orderBy('inquiry_Created_At', 'desc')->paginate(15);

        // Add latest assignment to each inquiry
        $inquiries->getCollection()->transform(function ($inquiry) {
            $inquiry->latestAssignment = $inquiry->assignments->first();
            return $inquiry;
        });

        $agencies = Agency::orderBy('agency_Name')->get();

        return view('mcmc.inquiries.list', compact('inquiries', 'agencies'));
    }

    /**
     * Show inquiry details for MCMC staff
     */
    public function mcmcInquiryDetails($inquiryId)
    {
        $inquiry = InquirySubmissionRecord::with([
            'user',
            'assignments.agency',
            'assignments.assignedByStaff'
        ])->findOrFail($inquiryId);

        $agencies = Agency::orderBy('agency_Name')->get();

        return view('mcmc.inquiries.details', compact('inquiry', 'agencies'));
    }

    /**
     * Update inquiry status (for MCMC staff)
     */
    public function updateInquiryStatus(Request $request, $inquiryId)
    {
        $request->validate([
            'status' => 'required|string',
            'comments' => 'nullable|string|max:1000'
        ]);

        $inquiry = InquirySubmissionRecord::findOrFail($inquiryId);
        $inquiry->inquiry_Status = $request->status;
        $inquiry->save();

        // Create approval record for status change
        if ($request->comments) {
            $approval = new Approval();
            $approval->inquiry_ID = $inquiry->inquiry_ID;
            $approval->staff_ID = Auth::guard('mcmc')->id();
            $approval->approval_Status = $request->status;
            $approval->approval_Comments = $request->comments;
            $approval->approval_Type = 'status_change';
            $approval->approval_Date = now();
            $approval->save();
        }

        return redirect()->back()->with('success', 'Inquiry status updated successfully.');
    }

    /**
     * Display the agency assignment form for MCMC staff
     */
    public function showAssignmentForm($inquiryId)
    {
        $inquiry = InquirySubmissionRecord::with('user')->findOrFail($inquiryId);
        $agencies = Agency::orderBy('agency_Name')->get();

        // Get existing assignment if any
        $existingAssignment = InquiryAssignment::whereHas('approval', function ($query) use ($inquiryId) {
            $query->where('inquiry_ID', $inquiryId);
        })->first();

        return view('mcmc.assignments.assign', compact('inquiry', 'agencies', 'existingAssignment'));
    }

    /**
     * Assign inquiry to agency
     */
    public function assignToAgency(Request $request, $inquiryId)
    {
        $request->validate([
            'agency_id' => 'required|exists:agencies,agency_ID',
            'comments' => 'nullable|string|max:1000'
        ]);

        $inquiry = InquirySubmissionRecord::findOrFail($inquiryId);
        $agency = Agency::findOrFail($request->agency_id);

        DB::beginTransaction();

        try {
            // Create approval record for agency assignment
            $approval = new Approval();
            $approval->inquiry_ID = $inquiry->inquiry_ID;
            $approval->staff_ID = Auth::guard('mcmc')->id();
            $approval->approval_Status = 'assigned';
            $approval->approval_Comments = $request->comments;
            $approval->approval_Type = 'agency_assignment';
            $approval->approval_Date = now();
            $approval->save();

            // Create assignment record
            $assignment = new InquiryAssignment();
            $assignment->agency_ID = $request->agency_id;
            $assignment->approval_ID = $approval->approval_ID;
            $assignment->assignment_Date = now();
            $assignment->assignment_Status = 'pending';
            $assignment->assignment_Comments = $request->comments;
            $assignment->assigned_By = Auth::guard('mcmc')->id();
            $assignment->save();

            // Update inquiry status
            $inquiry->inquiry_Status = 'assigned_to_agency';
            $inquiry->save();

            // Send notification to user about assignment
            $user = $inquiry->user;
            if ($user) {
                Notification::send($user, new InquiryAssignedNotification($inquiry, $agency, $assignment));
            }

            // Send notification to agency
            Notification::send($agency, new InquiryAssignedNotification($inquiry, $agency, $assignment));

            DB::commit();

            return redirect()->route('mcmc.inquiries.list')
                ->with('success', "Inquiry has been assigned to {$agency->agency_Name}");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to assign inquiry to agency. Please try again.');
        }
    }

    /**
     * Display assignments list for MCMC staff
     */
    public function assignmentsList(Request $request)
    {
        $query = InquiryAssignment::with(['agency', 'approval.inquiry.user', 'assignedByStaff']);

        // Filter by agency
        if ($request->filled('agency_id')) {
            $query->where('agency_ID', $request->agency_id);
        }

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

        $assignments = $query->orderBy('assignment_Date', 'desc')->paginate(15);
        $agencies = Agency::orderBy('agency_Name')->get();

        return view('mcmc.assignments.list', compact('assignments', 'agencies'));
    }

    /**
     * Display assignment reports
     */
    public function assignmentReports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Assignments by agency
        $assignmentsByAgency = InquiryAssignment::select('agency_ID', DB::raw('count(*) as total'))
            ->with('agency')
            ->whereDate('assignment_Date', '>=', $startDate)
            ->whereDate('assignment_Date', '<=', $endDate)
            ->groupBy('agency_ID')
            ->get();

        // Assignments by status
        $assignmentsByStatus = InquiryAssignment::select('assignment_Status', DB::raw('count(*) as total'))
            ->whereDate('assignment_Date', '>=', $startDate)
            ->whereDate('assignment_Date', '<=', $endDate)
            ->groupBy('assignment_Status')
            ->get();

        // Monthly assignment trends (last 12 months)
        $monthlyTrends = InquiryAssignment::select(
            DB::raw('YEAR(assignment_Date) as year'),
            DB::raw('MONTH(assignment_Date) as month'),
            DB::raw('count(*) as total')
        )
            ->where('assignment_Date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Average response time by agency
        $responseTimeByAgency = InquiryAssignment::select('agency_ID')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assignment_Date, completed_At)) as avg_hours')
            ->with('agency')
            ->whereNotNull('completed_At')
            ->whereDate('assignment_Date', '>=', $startDate)
            ->whereDate('assignment_Date', '<=', $endDate)
            ->groupBy('agency_ID')
            ->get();

        return view('mcmc.assignments.reports', compact(
            'assignmentsByAgency',
            'assignmentsByStatus',
            'monthlyTrends',
            'responseTimeByAgency',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display assignments for agency users
     */
    public function agencyAssignments(Request $request)
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

        $assignments = $query->orderBy('assignment_Date', 'desc')->paginate(15);

        return view('agency.assignments.list', compact('assignments'));
    }

    /**
     * Show assignment details for agency
     */
    public function showAssignmentDetails($assignmentId)
    {
        $assignment = InquiryAssignment::with([
            'agency',
            'approval.inquiry.user',
            'assignedByStaff'
        ])->findOrFail($assignmentId);

        // Check if current agency can view this assignment
        if (Auth::guard('agency')->check() && $assignment->agency_ID !== $this->getAgencyId()) {
            abort(403, 'Unauthorized to view this assignment');
        }

        return view('agency.assignments.details', compact('assignment'));
    }

    /**
     * Update assignment status by agency with enhanced review workflow
     */
    public function updateAssignmentStatus(Request $request, $assignmentId)
    {
        try {

            $validator = \Validator::make($request->all(), [
                'status' => 'required|in:in_progress,completed,rejected',
                'comments' => 'nullable|string|max:1000',
                'rejection_reason' => 'required_if:status,rejected|string|max:2000',
                'completion_summary' => 'required_if:status,completed|string|max:2000',
                'review_steps' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $assignment = InquiryAssignment::findOrFail($assignmentId);

            // Check if current agency can update this assignment
            if (Auth::guard('agency')->check() && $assignment->agency_ID !== $this->getAgencyId()) {
                \Log::error('Unauthorized assignment update attempt', [
                    'assignment_id' => $assignmentId,
                    'assignment_agency_id' => $assignment->agency_ID,
                    'current_agency_id' => $this->getAgencyId()
                ]);
                return back()->with('error', 'Unauthorized to update this assignment');
            }

            DB::beginTransaction();
            $assignment->assignment_Status = $request->status;

            // Build comprehensive comments based on status
            $updatedComments = $request->comments ?? '';

            if ($request->status === 'rejected') {
                $assignment->rejection_Reason = $request->rejection_reason;
                $updatedComments = "REJECTED: " . $request->rejection_reason . ($updatedComments ? "\n\nAdditional Comments: " . $updatedComments : '');
            } elseif ($request->status === 'completed') {
                $assignment->completed_At = now();
                $updatedComments = "COMPLETED: " . $request->completion_summary . ($updatedComments ? "\n\nAdditional Comments: " . $updatedComments : '');

                // Add review steps if provided
                if ($request->review_steps && is_array($request->review_steps)) {
                    $reviewSteps = implode(', ', $request->review_steps);
                    $updatedComments .= "\n\nReview Steps Completed: " . $reviewSteps;
                }
            } elseif ($request->status === 'in_progress') {
                $updatedComments = "STARTED REVIEW: " . ($updatedComments ?: 'Review process has been initiated by the agency.');

                // Add review steps if provided
                if ($request->review_steps && is_array($request->review_steps)) {
                    $reviewSteps = implode(', ', $request->review_steps);
                    $updatedComments .= "\n\nReview Steps in Progress: " . $reviewSteps;
                }
            }

            $assignment->assignment_Comments = $updatedComments;
            $assignment->save();

            // Update inquiry status with more detailed statuses
            $inquiry = $assignment->approval->inquiry;
            switch ($request->status) {
                case 'in_progress':
                    $inquiry->inquiry_Status = 'agency_review_in_progress';
                    break;
                case 'completed':
                    $inquiry->inquiry_Status = 'agency_review_completed';
                    break;
                case 'rejected':
                    $inquiry->inquiry_Status = 'agency_rejected';
                    break;
            }
            $inquiry->save();

            // Send enhanced notifications to all stakeholders
            try {
                $user = $inquiry->user;
                $mcmcStaff = $assignment->assignedByStaff;

                // Notify public user
                if ($user) {
                    Notification::send($user, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
                }

                // Notify MCMC staff
                if ($mcmcStaff) {
                    Notification::send($mcmcStaff, new AssignmentStatusUpdatedNotification($assignment, $inquiry));
                }

                // Additional notification for rejections
                if ($request->status === 'rejected' && $mcmcStaff) {
                    Notification::send($mcmcStaff, new AssignmentRejectedNotification($assignment, $inquiry));
                }
            } catch (\Exception $notificationError) {
                // Log notification error but don't fail the whole update
                \Log::warning('Notification failed but assignment updated successfully', [
                    'assignment_id' => $assignmentId,
                    'notification_error' => $notificationError->getMessage()
                ]);
            }

            // Log the status change for audit trail
            \Log::info('Assignment status updated', [
                'assignment_id' => $assignmentId,
                'old_status' => $assignment->getOriginal('assignment_Status'),
                'new_status' => $request->status,
                'agency_id' => $this->getAgencyId(),
                'inquiry_id' => $inquiry->inquiry_ID,
                'user_id' => $user->id ?? null,
                'mcmc_staff_id' => $mcmcStaff->staff_ID ?? null
            ]);

            DB::commit();

            $message = match ($request->status) {
                'in_progress' => 'Assignment accepted and review process started. MCMC staff and public user have been notified.',
                'completed' => 'Assignment completed successfully. Review summary has been sent to MCMC staff and public user.',
                'rejected' => 'Assignment rejected and returned to MCMC for reassignment. All stakeholders have been notified.',
            };

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to update assignment status', [
                'assignment_id' => $assignmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'agency_id' => $this->getAgencyId()
            ]);
            return back()->with('error', 'Failed to update assignment status: ' . $e->getMessage());
        }
    }

    /**
     * Reassign inquiry to different agency
     */
    public function reassignInquiry(Request $request, $assignmentId)
    {
        $request->validate([
            'new_agency_id' => 'required|exists:agencies,agency_ID',
            'reassignment_reason' => 'required|string|max:1000'
        ]);

        $oldAssignment = InquiryAssignment::findOrFail($assignmentId);
        $newAgency = Agency::findOrFail($request->new_agency_id);

        DB::beginTransaction();

        try {
            // Mark old assignment as reassigned
            $oldAssignment->assignment_Status = 'reassigned';
            $oldAssignment->rejection_Reason = $request->reassignment_reason;
            $oldAssignment->save();

            // Create new assignment
            $newAssignment = new InquiryAssignment();
            $newAssignment->agency_ID = $request->new_agency_id;
            $newAssignment->approval_ID = $oldAssignment->approval_ID;
            $newAssignment->assignment_Date = now();
            $newAssignment->assignment_Status = 'pending';
            $newAssignment->assignment_Comments = "Reassigned from {$oldAssignment->agency->agency_Name}. Reason: {$request->reassignment_reason}";
            $newAssignment->assigned_By = Auth::guard('mcmc')->id();
            $newAssignment->save();

            DB::commit();

            return redirect()->route('mcmc.assignments.list')
                ->with('success', "Inquiry has been reassigned to {$newAgency->agency_Name}");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reassign inquiry. Please try again.');
        }
    }

    /**
     * User view - Show inquiry assignment visibility for public users
     */
    public function userInquiryAssignments(Request $request)
    {
        $userId = Auth::guard('web')->id();

        $inquiries = InquirySubmissionRecord::with(['latestAssignment.agency'])
            ->where('user_ID', $userId)
            ->whereHas('assignments')
            ->orderBy('inquiry_Created_At', 'desc')
            ->paginate(10);

        return view('user.assignments.index', compact('inquiries'));
    }

    /**
     * User view - Show specific inquiry assignment details
     */
    public function userInquiryAssignmentDetails($inquiryId)
    {
        $userId = Auth::guard('web')->id();

        $inquiry = InquirySubmissionRecord::with([
            'assignments.agency',
            'assignments.assignedByStaff'
        ])
            ->where('user_ID', $userId)
            ->findOrFail($inquiryId);

        return view('user.assignments.details', compact('inquiry'));
    }

    /**
     * Agency Dashboard
     */
    public function agencyDashboard()
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
            ->limit(5)
            ->get();

        // Overdue assignments (pending for more than 48 hours)
        $overdueCount = InquiryAssignment::where('agency_ID', $agencyId)
            ->where('assignment_Status', 'pending')
            ->where('assignment_Date', '<', now()->subHours(48))
            ->count();

        return view('agency.dashboard', compact('stats', 'recentAssignments', 'overdueCount'));
    }

    /**
     * Agency Jurisdiction Review
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
     * Accept Assignment with Jurisdiction Confirmation
     */
    public function acceptWithJurisdiction(Request $request, $assignmentId)
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
            $assignment->assignment_Status = 'in_progress';
            $assignment->assignment_Comments = "Jurisdiction confirmed. " . ($request->comments ?? '');
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
                ->with('success', 'Assignment accepted and marked as in progress.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to accept assignment. Please try again.');
        }
    }

    /**
     * Reject Assignment with Jurisdiction Reason
     */
    public function rejectWithJurisdiction(Request $request, $assignmentId)
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
            $assignment->assignment_Status = 'rejected';
            $assignment->rejection_Reason = $request->jurisdiction_reason;

            if ($request->suggested_agency) {
                $suggestedAgency = Agency::find($request->suggested_agency);
                $assignment->assignment_Comments = "Jurisdiction rejected. Suggested agency: " . $suggestedAgency->agency_Name;
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
                ->with('success', 'Assignment rejected and returned to MCMC for reassignment.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject assignment. Please try again.');
        }
    }

    /**
     * Enhanced Assignment Reports with Charts
     */
    public function enhancedReports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        $agencyId = $request->input('agency_id');

        // Base query
        $query = InquiryAssignment::query();

        if ($startDate && $endDate) {
            $query->whereDate('assignment_Date', '>=', $startDate)
                ->whereDate('assignment_Date', '<=', $endDate);
        }

        if ($agencyId) {
            $query->where('agency_ID', $agencyId);
        }

        // Assignments by agency with detailed metrics
        $assignmentsByAgency = (clone $query)
            ->select([
                'agency_ID',
                DB::raw('COUNT(*) as total_assignments'),
                DB::raw('SUM(CASE WHEN assignment_Status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN assignment_Status = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN assignment_Status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, assignment_Date, completed_At)) as avg_completion_hours')
            ])
            ->with('agency')
            ->groupBy('agency_ID')
            ->get()
            ->map(function ($item) {
                $item->completion_rate = $item->total_assignments > 0
                    ? round(($item->completed / $item->total_assignments) * 100, 2)
                    : 0;
                $item->avg_completion_hours = round($item->avg_completion_hours ?? 0, 2);
                return $item;
            });

        // Monthly trends with status breakdown
        $monthlyTrends = InquiryAssignment::select([
            DB::raw('YEAR(assignment_Date) as year'),
            DB::raw('MONTH(assignment_Date) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN assignment_Status = "completed" THEN 1 ELSE 0 END) as completed'),
            DB::raw('SUM(CASE WHEN assignment_Status = "rejected" THEN 1 ELSE 0 END) as rejected')
        ])
            ->where('assignment_Date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::create()->month($item->month)->format('F Y');
                $item->completion_rate = $item->total > 0 ? round(($item->completed / $item->total) * 100, 2) : 0;
                return $item;
            });

        // Daily assignment distribution (last 30 days)
        $dailyDistribution = InquiryAssignment::select([
            DB::raw('DATE(assignment_Date) as date'),
            DB::raw('COUNT(*) as total')
        ])
            ->where('assignment_Date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Response time analysis
        $responseTimeAnalysis = InquiryAssignment::select([
            'agency_ID',
            DB::raw('AVG(TIMESTAMPDIFF(HOUR, assignment_Date, completed_At)) as avg_hours'),
            DB::raw('MIN(TIMESTAMPDIFF(HOUR, assignment_Date, completed_At)) as min_hours'),
            DB::raw('MAX(TIMESTAMPDIFF(HOUR, assignment_Date, completed_At)) as max_hours'),
            DB::raw('COUNT(*) as total_completed')
        ])
            ->with('agency')
            ->whereNotNull('completed_At')
            ->where('assignment_Date', '>=', now()->subMonths(3))
            ->groupBy('agency_ID')
            ->get();

        $agencies = Agency::orderBy('agency_Name')->get();

        return view('mcmc.reports.enhanced-assignments', compact(
            'assignmentsByAgency',
            'monthlyTrends',
            'dailyDistribution',
            'responseTimeAnalysis',
            'agencies',
            'startDate',
            'endDate',
            'agencyId'
        ));
    }

    /**
     * Export Enhanced Reports to PDF
     */
    public function exportReportsPDF(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        $agencyId = $request->input('agency_id');

        $query = InquiryAssignment::with(['agency', 'approval.inquiry.user', 'assignedByStaff']);

        if ($startDate && $endDate) {
            $query->whereDate('assignment_Date', '>=', $startDate)
                ->whereDate('assignment_Date', '<=', $endDate);
        }

        if ($agencyId) {
            $query->where('agency_ID', $agencyId);
        }

        $assignments = $query->orderBy('assignment_Date', 'desc')->get();

        // Summary statistics
        $stats = [
            'total_assignments' => $assignments->count(),
            'completed_assignments' => $assignments->where('assignment_Status', 'completed')->count(),
            'pending_assignments' => $assignments->where('assignment_Status', 'pending')->count(),
            'rejected_assignments' => $assignments->where('assignment_Status', 'rejected')->count(),
            'avg_completion_time' => $assignments->whereNotNull('completed_At')
                ->avg(function ($assignment) {
                    return Carbon::parse($assignment->completed_At)
                        ->diffInHours(Carbon::parse($assignment->assignment_Date));
                })
        ];

        $stats['completion_rate'] = $stats['total_assignments'] > 0
            ? round(($stats['completed_assignments'] / $stats['total_assignments']) * 100, 2)
            : 0;

        $stats['avg_completion_time'] = round($stats['avg_completion_time'] ?? 0, 2);

        $pdf = PDF::loadView('mcmc.reports.pdf.assignments', compact(
            'assignments',
            'stats',
            'startDate',
            'endDate'
        ));

        $filename = 'assignment_reports_' . Carbon::parse($startDate)->format('Y-m-d') .
            '_to_' . Carbon::parse($endDate)->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Reports to Excel
     */
    public function exportReportsExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        $agencyId = $request->input('agency_id');

        $filename = 'assignment_reports_' . Carbon::parse($startDate)->format('Y-m-d') .
            '_to_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';

        return Excel::download(new AssignmentReportsExport($startDate, $endDate, $agencyId), $filename);
    }

    /**
     * Bulk Assignment Operations
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'inquiry_ids' => 'required|array',
            'inquiry_ids.*' => 'exists:inquiries,inquiry_ID',
            'agency_id' => 'required|exists:agencies,agency_ID',
            'comments' => 'nullable|string|max:1000'
        ]);

        $agency = Agency::findOrFail($request->agency_id);
        $assignedCount = 0;
        $failedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($request->inquiry_ids as $inquiryId) {
                try {
                    $inquiry = InquirySubmissionRecord::findOrFail($inquiryId);

                    // Check if inquiry can be assigned
                    if (!in_array($inquiry->inquiry_Status, ['submitted', 'under_review'])) {
                        $failedCount++;
                        continue;
                    }

                    // Create approval record
                    $approval = new Approval();
                    $approval->inquiry_ID = $inquiry->inquiry_ID;
                    $approval->staff_ID = Auth::guard('mcmc')->id();
                    $approval->approval_Status = 'assigned';
                    $approval->approval_Comments = $request->comments;
                    $approval->approval_Type = 'agency_assignment';
                    $approval->approval_Date = now();
                    $approval->save();

                    // Create assignment record
                    $assignment = new InquiryAssignment();
                    $assignment->agency_ID = $request->agency_id;
                    $assignment->approval_ID = $approval->approval_ID;
                    $assignment->assignment_Date = now();
                    $assignment->assignment_Status = 'pending';
                    $assignment->assignment_Comments = $request->comments;
                    $assignment->assigned_By = Auth::guard('mcmc')->id();
                    $assignment->save();

                    // Update inquiry status
                    $inquiry->inquiry_Status = 'assigned_to_agency';
                    $inquiry->save();

                    // Send notifications
                    $user = $inquiry->user;
                    if ($user) {
                        Notification::send($user, new InquiryAssignedNotification($inquiry, $agency, $assignment));
                    }

                    $assignedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    continue;
                }
            }

            // Send notification to agency about bulk assignment
            Notification::send($agency, new InquiryAssignedNotification(null, $agency, null, $assignedCount));

            DB::commit();

            $message = "Successfully assigned {$assignedCount} inquiries to {$agency->agency_Name}";
            if ($failedCount > 0) {
                $message .= ". {$failedCount} inquiries could not be assigned.";
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to perform bulk assignment. Please try again.');
        }
    }

    /**
     * Get agency ID from authenticated user
     */
    private function getAgencyId()
    {
        $user = Auth::guard('agency')->user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // For agency users, get the agency_ID from the user record
        if ($user->user_type === 'agency' && $user->agency_ID) {
            return $user->agency_ID;
        }

        // Fallback: try to find agency by email
        $agency = Agency::where('agency_Email', $user->email)->first();
        if ($agency) {
            return $agency->agency_ID;
        }

        abort(403, 'Agency not found');
    }
}
