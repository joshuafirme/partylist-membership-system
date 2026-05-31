@extends('core.layouts.app')

@section('title', 'Teams Management - System Portal')

@section('content')
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Teams Management</h2>
                <p class="text-sm text-slate-500 mt-1">Organize users into operational groups.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button type="button" data-target="#teamModal" data-role="fill-modal" data-mode="create"
                    data-action="{{ route('teams.store') }}" data-module="Team"
                    class="open-modal-btn inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Add New Team
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
                        placeholder="Search teams by name..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>

                <div class="w-full md:w-48">
                    <select name="status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-slate-700">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
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
                            <th class="px-6 py-4 w-1/4">Team Name</th>
                            <th class="px-6 py-4 w-1/3">Description</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Created Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">

                        @forelse($teams as $team)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-9 w-9 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm mr-3">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                        <div class="font-medium text-slate-900">{{ $team->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ \Illuminate\Support\Str::limit($team->description, 60, '...') ?? 'No description provided.' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($team->status)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $team->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        
                                        <button type="button" data-target="#teamModal"
                                            data-role="fill-modal" data-mode="edit"
                                            data-action="{{ route('teams.update', $team->id) }}" data-method="PUT"
                                            data-module="Team" 
                                            data-name="{{ $team->name }}" 
                                            data-description="{{ $team->description }}"
                                            data-status="{{ $team->status }}"
                                            class="open-modal-btn p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        
                                        <button type="button" 
                                            class="delete-btn p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            data-url="{{ route('teams.destroy', $team->id) }}"
                                            data-name="{{ $team->name }}"
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
                                        <i class="fa-solid fa-users-slash text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900">No teams found</h3>
                                    <p class="text-sm text-slate-500 mt-1">Adjust your search or create a new team.</p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            @if($teams->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $teams->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="teamModal" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true">
        <div class="relative w-full max-w-md bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 bg-slate-800/50">
                <h5 class="modal-title text-lg font-semibold text-white">System Team</h5>
                <button type="button" class="close-modal text-slate-400 hover:text-white transition-colors" aria-label="Close">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="" method="POST">
                @csrf

                <div class="p-6 space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Team Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required placeholder="e.g., Marketing Team"
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Brief details about this team..."
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Operational Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>

                <div class="flex items-center justify-end px-6 py-4 border-t border-slate-700 bg-slate-800/50 space-x-3">
                    <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-slate-300 bg-transparent border border-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="submit-btn px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                        Save Team
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/crud-helper.js?v=') }}"></script>
@endpush