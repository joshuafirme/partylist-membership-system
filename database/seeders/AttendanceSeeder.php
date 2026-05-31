<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Fetch necessary roles
        $memberRole = UserRole::where('name', 'Member')->first();
        $staffRole = UserRole::where('name', 'Event Staff')->first();
        $adminRole = UserRole::where('name', 'Super Admin')->first();

        // 2. Fetch appropriate users
        $members = User::where('user_role_id', $memberRole?->id)->get();
        // Fallback to Super Admin if no Event Staff exists
        $scanner = User::where('user_role_id', $staffRole?->id)->first() 
                ?? User::where('user_role_id', $adminRole?->id)->first();
                
        $events = Event::where('status', 1)->get();

        // Safety check to ensure we have the required data before seeding
        if ($events->isEmpty() || $members->isEmpty() || !$scanner) {
            $this->command->info('Skipping Attendance Seeder: Please ensure Events and Users (Members & Scanners) exist first.');
            return;
        }

        // 3. Seed realistic attendances for up to 3 events
        foreach ($events->take(3) as $event) {
            
            // Take a random number of members to attend this specific event
            $attendeeCount = min(rand(2, 5), $members->count());
            $attendees = $members->random($attendeeCount);

            foreach ($attendees as $member) {
                // Generate a realistic time_in (between 5 and 60 minutes before the event started)
                $timeIn = Carbon::parse($event->event_date)->subMinutes(rand(5, 60));

                Attendance::firstOrCreate([
                    'event_id' => $event->id,
                    'user_id' => $member->id,
                ], [
                    'scanned_by' => $scanner->id,
                    'time_in' => $timeIn,
                ]);
            }
        }
    }
}