<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\UserRecord;
use App\Models\Agency;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Notifications\AgencyCredentialsNotification;

class UserController extends Controller
{
    // ==================== AUTH METHODS ====================

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('UserRegistration');
    }

    /**
     * Handle registration request
     */
    public function register(RegisterRequest $request)
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'password' => Hash::make($request->password),
            'user_type' => 'public',
            'email_verified_at' => null,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData['profile_picture'] = $path;
        }

        $user = UserRecord::create($userData);

        // Send email verification
        $user->sendEmailVerificationNotification();

        return redirect()->route('login', ['type' => 'public'])
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }

    /**
     * Show the login form
     */
    public function showLoginForm(Request $request)
    {
        $type = $request->get('type', 'public');
        return view('UserLogin', compact('type'));
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $type = $request->type;
        $guard = $type;

        // Find user by email and type (now includes agencies)
        $user = UserRecord::where('email', $credentials['email'])
            ->where('user_type', $type)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Password check
        if (Hash::check($credentials['password'], $user->password)) {
            // Clear other guard sessions
            $this->clearOtherGuardSessions($request, $guard);

            Auth::guard($guard)->login($user, $request->filled('remember'));
            $request->session()->regenerate();

            // Log successful login for debugging
            Log::info("User logged in successfully", [
                'user_id' => $user->id,
                'user_type' => $type,
                'guard' => $guard
            ]);

            // Redirect based on user type
            switch ($type) {
                case 'public':
                    return redirect()->route('public.dashboard');
                case 'mcmc':
                    return redirect()->route('mcmc.dashboard');
                case 'agency':
                    return redirect()->route('agency.dashboard');
                default:
                    return redirect()->route('home');
            }
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Handle agency login
     */
    private function handleAgencyLogin(Request $request, array $credentials)
    {
        $agency = Agency::where('agency_Email', $credentials['email'])->first();

        if (!$agency) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Check password
        if (Hash::check($credentials['password'], $agency->agency_Password)) {
            // Clear other guard sessions
            $this->clearOtherGuardSessions($request, 'agency');

            Auth::guard('agency')->login($agency, $request->filled('remember'));
            $request->session()->regenerate();

            // Log successful login for debugging
            Log::info("Agency logged in successfully", [
                'agency_id' => $agency->agency_ID,
                'agency_email' => $agency->agency_Email,
                'guard' => 'agency'
            ]);

            // Check if first time login
            if ($agency->isFirstTimeLogin()) {
                return redirect()->route('agency.password.reset');
            }

            return redirect()->route('agency.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Clear sessions from other guards to prevent conflicts
     */
    private function clearOtherGuardSessions(Request $request, string $currentGuard)
    {
        $guards = ['web', 'public', 'mcmc', 'agency'];

        foreach ($guards as $guard) {
            if ($guard !== $currentGuard && Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $redirectRoute = 'login'; // Default redirect

        // Determine which guard to logout from and set appropriate redirect
        if (Auth::guard('public')->check()) {
            Auth::guard('public')->logout();
            $redirectRoute = route('login', ['type' => 'public']);
        } elseif (Auth::guard('mcmc')->check()) {
            Auth::guard('mcmc')->logout();
            $redirectRoute = route('login', ['type' => 'mcmc']);
        } elseif (Auth::guard('agency')->check()) {
            Auth::guard('agency')->logout();
            $redirectRoute = route('login', ['type' => 'agency']);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($redirectRoute)->with('message', 'You have been logged out successfully.');
    }

    // ==================== PUBLIC USER METHODS ====================

    /**
     * Show public user registration form
     */
    public function showPublicRegistrationForm()
    {
        return view('users.register');
    }

    /**
     * Handle public user registration
     */
    public function registerPublicUser(RegisterRequest $request)
    {
        $user = UserRecord::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'password' => Hash::make($request->password),
            'user_type' => 'public',
        ]);

        // Send email verification
        $user->sendEmailVerificationNotification();

        Auth::guard('public')->login($user);

        return redirect()->route('verification.notice');
    }

    /**
     * Show public user dashboard
     */
    public function publicDashboard()
    {
        $user = Auth::guard('public')->user();
        return view('UserDashboard', compact('user'));
    }

    /**
     * Show public user profile
     */
    public function showPublicProfile()
    {
        $user = Auth::guard('public')->user();
        return view('UserProfile', compact('user'));
    }

    /**
     * Update public user profile
     */
    public function updatePublicProfile(ProfileUpdateRequest $request)
    {
        /** @var UserRecord $user */
        $user = Auth::guard('public')->user();

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('public.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show public user settings
     */
    public function showPublicSettings()
    {
        /** @var UserRecord $user */
        $user = Auth::guard('public')->user();

        return view('UserSettings', compact('user'));
    }

    /**
     * Update public user settings
     */
    public function updatePublicSettings(Request $request)
    {
        /** @var UserRecord $user */
        $user = Auth::guard('public')->user();

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'language' => 'string|in:en,ms',
            'timezone' => 'string',
        ]);

        $user->update([
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'language' => $request->language ?? 'en',
            'timezone' => $request->timezone ?? 'Asia/Kuala_Lumpur',
        ]);

        return redirect()->route('public.settings')->with('success', 'Settings updated successfully!');
    }

    // ==================== AGENCY METHODS ====================

    /**
     * Show agency dashboard
     */
    public function agencyDashboard()
    {
        /** @var UserRecord $user */
        $user = Auth::guard('agency')->user();

        if (!$user) {
            return redirect()->route('login', ['type' => 'agency']);
        }

        return view('UserDashboard', compact('user'));
    }

    /**
     * Show agency profile
     */
    public function showAgencyProfile()
    {
        /** @var UserRecord $user */
        $user = Auth::guard('agency')->user();

        if (!$user) {
            return redirect()->route('login', ['type' => 'agency']);
        }

        return view('UserProfile', compact('user'));
    }

    /**
     * Update agency profile
     */
    public function updateAgencyProfile(Request $request)
    {
        /** @var UserRecord $user */
        $user = Auth::guard('agency')->user();

        if (!$user) {
            return redirect()->route('login', ['type' => 'agency']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'contact_number' => 'required|string|max:20',
            'current_password' => 'nullable|string',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $updateData['password'] = Hash::make($request->password);
            $updateData['force_password_reset'] = false; // Remove force reset flag
        }

        $user->update($updateData);

        return redirect()->route('agency.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show agency password reset form
     */
    public function showAgencyPasswordResetForm()
    {
        /** @var UserRecord $user */
        $user = Auth::guard('agency')->user();

        if (!$user) {
            return redirect()->route('login', ['type' => 'agency']);
        }

        if (!$user->needsPasswordReset()) {
            return redirect()->route('agency.dashboard');
        }

        return view('agency.password-reset', compact('user'));
    }

    /**
     * Handle agency password reset
     */
    public function resetAgencyPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var UserRecord $user */
        $user = Auth::guard('agency')->user();

        if (!$user) {
            return redirect()->route('login', ['type' => 'agency']);
        }

        if (!$user->needsPasswordReset()) {
            return redirect()->route('agency.dashboard');
        }

        $user->update([
            'password' => Hash::make($request->password),
            'force_password_reset' => false,
            'temporary_password' => null,
        ]);

        return redirect()->route('agency.dashboard')->with('success', 'Password updated successfully!');
    }

    /**
     * Show agency settings
     */
    public function showAgencySettings()
    {
        /** @var UserRecord $user */
        $user = Auth::guard('agency')->user();

        if (!$user) {
            return redirect()->route('login', ['type' => 'agency']);
        }

        return view('UserSettings', compact('user'));
    }

    /**
     * Update agency settings
     */
    public function updateAgencySettings(Request $request)
    {
        /** @var UserRecord $user */
        $user = Auth::guard('agency')->user();

        if (!$user) {
            return redirect()->route('login', ['type' => 'agency']);
        }

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'language' => 'string|in:en,ms',
            'timezone' => 'string',
        ]);

        $user->update([
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'language' => $request->language ?? 'en',
            'timezone' => $request->timezone ?? 'Asia/Kuala_Lumpur',
        ]);

        return redirect()->route('agency.settings')->with('success', 'Settings updated successfully!');
    }

    // ==================== MCMC STAFF METHODS ====================

    /**
     * Show MCMC staff dashboard
     */
    public function mcmcDashboard()
    {
        $user = Auth::guard('mcmc')->user();
        $publicUsersCount = UserRecord::where('user_type', 'public')->count();
        $agenciesCount = UserRecord::where('user_type', 'agency')->count();

        return view('UserDashboard', compact('user', 'publicUsersCount', 'agenciesCount'));
    }

    /**
     * Show MCMC staff profile
     */
    public function showMcmcProfile()
    {
        $user = Auth::guard('mcmc')->user();
        return view('UserProfile', compact('user'));
    }

    /**
     * Update MCMC staff profile
     */
    public function updateMcmcProfile(Request $request)
    {
        /** @var UserRecord $user */
        $user = Auth::guard('mcmc')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'contact_number' => 'required|string|max:20',
            'current_password' => 'nullable|string',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('mcmc.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show MCMC staff settings
     */
    public function showMcmcSettings()
    {
        /** @var UserRecord $user */
        $user = Auth::guard('mcmc')->user();

        return view('UserSettings', compact('user'));
    }

    /**
     * Update MCMC staff settings
     */
    public function updateMcmcSettings(Request $request)
    {
        /** @var UserRecord $user */
        $user = Auth::guard('mcmc')->user();

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'language' => 'string|in:en,ms',
            'timezone' => 'string',
        ]);

        $user->update([
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'language' => $request->language ?? 'en',
            'timezone' => $request->timezone ?? 'Asia/Kuala_Lumpur',
        ]);

        return redirect()->route('mcmc.settings')->with('success', 'Settings updated successfully!');
    }

    /**
     * Show all users (MCMC staff only)
     */
    public function showAllUsers()
    {
        try {
            // Clear any potential query cache
            DB::purge();

            // Get public users with explicit filtering and validation
            $publicUsers = UserRecord::select('*')
                ->where('user_type', '=', 'public')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'public_page');

            // Get agency users from agencies table
            $agencies = Agency::select('*')
                ->orderBy('agency_Created_At', 'desc')
                ->paginate(10, ['*'], 'agency_page');

            // Get accurate counts with fresh queries
            $publicUsersCount = UserRecord::where('user_type', '=', 'public')->count();
            $agenciesCount = Agency::count();

            // Additional validation - check if we have any data inconsistencies
            $allUsersCount = UserRecord::count();
            $mcmcUsersCount = UserRecord::where('user_type', '=', 'mcmc')->count();
            $totalCalculated = $publicUsersCount + $agenciesCount + $mcmcUsersCount;

            // Debug logging
            if (config('app.debug')) {
                Log::info('MCMC User Management Debug', [
                    'all_users_count' => $allUsersCount,
                    'public_users_count' => $publicUsersCount,
                    'agencies_count' => $agenciesCount,
                    'mcmc_users_count' => $mcmcUsersCount,
                    'total_calculated' => $totalCalculated,
                    'public_users_paginated_count' => $publicUsers->count(),
                    'agencies_paginated_count' => $agencies->count(),
                    'public_users_total' => $publicUsers->total(),
                    'agencies_total' => $agencies->total(),
                    'request_params' => request()->all(),
                ]);

                // Log actual user types in the results
                $publicUserTypes = $publicUsers->pluck('user_type')->unique()->toArray();
                $agencyUserTypes = $agencies->pluck('user_type')->unique()->toArray();

                Log::info('User Types in Results', [
                    'public_tab_user_types' => $publicUserTypes,
                    'agency_tab_user_types' => $agencyUserTypes,
                ]);
            }

            return view('mcmc.manage-users', [
                'user' => Auth::guard('mcmc')->user(),
                'publicUsers' => $publicUsers,
                'agencies' => $agencies,
                'publicUsersCount' => $publicUsersCount,
                'agenciesCount' => $agenciesCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in showAllUsers: ' . $e->getMessage());

            return redirect()->route('mcmc.dashboard')
                ->with('error', 'There was an error loading the user management page. Please try again.');
        }
    }

    /**
     * Get public users only (MCMC staff only)
     */
    public function getPublicUsers()
    {
        $publicUsers = UserRecord::where('user_type', '=', 'public')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'users' => $publicUsers->items(),
            'pagination' => [
                'current_page' => $publicUsers->currentPage(),
                'last_page' => $publicUsers->lastPage(),
                'total' => $publicUsers->total(),
                'per_page' => $publicUsers->perPage(),
            ]
        ]);
    }

    /**
     * Get agency users only (MCMC staff only)
     */
    public function getAgencyUsers()
    {
        $agencies = UserRecord::where('user_type', '=', 'agency')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'users' => $agencies->items(),
            'pagination' => [
                'current_page' => $agencies->currentPage(),
                'last_page' => $agencies->lastPage(),
                'total' => $agencies->total(),
                'per_page' => $agencies->perPage(),
            ]
        ]);
    }

    /**
     * Send verification email to a user (MCMC staff only)
     */
    public function sendVerificationEmail(Request $request, $userId)
    {
        $user = UserRecord::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['error' => 'User email is already verified.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['success' => 'Verification email sent successfully.']);
    }

    /**
     * Reset agency password (MCMC staff only)
     */
    public function resetAgencyPasswordByMcmc(Request $request, $agencyId)
    {
        $agency = UserRecord::where('id', $agencyId)
            ->where('user_type', 'agency')
            ->firstOrFail();

        // Generate new temporary password
        $temporaryPassword = Str::random(12);

        $agency->update([
            'temporary_password' => Hash::make($temporaryPassword),
            'force_password_reset' => true,
        ]);

        // Send email with new temporary credentials
        $this->sendAgencyCredentials($agency, $temporaryPassword);

        return response()->json(['success' => 'Password reset successfully. New credentials sent to agency email.']);
    }

    /**
     * Generate user report (MCMC staff only)
     */
    public function generateUserReport(Request $request)
    {
        try {
            $format = $request->input('format', 'pdf');

            // Debug logging
            Log::info('User report generation started', ['format' => $format]);

            // Get all public users
            $publicUsers = UserRecord::where('user_type', 'public')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_type' => 'Public User',
                        'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
                        'email_verified_at' => $user->email_verified_at,
                        'is_active' => true, // Assuming all users are active
                    ];
                })
                ->toArray();

            // Get all agencies
            $agencies = Agency::orderBy('agency_Created_At', 'desc')
                ->get()
                ->map(function ($agency) {
                    return [
                        'name' => $agency->agency_Name,
                        'email' => $agency->agency_Email,
                        'user_type' => 'Agency (' . ucfirst($agency->agency_Type ?? 'Unknown') . ')',
                        'created_at' => $agency->agency_Created_At ? $agency->agency_Created_At->toDateTimeString() : null,
                        'email_verified_at' => $agency->agency_Created_At, // Agencies are considered verified upon creation
                        'is_active' => !$agency->agency_First_Time_Login, // Active if not first time login
                    ];
                })
                ->toArray();

            $data = [
                'public_users' => $publicUsers,
                'agencies' => $agencies,
            ];

            Log::info('Data prepared for report', [
                'public_users_count' => count($publicUsers),
                'agencies_count' => count($agencies)
            ]);

            if ($format === 'pdf') {
                Log::info('Generating PDF report');
                return $this->generateUserReportPdf($data);
            } else {
                Log::info('Generating Excel report');
                return $this->generateUserReportExcel($data);
            }
        } catch (\Exception $e) {
            Log::error('Error generating user report: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate user report. Please try again.');
        }
    }

    /**
     * Generate PDF user report
     */
    private function generateUserReportPdf($data)
    {
        $html = $this->generateUserReportHtml($data);

        $filename = 'user-report-' . now()->format('Y-m-d-H-i-s') . '.html';

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generate Excel user report
     */
    private function generateUserReportExcel($data)
    {
        $csv = $this->generateUserReportCsv($data);

        $filename = 'user-report-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generate HTML for user report
     */
    private function generateUserReportHtml($data)
    {
        $publicCount = count($data['public_users']);
        $agencyCount = count($data['agencies']);
        $totalCount = $publicCount + $agencyCount;
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySebenarnya - Complete Users Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .report-meta {
            color: #666;
            font-size: 14px;
        }
        .summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        .summary h3 {
            margin-top: 0;
            color: #007bff;
            font-size: 20px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: left;
            font-size: 13px;
        }
        th {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e3f2fd;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 2px solid #dee2e6;
            padding-top: 20px;
        }
        .status-verified { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; font-weight: bold; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">MySebenarnya</div>
        <div class="report-title">Complete Users Report</div>
        <div class="report-meta">Generated on ' . $generatedAt . '</div>
    </div>

    <div class="summary">
        <h3>📊 Report Summary</h3>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">' . $totalCount . '</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">' . $publicCount . '</div>
                <div class="stat-label">Public Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">' . $agencyCount . '</div>
                <div class="stat-label">Agencies</div>
            </div>
        </div>
    </div>';

        // Public Users Section
        if (!empty($data['public_users'])) {
            $html .= '<div class="section-title">👥 Public Users (' . $publicCount . ')</div>';
            $html .= $this->generateUserReportTable($data['public_users']);
        }

        // Agencies Section
        if (!empty($data['agencies'])) {
            $html .= '<div class="section-title">🏢 Agencies (' . $agencyCount . ')</div>';
            $html .= $this->generateUserReportTable($data['agencies']);
        }

        $html .= '
    <div class="footer">
        <p><strong>MySebenarnya Platform</strong> - User Management System</p>
        <p>© ' . date('Y') . ' Malaysian Communications and Multimedia Commission (MCMC)</p>
        <p>This report contains confidential information. Handle with care.</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Generate user table HTML for report
     */
    private function generateUserReportTable($users)
    {
        if (empty($users)) {
            return '<p style="text-align: center; color: #666; font-style: italic;">No users found.</p>';
        }

        $html = '<table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Registration Date</th>
                    <th>Email Status</th>
                    <th>Account Status</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($users as $user) {
            $registrationDate = isset($user['created_at']) && $user['created_at']
                ? date('M j, Y', strtotime($user['created_at']))
                : '<span style="color: #999;">N/A</span>';

            $emailVerified = isset($user['email_verified_at']) && $user['email_verified_at']
                ? '<span class="status-verified">✓ Verified</span>'
                : '<span class="status-pending">⏳ Pending</span>';

            $status = isset($user['is_active']) && $user['is_active']
                ? '<span class="status-active">✓ Active</span>'
                : '<span class="status-inactive">✗ Inactive</span>';

            $html .= '<tr>
                <td><strong>' . htmlspecialchars($user['name'] ?? 'N/A') . '</strong></td>
                <td>' . htmlspecialchars($user['email'] ?? 'N/A') . '</td>
                <td><em>' . htmlspecialchars($user['user_type'] ?? 'N/A') . '</em></td>
                <td>' . $registrationDate . '</td>
                <td>' . $emailVerified . '</td>
                <td>' . $status . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Generate CSV content for user report
     */
    private function generateUserReportCsv($data)
    {
        $publicCount = count($data['public_users']);
        $agencyCount = count($data['agencies']);
        $totalCount = $publicCount + $agencyCount;

        $csv = "MySebenarnya - Complete Users Report\n";
        $csv .= "Generated on: " . now()->format('F j, Y \a\t g:i A') . "\n";
        $csv .= "Total Users: $totalCount (Public: $publicCount, Agencies: $agencyCount)\n\n";

        // Public Users
        if (!empty($data['public_users'])) {
            $csv .= "PUBLIC USERS ($publicCount)\n";
            $csv .= "Name,Email,User Type,Registration Date,Email Verified,Status\n";

            foreach ($data['public_users'] as $user) {
                $registrationDate = isset($user['created_at']) && $user['created_at']
                    ? date('M j, Y', strtotime($user['created_at']))
                    : 'N/A';
                $emailVerified = isset($user['email_verified_at']) && $user['email_verified_at'] ? 'Yes' : 'No';
                $status = isset($user['is_active']) && $user['is_active'] ? 'Active' : 'Inactive';

                $csv .= '"' . str_replace('"', '""', $user['name'] ?? 'N/A') . '",';
                $csv .= '"' . str_replace('"', '""', $user['email'] ?? 'N/A') . '",';
                $csv .= '"' . str_replace('"', '""', $user['user_type'] ?? 'N/A') . '",';
                $csv .= '"' . $registrationDate . '",';
                $csv .= '"' . $emailVerified . '",';
                $csv .= '"' . $status . '"' . "\n";
            }
            $csv .= "\n";
        }

        // Agencies
        if (!empty($data['agencies'])) {
            $csv .= "AGENCIES ($agencyCount)\n";
            $csv .= "Name,Email,User Type,Registration Date,Email Verified,Status\n";

            foreach ($data['agencies'] as $user) {
                $registrationDate = isset($user['created_at']) && $user['created_at']
                    ? date('M j, Y', strtotime($user['created_at']))
                    : 'N/A';
                $emailVerified = isset($user['email_verified_at']) && $user['email_verified_at'] ? 'Yes' : 'No';
                $status = isset($user['is_active']) && $user['is_active'] ? 'Active' : 'Inactive';

                $csv .= '"' . str_replace('"', '""', $user['name'] ?? 'N/A') . '",';
                $csv .= '"' . str_replace('"', '""', $user['email'] ?? 'N/A') . '",';
                $csv .= '"' . str_replace('"', '""', $user['user_type'] ?? 'N/A') . '",';
                $csv .= '"' . $registrationDate . '",';
                $csv .= '"' . $emailVerified . '",';
                $csv .= '"' . $status . '"' . "\n";
            }
        }

        return $csv;
    }

    /**
     * Show agency registration form (MCMC staff only)
     */
    public function showAgencyRegistrationForm()
    {
        return view('mcmc.register-agency');
    }

    /**
     * Register new agency (MCMC staff only)
     */
    public function registerAgency(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:ministry,department,statutory_body,government_agency,other',
            'email' => 'required|string|email|max:255|unique:agencies,agency_Email|unique:users,email',
            'phone' => 'required|string|max:20',
        ]);

        // Generate temporary password
        $temporaryPassword = Str::random(12);
        $hashedPassword = Hash::make($temporaryPassword);

        DB::transaction(function () use ($request, $temporaryPassword, $hashedPassword, &$agency, &$user) {
            // Create agency record in agencies table
            $agency = Agency::create([
                'agency_Name' => $request->name,
                'agency_Type' => $request->type,
                'agency_Email' => $request->email,
                'agency_Phone' => $request->phone,
                'agency_Password' => $hashedPassword,
                'agency_First_Time_Login' => true,
                'agency_Created_At' => now(),
                'agency_Updated_At' => now(),
            ]);

            // Also create corresponding user record in users table for unified authentication
            $user = UserRecord::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->phone,
                'user_type' => 'agency',
                'password' => $hashedPassword,
                'agency_ID' => $agency->agency_ID, // Link to agencies table
                'email_verified_at' => now(), // Auto-verify agency emails
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Send email with temporary credentials
        $this->sendAgencyCredentials($agency, $temporaryPassword);

        return redirect()->route('mcmc.register.agency')
            ->with('success', 'Agency registered successfully! Login credentials have been sent to their email.');
    }

    /**
     * Show reports page (MCMC staff only)
     */
    public function showReports()
    {
        return view('mcmc.reports');
    }

    /**
     * Generate user report (MCMC staff only)
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:public_users,agencies,all',
            'format' => 'required|in:pdf,excel',
        ]);

        $reportType = $request->report_type;
        $format = $request->format;

        $data = [];

        switch ($reportType) {
            case 'public_users':
                $data = UserRecord::where('user_type', 'public')->get()->toArray();
                break;
            case 'agencies':
                $data = UserRecord::where('user_type', 'agency')->get()->toArray();
                break;
            case 'all':
                $data = [
                    'public_users' => UserRecord::where('user_type', 'public')->get()->toArray(),
                    'agencies' => UserRecord::where('user_type', 'agency')->get()->toArray(),
                ];
                break;
        }

        if ($format === 'pdf') {
            return $this->generatePdfReport($data, $reportType);
        } else {
            return $this->generateExcelReport($data, $reportType);
        }
    }

    // ==================== EMAIL VERIFICATION METHODS ====================

    /**
     * Show email verification notice
     */
    public function emailVerificationNotice()
    {
        /** @var UserRecord $user */
        $user = Auth::guard('public')->user();

        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('public.dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Handle email verification
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('public.dashboard')->with('success', 'Email verified successfully!');
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        /** @var UserRecord $user */
        $user = Auth::guard('public')->user();

        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('public.dashboard');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }

    // ==================== GENERAL SETTINGS METHODS ====================

    /**
     * Show settings page (redirects to appropriate user type settings)
     */
    public function showSettings()
    {
        if (Auth::guard('public')->check()) {
            return redirect()->route('public.settings');
        } elseif (Auth::guard('mcmc')->check()) {
            return redirect()->route('mcmc.settings');
        } elseif (Auth::guard('agency')->check()) {
            return redirect()->route('agency.settings');
        }

        return redirect()->route('login');
    }

    /**
     * Update settings (redirects to appropriate user type settings)
     */
    public function updateSettings(Request $request)
    {
        if (Auth::guard('public')->check()) {
            return $this->updatePublicSettings($request);
        } elseif (Auth::guard('mcmc')->check()) {
            return $this->updateMcmcSettings($request);
        } elseif (Auth::guard('agency')->check()) {
            return $this->updateAgencySettings($request);
        }

        return redirect()->route('login');
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Send agency credentials via email
     */
    private function sendAgencyCredentials($agency, $temporaryPassword)
    {
        $agency->notify(new AgencyCredentialsNotification($temporaryPassword));
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport($data, $reportType)
    {
        try {
            // Generate HTML content for the report
            $html = $this->generateReportHtml($data, $reportType);

            // Create filename with timestamp
            $filename = 'report_' . $reportType . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // For now, we'll generate an HTML report that can be printed to PDF
            // This is a temporary solution until we can install proper PDF libraries
            return response($html)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'inline; filename="' . str_replace('.pdf', '.html', $filename) . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
        }
    }

    /**
     * Generate Excel report
     */
    private function generateExcelReport($data, $reportType)
    {
        try {
            // Generate CSV content (Excel-compatible)
            $csv = $this->generateCsvContent($data, $reportType);

            // Create filename with timestamp
            $filename = 'report_' . $reportType . '_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate Excel report: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML content for PDF report
     */
    private function generateReportHtml($data, $reportType)
    {
        $title = $this->getReportTitle($reportType);
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-meta {
            color: #666;
            font-size: 14px;
        }
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .summary h3 {
            margin-top: 0;
            color: #007bff;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">MySebenarnya</div>
        <div class="report-title">' . $title . '</div>
        <div class="report-meta">Generated on ' . $generatedAt . '</div>
    </div>';

        if ($reportType === 'all') {
            $html .= $this->generateAllUsersReportHtml($data);
        } else {
            $html .= $this->generateSingleTypeReportHtml($data, $reportType);
        }

        $html .= '
    <div class="footer">
        <p>This report was generated by MySebenarnya platform.</p>
        <p>© ' . date('Y') . ' Malaysian Communications and Multimedia Commission (MCMC)</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Generate HTML for all users report
     */
    private function generateAllUsersReportHtml($data)
    {
        $publicCount = count($data['public_users']);
        $agencyCount = count($data['agencies']);
        $totalCount = $publicCount + $agencyCount;

        $html = '
    <div class="summary">
        <h3>Report Summary</h3>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">' . $totalCount . '</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">' . $publicCount . '</div>
                <div class="stat-label">Public Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">' . $agencyCount . '</div>
                <div class="stat-label">Agencies</div>
            </div>
        </div>
    </div>';

        // Public Users Section
        if (!empty($data['public_users'])) {
            $html .= '<h3>Public Users (' . $publicCount . ')</h3>';
            $html .= $this->generateUserTable($data['public_users']);
        }

        // Agencies Section
        if (!empty($data['agencies'])) {
            $html .= '<h3 style="margin-top: 40px;">Agencies (' . $agencyCount . ')</h3>';
            $html .= $this->generateUserTable($data['agencies']);
        }

        return $html;
    }

    /**
     * Generate HTML for single type report
     */
    private function generateSingleTypeReportHtml($data, $reportType)
    {
        $count = count($data);
        $typeLabel = $reportType === 'public_users' ? 'Public Users' : 'Agencies';

        $html = '
    <div class="summary">
        <h3>Report Summary</h3>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">' . $count . '</div>
                <div class="stat-label">' . $typeLabel . '</div>
            </div>
        </div>
    </div>';

        $html .= '<h3>' . $typeLabel . ' (' . $count . ')</h3>';
        $html .= $this->generateUserTable($data);

        return $html;
    }

    /**
     * Generate user table HTML
     */
    private function generateUserTable($users)
    {
        if (empty($users)) {
            return '<p>No users found.</p>';
        }

        $html = '<table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Registration Date</th>
                    <th>Email Verified</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($users as $user) {
            $registrationDate = isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A';
            $emailVerified = isset($user['email_verified_at']) && $user['email_verified_at'] ? 'Yes' : 'No';
            $status = isset($user['is_active']) && $user['is_active'] ? 'Active' : 'Inactive';

            $html .= '<tr>
                <td>' . htmlspecialchars($user['name'] ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($user['email'] ?? 'N/A') . '</td>
                <td>' . htmlspecialchars(ucfirst($user['user_type'] ?? 'N/A')) . '</td>
                <td>' . $registrationDate . '</td>
                <td>' . $emailVerified . '</td>
                <td>' . $status . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Generate CSV content for Excel export
     */
    private function generateCsvContent($data, $reportType)
    {
        $csv = '';

        // Add header
        $csv .= "MySebenarnya - " . $this->getReportTitle($reportType) . "\n";
        $csv .= "Generated on: " . now()->format('F j, Y \a\t g:i A') . "\n\n";

        if ($reportType === 'all') {
            // Public Users
            if (!empty($data['public_users'])) {
                $csv .= "PUBLIC USERS\n";
                $csv .= $this->generateCsvTable($data['public_users']);
                $csv .= "\n";
            }

            // Agencies
            if (!empty($data['agencies'])) {
                $csv .= "AGENCIES\n";
                $csv .= $this->generateCsvTable($data['agencies']);
            }
        } else {
            $csv .= $this->generateCsvTable($data);
        }

        return $csv;
    }

    /**
     * Generate CSV table
     */
    private function generateCsvTable($users)
    {
        if (empty($users)) {
            return "No users found.\n";
        }

        $csv = "Name,Email,User Type,Registration Date,Email Verified,Status\n";

        foreach ($users as $user) {
            $registrationDate = isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A';
            $emailVerified = isset($user['email_verified_at']) && $user['email_verified_at'] ? 'Yes' : 'No';
            $status = isset($user['is_active']) && $user['is_active'] ? 'Active' : 'Inactive';

            $csv .= '"' . str_replace('"', '""', $user['name'] ?? 'N/A') . '",';
            $csv .= '"' . str_replace('"', '""', $user['email'] ?? 'N/A') . '",';
            $csv .= '"' . str_replace('"', '""', ucfirst($user['user_type'] ?? 'N/A')) . '",';
            $csv .= '"' . $registrationDate . '",';
            $csv .= '"' . $emailVerified . '",';
            $csv .= '"' . $status . '"' . "\n";
        }

        return $csv;
    }

    /**
     * Get report title based on type
     */
    private function getReportTitle($reportType)
    {
        switch ($reportType) {
            case 'public_users':
                return 'Public Users Report';
            case 'agencies':
                return 'Agencies Report';
            case 'all':
                return 'Complete Users Report';
            default:
                return 'User Report';
        }
    }
}
