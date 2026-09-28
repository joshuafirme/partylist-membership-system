<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->updatePermissions(function (array $permissions) {
            return array_values(array_unique([...$permissions, 'view_members', 'manage_members']));
        });
    }

    public function down(): void
    {
        $this->updatePermissions(function (array $permissions) {
            return array_values(array_diff($permissions, ['manage_members']));
        });
    }

    private function updatePermissions(callable $callback): void
    {
        $role = DB::table('user_roles')->where('name', 'Team Leader')->first();

        if (! $role) {
            return;
        }

        $permissions = json_decode($role->permissions ?? '[]', true);
        $permissions = is_array($permissions) ? $permissions : [];

        DB::table('user_roles')->where('id', $role->id)->update([
            'permissions' => json_encode($callback($permissions)),
            'updated_at' => now(),
        ]);
    }
};
