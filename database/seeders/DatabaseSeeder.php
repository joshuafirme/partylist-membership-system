<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            TeamSeeder::class,
            UserSeeder::class,
            EventSeeder::class,      // <-- Add this here
            AttendanceSeeder::class, // <-- Must be last
        ]);
    }
}
