<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\MCMC\ReportController;

class TestWebInterface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-web-interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the web interface for the reporting system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TESTING WEB INTERFACE ===');
        $this->newLine();

        try {
            // Test the report controller index method
            $controller = new ReportController();
            $request = new Request();

            $this->info('Testing /mcmc/reports route...');

            // Call the index method
            $response = $controller->index($request);

            if ($response) {
                $this->line('✓ MCMC Reports index method executed successfully');

                // Check if response has view data
                $viewData = $response->getData();
                if (!empty($viewData)) {
                    $this->line('✓ View data generated successfully');

                    // Check for required data
                    $requiredKeys = ['agencies', 'filters', 'dashboardData', 'chartData'];
                    foreach ($requiredKeys as $key) {
                        if (isset($viewData[$key])) {
                            $this->line("  ✓ {$key} data available");
                        } else {
                            $this->error("  ✗ {$key} data missing");
                        }
                    }
                } else {
                    $this->line('⚠ No view data returned');
                }
            } else {
                $this->error('✗ Failed to get response from controller');
            }

        } catch (\Exception $e) {
            $this->error('✗ Web interface test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }

        $this->newLine();
        $this->info('=== WEB INTERFACE TEST COMPLETED ===');

        return Command::SUCCESS;
    }
}
