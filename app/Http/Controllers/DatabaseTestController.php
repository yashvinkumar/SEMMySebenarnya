<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InquirySubmissionRecord;
use App\Models\PublicUser;
use App\Models\Agency;
use App\Models\McmcStaff;
use App\Models\InquiryAssignment;
use App\Models\InquiryProgress;

class DatabaseTestController extends Controller
{
    public function testConnection()
    {
        try {
            // Test basic DB connection
            DB::connection()->getPdo();

            // Test data retrieval
            $stats = [
                'public_users' => PublicUser::count(),
                'agencies' => Agency::count(),
                'mcmc_staff' => McmcStaff::count(),
                'inquiries' => InquirySubmissionRecord::count(),
                'assignments' => InquiryAssignment::count(),
                'progress_records' => InquiryProgress::count(),
            ];

            // Test relationships
            $inquiriesWithUsers = InquirySubmissionRecord::with('user')->get();
            $inquiriesWithAssignments = InquirySubmissionRecord::with(['assignments.agency'])->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Database connection successful',
                'stats' => $stats,
                'sample_data' => [
                    'inquiries_count' => $inquiriesWithUsers->count(),
                    'assignments_count' => $inquiriesWithAssignments->count(),
                    'latest_inquiry' => $inquiriesWithUsers->first() ? [
                        'title' => $inquiriesWithUsers->first()->inquiry_Title,
                        'user_name' => $inquiriesWithUsers->first()->user->user_Name ?? 'N/A',
                        'status' => $inquiriesWithUsers->first()->inquiry_Status
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showDashboard()
    {
        try {
            $inquiries = InquirySubmissionRecord::with(['user', 'assignments.agency', 'progressRecords'])
                ->orderBy('inquiry_Created_At', 'desc')
                ->get();

            $agencies = Agency::all();
            $staff = McmcStaff::all();

            return view('database-test', compact('inquiries', 'agencies', 'staff'));

        } catch (\Exception $e) {
            return view('database-test')->with('error', $e->getMessage());
        }
    }
}
