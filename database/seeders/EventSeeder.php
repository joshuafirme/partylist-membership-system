<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find an admin to be the creator of these events
        $adminRole = UserRole::where('name', 'Super Admin')->first();
        $admin = User::where('user_role_id', $adminRole?->id)->first();

        // Fallback to the very first user if no specific admin exists yet
        if (!$admin) {
            $admin = User::first();
        }

        // Safety check
        if (!$admin) {
            $this->command->info('Skipping Event Seeder: No users found to assign as the event creator. Run UserSeeder first.');
            return;
        }

        $events = [
            [
                'title' => 'Calabarzon Grand Rally 2026',
                'description' => 'The kickoff rally for the upcoming elections. All regional coordinators and team leaders must be present to secure their event kits.',
                'venue' => 'Nasugbu Town Plaza',
                'event_date' => Carbon::now()->addDays(30)->setTime(15, 0), // 30 days from now at 3:00 PM
                'status' => 1,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Provincial Volunteer Orientation',
                'description' => 'Orientation, scanning dry-run, and e-ID distribution for new Batangas volunteers.',
                'venue' => 'Batangas Provincial Capitol',
                'event_date' => Carbon::now()->addDays(5)->setTime(9, 0), // 5 days from now at 9:00 AM
                'status' => 1,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Barangay Leaders Strategic Planning',
                'description' => 'Closed-door meeting for all Team Leaders regarding precinct mapping and attendance quotas.',
                'venue' => 'Party Headquarters',
                'event_date' => Carbon::now()->subDays(2)->setTime(13, 0), // 2 days ago at 1:00 PM
                'status' => 0, // Inactive/Completed
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Relief Operations Task Force',
                'description' => 'Emergency meeting for the packing and distribution of relief goods to affected local barangays.',
                'venue' => 'Warehouse A',
                'event_date' => Carbon::now()->addHours(48)->setTime(8, 0), // 2 days from now at 8:00 AM
                'status' => 1,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($events as $eventData) {
            Event::updateOrCreate(
                ['title' => $eventData['title']], // Check by title to prevent duplicate entries
                $eventData
            );
        }
    }
}