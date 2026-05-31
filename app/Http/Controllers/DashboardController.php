<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard overview.
     */
    public function index()
    {
        // Fetch top-level statistics
        $activeEventsCount = Event::where('status', 1)->count();
        $totalTeamsCount = Team::count();
        $recentAttendancesCount = Attendance::count();

        // Fetch the 5 most recent attendances for the activity feed
        $recentActivities = Attendance::with(['member', 'event'])
            ->latest('time_in')
            ->take(5)
            ->get();

        return view('core.dashboard', compact(
            'activeEventsCount',
            'totalTeamsCount',
            'recentAttendancesCount',
            'recentActivities'
        ));
    }
}