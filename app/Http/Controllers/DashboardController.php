<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Display the dashboard overview.
     */
    public function index()
    {
        // Fetch top-level statistics
        $activeEventsCount = Gate::allows('view_events')
            ? Event::where('status', 1)->count()
            : null;
        $totalTeamsCount = Gate::allows('view_teams')
            ? Team::count()
            : null;
        $recentAttendancesCount = Gate::allows('view_attendances')
            ? Attendance::count()
            : null;

        // Fetch the 5 most recent attendances for the activity feed
        $recentActivities = Gate::allows('view_attendances')
            ? Attendance::with(['member', 'event'])
                ->latest('time_in')
                ->take(5)
                ->get()
            : collect();

        return view('core.dashboard', compact(
            'activeEventsCount',
            'totalTeamsCount',
            'recentAttendancesCount',
            'recentActivities'
        ));
    }
}
