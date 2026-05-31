<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users with search and filters.
     */
    public function index(Request $request)
    {
        // Start a query, eager loading the role to prevent N+1 database queries
        $query = User::with(['role', 'team']);
        $query->whereHas('role', function ($q) {
            $q->where('name', '!=', 'Member');
        });
        // Apply Search Filter (Name or Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply Role Filter
        if ($request->filled('user_role_id')) {
            $query->where('user_role_id', $request->user_role_id);
        }

        // Fetch paginated results and all roles for the dropdown
        $users = $query->latest()->paginate(10);
        $roles = UserRole::orderBy('name')->get();
        $teams = Team::where('status', 1)->orderBy('name')->get();
        return view('core.users.list', compact('users', 'roles', 'teams'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = UserRole::orderBy('name')->get();

        // We assume you will create this view next (core/users/create.blade.php)
        return view('core.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'user_role_id' => 'required|exists:user_roles,id',
            ]);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_role_id' => $validated['user_role_id'],
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = UserRole::orderBy('name')->get();

        // We assume you will create this view next (core/users/edit.blade.php)
        return view('core.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Ensure the email is unique, but ignore the current user's email
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'user_role_id' => 'required|exists:user_roles,id',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->user_role_id = $validated['user_role_id'];

        // Only update the password if the administrator typed a new one
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent the currently logged-in admin from deleting themselves
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}