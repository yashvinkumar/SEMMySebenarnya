<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InquirySubmissionController;
use App\Http\Controllers\InquiryProgressController;
// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Database test routes
Route::get('/test-db', [\App\Http\Controllers\DatabaseTestController::class, 'testConnection']);
Route::get('/db-dashboard', [\App\Http\Controllers\DatabaseTestController::class, 'showDashboard']);

// Test route for agency status update
Route::get('/test-agency-status', function () {
    $assignment = \App\Models\InquiryAssignment::with(['approval.inquiry.user', 'agency', 'assignedByStaff'])
        ->whereHas('agency', function ($q) {
            $q->where('agency_Email', 'agency@test.com');
        })
        ->where('assignment_Status', 'pending')
        ->first();

    if (!$assignment) {
        return 'No pending assignment found. Please create one first.';
    }

    return view('test.agency-status-test', compact('assignment'));
});

// Test agency assignment system
Route::get('/test-agency-system', function () {
    $agency = \App\Models\Agency::where('agency_Email', 'agency@test.com')->first();
    $user = \App\Models\UserRecord::where('email', 'agency@test.com')->first();
    $assignments = \App\Models\InquiryAssignment::with(['approval.inquiry.user', 'agency', 'assignedByStaff'])
        ->where('agency_ID', $agency->agency_ID ?? 0)
        ->get();

    return response()->json([
        'agency' => $agency,
        'user' => $user,
        'assignments_count' => $assignments->count(),
        'assignments' => $assignments,
        'login_url' => url('/login'),
        'assignments_url' => url('/agency/assignments')
    ]);
});

// Authentication Routes
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Public User Routes
Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);

// Email Verification Routes
Route::get('/email/verify', [UserController::class, 'emailVerificationNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])->middleware(['auth:public', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [UserController::class, 'resendVerificationEmail'])->middleware(['auth:public', 'throttle:6,1'])->name('verification.send');

// Public User Protected Routes
Route::middleware(['auth:public'])->prefix('public')->name('public.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'publicDashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'showPublicProfile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updatePublicProfile'])->name('profile.update');
    Route::get('/settings', [UserController::class, 'showPublicSettings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updatePublicSettings'])->name('settings.update');

    //inquiry progress tracking (module 4)
    Route::get('/inquiries/{inquiry}/track', [InquiryProgressController::class, 'trackInquiry'])->name('inquiries.track');
    Route::get('/inquiries/{inquiry}/details', [InquiryProgressController::class, 'showInquiryDetails'])->name('inquiries.details');
});

// User Assignment Visibility Routes
Route::middleware(['auth:public'])->prefix('user')->name('user.')->group(function () {
    Route::get('/inquiries', [\App\Http\Controllers\AgencyAssignmentController::class, 'userInquiryAssignments'])->name('inquiries');
    Route::get('/inquiries/{inquiry}', [\App\Http\Controllers\AgencyAssignmentController::class, 'userInquiryAssignmentDetails'])->name('inquiries.details');
});

// MCMC Staff Routes
Route::middleware(['auth:mcmc'])->prefix('mcmc')->name('mcmc.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'mcmcDashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'showMcmcProfile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateMcmcProfile'])->name('profile.update');
    Route::get('/users', [UserController::class, 'showAllUsers'])->name('users');
    Route::get('/users/public', [UserController::class, 'getPublicUsers'])->name('users.public');
    Route::get('/users/agencies', [UserController::class, 'getAgencyUsers'])->name('users.agencies');
    Route::post('/users/{userId}/send-verification', [UserController::class, 'sendVerificationEmail'])->name('users.send-verification');
    Route::post('/users/{agencyId}/reset-password', [UserController::class, 'resetAgencyPasswordByMcmc'])->name('users.reset-password');
    Route::post('/users/report', [UserController::class, 'generateUserReport'])->name('users.report');
    Route::get('/register-agency', [UserController::class, 'showAgencyRegistrationForm'])->name('register.agency');
    Route::post('/register-agency', [UserController::class, 'registerAgency']);
    Route::get('/reports', [UserController::class, 'showReports'])->name('reports');
    Route::post('/reports/generate', [UserController::class, 'generateReport'])->name('reports.generate');
    Route::get('/settings', [UserController::class, 'showMcmcSettings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updateMcmcSettings'])->name('settings.update');

    // Inquiry Management Routes
    Route::get('/inquiries', [InquirySubmissionController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiries/export-pdf', [InquirySubmissionController::class, 'exportPdf'])->name('inquiries.export-pdf');

    // Advanced Reporting Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MCMC\ReportController::class, 'index'])->name('index');
        Route::get('/data', [\App\Http\Controllers\MCMC\ReportController::class, 'getReportData'])->name('data');
        Route::get('/export/excel', [\App\Http\Controllers\MCMC\ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [\App\Http\Controllers\MCMC\ReportController::class, 'exportPdf'])->name('export.pdf');
    });
});

