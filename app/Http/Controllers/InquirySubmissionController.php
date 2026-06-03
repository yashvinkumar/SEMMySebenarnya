<?php

namespace App\Http\Controllers;

use App\Models\InquirySubmissionRecord;
use App\Models\UserRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class InquirySubmissionController extends Controller
{
    /**
     * Get the currently authenticated user from any guard.
     *
     * @return UserRecord|null
     */
    private function getCurrentUser(): ?UserRecord
    {
        $guards = ['public', 'mcmc', 'agency'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }

        return null;
    }

    /**
     * Get the currently authenticated user ID from any guard.
     *
     * @return int|null
     */
    private function getCurrentUserId(): ?int
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return null;
        }

        // For PublicUser model, use user_ID; for UserRecord model, use id
        return $user->user_ID ?? $user->id ?? null;
    }

    /**
     * Check if the current user can modify the inquiry.
     *
     * @param Inquiry $inquiry
     * @return bool
     */
    private function canModifyInquiry(InquirySubmissionRecord $inquiry): bool
    {
        $user = $this->getCurrentUser();
        $userId = $this->getCurrentUserId();

        if (!$user || !$userId) {
            return false;
        }

        // Allow if user is the owner OR has MCMC role
        return $inquiry->user_ID === $userId || $user->user_type === 'mcmc';
    }

    /**
     * Display a listing of inquiries.
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        $userId = $this->getCurrentUserId();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your inquiries.');
        }

        $query = InquirySubmissionRecord::with([
            'user',
            'assignments.agency', // Load assignments with agency information
            'assignments' => function ($query) {
                $query->whereIn('assignment_Status', ['pending', 'in_progress', 'completed'])
                    ->latest('assignment_Date');
            }
        ]);

        // Check if user wants to view all public inquiries
        $viewAllPublic = $request->input('view_scope') === 'all_public';
        $showPersonalInfo = true;

        // For public users, show only their own inquiries by default
        // For MCMC users, show all inquiries
        // Add option to view all public inquiries without personal info
        if ($user->user_type === 'public' || $user->user_type === 'agency') {
            if ($viewAllPublic) {
                // Show all inquiries (all are from public users) but hide personal info
                // No additional filtering needed since all inquiries are from public_users table
                $showPersonalInfo = false;
            } else {
                // Show only user's own inquiries
                $query->where('user_ID', $userId);
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('inquiry_Title', 'like', "%$search%")
                    ->orWhere('inquiry_Description', 'like', "%$search%");
            });
        }
        if ($request->filled('status')) {
            $query->where('inquiry_Status', $request->input('status'));
        }
        if ($request->filled('category')) {
            $query->where('inquiry_Category', $request->input('category'));
        }

        $inquiries = $query->orderBy('inquiry_Created_At', 'desc')->paginate(10)->appends($request->all());

        // Get counts for stats cards (without pagination)
        $baseQuery = InquirySubmissionRecord::query();

        // Apply same user filter for stats
        if ($user->user_type === 'public' || $user->user_type === 'agency') {
            if ($viewAllPublic) {
                // Show stats for all inquiries (all are from public users)
                // No additional filtering needed
            } else {
                $baseQuery->where('user_ID', $userId);
            }
        }

        // Apply same search/filter criteria to stats
        if ($request->filled('search')) {
            $search = $request->input('search');
            $baseQuery->where(function ($q) use ($search) {
                $q->where('inquiry_Title', 'like', "%$search%")
                    ->orWhere('inquiry_Description', 'like', "%$search%");
            });
        }
        if ($request->filled('status')) {
            $baseQuery->where('inquiry_Status', $request->input('status'));
        }
        if ($request->filled('category')) {
            $baseQuery->where('inquiry_Category', $request->input('category'));
        }

        $totalCount = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->where('inquiry_Status', 'pending')->count();
        $inProgressCount = (clone $baseQuery)->where('inquiry_Status', 'in_progress')->count();
        $completedCount = (clone $baseQuery)->where('inquiry_Status', 'completed')->count();
        $rejectedCount = (clone $baseQuery)->where('inquiry_Status', 'rejected')->count();



        return view('Inquiry Submission.Public User.SubmissionHistory', compact(
            'inquiries',
            'totalCount',
            'pendingCount',
            'inProgressCount',
            'completedCount',
            'rejectedCount',
            'showPersonalInfo',
            'viewAllPublic'
        ));
    }

    /**
     * Show the form for creating a new inquiry.
     */
    public function create()
    {
        return view('Inquiry Submission.Public User.AddInquirySubmission');
    }

    /**
     * Store a newly created inquiry in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'inquiry_Title' => 'required|string|max:255',
            'inquiry_Description' => 'required|string',
            'inquiry_Category' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentUrl = $file->storeAs('inquiries', $filename, 'public');
        }

        $inquiry = InquirySubmissionRecord::create([
            'user_ID' => $this->getCurrentUserId(),
            'inquiry_Title' => $request->inquiry_Title,
            'inquiry_Description' => $request->inquiry_Description,
            'inquiry_Category' => $request->inquiry_Category,
            'inquiry_Attachment_URL' => $attachmentUrl,
            'inquiry_Status' => 'pending',
            'inquiry_Created_At' => now(),
        ]);

        return redirect()->route('inquiry.history')->with('success', 'Inquiry submitted successfully!');
    }

    /**
     * Display the specified inquiry.
     */
    public function show($id)
    {
        // Details are now shown in SubmissionHistory.blade.php modal
        return redirect()->route('inquiry.history');
    }

    /**
     * Show the form for editing the specified inquiry.
     */
    public function edit($id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        // Check if user can edit this inquiry
        if (!$this->canModifyInquiry($inquiry)) {
            abort(403, 'Unauthorized action.');
        }

        return view('Inquiry Submission.Public User.EditInquirySubmission', compact('inquiry'));
    }

    /**
     * Update the specified inquiry in storage.
     */
    public function update(Request $request, $id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        // Check if user can update this inquiry
        if (!$this->canModifyInquiry($inquiry)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'inquiry_Title' => 'required|string|max:255',
            'inquiry_Description' => 'required|string',
            'inquiry_Category' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $attachmentUrl = $inquiry->inquiry_Attachment_URL;
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($attachmentUrl) {
                Storage::disk('public')->delete($attachmentUrl);
            }

            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentUrl = $file->storeAs('inquiries', $filename, 'public');
        }

        $inquiry->update([
            'inquiry_Title' => $request->inquiry_Title,
            'inquiry_Description' => $request->inquiry_Description,
            'inquiry_Category' => $request->inquiry_Category,
            'inquiry_Attachment_URL' => $attachmentUrl,
        ]);

        return redirect()->route('inquiry.history')->with('success', 'Inquiry updated successfully!');
    }

    /**
     * Show the form for confirming deletion.
     */
    public function delete($id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        // Check if user can delete this inquiry
        if (!$this->canModifyInquiry($inquiry)) {
            abort(403, 'Unauthorized action.');
        }

        return view('Inquiry Submission.Public User.DeleteInquirySubmission', compact('inquiry'));
    }

    /**
     * Remove the specified inquiry from storage.
     */
    public function destroy($id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        // Check if user can delete this inquiry
        if (!$this->canModifyInquiry($inquiry)) {
            abort(403, 'Unauthorized action.');
        }

        // Delete attachment if exists
        if ($inquiry->inquiry_Attachment_URL) {
            Storage::disk('public')->delete($inquiry->inquiry_Attachment_URL);
        }

        $inquiry->delete();

        return redirect()->route('inquiry.history')->with('success', 'Inquiry deleted successfully!');
    }

    /**
     * Download inquiry attachment.
     */
    public function downloadAttachment($id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        // Check authorization - user can download if they own the inquiry or are MCMC/agency staff
        if (Auth::user()->user_type === 'public' && $inquiry->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to download this attachment.');
        }

        if (!$inquiry->inquiry_Attachment_URL) {
            abort(404, 'Attachment not found.');
        }

        $filePath = storage_path('app/public/' . $inquiry->inquiry_Attachment_URL);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        // Get original filename for better download experience
        $originalName = basename($inquiry->inquiry_Attachment_URL);

        return Response::download($filePath, $originalName, [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => 'attachment; filename="' . $originalName . '"'
        ]);
    }

    /**
     * Display inquiries for MCMC staff.
     */
    public function mcmcInquiryList(Request $request)
    {
        $query = InquirySubmissionRecord::with(['user', 'assignments.agency']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('inquiry_Title', 'like', "%$search%")
                    ->orWhere('inquiry_Description', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('inquiry_Status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('inquiry_Category', $request->input('category'));
        }

        // Date filtering
        if ($request->filled('start_date')) {
            $query->whereDate('inquiry_Created_At', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('inquiry_Created_At', '<=', $request->input('end_date'));
        }

        // Agency filtering
        if ($request->filled('agency')) {
            $agencyId = $request->input('agency');
            $query->whereHas('assignments', function ($q) use ($agencyId) {
                $q->where('agency_ID', $agencyId);
            });
        }

        $inquiries = $query->orderBy('inquiry_Created_At', 'desc')->paginate(15)->appends($request->all());

        // Get all agencies for the filter dropdown
        $agencies = \App\Models\Agency::orderBy('agency_Name')->get();

        return view('Inquiry Submission.MCMC.InquiryList', compact('inquiries', 'agencies'));
    }

    /**
     * Show approval form for MCMC staff.
     */
    public function showApprovalForm($id)
    {
        $inquiry = InquirySubmissionRecord::with('user')->findOrFail($id);
        return view('Inquiry Submission.MCMC.InquiryApproval', compact('inquiry'));
    }

    /**
     * Approve inquiry by MCMC staff.
     */
    public function approveInquiry(Request $request, $id)
    {
        $inquiry = InquirySubmissionRecord::findOrFail($id);

        $request->validate([
            'status' => 'required|in:in_progress,completed,rejected',
            'comments' => 'nullable|string',
        ]);

        // Store approval in approvals table
        $inquiry->createApproval($this->getCurrentUserId(), $request->status, $request->comments);

        // Update inquiry status to match approval
        $inquiry->update([
            'inquiry_Status' => $request->status,
        ]);

        return redirect()->route('mcmc.inquiries.list')->with('success', 'Inquiry status updated successfully!');
    }

    /**
     * Display inquiry reports.
     */
    public function inquiryReports()
    {
        $startDate = request('start_date', now()->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->endOfDay()->toDateString());

        // Handle year and month filters
        $year = request('year');
        $month = request('month');

        $query = InquirySubmissionRecord::with('user');

        // Apply year and month filters if provided
        if ($year && $month) {
            // Filter by specific year and month
            $query->whereYear('inquiry_Created_At', $year)
                ->whereMonth('inquiry_Created_At', $month);
        } elseif ($year) {
            // Filter by year only
            $query->whereYear('inquiry_Created_At', $year);
        } elseif ($month) {
            // Filter by month only (current year)
            $query->whereMonth('inquiry_Created_At', $month)
                ->whereYear('inquiry_Created_At', now()->year);
        } else {
            // Use date range filters if no year/month specified
            $query->whereDate('inquiry_Created_At', '>=', $startDate)
                ->whereDate('inquiry_Created_At', '<=', $endDate);
        }

        $inquiries = $query->orderBy('inquiry_Created_At', 'desc')->get();

        // Attach latest approval info to each inquiry
        foreach ($inquiries as $inquiry) {
            $latestApproval = $inquiry->getLatestApproval();
            $inquiry->latest_approval = $latestApproval;
        }

        $totalInquiries = $inquiries->count();

        // Count all possible statuses
        $pendingInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'pending';
        })->count();
        $completedInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'completed';
        })->count();
        $rejectedInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'rejected';
        })->count();
        $inProgressInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'in_progress';
        })->count();
        $underReviewInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'under_review';
        })->count();
        $assignToAgencyInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'assign_to_agency';
        })->count();

        // Group inquiries by status for dynamic handling
        $inquiriesByStatus = $inquiries->groupBy('inquiry_Status')->map(function ($group) {
            return (object)[
                'inquiry_Status' => $group->first()->inquiry_Status,
                'count' => $group->count()
            ];
        });

        $recentInquiries = $inquiries->sortByDesc('inquiry_Created_At')->take(3);

        $inquiriesByCategory = $inquiries->groupBy('inquiry_Category')->map(function ($group) {
            return (object)[
                'inquiry_Category' => $group->first()->inquiry_Category,
                'count' => $group->count()
            ];
        });

        return view('Inquiry Submission.MCMC.InquiryReports', compact(
            'inquiries',
            'startDate',
            'endDate',
            'totalInquiries',
            'pendingInquiries',
            'inProgressInquiries',
            'completedInquiries',
            'rejectedInquiries',
            'underReviewInquiries',
            'assignToAgencyInquiries',
            'recentInquiries',
            'inquiriesByCategory',
            'inquiriesByStatus'
        ));
    }

    /**
     * Export inquiries to Excel.
     */
    public function exportExcel()
    {
        $startDate = request('start_date', now()->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->endOfDay()->toDateString());
        $year = request('year');
        $month = request('month');

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\InquiryReportsExport($startDate, $endDate, $year, $month), 'inquiries_report.xlsx');
    }

    /**
     * Export inquiries to PDF.
     */
    public function exportPdf(Request $request = null)
    {
        $startDate = request('start_date', now()->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->endOfDay()->toDateString());

        // Handle year and month filters
        $year = request('year');
        $month = request('month');

        $query = InquirySubmissionRecord::with('user');

        // Apply year and month filters if provided
        if ($year && $month) {
            // Filter by specific year and month
            $query->whereYear('inquiry_Created_At', $year)
                ->whereMonth('inquiry_Created_At', $month);
        } elseif ($year) {
            // Filter by year only
            $query->whereYear('inquiry_Created_At', $year);
        } elseif ($month) {
            // Filter by month only (current year)
            $query->whereMonth('inquiry_Created_At', $month)
                ->whereYear('inquiry_Created_At', now()->year);
        } else {
            // Use date range filters if no year/month specified
            $query->whereDate('inquiry_Created_At', '>=', $startDate)
                ->whereDate('inquiry_Created_At', '<=', $endDate);
        }

        $inquiries = $query->orderBy('inquiry_Created_At', 'desc')->get();

        $totalInquiries = $inquiries->count();

        // Count all possible statuses
        $pendingInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'pending';
        })->count();
        $completedInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'completed';
        })->count();
        $rejectedInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'rejected';
        })->count();
        $inProgressInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'in_progress';
        })->count();

        $underReviewInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'under_review';
        })->count();
        $assignToAgencyInquiries = $inquiries->filter(function ($inquiry) {
            return strtolower($inquiry->inquiry_Status) == 'assign_to_agency';
        })->count();

        // Group inquiries by status for dynamic handling
        $inquiriesByStatus = $inquiries->groupBy('inquiry_Status')->map(function ($group) {
            return (object)[
                'inquiry_Status' => $group->first()->inquiry_Status,
                'count' => $group->count()
            ];
        });

        $recentInquiries = $inquiries->sortByDesc('inquiry_Created_At')->take(3);
        $inquiriesByCategory = $inquiries->groupBy('inquiry_Category')->map(function ($group) {
            return (object)[
                'inquiry_Category' => $group->first()->inquiry_Category,
                'count' => $group->count()
            ];
        });

        $pdf = Pdf::loadView('Inquiry Submission.MCMC.InquiryReportsPdf', [
            'inquiries' => $inquiries,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalInquiries' => $totalInquiries,
            'pendingInquiries' => $pendingInquiries,
            'inProgressInquiries' => $inProgressInquiries,
            'completedInquiries' => $completedInquiries,
            'rejectedInquiries' => $rejectedInquiries,
            'underReviewInquiries' => $underReviewInquiries,
            'assignToAgencyInquiries' => $assignToAgencyInquiries,
            'recentInquiries' => $recentInquiries,
            'inquiriesByCategory' => $inquiriesByCategory,
            'inquiriesByStatus' => $inquiriesByStatus,
        ]);
        return $pdf->download('inquiries-report.pdf');
    }
}
