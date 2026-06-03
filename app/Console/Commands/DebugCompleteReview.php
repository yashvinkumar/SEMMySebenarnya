<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Agency, UserRecord, InquiryAssignment};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AgencyAssignmentController;

class DebugCompleteReview extends Command
{
    protected $signature = 'debug:complete-review';
    protected $description = 'Debug the complete review functionality';

    public function handle()
    {
        $this->info('🔍 Debugging Complete Review...');

        // Authenticate as agency
        $agencyUser = UserRecord::where('email', 'agency@test.com')->first();
        Auth::guard('agency')->login($agencyUser);

        $assignment = InquiryAssignment::with(['approval.inquiry.user', 'assignedByStaff'])
            ->whereHas('agency', function($q) {
                $q->where('agency_Email', 'agency@test.com');
            })
            ->first();

        if (!$assignment) {
            $this->error('No assignment found');
            return;
        }

        // Test without review_steps first
        $this->info('Testing without review_steps...');
        $this->testUpdate($assignment->assignment_ID, [
            'status' => 'completed',
            'completion_summary' => 'Review completed successfully',
            'comments' => 'All requirements verified'
        ]);

        Auth::guard('agency')->logout();
        return 0;
    }

    private function testUpdate($assignmentId, $data)
    {
        try {
            $controller = new AgencyAssignmentController();
            $request = new Request();
            $request->replace($data);
            $request->setMethod('PUT');

            $this->info("Data: " . json_encode($data));

            $response = $controller->updateAssignmentStatus($request, $assignmentId);

            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                $session = $response->getSession();
                if ($session && $session->has('success')) {
                    $this->info("✅ Success: " . $session->get('success'));
                } elseif ($session && $session->has('error')) {
                    $this->error("❌ Error: " . $session->get('error'));
                }
            }

        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ':' . $e->getLine());
        }
    }
}
