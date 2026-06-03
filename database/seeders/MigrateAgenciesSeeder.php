<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\UserRecord;
use App\Models\Agency;
use Carbon\Carbon;

class MigrateAgenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting migration of agency data from users table to agencies table...');

        // Get all agency users from the users table
        $agencyUsers = UserRecord::where('user_type', 'agency')->get();

        if ($agencyUsers->isEmpty()) {
            $this->command->info('No agency users found in users table.');
            return;
        }

        $migratedCount = 0;

        foreach ($agencyUsers as $user) {
            // Check if agency already exists
            $existingAgency = Agency::where('agency_Email', $user->email)->first();

            if (!$existingAgency) {
                // Determine agency type based on email domain or name
                $agencyType = $this->determineAgencyType($user->name, $user->email);

                // Create new agency record
                Agency::create([
                    'agency_Name' => $user->name,
                    'agency_Type' => $agencyType,
                    'agency_Email' => $user->email,
                    'agency_Phone' => $user->contact_number ?? 'Not provided',
                    'agency_Password' => $user->password, // Keep the hashed password
                    'agency_First_Time_Login' => $user->force_password_reset ?? true,
                    'agency_Created_At' => $user->created_at ?? now(),
                    'agency_Updated_At' => $user->updated_at ?? now(),
                ]);

                $migratedCount++;
                $this->command->info("✓ Migrated: {$user->name} ({$user->email})");
            } else {
                $this->command->info("- Skipped: {$user->name} ({$user->email}) - Already exists");
            }
        }

        $this->command->info("Migration completed! {$migratedCount} agencies migrated.");

        // Display the migrated agencies
        if ($migratedCount > 0) {
            $this->command->info('');
            $this->command->info('Migrated Agencies:');
            $this->command->info('==================');

            $agencies = Agency::all();
            foreach ($agencies as $agency) {
                $this->command->info("Name: {$agency->agency_Name}");
                $this->command->info("Type: {$agency->agency_Type}");
                $this->command->info("Email: {$agency->agency_Email}");
                $this->command->info("First Time Login: " . ($agency->agency_First_Time_Login ? 'Yes' : 'No'));
                $this->command->info('---');
            }
        }
    }

    /**
     * Determine agency type based on name and email
     */
    private function determineAgencyType($name, $email): string
    {
        $name = strtolower($name);
        $email = strtolower($email);

        // Ministry mappings
        if (str_contains($name, 'health') || str_contains($email, 'moh')) {
            return 'ministry_health';
        }

        if (str_contains($name, 'education') || str_contains($email, 'moe')) {
            return 'ministry_education';
        }

        if (str_contains($name, 'finance') || str_contains($email, 'mof')) {
            return 'ministry_finance';
        }

        if (str_contains($name, 'transport') || str_contains($email, 'mot')) {
            return 'ministry_transport';
        }

        if (str_contains($name, 'energy') || str_contains($email, 'mestecc')) {
            return 'ministry_energy';
        }

        // Department mappings
        if (str_contains($name, 'department') || str_contains($name, 'jabatan')) {
            return 'government_department';
        }

        // Agency mappings
        if (str_contains($name, 'agency') || str_contains($name, 'agensi')) {
            return 'government_agency';
        }

        // Default for ministries
        if (str_contains($name, 'ministry') || str_contains($name, 'kementerian')) {
            return 'ministry';
        }

        // Default type
        return 'government_agency';
    }
}
