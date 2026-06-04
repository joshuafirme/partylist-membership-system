<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define standard permissions for Leaders
        $leaderPermissions = [
            "view_teams", 
            "view_events", 
            "scan_qr", 
            "view_attendances", 
            "view_members", 
            "manage_members"
        ];

        // 2. Ensure Required Roles Exist (using 'permissions' instead of 'description')
        $roles = [
            'Coordinator' => UserRole::firstOrCreate(
                ['name' => 'Coordinator'], 
                ['permissions' => $leaderPermissions]
            ),
            'Team Leader' => UserRole::firstOrCreate(
                ['name' => 'Team Leader'], 
                ['permissions' => $leaderPermissions]
            ),
            'Barangay Captain' => UserRole::firstOrCreate(
                ['name' => 'Barangay Captain'], 
                ['permissions' => $leaderPermissions]
            ),
            'Member' => UserRole::firstOrCreate(
                ['name' => 'Member'], 
                ['permissions' => []]
            ),
        ];

        // 3. Ensure at least two Teams exist
        $team1 = Team::firstOrCreate(['name' => 'Nasugbu North Team'], ['status' => 1]);
        $team2 = Team::firstOrCreate(['name' => 'Nasugbu South Team'], ['status' => 1]);
        $teams = [$team1->id, $team2->id];

        $defaultPassword = Hash::make('password123');

        // 4. Create 3 Political Leaders
        $leaders = [];
        $leaderData = [
            ['name' => 'Kapitan Arturo Santos', 'role' => 'Barangay Captain', 'brgy' => 'Barangay 1'],
            ['name' => 'Elena Reyes', 'role' => 'Coordinator', 'brgy' => 'Barangay 2'],
            ['name' => 'Miguel Mercado', 'role' => 'Team Leader', 'brgy' => 'Barangay 3'],
        ];

        foreach ($leaderData as $data) {
            $leaders[] = User::create([
                'name' => $data['name'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])) . '@example.com',
                'mobile_number' => '09' . mt_rand(100000000, 999999999),
                'password' => $defaultPassword,
                'user_role_id' => $roles[$data['role']]->id,
                'team_id' => $teams[array_rand($teams)],
                'voter_status' => 'registered',
                'barangay' => $data['brgy'],
                'city' => 'Nasugbu',
                'province' => 'Batangas',
                'status' => 1,
                'membership_number' => 'PL-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'qr_token' => (string) Str::uuid(),
                'registered_from' => 'seeder'
            ]);
        }

        // 5. Create 16 Regular Members (Downlines)
        $firstNames = ['Juan', 'Maria', 'Pedro', 'Ana', 'Jose', 'Luz', 'Antonio', 'Teresa', 'Manuel', 'Carmen', 'Ricardo', 'Rosario', 'Eduardo', 'Gloria', 'Francisco', 'Teresita'];
        $lastNames = ['Dela Cruz', 'Garcia', 'Reyes', 'Ramos', 'Mendoza', 'Santos', 'Flores', 'Gonzales', 'Bautista', 'Villanueva'];

        for ($i = 0; $i < 16; $i++) {
            // Randomly pick one of the 3 leaders to be this member's coordinator
            $leader = $leaders[array_rand($leaders)]; 
            
            $fName = $firstNames[array_rand($firstNames)];
            $lName = $lastNames[array_rand($lastNames)];
            
            // Appended the index to guarantee the unique validation rule doesn't fail on duplicate names
            $fullName = $fName . ' ' . $lName . ' ' . str_pad($i + 1, 2, '0', STR_PAD_LEFT); 
            
            User::create([
                'name' => $fullName,
                'email' => strtolower($fName . $lName . $i) . '@example.com',
                'mobile_number' => '09' . mt_rand(100000000, 999999999),
                'password' => $defaultPassword,
                'user_role_id' => $roles['Member']->id,
                
                // Inherit Team and Location from their Leader
                'team_id' => $leader->team_id, 
                'coordinator_id' => $leader->id, 
                'barangay' => $leader->barangay, 
                
                'voter_status' => (mt_rand(1, 10) > 2) ? 'registered' : 'unregistered', // 80% chance registered
                'city' => 'Nasugbu',
                'province' => 'Batangas',
                'status' => 1,
                'membership_number' => 'PL-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'qr_token' => (string) Str::uuid(),
                'registered_from' => 'seeder'
            ]);
        }
    }
}