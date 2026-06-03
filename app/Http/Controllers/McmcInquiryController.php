<?php

namespace App\Http\Controllers;

use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\InquiryAssignment;
use App\Models\Approval;
use App\Models\McmcStaff;
use App\Http\Requests\AssignInquiryRequest;
use App\Http\Requests\BulkAssignInquiryRequest;
use App\Services\InquiryAssignmentService;
use App\Notifications\InquiryAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class McmcInquiryController extends Controller
{
    protected $assignmentService;

    public function __construct(InquiryAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }
    /**
     * Display a listing of inquiries for MCMC staff
     */
    public function index(Request $request)
    {
        $query = InquirySubmissionRecord::with(['user', 'assignments.agency']);

        // Apply filters
        $this->applyFilters($query, $request);

        $inquiries = $query->orderBy('inquiry_Created_At', 'desc')->paginate(15);

        // Transform inquiries to include latest assignment info
        $inquiries->getCollection()->transform(function ($inquiry) {
            $inquiry->latestAssignment = $inquiry->assignments->first();
            return $inquiry;
        });

        $agencies = Agency::orderBy('agency_Name')->get();
        $filters = $this->getAvailableFilters();

        return view('mcmc.inquiries.index', compact('inquiries', 'agencies', 'filters'));
    }

    /**
     * Display the specified inquiry
     */
    public function show($id)
    {
        $inquiry = InquirySubmissionRecord::with([
            'user',
            'assignments.agency',
            'assignments.assignedByStaff',
            'approvals.staff'
        ])->findOrFail($id);

        $agencies = Agency::orderBy('agency_Name')->get();
        $assignmentHistory = $this->getAssignmentHistory($inquiry);

        return view('mcmc.inquiries.show', compact('inquiry', 'agencies', 'assignmentHistory'));
    }

    /**
     * Show the form for assigning inquiry to agency
     */
    public function assignForm($id)
    {
        $inquiry = InquirySubmissionRecord::with('user')->findOrFail($id);

        // Check if inquiry can be assigned
        if (!$this->canBeAssigned($inquiry)) {
            return redirect()
                ->route('mcmc.inquiries.index')
                ->with('error', 'This inquiry cannot be assigned in its current state.');
        }

        $agencies = Agency::active()->orderBy('agency_Name')->get();
        $existingAssignment = $this->getCurrentAssignment($inquiry);

        return view('mcmc.inquiries.assign', compact('inquiry', 'agencies', 'existingAssignment'));
    }

    /**
     * Assign inquiry to agency
     */
    public function assign(AssignInquiryRequest $request, $id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        DB::beginTransaction();

        try {
            $assignment = $this->createAssignment($inquiry, $request);
            $this->updateInquiryStatus($inquiry, 'assigned_to_agency');
            $this->sendAssignmentNotifications($inquiry, $assignment);

            DB::commit();

            return redirect()
                ->route('mcmc.inquiries.index')
                ->with('success', "Inquiry has been successfully assigned to {$assignment->agency->agency_Name}");

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->with('error', 'Failed to assign inquiry to agency. Please try again.')
                ->withInput();
        }
    }

    /**
     * Reassign inquiry to different agency
     */
    public function reassign(AssignInquiryRequest $request, $id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        DB::beginTransaction();

        try {
            // Mark current assignment as completed/cancelled
            $currentAssignment = $this->getCurrentAssignment($inquiry);
            if ($currentAssignment) {
                $currentAssignment->update([
                    'assignment_Status' => 'reassigned',
                    'assignment_Comments' => $request->reassignment_reason
                ]);
            }

            // Create new assignment
            $newAssignment = $this->createAssignment($inquiry, $request);
            $this->sendReassignmentNotifications($inquiry, $currentAssignment, $newAssignment);

            DB::commit();

            return redirect()
                ->route('mcmc.inquiries.show', $inquiry->inquiry_ID)
                ->with('success', "Inquiry has been reassigned to {$newAssignment->agency->agency_Name}");

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->with('error', 'Failed to reassign inquiry. Please try again.')
                ->withInput();
        }
    }

    /**
     * Bulk assign multiple inquiries to agencies
     */
    public function bulkAssign(BulkAssignInquiryRequest $request)
    {

        $inquiries = InquirySubmissionRecord::whereIn('inquiry_ID', $request->inquiry_ids)->get();
        $agency = Agency::findOrFail($request->agency_id);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($inquiries as $inquiry) {
            if (!$this->canBeAssigned($inquiry)) {
                $errorCount++;
                $errors[] = "Inquiry #{$inquiry->inquiry_ID} cannot be assigned in its current state";
                continue;
            }

            DB::beginTransaction();

            try {
                $assignmentRequest = new Request([
                    'agency_id' => $request->agency_id,
                    'comments' => $request->comments
                ]);

                $assignment = $this->createAssignment($inquiry, $assignmentRequest);
                $this->updateInquiryStatus($inquiry, 'assigned_to_agency');
                $this->sendAssignmentNotifications($inquiry, $assignment);

                DB::commit();
                $successCount++;

            } catch (\Exception $e) {
                DB::rollback();
                $errorCount++;
                $errors[] = "Failed to assign inquiry #{$inquiry->inquiry_ID}";
            }
        }

        $message = "Bulk assignment completed: {$successCount} inquiries assigned successfully";

        if ($errorCount > 0) {
            $message .= ", {$errorCount} failed";
        }

        $messageType = $errorCount === 0 ? 'success' : 'warning';

        return redirect()
            ->route('mcmc.inquiries.index')
            ->with($messageType, $message)
            ->with('bulk_errors', $errors);
    }

    /**
     * Update inquiry status
     */
    public function updateStatus(Request $request, $id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'under_review', 'approved', 'rejected', 'closed'])
            ],
            'comments' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $this->updateInquiryStatus($inquiry, $request->status);

            // Create approval record for status change
            if ($request->comments) {
                $this->createApprovalRecord($inquiry, $request->status, $request->comments, 'status_update');
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Inquiry status updated successfully');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->with('error', 'Failed to update inquiry status. Please try again.');
        }
    }

    /**
     * Apply filters to inquiry query
     */
    private function applyFilters($query, Request $request)
    {
        // Status filter
        if ($request->filled('status')) {
            $query->where('inquiry_Status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('inquiry_Category', $request->category);
        }

        // Assignment status filter
        if ($request->filled('assignment_filter')) {
            switch ($request->assignment_filter) {
                case 'assigned':
                    $query->whereHas('assignments', function($q) {
                        $q->whereIn('assignment_Status', ['pending', 'in_progress']);
                    });
                    break;
                case 'unassigned':
                    $query->whereDoesntHave('assignments');
                    break;
                case 'completed':
                    $query->whereHas('assignments', function($q) {
                        $q->where('assignment_Status', 'completed');
                    });
                    break;
            }
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('inquiry_Created_At', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('inquiry_Created_At', '<=', $request->end_date);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inquiry_Title', 'like', "%{$search}%")
                  ->orWhere('inquiry_Description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Agency filter
        if ($request->filled('agency_id')) {
            $query->whereHas('assignments', function($q) use ($request) {
                $q->where('agency_ID', $request->agency_id);
            });
        }
    }

    /**
     * Get available filter options
     */
    private function getAvailableFilters()
    {
        return [
            'statuses' => InquirySubmissionRecord::STATUSES,
            'categories' => InquirySubmissionRecord::INQUIRY_TYPES,
            'assignment_statuses' => [
                '' => 'All',
                'assigned' => 'Assigned',
                'unassigned' => 'Unassigned',
                'completed' => 'Completed'
            ]
        ];
    }

    /**
     * Get assignment history for an inquiry
     */
    private function getAssignmentHistory($inquiry)
    {
        return $inquiry->assignments()
            ->with(['agency', 'assignedByStaff'])
            ->orderBy('assignment_Date', 'desc')
            ->get();
    }

    /**
     * Check if inquiry can be assigned
     */
    private function canBeAssigned($inquiry)
    {
        $allowedStatuses = ['pending', 'under_review', 'assigned_to_agency'];
        return in_array($inquiry->inquiry_Status, $allowedStatuses);
    }

    /**
     * Get current active assignment for inquiry
     */
    private function getCurrentAssignment($inquiry)
    {
        return $inquiry->assignments()
            ->whereIn('assignment_Status', ['pending', 'in_progress'])
            ->with('agency')
            ->latest('assignment_Date')
            ->first();
    }

    /**
     * Validate assignment request
     */
    private function validateAssignmentRequest(Request $request, $inquiry)
    {
        $rules = [
            'agency_id' => 'required|exists:agencies,agency_ID',
            'comments' => 'nullable|string|max:1000'
        ];

        // Additional validation for reassignment
        if ($this->getCurrentAssignment($inquiry)) {
            $rules['reassignment_reason'] = 'required|string|max:1000';
        }

        return Validator::make($request->all(), $rules);
    }

    /**
     * Validate reassignment request
     */
    private function validateReassignmentRequest(Request $request, $inquiry)
    {
        return Validator::make($request->all(), [
            'agency_id' => 'required|exists:agencies,agency_ID',
            'comments' => 'nullable|string|max:1000',
            'reassignment_reason' => 'required|string|max:1000'
        ]);
    }

    /**
     * Create assignment record
     */
    private function createAssignment($inquiry, Request $request)
    {
        // Create approval record first
        $approval = $this->createApprovalRecord(
            $inquiry,
            'assigned',
            $request->comments,
            'agency_assignment'
        );

        // Create assignment
        $assignment = new InquiryAssignment();
        $assignment->agency_ID = $request->agency_id;
        $assignment->approval_ID = $approval->approval_ID;
        $assignment->assignment_Date = now();
        $assignment->assignment_Status = 'pending';
        $assignment->assignment_Comments = $request->comments;
        $assignment->assigned_By = Auth::guard('mcmc')->id();
        $assignment->save();

        // Load agency relationship
        $assignment->load('agency');

        return $assignment;
    }

    /**
     * Create approval record
     */
    private function createApprovalRecord($inquiry, $status, $comments, $type)
    {
        $approval = new Approval();
        $approval->inquiry_ID = $inquiry->inquiry_ID;
        $approval->staff_ID = Auth::guard('mcmc')->id();
        $approval->approval_Status = $status;
        $approval->approval_Comments = $comments;
        $approval->approval_Type = $type;
        $approval->approval_Date = now();
        $approval->save();

        return $approval;
    }

    /**
     * Update inquiry status
     */
    private function updateInquiryStatus($inquiry, $status)
    {
        $inquiry->inquiry_Status = $status;
        $inquiry->save();
    }

    /**
     * Send assignment notifications
     */
    private function sendAssignmentNotifications($inquiry, $assignment)
    {
        // Notify user
        if ($inquiry->user) {
            Notification::send($inquiry->user, new InquiryAssignedNotification($inquiry, $assignment->agency, $assignment));
        }

        // Notify agency
        Notification::send($assignment->agency, new InquiryAssignedNotification($inquiry, $assignment->agency, $assignment));
    }

    /**
     * Send reassignment notifications
     */
    private function sendReassignmentNotifications($inquiry, $oldAssignment, $newAssignment)
    {
        // Notify user about reassignment
        if ($inquiry->user) {
            Notification::send($inquiry->user, new InquiryAssignedNotification($inquiry, $newAssignment->agency, $newAssignment));
        }

        // Notify new agency
        Notification::send($newAssignment->agency, new InquiryAssignedNotification($inquiry, $newAssignment->agency, $newAssignment));

        // Optionally notify old agency (if different)
        if ($oldAssignment && $oldAssignment->agency_ID !== $newAssignment->agency_ID) {
            // Send reassignment notification to old agency
        }
    }
}
