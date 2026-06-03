<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MCMC\ReportController;
use Illuminate\Http\Request;
use App\Models\InquiryAssignment;
use App\Models\Agency;
use Carbon\Carbon;

class TestReportingSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-reporting-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the inquiry assignment reporting system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TESTING INQUIRY ASSIGNMENT REPORTING SYSTEM ===');
        $this->newLine();

        // Test 1: Basic Data Availability
        $this->testDataAvailability();

        // Test 2: Controller Methods
        $this->testControllerMethods();

        // Test 3: Chart Data Generation
        $this->testChartData();

        // Test 4: Export Functionality
        $this->testExportFunctionality();

        $this->newLine();
        $this->info('=== REPORTING SYSTEM TEST COMPLETED ===');

        return Command::SUCCESS;
    }

    /**
     * Test data availability
     */
    private function testDataAvailability()
    {
        $this->info('1. Testing Data Availability:');
        $this->line('-----------------------------');

        $assignmentCount = InquiryAssignment::count();
        $agencyCount = Agency::count();

        $this->line("✓ Total Assignments: {$assignmentCount}");
        $this->line("✓ Total Agencies: {$agencyCount}");

        if ($assignmentCount > 0 && $agencyCount > 0) {
            $this->line('✓ Sufficient data available for reporting');
        } else {
            $this->error('✗ Insufficient data for testing. Please run ReportingTestDataSeeder');
            return;
        }

        // Check date range of assignments
        $dateRange = InquiryAssignment::selectRaw('MIN(assignment_Date) as min_date, MAX(assignment_Date) as max_date')->first();
        $this->line("✓ Assignment Date Range: {$dateRange->min_date} to {$dateRange->max_date}");

        $this->newLine();
    }

    /**
     * Test controller methods
     */
    private function testControllerMethods()
    {
        $this->info('2. Testing Controller Methods:');
        $this->line('-------------------------------');

        try {
            $controller = new ReportController();

            // Create mock request with default filters
            $request = new Request([
                'date_from' => Carbon::now()->startOfYear()->format('Y-m-d'),
                'date_to' => Carbon::now()->format('Y-m-d'),
                'group_by' => 'agency'
            ]);

            // Test getReportData method
            $response = $controller->getReportData($request);
            $this->line('✓ getReportData() method working');

            // Check if response contains data
            $responseData = $response->getData(true);
            if (!empty($responseData)) {
                $this->line('✓ Report data generated successfully');
                $this->line('  - Groups found: ' . count($responseData));
            } else {
                $this->line('⚠ No report data returned (this may be normal if no assignments exist)');
            }

        } catch (\Exception $e) {
            $this->error('✗ Controller method test failed: ' . $e->getMessage());
        }

        $this->newLine();
    }

    /**
     * Test chart data generation
     */
    private function testChartData()
    {
        $this->info('3. Testing Chart Data Generation:');
        $this->line('----------------------------------');

        try {
            // Test monthly trend data
            $monthlyData = InquiryAssignment::selectRaw('
                DATE_FORMAT(assignment_Date, "%Y-%m") as month,
                COUNT(*) as total_assignments
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $this->line('✓ Monthly trend data: ' . $monthlyData->count() . ' months');

            // Test agency distribution
            $agencyData = InquiryAssignment::join('agencies', 'inquiry_assignments.agency_ID', '=', 'agencies.agency_ID')
                ->selectRaw('agencies.agency_Name, COUNT(*) as total_assignments')
                ->groupBy('agencies.agency_ID', 'agencies.agency_Name')
                ->orderBy('total_assignments', 'desc')
                ->get();

            $this->line('✓ Agency distribution data: ' . $agencyData->count() . ' agencies');

            // Test status distribution
            $statusData = InquiryAssignment::selectRaw('assignment_Status, COUNT(*) as total_assignments')
                ->groupBy('assignment_Status')
                ->get();

            $this->line('✓ Status distribution data: ' . $statusData->count() . ' statuses');

            // Display sample data
            if ($agencyData->count() > 0) {
                $this->line('  Sample agency data:');
                foreach ($agencyData->take(3) as $agency) {
                    $this->line("    - {$agency->agency_Name}: {$agency->total_assignments} assignments");
                }
            }

        } catch (\Exception $e) {
            $this->error('✗ Chart data generation failed: ' . $e->getMessage());
        }

        $this->newLine();
    }

    /**
     * Test export functionality
     */
    private function testExportFunctionality()
    {
        $this->info('4. Testing Export Functionality:');
        $this->line('---------------------------------');

        try {
            // Test if export classes exist and are properly configured
            if (class_exists('\App\Exports\InquiryAssignmentReportExport')) {
                $this->line('✓ InquiryAssignmentReportExport class exists');

                // Test Excel export class instantiation
                $reportData = collect();
                $filters = ['date_from' => '2024-01-01', 'date_to' => '2024-12-31'];

                $export = new \App\Exports\InquiryAssignmentReportExport($reportData, $filters);
                $this->line('✓ Excel export class can be instantiated');

                // Test array method
                $arrayData = $export->array();
                $this->line('✓ Excel export array() method working');

            } else {
                $this->error('✗ InquiryAssignmentReportExport class not found');
            }

            // Test PDF dependencies
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $this->line('✓ PDF export dependencies available');
            } else {
                $this->error('✗ PDF export dependencies missing');
            }

        } catch (\Exception $e) {
            $this->error('✗ Export functionality test failed: ' . $e->getMessage());
        }

        $this->newLine();
    }
}
