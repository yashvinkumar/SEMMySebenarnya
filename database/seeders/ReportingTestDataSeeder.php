<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PublicUser;
use App\Models\Agency;
use App\Models\McmcStaff;
use App\Models\InquirySubmissionRecord;
use App\Models\Approval;
use App\Models\InquiryAssignment;
use App\Models\InquiryProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Generating comprehensive test data for reporting...');

        // Get existing data
        $publicUsers = PublicUser::all();
        $agencies = Agency::all();
        $mcmcStaff = McmcStaff::all();

        if ($publicUsers->isEmpty() || $agencies->isEmpty() || $mcmcStaff->isEmpty()) {
            $this->command->error('Please run the basic seeders first!');
            return;
        }

        // Generate inquiries for the past 12 months
        $this->generateInquiries($publicUsers, $mcmcStaff);

        // Generate assignments for various agencies
        $this->generateAssignments($agencies, $mcmcStaff);

        // Generate progress records
        $this->generateProgressRecords($agencies, $mcmcStaff);

        $this->command->info('Reporting test data generated successfully!');
        $this->displayStatistics();
    }

    /**
     * Generate inquiries over the past 12 months
     */
    private function generateInquiries($publicUsers, $mcmcStaff)
    {
        $categories = ['general', 'technical', 'complaint', 'suggestion'];
        $statuses = ['pending', 'in_progress', 'completed', 'under_review'];

        // Generate 50 inquiries over the past 12 months
        for ($i = 0; $i < 50; $i++) {
            $createdDate = Carbon::now()->subMonths(rand(0, 12))->subDays(rand(0, 30));

            $inquiry = InquirySubmissionRecord::create([
                'user_ID' => $publicUsers->random()->user_ID,
                'inquiry_Title' => $this->generateInquiryTitle(),
                'inquiry_Description' => $this->generateInquiryDescription(),
                'inquiry_Category' => $categories[array_rand($categories)],
                'inquiry_Status' => $statuses[array_rand($statuses)],
                'inquiry_Created_At' => $createdDate,
            ]);

            // Create approval for this inquiry
            Approval::create([
                'inquiry_ID' => $inquiry->inquiry_ID,
                'staff_ID' => $mcmcStaff->random()->staff_ID,
                'approval_Status' => rand(0, 1) ? 'approved' : 'pending',
                'approval_Comments' => 'Processed for assignment',
                'approval_Type' => 'mcmc_review',
                'approval_Date' => $createdDate->addDays(rand(1, 3)),
            ]);
        }
    }

    /**
     * Generate assignments for agencies
     */
    private function generateAssignments($agencies, $mcmcStaff)
    {
        $approvals = Approval::where('approval_Status', 'approved')->get();
        $statuses = ['pending', 'in_progress', 'completed', 'rejected'];

        foreach ($approvals as $approval) {
            // 80% chance of creating an assignment
            if (rand(1, 100) <= 80) {
                $assignmentDate = Carbon::parse($approval->approval_Date)->addDays(rand(1, 5));
                $status = $statuses[array_rand($statuses)];

                $assignment = InquiryAssignment::create([
                    'agency_ID' => $agencies->random()->agency_ID,
                    'approval_ID' => $approval->approval_ID,
                    'assignment_Date' => $assignmentDate,
                    'assignment_Status' => $status,
                    'assignment_Comments' => $this->generateAssignmentComment($status),
                    'assigned_By' => $mcmcStaff->random()->staff_ID,
                    'completed_At' => $status === 'completed' ? $assignmentDate->addDays(rand(3, 15)) : null,
                ]);

                // Update approval status
                $approval->update(['approval_Status' => 'assigned']);
            }
        }
    }

    /**
     * Generate progress records
     */
    private function generateProgressRecords($agencies, $mcmcStaff)
    {
        $assignments = InquiryAssignment::with(['approval.inquiry'])->get();

        foreach ($assignments as $assignment) {
            // Generate 1-3 progress records per assignment
            $progressCount = rand(1, 3);

            for ($i = 0; $i < $progressCount; $i++) {
                $progressDate = Carbon::parse($assignment->assignment_Date)->addDays($i * rand(2, 5));

                InquiryProgress::create([
                    'inquiry_ID' => $assignment->approval->inquiry_ID,
                    'agency_ID' => $assignment->agency_ID,
                    'user_ID' => $assignment->approval->inquiry->user_ID,
                    'staff_ID' => $mcmcStaff->random()->staff_ID,
                    'progress_Status' => $this->getProgressStatus($i, $progressCount, $assignment->assignment_Status),
                    'progress_Remarks' => $this->generateProgressRemarks($i),
                    'progress_Updated_At' => $progressDate,
                ]);
            }
        }
    }

    /**
     * Generate inquiry titles
     */
    private function generateInquiryTitle()
    {
        $titles = [
            'Internet Connection Issues in Rural Areas',
            'Mobile Network Coverage Problems',
            'TV Broadcasting Signal Quality',
            'Cybersecurity Incident Report',
            'Telecom Service Billing Complaint',
            'Radio Frequency Interference',
            'Unauthorized Telecom Tower Installation',
            'Data Privacy Breach Report',
            'Network Outage Compensation Request',
            'Spectrum Allocation Inquiry',
            'Digital Service Accessibility Issues',
            'Telecommunications License Application',
            'Consumer Protection Complaint',
            'Technical Standards Compliance Query',
            'Emergency Communication System Issues',
            'Cross-Border Communication Problems',
            'Internet Service Quality Standards',
            'Mobile Payment Security Concerns',
            'Broadcasting Content Regulation Query',
            'Network Infrastructure Development',
        ];

        return $titles[array_rand($titles)];
    }

    /**
     * Generate inquiry descriptions
     */
    private function generateInquiryDescription()
    {
        $descriptions = [
            'Experiencing frequent disconnections and slow internet speeds in our area.',
            'Mobile network coverage is poor, affecting business communications.',
            'TV signal quality has deteriorated significantly over the past month.',
            'Suspected cybersecurity breach affecting our communication systems.',
            'Incorrect billing charges for telecommunications services.',
            'Radio interference affecting our licensed frequency operations.',
            'Unauthorized telecom tower construction without proper permits.',
            'Personal data privacy concerns regarding telecom service providers.',
            'Requesting compensation for extended network outage.',
            'Inquiry about spectrum allocation for new communication services.',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Generate assignment comments
     */
    private function generateAssignmentComment($status)
    {
        $comments = [
            'pending' => 'Assignment created and pending agency review.',
            'in_progress' => 'Agency has accepted and is actively working on this inquiry.',
            'completed' => 'Investigation completed and response provided to user.',
            'rejected' => 'Agency has rejected this assignment due to jurisdiction issues.',
        ];

        return $comments[$status] ?? 'Assignment processed.';
    }

    /**
     * Get progress status based on sequence
     */
    private function getProgressStatus($index, $total, $finalStatus)
    {
        if ($index === 0) {
            return 'received';
        } elseif ($index === $total - 1) {
            return $finalStatus === 'completed' ? 'resolved' : 'under_investigation';
        } else {
            return 'under_investigation';
        }
    }

    /**
     * Generate progress remarks
     */
    private function generateProgressRemarks($index)
    {
        $remarks = [
            'Initial assessment and case assignment to technical team.',
            'Investigation in progress. Gathering additional information.',
            'Field inspection conducted. Analyzing findings.',
            'Coordination with relevant departments for resolution.',
            'Technical solution implemented. Monitoring effectiveness.',
            'Case resolved. User notification sent.',
        ];

        return $remarks[min($index, count($remarks) - 1)];
    }

    /**
     * Display statistics
     */
    private function displayStatistics()
    {
        $inquiryCount = InquirySubmissionRecord::count();
        $approvalCount = Approval::count();
        $assignmentCount = InquiryAssignment::count();
        $progressCount = InquiryProgress::count();

        $this->command->info('');
        $this->command->info('Generated Data Statistics:');
        $this->command->info('==========================');
        $this->command->info("Total Inquiries: {$inquiryCount}");
        $this->command->info("Total Approvals: {$approvalCount}");
        $this->command->info("Total Assignments: {$assignmentCount}");
        $this->command->info("Total Progress Records: {$progressCount}");
        $this->command->info('');

        // Monthly distribution
        $monthlyStats = InquiryAssignment::select(
            DB::raw('DATE_FORMAT(assignment_Date, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $this->command->info('Monthly Assignment Distribution:');
        foreach ($monthlyStats as $stat) {
            $monthName = Carbon::createFromFormat('Y-m', $stat->month)->format('F Y');
            $this->command->info("  {$monthName}: {$stat->count} assignments");
        }

        // Agency distribution
        $agencyStats = InquiryAssignment::join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
            ->select('agencies.agency_Name', DB::raw('COUNT(*) as count'))
            ->groupBy('agencies.agency_ID', 'agencies.agency_Name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $this->command->info('');
        $this->command->info('Top 5 Agencies by Assignment Count:');
        foreach ($agencyStats as $stat) {
            $this->command->info("  {$stat->agency_Name}: {$stat->count} assignments");
        }
    }
}
