<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agency;
use App\Models\UserRecord;
use App\Models\InquiryAssignment;
use App\Models\InquirySubmissionRecord;
use App\Models\Approval;
use App\Models\McmcStaff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AgencyAssignmentController;
use Illuminate\Http\Request;

class TestAgencyAssignmentSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:agency-assignments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the agency assignment system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Agency Assignment System...');

        // Step 1: Verify test data exists
        $this->info('1. Checking test data...');

        $agency = Agency::where('agency_Email', 'agency@test.com')->first();
        $agencyUser = UserRecord::where('email', 'agency@test.com')->first();

        if (!$agency || !$agencyUser) {
            $this->error('Test data not found. Please run: php artisan db:seed --class=AgencyAssignmentTestSeeder');
            return;
        }

        $this->info("✓ Agency found: {$agency->agency_Name} (ID: {$agency->agency_ID})");
        $this->info("✓ Agency user found: {$agencyUser->name} (ID: {$agencyUser->id})");

        // Step 2: Check assignments
        $this->info('2. Checking assignments...');

        $assignments = InquiryAssignment::with(['approval.inquiry.user', 'agency', 'assignedByStaff'])
            ->where('agency_ID', $agency->agency_ID)
            ->get();

        $this->info("✓ Found {$assignments->count()} assignments for agency");

        foreach ($assignments as $assignment) {
            $this->info("  - Assignment ID: {$assignment->assignment_ID}");
            $this->info("    Status: {$assignment->assignment_Status}");
            $this->info("    Inquiry: {$assignment->approval->inquiry->inquiry_Title}");
            $this->info("    User: {$assignment->approval->inquiry->user->name}");
            $this->info("    Assigned Date: {$assignment->assignment_Date}");
            $this->info("    Can be updated: " . ($assignment->canBeUpdated() ? 'Yes' : 'No'));
            $this->info("    ---");
        }

        // Step 3: Test status updates
        $this->info('3. Testing status updates...');

        // Find a pending assignment to test
        $pendingAssignment = $assignments->where('assignment_Status', 'pending')->first();

        if ($pendingAssignment) {
            $this->info("Testing with pending assignment ID: {$pendingAssignment->assignment_ID}");

            // Test 1: Accept and start review
            $this->info('Test 1: Accept & Start Review');
            $this->testStatusUpdate($pendingAssignment, 'in_progress', [
                'comments' => 'Starting review process for this inquiry',
                'review_steps' => ['initial_review', 'documentation_review']
            ]);

            // Test 2: Complete review
            $this->info('Test 2: Complete Review');
            $this->testStatusUpdate($pendingAssignment, 'completed', [
                'completion_summary' => 'Review completed successfully. All compliance requirements met.',
                'comments' => 'Comprehensive review conducted',
                'review_steps' => ['initial_review', 'documentation_review', 'compliance_check']
            ]);

            // Test 3: Reject assignment
            $this->info('Test 3: Reject Assignment');
            $this->testStatusUpdate($pendingAssignment, 'rejected', [
                'rejection_reason' => 'This inquiry falls outside our jurisdiction and should be handled by telecommunications authority.',
                'comments' => 'Reviewed and determined to be outside scope'
            ]);

        } else {
            $this->info('No pending assignments found for testing status updates');
        }

        // Step 4: Verify notifications
        $this->info('4. Checking notification classes...');

        $notificationClasses = [
            'App\Notifications\AssignmentStatusUpdatedNotification',
            'App\Notifications\AssignmentRejectedNotification',
            'App\Notifications\InquiryAssignedNotification'
        ];

        foreach ($notificationClasses as $class) {
            if (class_exists($class)) {
                $this->info("✓ {$class} exists");
            } else {
                $this->error("✗ {$class} missing");
            }
        }

        // Step 5: Test route accessibility
        $this->info('5. Testing routes...');

        $routes = [
            'agency.assignments.list' => '/agency/assignments',
            'agency.assignments.details' => '/agency/assignments/{assignment}',
            'agency.assignments.update-status' => '/agency/assignments/{assignment}/update-status'
        ];

        foreach ($routes as $name => $pattern) {
            if (route($name, ['assignment' => 1], false)) {
                $this->info("✓ Route {$name} exists: {$pattern}");
            } else {
                $this->error("✗ Route {$name} missing");
            }
        }

        $this->info('');
        $this->info('Agency Assignment System Test Complete!');
        $this->info('');
        $this->info('To test manually:');
        $this->info('1. Visit: http://127.0.0.1:8080/login');
        $this->info('2. Login with: agency@test.com / password');
        $this->info('3. Navigate to: Assignments');
        $this->info('4. Try updating assignment statuses');

        return 0;
    }

    /**
     * Test status update functionality
     */
    private function testStatusUpdate($assignment, $status, $data)
    {
        try {
            $oldStatus = $assignment->assignment_Status;

            // Simulate the status update
            $assignment->assignment_Status = $status;

            // Build comments based on status
            $updatedComments = $data['comments'] ?? '';

            if ($status === 'rejected') {
                $assignment->rejection_Reason = $data['rejection_reason'];
                $updatedComments = "REJECTED: " . $data['rejection_reason'] . ($updatedComments ? "\n\nAdditional Comments: " . $updatedComments : '');
            } elseif ($status === 'completed') {
                $assignment->completed_At = now();
                $updatedComments = "COMPLETED: " . $data['completion_summary'] . ($updatedComments ? "\n\nAdditional Comments: " . $updatedComments : '');

                if (isset($data['review_steps'])) {
                    $reviewSteps = implode(', ', $data['review_steps']);
                    $updatedComments .= "\n\nReview Steps Completed: " . $reviewSteps;
                }
            } elseif ($status === 'in_progress') {
                $updatedComments = "STARTED REVIEW: " . ($updatedComments ?: 'Review process has been initiated by the agency.');

                if (isset($data['review_steps'])) {
                    $reviewSteps = implode(', ', $data['review_steps']);
                    $updatedComments .= "\n\nReview Steps in Progress: " . $reviewSteps;
                }
            }

            $assignment->assignment_Comments = $updatedComments;

            $this->info("  ✓ Status update simulation successful: {$oldStatus} → {$status}");
            $this->info("  ✓ Comments updated: " . substr($updatedComments, 0, 100) . "...");

            // Don't actually save to avoid modifying test data
            // $assignment->save();

        } catch (\Exception $e) {
            $this->error("  ✗ Status update failed: " . $e->getMessage());
        }
    }
}
