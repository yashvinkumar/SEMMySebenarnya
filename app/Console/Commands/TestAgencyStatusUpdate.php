<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Agency, UserRecord, InquiryAssignment};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AgencyAssignmentController;
use Illuminate\Support\Facades\DB;

class TestAgencyStatusUpdate extends Command
{
    protected $signature = 'test:agency-status-update';
    protected $description = 'Test the agency status update functionality to identify issues';

    public function handle()
    {
        $this->info('🔍 Testing Agency Status Update Functionality...');
        $this->info('================================================');

        // Step 1: Authenticate as agency
        $agencyUser = UserRecord::where('email', 'agency@test.com')->first();
        if (!$agencyUser) {
            $this->error('Agency user not found. Please run the seeder first.');
            return;
        }

        Auth::guard('agency')->login($agencyUser);
        $this->info("✅ Authenticated as: {$agencyUser->name}");

        // Step 2: Get a test assignment
        $assignment = InquiryAssignment::with(['approval.inquiry.user', 'assignedByStaff'])
            ->whereHas('agency', function($q) {
                $q->where('agency_Email', 'agency@test.com');
            })
            ->first();

        if (!$assignment) {
            $this->error('No assignment found for testing');
            return;
        }

        $this->info("✅ Found assignment: {$assignment->assignment_ID} (Status: {$assignment->assignment_Status})");

        // Step 3: Test different status updates
        $controller = new AgencyAssignmentController();

        $this->info('');
        $this->info('📝 Testing Status Updates...');

        // Test 1: Accept & Start Review
        $this->info('1. Testing: Accept & Start Review');
        $this->testUpdate($controller, $assignment->assignment_ID, [
            'status' => 'in_progress',
            'comments' => 'Starting review process'
        ]);

        // Test 2: Complete Review
        $this->info('2. Testing: Complete Review');
        $this->testUpdate($controller, $assignment->assignment_ID, [
            'status' => 'completed',
            'completion_summary' => 'Review completed successfully',
            'comments' => 'All requirements verified'
        ]);

        // Test 3: Reject Assignment
        $this->info('3. Testing: Reject Assignment');
        $this->testUpdate($controller, $assignment->assignment_ID, [
            'status' => 'rejected',
            'rejection_reason' => 'Outside our jurisdiction',
            'comments' => 'Should be handled by telecommunications authority'
        ]);

        Auth::guard('agency')->logout();

        $this->info('');
        $this->info('🎯 Test Complete!');

        return 0;
    }

    private function testUpdate($controller, $assignmentId, $data)
    {
        try {
            // Create a request object
            $request = new Request();
            $request->replace($data);
            $request->setMethod('PUT');

            // Add CSRF token simulation
            $request->merge(['_token' => 'test-token']);

            $this->info("   📤 Sending request with data: " . json_encode($data));

            // Call the controller method
            $response = $controller->updateAssignmentStatus($request, $assignmentId);

            // Check if it's a redirect response (success)
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                $sessionData = $response->getSession();
                if ($sessionData && $sessionData->has('success')) {
                    $this->info("   ✅ Success: " . $sessionData->get('success'));
                } elseif ($sessionData && $sessionData->has('error')) {
                    $this->error("   ❌ Error: " . $sessionData->get('error'));
                } else {
                    $this->info("   ✅ Update processed (redirect response received)");
                }
            } else {
                $this->info("   ✅ Response received: " . get_class($response));
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error("   ❌ Validation Error:");
            foreach ($e->errors() as $field => $errors) {
                foreach ($errors as $error) {
                    $this->error("     - {$field}: {$error}");
                }
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Exception: " . $e->getMessage());
            $this->error("   📍 File: " . $e->getFile() . ':' . $e->getLine());
        }
    }
}
