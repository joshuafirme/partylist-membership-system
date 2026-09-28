<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    private const EMAIL = 'teamleader@portal.com';

    public function up(): void
    {
        $now = now();
        $role = DB::table('user_roles')->where('name', 'Team Leader')->first();
        $permissions = $role ? json_decode($role->permissions ?? '[]', true) : [];
        $permissions = is_array($permissions) ? $permissions : [];
        $permissions = array_values(array_unique([...$permissions, 'view_members']));

        if ($role) {
            DB::table('user_roles')->where('id', $role->id)->update([
                'permissions' => json_encode($permissions),
                'status' => 1,
                'updated_at' => $now,
            ]);

            $roleId = $role->id;
        } else {
            $roleId = DB::table('user_roles')->insertGetId([
                'name' => 'Team Leader',
                'permissions' => json_encode($permissions),
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $existingLeader = DB::table('users')->where('email', self::EMAIL)->first();

        if ($existingLeader) {
            DB::table('users')->where('id', $existingLeader->id)->update([
                'user_role_id' => $roleId,
                'team_role' => 'Team Leader',
                'status' => 1,
                'updated_at' => $now,
            ]);

            return;
        }

        DB::table('users')->insert([
            'name' => 'Team Leader',
            'email' => self::EMAIL,
            'password' => Hash::make('password'),
            'user_role_id' => $roleId,
            'team_id' => DB::table('teams')->where('status', 1)->orderBy('id')->value('id'),
            'team_role' => 'Team Leader',
            'voter_status' => 'unregistered',
            'status' => 1,
            'registered_from' => 'team_leader_migration',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', self::EMAIL)
            ->where('registered_from', 'team_leader_migration')
            ->delete();

        $role = DB::table('user_roles')->where('name', 'Team Leader')->first();

        if ($role) {
            $permissions = json_decode($role->permissions ?? '[]', true);
            $permissions = is_array($permissions) ? $permissions : [];
            $permissions = array_values(array_diff($permissions, ['view_members']));

            DB::table('user_roles')->where('id', $role->id)->update([
                'permissions' => json_encode($permissions),
                'updated_at' => now(),
            ]);
        }
    }
};
