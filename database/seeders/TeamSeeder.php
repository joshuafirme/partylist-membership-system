<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'name' => 'National Headquarters',
                'description' => 'Main organizing body, national directors, and core leadership.',
                'status' => 1,
            ],
            [
                'name' => 'IT & Systems Admin',
                'description' => 'Technical staff managing the e-ID system and databases.',
                'status' => 1,
            ],
            [
                'name' => 'Metro Manila Coordinators',
                'description' => 'NCR regional operations and event organizers.',
                'status' => 1,
            ],
            [
                'name' => 'Events & Rallies Task Force',
                'description' => 'Team responsible for scanning QR codes and managing ground events.',
                'status' => 1,
            ],
            [
                'name' => 'Legacy Campaign Team',
                'description' => 'Previous election campaign members (Archived).',
                'status' => 0, // Inactive team
            ],
        ];

        foreach ($teams as $teamData) {
            Team::updateOrCreate(
                ['name' => $teamData['name']], // Check by name to prevent duplicates
                $teamData
            );
        }
    }
}