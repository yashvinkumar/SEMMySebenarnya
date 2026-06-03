<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\McmcStaff;
use Carbon\Carbon;

class McmcStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding MCMC Staff table...');

        $staff = [
            [
                'staff_Name' => 'Ahmad Rahman',
                'staff_Email' => 'ahmad.rahman@mcmc.gov.my',
                'staff_Phone_Number' => '+603-8688-8000',
                'staff_Password' => Hash::make('password'),
                'staff_Created_At' => now(),
                'staff_Updated_At' => now(),
            ],
            [
                'staff_Name' => 'Siti Nurhaliza',
                'staff_Email' => 'siti.nurhaliza@mcmc.gov.my',
                'staff_Phone_Number' => '+603-8688-8001',
                'staff_Password' => Hash::make('password'),
                'staff_Created_At' => now(),
                'staff_Updated_At' => now(),
            ],
            [
                'staff_Name' => 'Lim Wei Ming',
                'staff_Email' => 'lim.weiming@mcmc.gov.my',
                'staff_Phone_Number' => '+603-8688-8002',
                'staff_Password' => Hash::make('password'),
                'staff_Created_At' => now(),
                'staff_Updated_At' => now(),
            ],
            [
                'staff_Name' => 'Raj Kumar',
                'staff_Email' => 'raj.kumar@mcmc.gov.my',
                'staff_Phone_Number' => '+603-8688-8003',
                'staff_Password' => Hash::make('password'),
                'staff_Created_At' => now(),
                'staff_Updated_At' => now(),
            ],
            [
                'staff_Name' => 'Fatimah Zahra',
                'staff_Email' => 'fatimah.zahra@mcmc.gov.my',
                'staff_Phone_Number' => '+603-8688-8004',
                'staff_Password' => Hash::make('password'),
                'staff_Created_At' => now(),
                'staff_Updated_At' => now(),
            ],
        ];

        $createdCount = 0;

        foreach ($staff as $staffData) {
            // Check if staff already exists
            $existingStaff = McmcStaff::where('staff_Email', $staffData['staff_Email'])->first();

            if (!$existingStaff) {
                McmcStaff::create($staffData);
                $createdCount++;
                $this->command->info("✓ Created: {$staffData['staff_Name']}");
            } else {
                $this->command->info("- Skipped: {$staffData['staff_Name']} - Already exists");
            }
        }

        $this->command->info('');
        $this->command->info("MCMC Staff seeding completed! {$createdCount} staff members created.");

        // Display login information
        if ($createdCount > 0) {
            $this->command->info('');
            $this->command->info('MCMC STAFF LOGIN CREDENTIALS:');
            $this->command->info('============================');

            foreach ($staff as $staffMember) {
                $this->command->info("Email: {$staffMember['staff_Email']} | Password: password");
            }
        }
    }
}
