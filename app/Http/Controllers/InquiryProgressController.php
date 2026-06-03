<?php

namespace App\Http\Controllers;

use App\Models\InquiryProgress;
use App\Models\InquirySubmissionRecord;
use App\Models\InquiryAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InquiryProgressController extends Controller
{
    /**
     * Default staff ID for agency-submitted progress updates
     * Uses MCMC Admin as the system representative
     */
    const AGENCY_SYSTEM_STAFF_ID = 6;
    /**
     * Public: Track inquiry progress
     */
    public function trackInquiry($inquiry_id)
    {
        $inquiry = InquirySubmissionRecord::with('progress')->findOrFail($inquiry_id);
        return view('module4.public.inquiry-tracker', compact('inquiry'));
    }

    /**
     * Shared: Show inquiry details
     */
    public function showInquiryDetails($inquiry_id)
    {
        $inquiry = InquirySubmissionRecord::with(['progress', 'user'])->findOrFail($inquiry_id);
        return view('module4.partials.inquiry-details', compact('inquiry'));
    }

    /**
     * Agency: Show all assigned inquiries for agency
     */
    public function showAgencyInquiryList(Request $request)
    {
        $agencyId = Auth::user()->agency_ID;

        $query = InquiryAssignment::with(['inquiry.user', 'inquiry.progressRecords'])
            ->where('agency_ID', $agencyId)
            ->whereIn('assignment_Status', ['pending', 'in_progress', 'completed']);

        // Filter by progress status - get inquiries with latest progress status
        if ($request->filled('progress_status')) {
            $query->whereHas('inquiry', function ($q) use ($request) {
                $q->whereExists(function ($subQuery) use ($request) {
                    $subQuery->select(DB::raw(1))
                        ->from('inquiry_progress as ip1')
                        ->whereRaw('ip1.inquiry_ID = inquiries.inquiry_ID')
                        ->where('ip1.progress_Status', $request->progress_status)
                        ->whereRaw('ip1.progress_Updated_At = (
                                 SELECT MAX(ip2.progress_Updated_At)
                                 FROM inquiry_progress ip2
                                 WHERE ip2.inquiry_ID = ip1.inquiry_ID
                             )');
                });
            });
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('assignment_Date', $request->year);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('assignment_Date', $request->month);
        }

        $assignments = $query->orderBy('assignment_Date', 'desc')->get();

        return view('module4.agency.inquiry-management', compact('assignments'));
    }

    /**
     * Agency: Show inquiry details from assignment
     */
    public function showAgencyInquiryDetails($assignmentId)
    {
        $assignment = InquiryAssignment::with(['inquiry.user', 'inquiry.progress'])
            ->findOrFail($assignmentId);

        if ($assignment->agency_ID !== Auth::user()->agency_ID) {
            abort(403);
        }

        return view('module4.agency.inquiry-details', compact('assignment'));
    }

    /**
     * Agency: Submit investigation status update
     */
    public function submitAgencyProgressUpdate(Request $request, $assignmentId)
    {
        $request->validate([
            'progress_Status' => 'required|string',
            'progress_Remarks' => 'nullable|string'
        ]);

        $assignment = InquiryAssignment::with('inquiry')->findOrFail($assignmentId);

        if ($assignment->agency_ID !== Auth::user()->agency_ID) {
            abort(403);
        }

        InquiryProgress::create([
            'inquiry_ID' => $assignment->inquiry->inquiry_ID,
            'agency_ID' => $assignment->agency_ID,
            'user_ID' => $assignment->inquiry->user_ID,
            'staff_ID' => self::AGENCY_SYSTEM_STAFF_ID,
            'progress_Status' => $request->input('progress_Status'),
            'progress_Remarks' => $request->input('progress_Remarks'),
            'progress_Updated_At' => Carbon::now(),
            'assignment_ID' => $assignment->assignment_ID,
        ]);

        return redirect()->route('agency.progress.inquiry-list')->with('success', 'Status updated.');
    }

    /**
     * MCMC: Monitor progress overview
     */
    public function monitorProgress()
    {
        $inquiries = InquirySubmissionRecord::with(['progress', 'user'])
            ->orderBy('inquiry_Created_At', 'desc')
            ->get();

        return view('module4.mcmc.progress-monitor', compact('inquiries'));
    }

    /**
     * MCMC: Generate report by agency
     */
    public function generatePerformanceReport()
    {
        $reportData = InquiryProgress::with('agency')
            ->selectRaw('agency_ID, COUNT(*) as total_updates, MAX(progress_Updated_At) as last_update')
            ->groupBy('agency_ID')
            ->get();

        return view('module4.mcmc.progress-monitor', compact('reportData'));
    }

    /**
     * Shared: JSON history
     */
    public function getProgressHistory($inquiry_id)
    {
        $timeline = InquiryProgress::where('inquiry_ID', $inquiry_id)
            ->orderBy('progress_Updated_At')
            ->get();

        return response()->json($timeline);
    }

    /**
     * Shared: Dummy notification
     */
    public function sendStatusNotification($inquiry_id)
    {
        return response()->json(['message' => 'Notification sent (placeholder).']);
    }
}
