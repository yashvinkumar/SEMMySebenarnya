<?php

namespace App\Http\Controllers;

use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\InquiryAssignment;
use App\Models\InquiryProgress;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class McmcSimpleInquiryController extends Controller
{
    /**
     * Display a listing of inquiries for MCMC staff
     */
    public function index(Request $request)
    {
        $query = InquirySubmissionRecord::with([
            'user',
            'assignments.agency',
            'assignments.assignedByStaff',
            'progressRecords.agency',
            'progressRecords.staff',
            'latestProgress'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('inquiry_Status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('inquiry_Category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inquiry_Title', 'like', "%{$search}%")
                    ->orWhere('inquiry_Description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('inquiry_Created_At', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('inquiry_Created_At', '<=', $request->end_date);
        }

        $inquiries = $query->orderBy('inquiry_Created_At', 'desc')->paginate(15);

        // Transform inquiries to include latest assignment info
        $inquiries->getCollection()->transform(function ($inquiry) {
            $inquiry->latestAssignment = $inquiry->assignments->first();
            return $inquiry;
        });

        $agencies = Agency::orderBy('agency_Name')->get();

        // Define filter options
        $filters = [
            'statuses' => [
                'submitted' => 'Submitted',
                'under_review' => 'Under Review',
                'assigned_to_agency' => 'Assigned to Agency',
                'agency_review_in_progress' => 'Agency Review in Progress',
                'agency_review_completed' => 'Agency Review Completed',
                'agency_rejected' => 'Agency Rejected',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'closed' => 'Closed'
            ],
            'categories' => [
                'broadcasting' => 'Broadcasting',
                'telecommunications' => 'Telecommunications',
                'internet' => 'Internet Services',
                'multimedia' => 'Multimedia Content',
                'technical' => 'Technical Issues',
                'complaint' => 'Complaint',
                'other' => 'Other'
            ],
            'assignment_statuses' => [
                '' => 'All',
                'assigned' => 'Assigned',
                'unassigned' => 'Unassigned',
                'completed' => 'Completed'
            ]
        ];

        return view('mcmc.inquiries.index', compact('inquiries', 'agencies', 'filters'));
    }

    /**
     * Display the assignment page for MCMC staff
     */
    public function assignInquiriesPage(Request $request)
    {
        try {
            // Test database connection
            DB::connection()->getPdo();

            // Get inquiries that can be assigned (not currently assigned to any agency)
            $query = InquirySubmissionRecord::with([
                'user',
                'assignments.agency',
                'assignments.assignedByStaff',
                'progressRecords',
                'latestProgress'
            ])->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('assignment_Status', ['pending', 'in_progress']);
            });

            // Apply filters
            if ($request->filled('category')) {
                $query->where('inquiry_Category', $request->category);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('inquiry_Title', 'like', "%{$search}%")
                        ->orWhere('inquiry_Description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('start_date')) {
                $query->whereDate('inquiry_Created_At', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('inquiry_Created_At', '<=', $request->end_date);
            }

            $unassignedInquiries = $query->orderBy('inquiry_Created_At', 'desc')->paginate(15);

            // Get all active agencies for assignment
            $agencies = Agency::orderBy('agency_Name')->get();

            // Get already assigned inquiries for reference
            $assignedInquiries = InquirySubmissionRecord::with(['assignments.agency'])
                ->whereHas('assignments', function ($q) {
                    $q->whereIn('assignment_Status', ['pending', 'in_progress']);
                })
                ->orderBy('inquiry_Created_At', 'desc')
                ->limit(10)
                ->get();

            // Calculate assignment statistics
            $stats = [
                'total_unassigned' => InquirySubmissionRecord::whereDoesntHave('assignments', function ($q) {
                    $q->whereIn('assignment_Status', ['pending', 'in_progress']);
                })->count(),
                'total_assigned' => InquirySubmissionRecord::whereHas('assignments', function ($q) {
                    $q->whereIn('assignment_Status', ['pending', 'in_progress']);
                })->count(),
                'total_completed' => InquirySubmissionRecord::whereHas('assignments', function ($q) {
                    $q->where('assignment_Status', 'completed');
                })->count(),
                'total_agencies' => Agency::count()
            ];

            // Define filter options
            $filters = [
                'categories' => [
                    'broadcasting' => 'Broadcasting',
                    'telecommunications' => 'Telecommunications',
                    'internet' => 'Internet Services',
                    'multimedia' => 'Multimedia Content',
                    'technical' => 'Technical Issues',
                    'complaint' => 'Complaint',
                    'other' => 'Other'
                ]
            ];

            return view('mcmc.inquiries.assign-inquiries', compact(
                'unassignedInquiries',
                'assignedInquiries',
                'agencies',
                'stats',
                'filters'
            ));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Database connection error in assignInquiriesPage: ' . $e->getMessage());

            // Return error view or redirect with error message
            return redirect()->back()->with('error', 'Database connection failed. Please check your database configuration.');
        }
    }

    /**
     * Display the specified inquiry
     */
    public function show($id)
    {
        $inquiry = InquirySubmissionRecord::with([
            'user',
            'assignments.agency',
            'assignments.assignedByStaff'
        ])->findOrFail($id);

        $agencies = Agency::orderBy('agency_Name')->get();
        $assignmentHistory = $inquiry->assignments()
            ->with(['agency', 'assignedByStaff'])
            ->orderBy('assignment_Date', 'desc')
            ->get();

        return view('mcmc.inquiries.show', compact('inquiry', 'agencies', 'assignmentHistory'));
    }

    /**
     * Show the form for assigning inquiry to agency
     */
    public function assignForm($id)
    {
        $inquiry = InquirySubmissionRecord::with('user')->findOrFail($id);

        $agencies = Agency::orderBy('agency_Name')->get();
        $existingAssignment = $inquiry->assignments()
            ->whereIn('assignment_Status', ['pending', 'in_progress'])
            ->with('agency')
            ->latest('assignment_Date')
            ->first();

        return view('mcmc.inquiries.assign', compact('inquiry', 'agencies', 'existingAssignment'));
    }

    /**
     * Assign inquiry to agency
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'agency_id' => 'required|exists:agencies,agency_ID',
            'comments' => 'nullable|string|max:1000'
        ]);

        $inquiry = InquirySubmissionRecord::findOrFail($id);
        $agency = Agency::findOrFail($request->agency_id);

        DB::beginTransaction();

        try {
            // Create approval record
            $approval = new Approval();
            $approval->inquiry_ID = $inquiry->inquiry_ID;
            $approval->staff_ID = Auth::guard('mcmc')->id();
            $approval->approval_Status = 'assigned';
            $approval->approval_Comments = $request->comments ?? '';
            $approval->approval_Type = 'agency_assignment';
            $approval->approval_Date = now();
            $approval->save();

            // Create assignment record
            $assignment = new InquiryAssignment();
            $assignment->agency_ID = $agency->agency_ID;
            $assignment->approval_ID = $approval->approval_ID;
            $assignment->assignment_Date = now();
            $assignment->assignment_Status = 'pending';
            $assignment->assignment_Comments = $request->comments ?? '';
            $assignment->assigned_By = Auth::guard('mcmc')->id();
            $assignment->save();

            // Update inquiry status
            $inquiry->inquiry_Status = 'assigned_to_agency';
            $inquiry->save();

            DB::commit();

            return redirect()
                ->route('mcmc.inquiries.list')
                ->with('success', "Inquiry has been successfully assigned to {$agency->agency_Name}");
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->with('error', 'Failed to assign inquiry to agency. Please try again.')
                ->withInput();
        }
    }

    /**
     * Bulk assign multiple inquiries to agencies
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'inquiry_ids' => 'required|array|min:1',
            'inquiry_ids.*' => 'exists:inquiries,inquiry_ID',
            'agency_id' => 'required|exists:agencies,agency_ID',
            'comments' => 'nullable|string|max:1000'
        ]);

        $inquiries = InquirySubmissionRecord::whereIn('inquiry_ID', $request->inquiry_ids)->get();
        $agency = Agency::findOrFail($request->agency_id);

        $successCount = 0;
        $errorCount = 0;

        foreach ($inquiries as $inquiry) {
            DB::beginTransaction();

            try {
                // Create approval record
                $approval = new Approval();
                $approval->inquiry_ID = $inquiry->inquiry_ID;
                $approval->staff_ID = Auth::guard('mcmc')->id();
                $approval->approval_Status = 'assigned';
                $approval->approval_Comments = $request->comments ?? '';
                $approval->approval_Type = 'agency_assignment';
                $approval->approval_Date = now();
                $approval->save();

                // Create assignment record
                $assignment = new InquiryAssignment();
                $assignment->agency_ID = $agency->agency_ID;
                $assignment->approval_ID = $approval->approval_ID;
                $assignment->assignment_Date = now();
                $assignment->assignment_Status = 'pending';
                $assignment->assignment_Comments = $request->comments ?? '';
                $assignment->assigned_By = Auth::guard('mcmc')->id();
                $assignment->save();

                // Update inquiry status
                $inquiry->inquiry_Status = 'assigned_to_agency';
                $inquiry->save();

                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollback();
                $errorCount++;
            }
        }

        $message = "Bulk assignment completed: {$successCount} inquiries assigned successfully";

        if ($errorCount > 0) {
            $message .= ", {$errorCount} failed";
        }

        $messageType = $errorCount === 0 ? 'success' : 'warning';

        return redirect()
            ->route('mcmc.inquiries.list')
            ->with($messageType, $message);
    }

    /**
     * Update inquiry status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,under_review,approved,rejected,closed',
            'comments' => 'nullable|string|max:1000'
        ]);

        $inquiry = InquirySubmissionRecord::findOrFail($id);

        DB::beginTransaction();

        try {
            $inquiry->inquiry_Status = $request->status;
            $inquiry->save();

            // Create approval record for status change
            if ($request->comments) {
                $approval = new Approval();
                $approval->inquiry_ID = $inquiry->inquiry_ID;
                $approval->staff_ID = Auth::guard('mcmc')->id();
                $approval->approval_Status = $request->status;
                $approval->approval_Comments = $request->comments;
                $approval->approval_Type = 'status_update';
                $approval->approval_Date = now();
                $approval->save();
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
}
