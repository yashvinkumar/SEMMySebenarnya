<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\UserRecord;
use App\Models\McmcStaff;
use App\Models\InquirySubmissionRecord;
use App\Models\Approval;
use App\Models\InquiryAssignment;
use Illuminate\Support\Facades\Hash;

class AgencyAssignmentTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test MCMC staff
        $mcmcStaff = McmcStaff::firstOrCreate([
            'staff_Email' => 'mcmc@test.com'
        ], [
            'staff_Name' => 'MCMC Test Staff',
            'staff_Phone_Number' => '03-1234567',
            'staff_Password' => Hash::make('password'),
            'staff_Created_At' => now(),
            'staff_Updated_At' => now()
        ]);

        // Create a test agency
        $agency = Agency::firstOrCreate([
            'agency_Email' => 'agency@test.com'
        ], [
            'agency_Name' => 'Test Regulatory Agency',
            'agency_Type' => 'regulatory',
            'agency_Phone' => '03-1234567',
            'agency_Password' => Hash::make('password'),
            'agency_First_Time_Login' => false,
            'agency_Created_At' => now(),
            'agency_Updated_At' => now()
        ]);

        // Create agency user
        $agencyUser = UserRecord::firstOrCreate([
            'email' => 'agency@test.com'
        ], [
            'name' => 'Agency Test User',
            'password' => Hash::make('password'),
            'user_type' => 'agency',
            'email_verified_at' => now(),
            'agency_ID' => $agency->agency_ID
        ]);

        // Create a test public user
        $publicUser = UserRecord::firstOrCreate([
            'email' => 'user@test.com'
        ], [
            'name' => 'Test Public User',
            'password' => Hash::make('password'),
            'user_type' => 'public',
            'email_verified_at' => now()
        ]);

        // Create sample inquiries
        $inquiry1 = InquirySubmissionRecord::firstOrCreate([
            'inquiry_Title' => 'Test Internet Service Complaint'
        ], [
            'user_ID' => $publicUser->id,
            'inquiry_Category' => 'internet_services',
            'inquiry_Description' => 'Testing internet service quality issues in my area. Need regulatory review.',
            'inquiry_Status' => 'assigned_to_agency',
            'inquiry_Created_At' => now()->subDays(2)
        ]);

        $inquiry2 = InquirySubmissionRecord::firstOrCreate([
            'inquiry_Title' => 'Telecommunications Billing Issue'
        ], [
            'user_ID' => $publicUser->id,
            'inquiry_Category' => 'telecommunications',
            'inquiry_Description' => 'Disputing billing charges from telecommunications provider.',
            'inquiry_Status' => 'assigned_to_agency',
            'inquiry_Created_At' => now()->subDays(1)
        ]);

        // Create approvals
        $approval1 = Approval::firstOrCreate([
            'inquiry_ID' => $inquiry1->inquiry_ID
        ], [
            'staff_ID' => $mcmcStaff->staff_ID,
            'approval_Status' => 'assigned',
            'approval_Comments' => 'Assigned to agency for review',
            'approval_Type' => 'agency_assignment',
            'approval_Date' => now()->subDays(2)
        ]);

        $approval2 = Approval::firstOrCreate([
            'inquiry_ID' => $inquiry2->inquiry_ID
        ], [
            'staff_ID' => $mcmcStaff->staff_ID,
            'approval_Status' => 'assigned',
            'approval_Comments' => 'Assigned to agency for review',
            'approval_Type' => 'agency_assignment',
            'approval_Date' => now()->subDays(1)
        ]);

        // Create assignments
        InquiryAssignment::firstOrCreate([
            'agency_ID' => $agency->agency_ID,
            'approval_ID' => $approval1->approval_ID
        ], [
            'assignment_Date' => now()->subDays(2),
            'assignment_Status' => 'pending',
            'assignment_Comments' => 'Please review this internet service complaint',
            'assigned_By' => $mcmcStaff->staff_ID
        ]);

        InquiryAssignment::firstOrCreate([
            'agency_ID' => $agency->agency_ID,
            'approval_ID' => $approval2->approval_ID
        ], [
            'assignment_Date' => now()->subDays(1),
            'assignment_Status' => 'in_progress',
            'assignment_Comments' => 'STARTED REVIEW: Review process has been initiated by the agency.',
            'assigned_By' => $mcmcStaff->staff_ID
        ]);

        $this->command->info('Test data created successfully!');
        $this->command->info('Agency Login: agency@test.com / password');
        $this->command->info('MCMC Login: mcmc@test.com / password');
        $this->command->info('Public User Login: user@test.com / password');
    }
}
