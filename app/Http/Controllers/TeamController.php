<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    /**
     * Display a listing of the teams with search and filters.
     */
    public function index(Request $request)
    {
        $query = Team::query();

        // Apply Search Filter (Name or Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter (Active = 1, Inactive = 0)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fetch paginated results
        $teams = $query->latest()->paginate(10);

        return view('core.teams.list', compact('teams'));
    }

    /**
     * Store a newly created team in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'description' => 'nullable|string',
            'status' => 'required|boolean', // Validates 1 or 0
        ]);

        Team::create($validated);

        return redirect()->route('teams.index')
            ->with('success', 'Team created successfully.');
    }

    /**
     * Update the specified team in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            // Ensure the name is unique, ignoring the current team
            'name' => ['required', 'string', 'max:255', Rule::unique('teams')->ignore($team->id)],
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $team->update($validated);

        return redirect()->route('teams.index')
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified team from storage.
     */
    public function destroy(Team $team)
    {
        // Optional: Add a check here if you don't want to delete teams that have users assigned
        // if ($team->users()->count() > 0) { ... return with error ... }

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully.');
    }
}