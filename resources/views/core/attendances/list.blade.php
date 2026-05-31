@extends('core.layouts.app')

@section('title', 'Attendance Logs - System Portal')

@section('content')
    <div class="max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Attendance Records</h2>
                <p class="text-sm text-slate-500 mt-1">Monitor real-time event check-ins and execute manual overrides.</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button type="button" data-target="#overrideModal" data-role="fill-modal" data-mode="create"
                    data-action="{{ route('attendances.store') }}" data-module="Manual Check-In"
                    class="open-modal-btn inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-user-check mr-2"></i> Manual Override
                </button>
            </div>
        </div>

        <!-- Filter Configuration Panel -->
        <div class="bg-white p-4 rounded-t-xl border border-slate-100 border-b-0 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ url()->current() }}" class="w-full flex flex-col md:flex-row gap-4">
                
                <!-- Member Search -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by member name..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>

                <!-- Event Dropdown Filter -->
                <div class="w-full md:w-64">
                    <select name="event_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-slate-700">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Reset Options -->
                @if (request()->hasAny(['search', 'event_id']) && (request('search') != '' || request('event_id') != ''))
                    <a href="{{ url()->current() }}"
                        class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark mr-2"></i> Clear Filters
                    </a>
                @endif
            </form>
        </div>

        <!-- Main Logs Content Table -->
        <div class="bg-white border border-slate-100 rounded-b-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-medium">
                            <th class="px-6 py-4">Attending Member</th>
                            <th class="px-6 py-4">Target Event</th>
                            <th class="px-6 py-4">Verification Context</th>
                            <th class="px-6 py-4">Time Registered</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">

                        @forelse($attendances as $log)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-9 w-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm mr-3 uppercase">
                                            {{ substr($log->member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900">{{ $log->member->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $log->member->team->name ?? 'Unassigned Team' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-900">{{ $log->event->title }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $log->event->venue }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-800 flex items-center">
                                        <i class="fa-solid fa-user-shield text-slate-400 mr-2 text-xs"></i>
                                        {{ $log->scannedBy->name ?? 'System Process' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <div>{{ $log->time_in->format('h:i:s A') }}</div>
                                    <div class="text-xs text-blue-600 mt-0.5 font-medium">{{ $log->time_in->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" 
                                            class="delete-btn p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            data-url="{{ route('attendances.destroy', $log->id) }}"
                                            data-name="Attendance for {{ $log->member->name }}"
                                            data-token="{{ csrf_token() }}"
                                            title="Delete Record">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-4">
                                        <i class="fa-solid fa-clipboard-user text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900">No logs discovered</h3>
                                    <p class="text-sm text-slate-500 mt-1">Adjust structural parameters or complete manual log requests.</p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $attendances->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ================= Manual Override Modal ================= -->
    <div id="overrideModal" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true">
        <div class="relative w-full max-w-md bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 bg-slate-800/50">
                <h5 class="modal-title text-lg font-semibold text-white">Manual Override Log</h5>
                <button type="button" class="close-modal text-slate-400 hover:text-white transition-colors" aria-label="Close">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="" method="POST">
                @csrf

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Target Event <span class="text-red-500">*</span></label>
                        <select name="event_id" id="event_id" required
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            <option value="">Select event context...</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Select Member Profile <span class="text-red-500">*</span></label>
                        <select name="user_id" id="user_id" required
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            <option value="">Select target member name...</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email ?? 'No email' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end px-6 py-4 border-t border-slate-700 bg-slate-800/50 space-x-3">
                    <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-slate-300 bg-transparent border border-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="submit-btn px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                        Process Override
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/crud-helper.js?v=') }}"></script>
@endpush