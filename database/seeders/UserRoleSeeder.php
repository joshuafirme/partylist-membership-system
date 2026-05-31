<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'permissions' => json_encode(['all'])],
            ['name' => 'National Admin', 'permissions' => json_encode(['manage_users', 'manage_events', 'view_reports'])],
            ['name' => 'Regional Coordinator', 'permissions' => json_encode(['manage_regional_users', 'manage_events'])],
            ['name' => 'City Coordinator', 'permissions' => json_encode(['manage_city_users'])],
            ['name' => 'Team Leader', 'permissions' => json_encode(['manage_team'])],
            ['name' => 'Event Staff', 'permissions' => json_encode(['scan_qr'])],
            ['name' => 'Member', 'permissions' => json_encode(['view_profile', 'view_events'])],
        ];

        DB::table('user_roles')->insert($roles);
    }
}
