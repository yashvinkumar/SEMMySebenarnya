<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\PublicUser;
use Carbon\Carbon;

class PublicUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Public Users table...');

        $publicUsers = [
            [
                'user_Name' => 'John Doe',
                'user_Email' => 'john.doe@example.com',
                'user_Phone_Number' => '+60123456789',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'active',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
            [
                'user_Name' => 'Jane Smith',
                'user_Email' => 'jane.smith@example.com',
                'user_Phone_Number' => '+60123456788',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'active',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
            [
                'user_Name' => 'Ali Rahman',
                'user_Email' => 'ali.rahman@example.com',
                'user_Phone_Number' => '+60123456787',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'active',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
            [
                'user_Name' => 'Michelle Tan',
                'user_Email' => 'michelle.tan@example.com',
                'user_Phone_Number' => '+60123456786',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'active',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
            [
                'user_Name' => 'Raj Patel',
                'user_Email' => 'raj.patel@example.com',
                'user_Phone_Number' => '+60123456785',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'active',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
            [
                'user_Name' => 'Sarah Johnson',
                'user_Email' => 'sarah.johnson@example.com',
                'user_Phone_Number' => '+60123456784',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'pending',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
            [
                'user_Name' => 'Kumar Krishnan',
                'user_Email' => 'kumar.krishnan@example.com',
                'user_Phone_Number' => '+60123456783',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'active',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
            [
                'user_Name' => 'Nurul Aina',
                'user_Email' => 'nurul.aina@example.com',
                'user_Phone_Number' => '+60123456782',
                'user_Password' => Hash::make('password'),
                'user_Status' => 'active',
                'user_Created_At' => now(),
                'user_Updated_At' => now(),
            ],
        ];

        $createdCount = 0;

        foreach ($publicUsers as $userData) {
            // Check if user already exists
            $existingUser = PublicUser::where('user_Email', $userData['user_Email'])->first();

            if (!$existingUser) {
                PublicUser::create($userData);
                $createdCount++;
                $this->command->info("✓ Created: {$userData['user_Name']}");
            } else {
                $this->command->info("- Skipped: {$userData['user_Name']} - Already exists");
            }
        }

        $this->command->info('');
        $this->command->info("Public Users seeding completed! {$createdCount} users created.");

        // Display login information
        if ($createdCount > 0) {
            $this->command->info('');
            $this->command->info('PUBLIC USER LOGIN CREDENTIALS:');
            $this->command->info('=============================');

            foreach ($publicUsers as $user) {
                $status = $user['user_Status'] === 'active' ? '(Active)' : '(Pending)';
                $this->command->info("Email: {$user['user_Email']} | Password: password {$status}");
            }
        }
    }
}
