<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch roles to assign the correct foreign keys
        $superAdminRole = UserRole::where('name', 'Super Admin')->first();
        $eventStaffRole = UserRole::where('name', 'Event Staff')->first();
        $memberRole = UserRole::where('name', 'Member')->first();

        // Default password for all seeded users
        $defaultPassword = Hash::make('password');

        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@portal.com',
                'password' => $defaultPassword,
                'user_role_id' => $superAdminRole->id ?? null,
            ],
            [
                'name' => 'Event Scanner',
                'email' => 'scanner@portal.com',
                'password' => $defaultPassword,
                'user_role_id' => $eventStaffRole->id ?? null,
            ],
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan@portal.com',
                'password' => $defaultPassword,
                'user_role_id' => $memberRole->id ?? null,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Check by email to prevent duplicates
                $userData
            );
        }
    }
}