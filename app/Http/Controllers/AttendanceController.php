<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance logs with search and filters.
     */
    public function index(Request $request)
    {
        // Eager load relations to optimize query performance (prevent N+1 problem)
        $query = Attendance::with(['event', 'member.team', 'scannedBy']);

        // Filter by Event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Search by Member Name or Email
      if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('time_in', 'desc')->paginate(15);
        
        // Fetch active datasets to populate filters and manual override modals
        $events = Event::where('status', 1)->orderBy('title')->get();
        $members = User::whereHas('role', function($q) {
            $q->where('name', 'Member');
        })->orderBy('name')->get();

        return view('core.attendances.list', compact('attendances', 'events', 'members'));
    }

    /**
     * Handle manual override check-in by an administrator.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // Check for duplicate attendance record
        $exists = Attendance::where('event_id', $validated['event_id'])
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return redirect()->route('attendances.index')
                ->with('error', 'This member has already checked into this event.');
        }

        // Create the manual log
        Attendance::create([
            'event_id' => $validated['event_id'],
            'user_id' => $validated['user_id'],
            'scanned_by' => auth()->id(), // Admin executing the override
            'time_in' => now(),
        ]);

        return redirect()->route('attendances.index')
            ->with('success', 'Manual attendance override logged successfully.');
    }

    /**
     * Remove an incorrect or accidental attendance entry.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record removed successfully.');
    }
}