// Agency Routes
Route::middleware(['auth:agency'])->prefix('agency')->name('agency.')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [\App\Http\Controllers\AgencyAssignmentController::class, 'agencyDashboard'])->name('dashboard');
    Route::get('/enhanced-dashboard', [\App\Http\Controllers\AgencyJurisdictionController::class, 'enhancedDashboard'])->name('enhanced-dashboard');

    // Profile and Settings
    Route::get('/profile', [UserController::class, 'showAgencyProfile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateAgencyProfile'])->name('profile.update');
    Route::get('/password/reset', [UserController::class, 'showAgencyPasswordResetForm'])->name('password.reset');
    Route::post('/password/reset', [UserController::class, 'resetAgencyPassword']);
    Route::get('/settings', [UserController::class, 'showAgencySettings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updateAgencySettings'])->name('settings.update');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // Assignment Management Routes
    Route::get('/assignments', [\App\Http\Controllers\AgencyAssignmentController::class, 'agencyAssignments'])->name('assignments.list');
    Route::get('/assignments/enhanced', [\App\Http\Controllers\AgencyJurisdictionController::class, 'enhancedList'])->name('assignments.enhanced-list');
    Route::get('/assignments/{assignment}', [\App\Http\Controllers\AgencyAssignmentController::class, 'showAssignmentDetails'])->name('assignments.details');
    Route::get('/assignments/{assignment}/enhanced-details', [\App\Http\Controllers\AgencyJurisdictionController::class, 'enhancedDetails'])->name('assignments.enhanced-details');
    Route::put('/assignments/{assignment}/update-status', [\App\Http\Controllers\AgencyAssignmentController::class, 'updateAssignmentStatus'])->name('assignments.update-status');

    // === Module 4: Inquiry Progress Management by Agency ===
    Route::get('/inquiry-management', [InquiryProgressController::class, 'showAgencyInquiryList'])->name('progress.inquiry-list'); // for the table view
    Route::get('/inquiry-management/{assignment}', [InquiryProgressController::class, 'showAgencyInquiryDetails'])->name('progress.inquiry-details'); // show details
    Route::post('/inquiry-management/{assignment}/status', [InquiryProgressController::class, 'submitAgencyProgressUpdate'])->name('progress.submit-update'); // form submission





    // Jurisdiction Review Routes
    Route::get('/assignments/{assignment}/jurisdiction-review', [\App\Http\Controllers\AgencyJurisdictionController::class, 'jurisdictionReview'])->name('assignments.jurisdiction-review');
    Route::post('/assignments/{assignment}/accept-jurisdiction', [\App\Http\Controllers\AgencyJurisdictionController::class, 'acceptJurisdiction'])->name('assignments.accept');
    Route::post('/assignments/{assignment}/reject-jurisdiction', [\App\Http\Controllers\AgencyJurisdictionController::class, 'rejectJurisdiction'])->name('assignments.reject');

    // Bulk Operations
    Route::post('/assignments/bulk-accept', [\App\Http\Controllers\AgencyJurisdictionController::class, 'bulkAccept'])->name('assignments.bulk-accept');
    Route::post('/assignments/bulk-reject', [\App\Http\Controllers\AgencyJurisdictionController::class, 'bulkReject'])->name('assignments.bulk-reject');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\AgencyJurisdictionController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\AgencyJurisdictionController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\AgencyJurisdictionController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');

    // API Routes for AJAX
    Route::get('/api/stats', [\App\Http\Controllers\AgencyJurisdictionController::class, 'getStats'])->name('api.stats');

    // Reports
    Route::post('/reports/generate', [\App\Http\Controllers\AgencyJurisdictionController::class, 'generateReport'])->name('reports.generate');

    // Legacy routes (for backward compatibility)
    Route::get('/assignments/{assignment}/jurisdiction-review-legacy', [\App\Http\Controllers\AgencyAssignmentController::class, 'jurisdictionReview'])->name('assignments.jurisdiction-review-legacy');
    Route::post('/assignments/{assignment}/accept-legacy', [\App\Http\Controllers\AgencyAssignmentController::class, 'acceptWithJurisdiction'])->name('assignments.accept-legacy');
    Route::post('/assignments/{assignment}/reject-legacy', [\App\Http\Controllers\AgencyAssignmentController::class, 'rejectWithJurisdiction'])->name('assignments.reject-legacy');
});

