<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Agency;
use Carbon\Carbon;

class AgenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding agencies table with sample data...');

        $agencies = [
            [
                'agency_Name' => 'Ministry of Health Malaysia',
                'agency_Type' => 'ministry_health',
                'agency_Email' => 'admin@moh.gov.my',
                'agency_Phone' => '+603-8883-3333',
                'agency_Password' => Hash::make('password'),
                'agency_First_Time_Login' => false,
            ],
            [
                'agency_Name' => 'Ministry of Education Malaysia',
                'agency_Type' => 'ministry_education',
                'agency_Email' => 'admin@moe.gov.my',
                'agency_Phone' => '+603-8884-4444',
                'agency_Password' => Hash::make('temppass123'),
                'agency_First_Time_Login' => true,
            ],
            [
                'agency_Name' => 'Ministry of Finance Malaysia',
                'agency_Type' => 'ministry_finance',
                'agency_Email' => 'admin@mof.gov.my',
                'agency_Phone' => '+603-8885-5555',
                'agency_Password' => Hash::make('password'),
                'agency_First_Time_Login' => false,
            ],
            [
                'agency_Name' => 'Ministry of Transport Malaysia',
                'agency_Type' => 'ministry_transport',
                'agency_Email' => 'admin@mot.gov.my',
                'agency_Phone' => '+603-8886-6666',
                'agency_Password' => Hash::make('temppass456'),
                'agency_First_Time_Login' => true,
            ],
            [
                'agency_Name' => 'Department of Environment',
                'agency_Type' => 'government_department',
                'agency_Email' => 'admin@doe.gov.my',
                'agency_Phone' => '+603-8887-7777',
                'agency_Password' => Hash::make('password'),
                'agency_First_Time_Login' => false,
            ],
            [
                'agency_Name' => 'Malaysian Anti-Corruption Commission',
                'agency_Type' => 'government_agency',
                'agency_Email' => 'admin@sprm.gov.my',
                'agency_Phone' => '+603-8888-8888',
                'agency_Password' => Hash::make('temppass789'),
                'agency_First_Time_Login' => true,
            ],
            [
                'agency_Name' => 'Securities Commission Malaysia',
                'agency_Type' => 'regulatory_body',
                'agency_Email' => 'admin@sc.com.my',
                'agency_Phone' => '+603-6204-8000',
                'agency_Password' => Hash::make('password'),
                'agency_First_Time_Login' => false,
            ],
            [
                'agency_Name' => 'Bank Negara Malaysia',
                'agency_Type' => 'central_bank',
                'agency_Email' => 'admin@bnm.gov.my',
                'agency_Phone' => '+603-2698-8044',
                'agency_Password' => Hash::make('temppass101'),
                'agency_First_Time_Login' => true,
            ],
            [
                'agency_Name' => 'Royal Malaysia Police',
                'agency_Type' => 'law_enforcement',
                'agency_Email' => 'admin@rmp.gov.my',
                'agency_Phone' => '+603-2266-2222',
                'agency_Password' => Hash::make('password'),
                'agency_First_Time_Login' => false,
            ],
            [
                'agency_Name' => 'Immigration Department of Malaysia',
                'agency_Type' => 'government_department',
                'agency_Email' => 'admin@imi.gov.my',
                'agency_Phone' => '+603-8880-1000',
                'agency_Password' => Hash::make('temppass202'),
                'agency_First_Time_Login' => true,
            ]
        ];

        $createdCount = 0;

        foreach ($agencies as $agencyData) {
            // Check if agency already exists
            $existingAgency = Agency::where('agency_Email', $agencyData['agency_Email'])->first();

            if (!$existingAgency) {
                Agency::create(array_merge($agencyData, [
                    'agency_Created_At' => now(),
                    'agency_Updated_At' => now(),
                ]));

                $createdCount++;
                $this->command->info("✓ Created: {$agencyData['agency_Name']}");
            } else {
                $this->command->info("- Skipped: {$agencyData['agency_Name']} - Already exists");
            }
        }

        $this->command->info('');
        $this->command->info("Agencies seeding completed! {$createdCount} agencies created.");

        // Display login information
        $this->command->info('');
        $this->command->info('AGENCY LOGIN CREDENTIALS:');
        $this->command->info('========================');

        foreach ($agencies as $agency) {
            $password = $agency['agency_First_Time_Login'] ?
                str_replace(Hash::make(''), '', $agency['agency_Password']) :
                'password';

            $status = $agency['agency_First_Time_Login'] ? '(First time login required)' : '(Ready to use)';

            $this->command->info("Email: {$agency['agency_Email']} | Password: password {$status}");
        }

        $this->command->info('');
        $this->command->info('Note: All agencies use "password" as default password.');
        $this->command->info('Agencies marked with first time login will need to reset their password.');
    }
}
