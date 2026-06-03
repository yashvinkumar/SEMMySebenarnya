<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PublicUser;
use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\McmcStaff;
use App\Models\Approval;
use App\Models\InquiryAssignment;
use App\Models\InquiryProgress;
use Illuminate\Support\Facades\Hash;

class InquiryTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test public users
        $user1 = PublicUser::create([
            'user_Name' => 'John Doe',
            'user_Email' => 'john@example.com',
            'user_Password' => Hash::make('password'),
            'user_Phone_Number' => '012-3456789',
            'user_Status' => 'active',
            'user_Created_At' => now(),
        ]);

        $user2 = PublicUser::create([
            'user_Name' => 'Jane Smith',
            'user_Email' => 'jane@example.com',
            'user_Password' => Hash::make('password'),
            'user_Phone_Number' => '012-9876543',
            'user_Status' => 'active',
            'user_Created_At' => now(),
        ]);

        // Create test agencies
        $agency1 = Agency::create([
            'agency_Name' => 'Malaysian Communications Commission',
            'agency_Type' => 'communications',
            'agency_Email' => 'mcc@gov.my',
            'agency_Phone' => '03-12345678',
            'agency_Password' => Hash::make('password'),
            'agency_First_Time_Login' => false,
            'agency_Created_At' => now(),
        ]);

        $agency2 = Agency::create([
            'agency_Name' => 'Broadcasting Authority of Malaysia',
            'agency_Type' => 'broadcasting',
            'agency_Email' => 'bam@gov.my',
            'agency_Phone' => '03-87654321',
            'agency_Password' => Hash::make('password'),
            'agency_First_Time_Login' => false,
            'agency_Created_At' => now(),
        ]);

        // Create test MCMC staff
        $staff = McmcStaff::create([
            'staff_Name' => 'MCMC Admin',
            'staff_Email' => 'admin@mcmc.gov.my',
            'staff_Phone_Number' => '03-11111111',
            'staff_Password' => Hash::make('password'),
            'staff_Created_At' => now(),
        ]);

        // Create test inquiries
        $inquiry1 = InquirySubmissionRecord::create([
            'user_ID' => $user1->user_ID,
            'inquiry_Title' => 'Internet Connection Issues',
            'inquiry_Description' => 'I am experiencing frequent disconnections with my internet service provider. The connection drops every few hours and affects my work from home.',
            'inquiry_Category' => 'internet',
            'inquiry_Status' => 'submitted',
            'inquiry_Created_At' => now()->subDays(2),
        ]);

        $inquiry2 = InquirySubmissionRecord::create([
            'user_ID' => $user1->user_ID,
            'inquiry_Title' => 'Mobile Network Coverage Problem',
            'inquiry_Description' => 'Poor mobile network coverage in my residential area. Unable to make calls or access data services properly.',
            'inquiry_Category' => 'telecommunications',
            'inquiry_Status' => 'pending',
            'inquiry_Created_At' => now()->subDays(1),
        ]);

        $inquiry3 = InquirySubmissionRecord::create([
            'user_ID' => $user2->user_ID,
            'inquiry_Title' => 'TV Broadcasting Signal Issues',
            'inquiry_Description' => 'Digital TV channels are experiencing poor signal quality and pixelation during prime time hours.',
            'inquiry_Category' => 'broadcasting',
            'inquiry_Status' => 'under_review',
            'inquiry_Created_At' => now()->subHours(12),
        ]);

        $inquiry4 = InquirySubmissionRecord::create([
            'user_ID' => $user2->user_ID,
            'inquiry_Title' => 'Spam SMS Messages',
            'inquiry_Description' => 'Receiving multiple unwanted promotional SMS messages daily from unknown numbers.',
            'inquiry_Category' => 'complaint',
            'inquiry_Status' => 'assigned_to_agency',
            'inquiry_Created_At' => now()->subDays(3),
        ]);

        // Create some assigned inquiries with approvals and assignments
        $approval1 = Approval::create([
            'inquiry_ID' => $inquiry4->inquiry_ID,
            'staff_ID' => $staff->staff_ID,
            'approval_Status' => 'assigned',
            'approval_Comments' => 'Assigned to Malaysian Communications Commission for investigation.',
            'approval_Type' => 'agency_assignment',
            'approval_Date' => now()->subDays(2),
        ]);

        $assignment1 = InquiryAssignment::create([
            'agency_ID' => $agency1->agency_ID,
            'approval_ID' => $approval1->approval_ID,
            'assignment_Date' => now()->subDays(2),
            'assignment_Status' => 'in_progress',
            'assignment_Comments' => 'Investigation started for spam SMS complaint.',
            'assigned_By' => $staff->staff_ID,
        ]);

        // Create progress records
        InquiryProgress::create([
            'inquiry_ID' => $inquiry4->inquiry_ID,
            'agency_ID' => $agency1->agency_ID,
            'user_ID' => $user2->user_ID,
            'staff_ID' => $staff->staff_ID,
            'progress_Status' => 'investigating',
            'progress_Remarks' => 'Started investigation into spam SMS sources.',
            'progress_Updated_At' => now()->subDays(1),
        ]);

        $this->command->info('Test data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Public Users');
        $this->command->info('- 2 Agencies');
        $this->command->info('- 1 MCMC Staff');
        $this->command->info('- 4 Inquiries (various statuses)');
        $this->command->info('- 1 Assignment with Progress');
    }
}
