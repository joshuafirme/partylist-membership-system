<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Display a listing of the constituents.
     */
    public function index(Request $request)
    {
        $query = $this->applyFilters(
            $this->visibleMembersQuery($request->user()),
            $request
        );

        // Ensure pagination remembers the filters
        $members = $query->latest()->paginate(15);

        // Fetch data for the modal dropdowns
        $teams = Team::where('status', 1)
            ->when($this->isTeamLeader($request->user()), function ($query) use ($request) {
                $query->whereKey($request->user()->team_id);
            })
            ->orderBy('name')
            ->get();

        // Only fetch constituent-level roles
        $roles = UserRole::whereNotIn('name', ['Super Admin', 'National Admin'])->orderBy('name')->get();

        $coordinators = User::whereHas('role', function ($q) {
            $q->whereIn('name', config('campaign.leader_roles'));
        })
            ->when($this->isTeamLeader($request->user()), function ($query) use ($request) {
                $query->whereKey($request->user()->id);
            })
            ->orderBy('name')
            ->get();

        return view('core.members.list', compact('members', 'teams', 'roles', 'coordinators'));
    }

    public function showEid(Request $request, User $member)
    {
        $this->authorizeMemberVisibility($member, $request->user());

        // Ensure the member has an assigned ID
        if (! $member->membership_number || ! $member->qr_token) {
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
            'email' => 'nullable|required_with:password|email|unique:users,email',
            'password' => 'nullable|required_with:email|string|min:8|confirmed',
            'mobile_number' => 'nullable|string|max:20|unique:users,mobile_number',
            'user_role_id' => [
                'required',
                Rule::exists('user_roles', 'id')->whereNotIn('name', ['Super Admin', 'National Admin']),
            ],
            'team_id' => 'nullable|exists:teams,id',
            'voter_status' => 'required|in:registered,unregistered',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'precinct_no' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'coordinator_id' => 'nullable|exists:users,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated = $this->enforceTeamLeaderAssignment($validated, $request->user());
        $validated = $this->storeProfilePhoto($validated, $request);

        // Auto-generate unique Membership Number (e.g., PL-2026-ABC123)
        do {
            $memberNo = 'PL-'.date('Y').'-'.strtoupper(Str::random(6));
        } while (User::where('membership_number', $memberNo)->exists());

        $validated['membership_number'] = $memberNo;

        // Auto-generate a secure UUID for the QR code
        $validated['qr_token'] = (string) Str::uuid();

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
        $this->authorizeMemberVisibility($member, $request->user());
        $oldProfilePhotoPath = $member->profile_photo_path;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'required_with:password', 'email', Rule::unique('users')->ignore($member->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'mobile_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($member->id)],
            'user_role_id' => [
                'required',
                Rule::exists('user_roles', 'id')->whereNotIn('name', ['Super Admin', 'National Admin']),
            ],
            'team_id' => 'nullable|exists:teams,id',
            'voter_status' => 'required|in:registered,unregistered',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'precinct_no' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'coordinator_id' => 'nullable|exists:users,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated = $this->enforceTeamLeaderAssignment($validated, $request->user());
        $validated = $this->storeProfilePhoto($validated, $request);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Auto-generate Membership Number ONLY if they don't have one yet
        if (empty($member->membership_number)) {
            do {
                $memberNo = 'PL-'.date('Y').'-'.strtoupper(Str::random(6));
            } while (User::where('membership_number', $memberNo)->exists());

            $validated['membership_number'] = $memberNo;
        }

        // Auto-generate QR Token ONLY if they don't have one yet
        if (empty($member->qr_token)) {
            $validated['qr_token'] = (string) Str::uuid();
        }

        $member->update($validated);

        if (isset($validated['profile_photo_path']) && $oldProfilePhotoPath) {
            Storage::disk('public')->delete($oldProfilePhotoPath);
        }

        return redirect()->route('members.index')
            ->with('success', 'Member profile updated successfully.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Request $request, User $member)
    {
        $this->authorizeMemberVisibility($member, $request->user());
        $profilePhotoPath = $member->profile_photo_path;

        // Delete member (Ensure you handle related attendances via cascading deletes in your DB schema)
        $member->delete();

        if ($profilePhotoPath) {
            Storage::disk('public')->delete($profilePhotoPath);
        }

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = $this->applyFilters(
            $this->visibleMembersQuery($request->user()),
            $request
        );

        $members = $query->get();

        $fileName = 'Members_Export_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Membership No', 'Name', 'Mobile Number', 'Role', 'Team', 'Leader (Coordinator)', 'Barangay', 'Voter Status'];

        $callback = function () use ($members, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($members as $member) {
                $row = [
                    $member->membership_number,
                    $member->name,
                    $member->mobile_number,
                    $member->role->name ?? 'N/A',
                    $member->team->name ?? 'N/A',
                    $member->coordinator->name ?? 'Direct (No Leader)',
                    $member->barangay,
                    ucfirst($member->voter_status),
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function visibleMembersQuery(User $viewer): Builder
    {
        return User::query()
            ->with(['team', 'coordinator', 'role'])
            ->whereHas('role', function ($query) {
                $query->where('name', '!=', 'Super Admin');
            })
            ->visibleTo($viewer);
    }

    private function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('membership_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('voter_status')) {
            $query->where('voter_status', $request->string('voter_status')->toString());
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->integer('team_id'));
        }

        if ($request->filled('coordinator_id')) {
            $query->where('coordinator_id', $request->integer('coordinator_id'));
        }

        return $query;
    }

    private function authorizeMemberVisibility(User $member, User $viewer): void
    {
        abort_unless(
            $this->visibleMembersQuery($viewer)->whereKey($member->id)->exists(),
            403
        );
    }

    private function enforceTeamLeaderAssignment(array $validated, User $viewer): array
    {
        if ($this->isTeamLeader($viewer)) {
            $validated['coordinator_id'] = $viewer->id;
            $validated['team_id'] = $viewer->team_id;
        }

        return $validated;
    }

    private function storeProfilePhoto(array $validated, Request $request): array
    {
        unset($validated['profile_photo']);

        if (! $request->hasFile('profile_photo')) {
            return $validated;
        }

        $path = $request->file('profile_photo')->store('member-profiles', 'public');

        throw_if($path === false, new \RuntimeException('The profile photo could not be stored.'));

        $validated['profile_photo_path'] = $path;

        return $validated;
    }

    private function isTeamLeader(User $user): bool
    {
        return $user->role?->name === 'Team Leader';
    }
}
