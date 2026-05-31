<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        // Fetch roles and count how many users belong to each role
        $roles = UserRole::withCount('users')->latest()->get();

        // Grouped permissions for a cleaner UI in the modal
        $availablePermissions = [
            'System Access' => [
                'all' => 'Full System Access (Super Admin)',
            ],
            'User Management' => [
                'manage_users' => 'Manage All Users',
                'manage_regional_users' => 'Manage Regional Users',
                'manage_city_users' => 'Manage City Users',
                'manage_team' => 'Manage Team Members',
            ],
            'Events & Attendance' => [
                'manage_events' => 'Create & Manage Events',
                'view_events' => 'View Events',
                'scan_qr' => 'Scan QR Codes (Attendance)',
            ],
            'Reporting' => [
                'view_reports' => 'View System Reports',
                'view_profile' => 'View Profiles',
            ]
        ];

        return view('core.user-roles.list', compact('roles', 'availablePermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:user_roles,name',
            // Permissions can be null if they save a role with no checkboxes selected
            'permissions' => 'nullable|array', 
            'permissions.*' => 'string',
        ]);

        UserRole::create([
            'name' => $validated['name'],
            // If permissions is null, default to an empty array
            'permissions' => $validated['permissions'] ?? [], 
        ]);

        return redirect()->route('user-roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, UserRole $userRole)
    {
        $validated = $request->validate([
            // Ensure the name is unique, but ignore the current role's ID
            'name' => ['required', 'string', 'max:255', Rule::unique('user_roles')->ignore($userRole->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $userRole->update([
            'name' => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('user-roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(UserRole $userRole)
    {
        // Safety Check 1: Do not allow deletion of the core Super Admin role
        if ($userRole->name === 'Super Admin') {
            return redirect()->route('user-roles.index')
                ->with('error', 'Action denied: You cannot delete the master Super Admin role.');
        }

        // Safety Check 2: Prevent deletion if users are currently tied to this role
        // (Assuming you have a 'users()' relationship defined on your UserRole model)
        if ($userRole->users()->count() > 0) {
            return redirect()->route('user-roles.index')
                ->with('error', 'Action denied: Cannot delete this role because there are users actively assigned to it.');
        }

        $userRole->delete();

        return redirect()->route('user-roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}