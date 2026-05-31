@extends('core.layouts.app')

@section('title', 'Events Management - System Portal')

@section('content')
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Events Management</h2>
                <p class="text-sm text-slate-500 mt-1">Schedule rallies, meetings, and volunteer activities.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button type="button" data-target="#eventModal" data-role="fill-modal" data-mode="create"
                    data-action="{{ route('events.store') }}" data-module="Event"
                    class="open-modal-btn inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Create Event
                </button>
            </div>
        </div>

        <div class="bg-white p-4 rounded-t-xl border border-slate-100 border-b-0 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ url()->current() }}" class="w-full flex flex-col md:flex-row gap-4">
                
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by title or venue..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>

                <div class="w-full md:w-48">
                    <select name="status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-slate-700">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive / Completed</option>
                    </select>
                </div>

                @if (request()->hasAny(['search', 'status']) && (request('search') != '' || request('status') != ''))
                    <a href="{{ url()->current() }}"
                        class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark mr-2"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white border border-slate-100 rounded-b-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-medium">
                            <th class="px-6 py-4 w-1/3">Event Details</th>
                            <th class="px-6 py-4">Date & Time</th>
                            <th class="px-6 py-4 text-center">Attendees</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">

                        @forelse($events as $event)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-start">
                                        <div class="h-10 w-10 rounded-lg bg-orange-50 border border-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm mr-3 mt-0.5 flex-shrink-0">
                                            <i class="fa-regular fa-calendar"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900">{{ $event->title }}</div>
                                            <div class="text-xs text-slate-500 mt-1 flex items-center">
                                                <i class="fa-solid fa-location-dot mr-1.5 text-slate-400"></i>
                                                {{ $event->venue ?? 'No venue specified' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-900">{{ $event->event_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $event->event_date->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center bg-blue-50 text-blue-600 rounded-full px-3 py-1 text-xs font-semibold border border-blue-100">
                                        {{ $event->attendances_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($event->status)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        
                                        <button type="button" data-target="#eventModal"
                                            data-role="fill-modal" data-mode="edit"
                                            data-action="{{ route('events.update', $event->id) }}" data-method="PUT"
                                            data-module="Event" 
                                            data-title="{{ $event->title }}" 
                                            data-venue="{{ $event->venue }}"
                                            data-description="{{ $event->description }}"
                                            data-event_date="{{ $event->event_date->format('Y-m-d\TH:i') }}"
                                            data-status="{{ $event->status }}"
                                            class="open-modal-btn p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        
                                        <button type="button" 
                                            class="delete-btn p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            data-url="{{ route('events.destroy', $event->id) }}"
                                            data-name="{{ $event->title }}"
                                            data-token="{{ csrf_token() }}"
                                            title="Delete">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-4">
                                        <i class="fa-regular fa-calendar-xmark text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900">No events found</h3>
                                    <p class="text-sm text-slate-500 mt-1">Adjust your search or create a new event.</p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            @if($events->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $events->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="eventModal" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true">
        <div class="relative w-full max-w-lg bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 bg-slate-800/50 flex-shrink-0">
                <h5 class="modal-title text-lg font-semibold text-white">System Event</h5>
                <button type="button" class="close-modal text-slate-400 hover:text-white transition-colors" aria-label="Close">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf

                <div class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Event Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required placeholder="e.g., Grand Rally 2026"
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Date & Time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="event_date" id="event_date" required
                                class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors [color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Venue</label>
                            <input type="text" name="venue" id="venue" placeholder="e.g., City Plaza"
                                class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Additional details, requirements, or agenda..."
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            <option value="1">Active</option>
                            <option value="0">Inactive / Completed</option>
                        </select>
                    </div>

                </div>

                <div class="flex items-center justify-end px-6 py-4 border-t border-slate-700 bg-slate-800/50 space-x-3 flex-shrink-0">
                    <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-slate-300 bg-transparent border border-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="submit-btn px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                        Save Event
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/crud-helper.js?v=') }}"></script>
@endpush