// Settings Routes (for all user types)
Route::get('/settings', [UserController::class, 'showSettings'])->name('settings');
Route::put('/settings', [UserController::class, 'updateSettings'])->name('settings.update');

// Test route for PDF generation (remove this in production)
Route::get('/test-pdf', function () {
    return app(App\Http\Controllers\InquirySubmissionController::class)->exportPdf(request());
})->name('test.pdf');

// Inquiry submission routes (protected by multiple auth guards)
Route::middleware(['auth:public,mcmc,agency'])->group(function () {
    // Inquiry history and listing
    Route::get('/inquiry/history', [InquirySubmissionController::class, 'index'])->name('inquiry.history');

    // Create new inquiry
    Route::get('/inquiry/create', [InquirySubmissionController::class, 'create'])->name('inquiry.create');
    Route::post('/inquiry/store', [InquirySubmissionController::class, 'store'])->name('inquiry.store');

    // View specific inquiry
    Route::get('/inquiry/{id}', [InquirySubmissionController::class, 'show'])->name('inquiry.show');

    // Edit inquiry
    Route::get('/inquiry/{id}/edit', [InquirySubmissionController::class, 'edit'])->name('inquiry.edit');
    Route::put('/inquiry/{id}', [InquirySubmissionController::class, 'update'])->name('inquiry.update');

    // Delete inquiry
    Route::get('/inquiry/{id}/delete', [InquirySubmissionController::class, 'delete'])->name('inquiry.delete');
    Route::delete('/inquiry/{id}', [InquirySubmissionController::class, 'destroy'])->name('inquiry.destroy');

    // Download attachment
    Route::get('/inquiry/{id}/attachment', [InquirySubmissionController::class, 'downloadAttachment'])->name('inquiry.attachment');

    // MCMC Admin routes (only for MCMC users)
    Route::middleware('auth')->prefix('mcmc')->name('mcmc.')->group(function () {
        Route::get('/inquiries', [InquirySubmissionController::class, 'mcmcInquiryList'])->name('inquiries.index');
        Route::get('/inquiries/{id}/approve', [InquirySubmissionController::class, 'showApprovalForm'])->name('inquiries.approve');
        Route::post('/inquiries/{id}/approve', [InquirySubmissionController::class, 'approveInquiry'])->name('inquiries.approve.submit');
        Route::get('/inquiries/reports', [InquirySubmissionController::class, 'inquiryReports'])->name('inquiries.reports');
        Route::get('/inquiries/reports/export-excel', [InquirySubmissionController::class, 'exportExcel'])->name('inquiries.reports.excel');
        Route::get('/inquiries/reports/export-pdf', [InquirySubmissionController::class, 'exportPdf'])->name('inquiries.reports.pdf');

        // Enhanced Inquiry Management Routes (New MVC Implementation)
        // Note: Specific routes must come before parameterized routes to avoid conflicts
        Route::get('/inquiries/list', [InquirySubmissionController::class, 'mcmcInquiryList'])->name('inquiries.list');
        Route::get('/inquiries/manage', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'index'])->name('inquiries.manage');
        Route::get('/inquiries/assign-inquiries', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'assignInquiriesPage'])->name('inquiries.assign-page');
        Route::post('/inquiries/bulk-assign', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'bulkAssign'])->name('inquiries.bulk-assign');

        // Parameterized routes (must come after specific routes)
        Route::get('/inquiries/{inquiry}', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'show'])->name('inquiries.show');
        Route::post('/inquiries/{inquiry}/update-status', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'updateStatus'])->name('inquiries.update-status');
        Route::get('/inquiries/{inquiry}/assign', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'assignForm'])->name('inquiries.assign-form');
        Route::post('/inquiries/{inquiry}/assign', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'assign'])->name('inquiries.assign');
        Route::post('/inquiries/{inquiry}/reassign', [\App\Http\Controllers\McmcSimpleInquiryController::class, 'reassign'])->name('inquiries.reassign');

        // Legacy routes (for backward compatibility) - can be removed after migration
        Route::get('/inquiries/{inquiry}/details', [\App\Http\Controllers\AgencyAssignmentController::class, 'mcmcInquiryDetails'])->name('inquiries.details');
        Route::get('/assignments/{inquiry}/assign-form', [\App\Http\Controllers\AgencyAssignmentController::class, 'showAssignmentForm'])->name('assignments.assign-form');
        Route::post('/assignments/{inquiry}/assign', [\App\Http\Controllers\AgencyAssignmentController::class, 'assignToAgency'])->name('assignments.assign');
        Route::get('/assignments', [\App\Http\Controllers\AgencyAssignmentController::class, 'assignmentsList'])->name('assignments.list');
        Route::get('/assignments/{assignment}/details', [\App\Http\Controllers\AgencyAssignmentController::class, 'showAssignmentDetails'])->name('assignments.details');
        Route::post('/assignments/{assignment}/reassign', [\App\Http\Controllers\AgencyAssignmentController::class, 'reassignInquiry'])->name('assignments.reassign');
        Route::post('/assignments/bulk-assign', [\App\Http\Controllers\AgencyAssignmentController::class, 'bulkAssign'])->name('assignments.bulk-assign');

        // Enhanced Reports and Analytics
        Route::get('/assignments/reports', [\App\Http\Controllers\AgencyAssignmentController::class, 'assignmentReports'])->name('assignments.reports');
        Route::get('/assignments/reports/enhanced', [\App\Http\Controllers\AgencyAssignmentController::class, 'enhancedReports'])->name('assignments.reports.enhanced');
        Route::get('/assignments/reports/export-pdf', [\App\Http\Controllers\AgencyAssignmentController::class, 'exportReportsPDF'])->name('assignments.reports.pdf');
        Route::get('/assignments/reports/export-excel', [\App\Http\Controllers\AgencyAssignmentController::class, 'exportReportsExcel'])->name('assignments.reports.excel');


        //Inquiry Progress Tracking (module 4)
        Route::get('/progress/monitor', [InquiryProgressController::class, 'monitorProgress'])->name('progress.monitor');
        Route::get('/progress/reports', [InquiryProgressController::class, 'generatePerformanceReport'])->name('progress.reports');
        Route::get('/progress/{inquiry}/timeline', [InquiryProgressController::class, 'getProgressHistory'])->name('progress.timeline');

        //inquiry progress tracking (module 4)
        Route::middleware(['auth'])->group(function () {
            Route::post('/progress/{inquiry}/notify', [InquiryProgressController::class, 'sendStatusNotification'])->name('progress.notify');
        });




        // Temporary debug route
        Route::get('/inquiries/debug-data', function () {
            $inquiries = \App\Models\InquirySubmissionRecord::all();

            $debug = [
                'total_inquiries' => $inquiries->count(),
                'sample_data' => $inquiries->take(3)->map(function ($inquiry) {
                    return [
                        'id' => $inquiry->inquiry_ID,
                        'title' => $inquiry->inquiry_Title,
                        'category' => $inquiry->inquiry_Category,
                        'status' => $inquiry->inquiry_Status,
                        'created_at' => $inquiry->inquiry_Created_At,
                        'created_at_formatted' => \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('Y-m-d H:i:s'),
                    ];
                }),
                'all_categories' => $inquiries->pluck('inquiry_Category')->unique()->values(),
                'category_counts' => $inquiries->groupBy('inquiry_Category')->map(function ($group) {
                    return $group->count();
                }),
                'date_range_test' => [
                    'today' => now()->format('Y-m-d'),
                    'last_month' => now()->subMonth()->format('Y-m-d'),
                    'inquiries_today' => $inquiries->whereDate('inquiry_Created_At', now()->format('Y-m-d'))->count(),
                    'inquiries_last_30_days' => $inquiries->where('inquiry_Created_At', '>=', now()->subDays(30))->count(),
                ]
            ];

            return response()->json($debug, JSON_PRETTY_PRINT);
        });
    });
});
