@extends('core.layouts.app')

@section('title', 'Dashboard - System Portal')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl font-semibold text-slate-900 mb-6">Overview</h2>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-slate-500">Active Events</h3>
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-slate-900">{{ number_format($activeEventsCount) }}</p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-slate-500">Total Teams</h3>
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-slate-900">{{ number_format($totalTeamsCount) }}</p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-slate-500">Total Attendances</h3>
                    <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-slate-900">{{ number_format($recentAttendancesCount) }}</p>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <h3 class="text-lg font-medium text-slate-900 mb-4">Recent Activity</h3>
            
            @if($recentActivities->isEmpty())
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-400 mb-4">
                        <i class="fa-solid fa-inbox text-2xl"></i>
                    </div>
                    <h4 class="text-slate-900 font-medium">No recent activity</h4>
                    <p class="text-sm text-slate-500 mt-1">Data from your events and QR scans will appear here.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentActivities as $activity)
                        <div class="py-4 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm uppercase flex-shrink-0">
                                {{ substr($activity->member->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-900 font-medium truncate">
                                    {{ $activity->member->name ?? 'Unknown Member' }}
                                </p>
                                <p class="text-sm text-slate-500 truncate mt-0.5">
                                    Checked into <span class="font-medium text-slate-700">{{ $activity->event->title ?? 'Unknown Event' }}</span>
                                </p>
                            </div>
                            <div class="text-xs text-slate-400 whitespace-nowrap font-medium">
                                {{ $activity->time_in->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                    <a href="{{ route('attendances.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        View Complete Log &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection