<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserRecord;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test public user
        UserRecord::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'contact_number' => '+60123456789',
            'user_type' => 'public',
            'email_verified_at' => now(),
        ]);

        // Create another public user (unverified)
        UserRecord::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'contact_number' => '+60123456788',
            'user_type' => 'public',
        ]);

        // Create a test MCMC staff
        UserRecord::create([
            'name' => 'MCMC Admin',
            'email' => 'admin@mcmc.gov.my',
            'password' => Hash::make('password'),
            'contact_number' => '+60312345678',
            'user_type' => 'mcmc',
        ]);

        // Create another MCMC staff
        UserRecord::create([
            'name' => 'MCMC Staff',
            'email' => 'staff@mcmc.gov.my',
            'password' => Hash::make('password'),
            'contact_number' => '+60312345679',
            'user_type' => 'mcmc',
        ]);

        // Create a test agency (with password reset required)
        UserRecord::create([
            'name' => 'Ministry of Health',
            'email' => 'admin@moh.gov.my',
            'password' => Hash::make('placeholder'), // Placeholder password
            'temporary_password' => Hash::make('temp123'),
            'contact_number' => '+60323456789',
            'user_type' => 'agency',
            'force_password_reset' => true,
        ]);

        // Create another agency (already reset password)
        UserRecord::create([
            'name' => 'Ministry of Education',
            'email' => 'admin@moe.gov.my',
            'password' => Hash::make('password'),
            'contact_number' => '+60323456788',
            'user_type' => 'agency',
            'force_password_reset' => false,
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('');
        $this->command->info('PUBLIC USERS:');
        $this->command->info('Email: john@example.com | Password: password (verified)');
        $this->command->info('Email: jane@example.com | Password: password (unverified)');
        $this->command->info('');
        $this->command->info('MCMC STAFF:');
        $this->command->info('Email: admin@mcmc.gov.my | Password: password');
        $this->command->info('Email: staff@mcmc.gov.my | Password: password');
        $this->command->info('');
        $this->command->info('AGENCIES:');
        $this->command->info('Email: admin@moh.gov.my | Password: temp123 (needs reset)');
        $this->command->info('Email: admin@moe.gov.my | Password: password (ready to use)');
    }
}
