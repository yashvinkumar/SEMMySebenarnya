<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserRecord;

class TestUserFiltering extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user-filtering';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user filtering logic to debug MCMC user management issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing User Filtering Logic...');
        $this->newLine();

        // Test basic counts
        $allUsers = UserRecord::count();
        $publicUsers = UserRecord::where('user_type', '=', 'public')->count();
        $agencyUsers = UserRecord::where('user_type', '=', 'agency')->count();
        $mcmcUsers = UserRecord::where('user_type', '=', 'mcmc')->count();

        $this->info("Total Users: {$allUsers}");
        $this->info("Public Users: {$publicUsers}");
        $this->info("Agency Users: {$agencyUsers}");
        $this->info("MCMC Users: {$mcmcUsers}");
        $this->info("Sum: " . ($publicUsers + $agencyUsers + $mcmcUsers));
        $this->newLine();

        // Test actual data retrieval
        $this->info('Testing Public Users Query:');
        $publicUsersData = UserRecord::where('user_type', '=', 'public')->get();
        foreach ($publicUsersData as $user) {
            $this->line("ID: {$user->id}, Name: {$user->name}, Type: {$user->user_type}");
        }
        $this->newLine();

        $this->info('Testing Agency Users Query:');
        $agencyUsersData = UserRecord::where('user_type', '=', 'agency')->get();
        foreach ($agencyUsersData as $user) {
            $this->line("ID: {$user->id}, Name: {$user->name}, Type: {$user->user_type}");
        }
        $this->newLine();

        // Test for any data inconsistencies
        $this->info('Checking for data inconsistencies:');
        $allUserTypes = UserRecord::select('user_type')->distinct()->pluck('user_type')->toArray();
        $this->info('All user types in database: ' . implode(', ', $allUserTypes));

        // Check for any null or unexpected user types
        $nullUserTypes = UserRecord::whereNull('user_type')->count();
        $this->info("Users with null user_type: {$nullUserTypes}");

        $unexpectedTypes = UserRecord::whereNotIn('user_type', ['public', 'agency', 'mcmc'])->get();
        if ($unexpectedTypes->count() > 0) {
            $this->warn('Found users with unexpected user_type values:');
            foreach ($unexpectedTypes as $user) {
                $this->line("ID: {$user->id}, Name: {$user->name}, Type: '{$user->user_type}'");
            }
        } else {
            $this->info('No unexpected user types found.');
        }

        $this->newLine();
        $this->info('Test completed!');
    }
}
