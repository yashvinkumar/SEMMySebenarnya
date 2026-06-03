<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{UserRecord, InquiryAssignment};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AgencyAssignmentController;

class TestAgencyRouteAuth extends Command
{
    protected $signature = 'test:agency-route-auth';
    protected $description = 'Test agency route with proper authentication context';

    public function handle()
    {
        $this->info('🔐 Testing Agency Route Authentication...');
        $this->info('=========================================');

        // Step 1: Authenticate as agency
        $agencyUser = UserRecord::where('email', 'agency@test.com')->first();
        if (!$agencyUser) {
            $this->error('Agency user not found');
            return;
        }

        Auth::guard('agency')->login($agencyUser);
        $this->info("✅ Authenticated as: {$agencyUser->name}");

        // Step 2: Test route URL generation
        $assignment = InquiryAssignment::whereHas('agency', function($q) {
            $q->where('agency_Email', 'agency@test.com');
        })->first();

        if (!$assignment) {
            $this->error('No assignment found for testing');
            return;
        }

        $this->info("✅ Found assignment: {$assignment->assignment_ID}");

        // Step 3: Test URL generation with different methods
        try {
            // Method 1: Using route helper
            $url1 = route('agency.assignments.update-status', $assignment->assignment_ID);
            $this->info("✅ Route helper URL: {$url1}");
        } catch (\Exception $e) {
            $this->error("❌ Route helper failed: " . $e->getMessage());
        }

        try {
            // Method 2: Direct URL
            $url2 = url("/agency/assignments/{$assignment->assignment_ID}/update-status");
            $this->info("✅ Direct URL: {$url2}");
        } catch (\Exception $e) {
            $this->error("❌ Direct URL failed: " . $e->getMessage());
        }

        // Step 4: Test actual form submission simulation
        $this->info('');
        $this->info('🧪 Testing Form Submission...');

        try {
            $controller = new AgencyAssignmentController();
            $request = new Request();
            $request->replace([
                'status' => 'in_progress',
                'comments' => 'Test update from route auth test'
            ]);
            $request->setMethod('PUT');
            $request->merge(['_token' => 'test-token']);

            // Simulate the web request environment
            $request->server->set('REQUEST_METHOD', 'PUT');
            $request->server->set('REQUEST_URI', "/agency/assignments/{$assignment->assignment_ID}/update-status");

            $response = $controller->updateAssignmentStatus($request, $assignment->assignment_ID);

            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                $session = $response->getSession();
                if ($session && $session->has('success')) {
                    $this->info("✅ Form submission successful: " . $session->get('success'));
                } elseif ($session && $session->has('error')) {
                    $this->error("❌ Form submission error: " . $session->get('error'));
                } else {
                    $this->info("✅ Form submitted (redirect response received)");
                }
            } else {
                $this->info("✅ Response received: " . get_class($response));
            }

        } catch (\Exception $e) {
            $this->error("❌ Form submission failed: " . $e->getMessage());
            $this->error("   File: " . $e->getFile() . ':' . $e->getLine());
        }

        // Step 5: Check all agency routes
        $this->info('');
        $this->info('📋 Checking All Agency Routes...');

        $routes = collect(\Route::getRoutes()->getRoutes());
        $agencyRoutes = $routes->filter(function ($route) {
            return str_starts_with($route->getName() ?? '', 'agency.');
        });

        foreach ($agencyRoutes as $route) {
            if (str_contains($route->getName(), 'assignments')) {
                $this->info("   ✅ {$route->getName()}: {$route->uri()}");
            }
        }

        Auth::guard('agency')->logout();

        $this->info('');
        $this->info('🎯 Test Complete!');

        return 0;
    }
}
