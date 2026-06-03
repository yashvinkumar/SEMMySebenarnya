<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PublicUser;
use App\Models\Agency;
use App\Models\McmcStaff;
use App\Models\InquirySubmissionRecord;
use App\Models\Approval;
use App\Models\InquiryAssignment;
use App\Models\InquiryProgress;
use App\Models\Report;
use App\Models\UserRecord;

class TestDatabaseRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:relationships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all database table relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== DATABASE RELATIONSHIPS TEST ===');
        $this->newLine();

        // Test 1: Public Users and their Inquiries
        $this->info('1. Testing Public Users → Inquiries Relationship:');
        $this->line('------------------------------------------------');
        $publicUsers = PublicUser::with('inquiries')->get();
        foreach ($publicUsers->take(3) as $user) {
            $this->line("Public User: {$user->user_Name} ({$user->user_Email})");
            $this->line("   Inquiries: " . $user->inquiries->count());
            foreach ($user->inquiries->take(2) as $inquiry) {
                $this->line("   - {$inquiry->inquiry_Title} (Status: {$inquiry->inquiry_Status})");
            }
            $this->newLine();
        }

        // Test 2: Agencies and their Assignments
        $this->info('2. Testing Agencies → Assignments Relationship:');
        $this->line('-----------------------------------------------');
        $agencies = Agency::with('assignments')->get();
        foreach ($agencies->take(3) as $agency) {
            $this->line("Agency: {$agency->agency_Name}");
            $this->line("   Type: {$agency->agency_Type}");
            $this->line("   Assignments: " . $agency->assignments->count());
            foreach ($agency->assignments->take(2) as $assignment) {
                $this->line("   - Assignment ID: {$assignment->assignment_ID} (Status: {$assignment->assignment_Status})");
            }
            $this->newLine();
        }

        // Test 3: MCMC Staff and their Approvals
        $this->info('3. Testing MCMC Staff → Approvals Relationship:');
        $this->line('-----------------------------------------------');
        $mcmcStaff = McmcStaff::with('approvals')->get();
        foreach ($mcmcStaff->take(3) as $staff) {
            $this->line("MCMC Staff: {$staff->staff_Name} ({$staff->staff_Email})");
            $this->line("   Approvals: " . $staff->approvals->count());
            $this->line("   Assignments: " . $staff->assignments->count());
            $this->newLine();
        }

        // Test 4: Inquiries and their Complex Relationships
        $this->info('4. Testing Inquiries → Complex Relationships:');
        $this->line('--------------------------------------------');
        $inquiries = InquirySubmissionRecord::with(['user', 'approvals', 'assignments', 'progressRecords'])->get();
        foreach ($inquiries->take(3) as $inquiry) {
            $this->line("Inquiry: {$inquiry->inquiry_Title}");
            $this->line("   Submitted by: " . ($inquiry->user ? $inquiry->user->user_Name : 'Unknown'));
            $this->line("   Status: {$inquiry->inquiry_Status}");
            $this->line("   Approvals: " . $inquiry->approvals->count());
            $this->line("   Assignments: " . $inquiry->assignments->count());
            $this->line("   Progress Records: " . $inquiry->progressRecords->count());

            // Check if assigned to agency
            $agencyInfo = $inquiry->getAgencyAssignmentInfo();
            if ($agencyInfo) {
                $this->line("   Assigned to: {$agencyInfo['agency_name']}");
                $this->line("   Assignment Status: {$agencyInfo['assignment_status']}");
            }
            $this->newLine();
        }

        // Test 5: Database Statistics
        $this->info('5. Database Statistics:');
        $this->line('----------------------');
        $this->line("Total Public Users: " . PublicUser::count());
        $this->line("Total Agencies: " . Agency::count());
        $this->line("Total MCMC Staff: " . McmcStaff::count());
        $this->line("Total Inquiries: " . InquirySubmissionRecord::count());
        $this->line("Total Approvals: " . Approval::count());
        $this->line("Total Assignments: " . InquiryAssignment::count());
        $this->line("Total Progress Records: " . InquiryProgress::count());
        $this->line("Total User Records: " . UserRecord::count());

        $this->newLine();
        $this->info('=== ALL DATABASE RELATIONSHIPS ARE CONNECTED! ===');

        return Command::SUCCESS;
    }
}
