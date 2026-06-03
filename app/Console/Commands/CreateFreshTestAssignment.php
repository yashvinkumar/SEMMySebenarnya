<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Agency, UserRecord, InquirySubmissionRecord, Approval, InquiryAssignment, McmcStaff};

class CreateFreshTestAssignment extends Command
{
    protected $signature = 'create:fresh-test-assignment';
    protected $description = 'Create a fresh pending assignment for testing';

    public function handle()
    {
        $agency = Agency::where('agency_Email', 'agency@test.com')->first();
        $user = UserRecord::where('email', 'user@test.com')->first();
        $mcmc = McmcStaff::where('staff_Email', 'mcmc@test.com')->first();

        if (!$agency || !$user || !$mcmc) {
            $this->error('Test data not found. Run the seeder first.');
            return;
        }

        // Create new inquiry
        $inquiry = InquirySubmissionRecord::create([
            'user_ID' => $user->id,
            'inquiry_Category' => 'internet_services',
            'inquiry_Title' => 'Final Test - Internet Service Quality Issue',
            'inquiry_Description' => 'This is a fresh test inquiry for final system verification with pending status.',
            'inquiry_Status' => 'assigned_to_agency',
            'inquiry_Created_At' => now()
        ]);

        // Create approval
        $approval = Approval::create([
            'inquiry_ID' => $inquiry->inquiry_ID,
            'staff_ID' => $mcmc->staff_ID,
            'approval_Status' => 'assigned',
            'approval_Comments' => 'Fresh assignment for final system test',
            'approval_Type' => 'agency_assignment',
            'approval_Date' => now()
        ]);

        // Create pending assignment
        $assignment = InquiryAssignment::create([
            'agency_ID' => $agency->agency_ID,
            'approval_ID' => $approval->approval_ID,
            'assignment_Date' => now(),
            'assignment_Status' => 'pending',
            'assignment_Comments' => 'Fresh assignment ready for status update testing',
            'assigned_By' => $mcmc->staff_ID
        ]);

        $this->info("✅ Created fresh pending assignment!");
        $this->info("Assignment ID: {$assignment->assignment_ID}");
        $this->info("Inquiry: {$inquiry->inquiry_Title}");
        $this->info("Status: {$assignment->assignment_Status}");

        return 0;
    }
}
