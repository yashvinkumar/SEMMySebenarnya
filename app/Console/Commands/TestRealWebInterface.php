<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Agency, UserRecord, InquiryAssignment};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class TestRealWebInterface extends Command
{
    protected $signature = 'test:real-web-interface';
    protected $description = 'Test the real web interface issues';

    public function handle()
    {
        $this->info('🌐 Testing Real Web Interface...');
        $this->info('================================');

        // Step 1: Check if the route exists and is accessible
        $this->info('1. Checking Routes...');

        $routeCollection = Route::getRoutes();
        $assignmentRoutes = $routeCollection->getByName('assignments.update-status');

        if ($assignmentRoutes) {
            $this->info('   ✅ Route exists: assignments.update-status');
            $this->info('   📍 URI: ' . $assignmentRoutes->uri());
            $this->info('   🔧 Methods: ' . implode(', ', $assignmentRoutes->methods()));
        } else {
            $this->error('   ❌ Route assignments.update-status NOT FOUND');
        }

        // Step 2: Check authentication
        $this->info('');
        $this->info('2. Testing Authentication...');

        $agencyUser = UserRecord::where('email', 'agency@test.com')->first();
        if ($agencyUser) {
            $this->info('   ✅ Agency user exists: ' . $agencyUser->name);
            Auth::guard('agency')->login($agencyUser);
            $this->info('   ✅ Agency user authenticated');
        } else {
            $this->error('   ❌ Agency user not found');
            return;
        }

        // Step 3: Check if assignments exist
        $this->info('');
        $this->info('3. Checking Assignments...');

        $assignments = InquiryAssignment::with(['approval.inquiry.user', 'agency', 'assignedByStaff'])
            ->whereHas('agency', function($q) {
                $q->where('agency_Email', 'agency@test.com');
            })
            ->get();

        $this->info('   📊 Total assignments: ' . $assignments->count());

        foreach ($assignments as $assignment) {
            $this->info("   📋 Assignment {$assignment->assignment_ID}: {$assignment->assignment_Status}");
        }

        // Step 4: Test the actual controller method directly
        $this->info('');
        $this->info('4. Testing Controller Access...');

        if ($assignments->count() > 0) {
            $testAssignment = $assignments->first();

            try {
                $controller = new \App\Http\Controllers\AgencyAssignmentController();
                $this->info('   ✅ Controller instantiated');

                // Test if the method exists
                if (method_exists($controller, 'updateAssignmentStatus')) {
                    $this->info('   ✅ updateAssignmentStatus method exists');
                } else {
                    $this->error('   ❌ updateAssignmentStatus method NOT FOUND');
                }

                // Test access to private method getAgencyId
                $reflection = new \ReflectionClass($controller);
                if ($reflection->hasMethod('getAgencyId')) {
                    $this->info('   ✅ getAgencyId method exists');
                } else {
                    $this->error('   ❌ getAgencyId method NOT FOUND');
                }

            } catch (\Exception $e) {
                $this->error('   ❌ Controller error: ' . $e->getMessage());
            }
        }

        // Step 5: Check middleware
        $this->info('');
        $this->info('5. Checking Middleware...');

        $route = $routeCollection->getByName('assignments.update-status');
        if ($route) {
            $middleware = $route->gatherMiddleware();
            $this->info('   🔒 Middleware: ' . implode(', ', $middleware));
        }

        // Step 6: Test URL generation
        $this->info('');
        $this->info('6. Testing URL Generation...');

        if ($assignments->count() > 0) {
            $testAssignment = $assignments->first();
            try {
                $url = route('assignments.update-status', $testAssignment->assignment_ID);
                $this->info('   ✅ URL generated: ' . $url);
            } catch (\Exception $e) {
                $this->error('   ❌ URL generation failed: ' . $e->getMessage());
            }
        }

        Auth::guard('agency')->logout();

        $this->info('');
        $this->info('🎯 Diagnosis Complete!');
        $this->info('Check the results above to identify any issues.');

        return 0;
    }
}
