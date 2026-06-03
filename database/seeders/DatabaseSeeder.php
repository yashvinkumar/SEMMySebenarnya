<?php

namespace Database\Seeders;

use App\Models\UserRecord;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call individual seeders
        $this->call([
            // Users table (for authentication)
            TestUsersSeeder::class,

            // Specialized tables
            PublicUsersSeeder::class,
            McmcStaffSeeder::class,
            MigrateAgenciesSeeder::class,
            AgenciesSeeder::class,

            // Test data
            InquiryTestDataSeeder::class,
        ]);
    }
}
