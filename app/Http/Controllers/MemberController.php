<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Display a listing of the constituents.
     */
    public function index(Request $request)
    {
        // Eager load relationships
        $query = User::query();

        // Exclude Super Admins from the Members list (keep them in the Admin Users page)
        $query->whereHas('role', function ($q) {
            $q->where('name', '!=', 'Super Admin');
        });

        // Apply Search Filter (Name, Email, or Membership No)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('membership_number', 'like', "%{$search}%");
            });
        }

        // Apply Voter Status Filter
        if ($request->filled('voter_status')) {
            $query->where('voter_status', $request->voter_status);
        }

        $members = $query->latest()->paginate(15);

        // Fetch data for the modal dropdowns
        $teams = Team::where('status', 1)->orderBy('name')->get();

        // Only fetch constituent-level roles
        $roles = UserRole::whereNotIn('name', ['Super Admin', 'National Admin'])->orderBy('name')->get();

        $coordinators = User::whereHas('role', function ($q) {
            $q->whereIn('name', config('campaign.leader_roles'));
        })->orderBy('name')->get();

        return view('core.members.list', compact('members', 'teams', 'roles', 'coordinators'));
    }

    public function showEid(User $member)
    {
        // Ensure the member has an assigned ID
        if (!$member->membership_number || !$member->qr_token) {
            return redirect()->route('members.index')
                ->with('error', 'This member does not have an active e-ID. Please update their profile.');
        }

        // Load relationships needed for the ID card
        $member->load(['team', 'role']);

        return view('core.members.e-id', compact('member'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'mobile_number' => 'nullable|string|max:20|unique:users,mobile_number',
            'user_role_id' => 'required|exists:user_roles,id',
            'team_id' => 'nullable|exists:teams,id',
            'voter_status' => 'required|in:registered,unregistered',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'precinct_no' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'coordinator_id' => 'nullable|exists:users,id',
        ]);

        // Auto-generate unique Membership Number (e.g., PL-2026-ABC123)
        do {
            $memberNo = 'PL-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (User::where('membership_number', $memberNo)->exists());

        $validated['membership_number'] = $memberNo;

        // Auto-generate a secure UUID for the QR code
        $validated['qr_token'] = (string) Str::uuid();

        // Default password for manually created members
        $validated['password'] = bcrypt('password123');
        $validated['registered_from'] = 'admin_panel';

        User::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member added and e-ID generated successfully.');
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, User $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($member->id)],
            'mobile_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($member->id)],
            'user_role_id' => 'required|exists:user_roles,id',
            'team_id' => 'nullable|exists:teams,id',
            'voter_status' => 'required|in:registered,unregistered',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'precinct_no' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'coordinator_id' => 'nullable|exists:users,id',
        ]);

        // Auto-generate Membership Number ONLY if they don't have one yet
        if (empty($member->membership_number)) {
            do {
                $memberNo = 'PL-' . date('Y') . '-' . strtoupper(Str::random(6));
            } while (User::where('membership_number', $memberNo)->exists());

            $validated['membership_number'] = $memberNo;
        }

        // Auto-generate QR Token ONLY if they don't have one yet
        if (empty($member->qr_token)) {
            $validated['qr_token'] = (string) Str::uuid();
        }

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member profile updated successfully.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(User $member)
    {
        // Delete member (Ensure you handle related attendances via cascading deletes in your DB schema)
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }
}