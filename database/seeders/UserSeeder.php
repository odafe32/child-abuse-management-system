<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@cams.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ADMIN,
            'employee_id' => 'ADM001',
            'department' => 'Administration',
            'phone' => '+1234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Social Worker User
        User::create([
            'name' => 'Jane Smith',
            'email' => 'socialworker@cams.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_SOCIAL_WORKER,
            'employee_id' => 'SW001',
            'department' => 'Social Services',
            'phone' => '+1234567891',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Police Officer User
        User::create([
            'name' => 'Officer John Doe',
            'email' => 'police@cams.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_POLICE_OFFICER,
            'employee_id' => 'PO001',
            'department' => 'Police Department',
            'phone' => '+1234567892',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create additional test users
        User::create([
            'name' => 'Sarah Wilson',
            'email' => 'sarah.wilson@cams.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_SOCIAL_WORKER,
            'employee_id' => 'SW002',
            'department' => 'Child Protection Services',
            'phone' => '+1234567893',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Detective Mike Johnson',
            'email' => 'mike.johnson@cams.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_POLICE_OFFICER,
            'employee_id' => 'PO002',
            'department' => 'Criminal Investigation Division',
            'phone' => '+1234567894',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create an inactive user for testing
        User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@cams.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_SOCIAL_WORKER,
            'employee_id' => 'SW003',
            'department' => 'Social Services',
            'phone' => '+1234567895',
            'is_active' => false,
            'email_verified_at' => now(),
        ]);
    }
}
