@extends('core.layouts.app')

@section('title', 'Members Directory - System Portal')

@section('content')
    <div class="max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Members Directory</h2>
                <p class="text-sm text-slate-500 mt-1">Manage constituents, view e-IDs, and assign teams.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button type="button" data-target="#memberModal" data-role="fill-modal" data-mode="create"
                    data-action="{{ route('members.store') }}" data-module="Member"
                    class="open-modal-btn inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-user-plus mr-2"></i> Register Member
                </button>
            </div>
        </div>

        <!-- Filters & Actions -->
        <div class="mb-6 bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
            <form method="GET" action="{{ route('members.index') }}"
                class="flex flex-col md:flex-row md:items-center gap-4">

                <!-- Search Input -->
                <div class="flex-1 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or ID..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Team Filter -->
                <div class="w-full md:w-48">
                    <select name="team_id"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Leader Filter -->
                <div class="w-full md:w-56">
                    <select name="coordinator_id"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Leaders</option>
                        @foreach ($coordinators as $leader)
                            <option value="{{ $leader->id }}"
                                {{ request('coordinator_id') == $leader->id ? 'selected' : '' }}>
                                {{ $leader->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'team_id', 'coordinator_id']))
                        <a href="{{ route('members.index') }}"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                            Clear
                        </a>
                    @endif

                    <!-- Export Button -->
                    <a href="{{ route('members.export', request()->query()) }}"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="fa-solid fa-file-excel mr-2"></i> Export
                    </a>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white border border-slate-100 rounded-b-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-medium">
                            <th class="px-6 py-4">Constituent</th>
                            <th class="px-6 py-4">Membership ID</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4">Voter Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">

                        @forelse($members as $member)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm mr-3 uppercase flex-shrink-0">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900">{{ $member->name }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                <i class="fa-solid fa-people-group mr-1"></i>
                                                {{ $member->team->name ?? 'No Team' }}
                                            </div>
                                            @if ($member->role?->name && in_array($member->role->name, config('campaign.leader_roles')))
                                                <div class="mt-1.5">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase tracking-wide">
                                                        <i class="fa-solid fa-star mr-1"></i> Leader
                                                    </span>
                                                </div>
                                            @endif
                                            @if ($member->coordinator)
                                                <div class="text-xs text-amber-600 mt-0.5 font-medium">
                                                    <i class="fa-solid fa-link mr-1"></i> Under:
                                                    {{ $member->coordinator->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-mono text-slate-800 bg-slate-100 px-2 py-1 rounded inline-block">
                                        {{ $member->membership_number ?? 'PENDING' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @if ($member->barangay || $member->city)
                                        <div>
                                            {{ $member->barangay }}{{ $member->barangay && $member->city ? ', ' : '' }}{{ $member->city }}
                                        </div>
                                        <div class="text-xs text-slate-400 mt-0.5">Precinct:
                                            {{ $member->precinct_no ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-slate-400 italic">Unspecified</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($member->voter_status === 'registered')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-check-to-slot mr-1.5 text-[10px]"></i> Registered
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                            Unregistered
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button type="button"
                                            class="open-eid-btn p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                            data-name="{{ $member->name }}"
                                            data-membership_number="{{ $member->membership_number ?? 'PENDING' }}"
                                            data-role="{{ $member->role->name ?? 'Constituent' }}"
                                            data-team="{{ $member->team->name ?? 'Unassigned' }}"
                                            data-barangay="{{ $member->barangay ?? 'N/A' }}"
                                            data-city="{{ $member->city ?? 'N/A' }}"
                                            data-precinct="{{ $member->precinct_no ?? 'N/A' }}"
                                            data-voter_status="{{ $member->voter_status === 'registered' ? 'Registered Voter' : 'Unregistered' }}"
                                            data-status="{{ $member->status }}" data-token="{{ $member->qr_token }}"
                                            title="View Digital e-ID">
                                            <i class="fa-solid fa-id-badge text-lg"></i>
                                        </button>
                                        <button type="button" data-target="#memberModal" data-role="fill-modal"
                                            data-mode="edit" data-action="{{ route('members.update', $member->id) }}"
                                            data-method="PUT" data-module="Member" data-name="{{ $member->name }}"
                                            data-email="{{ $member->email }}"
                                            data-mobile_number="{{ $member->mobile_number }}"
                                            data-voter_status="{{ $member->voter_status }}"
                                            data-barangay="{{ $member->barangay }}" data-city="{{ $member->city }}"
                                            data-province="{{ $member->province }}"
                                            data-precinct_no="{{ $member->precinct_no }}"
                                            data-user_role_id="{{ $member->user_role_id }}"
                                            data-team_id="{{ $member->team_id }}" data-status="{{ $member->status }}"
                                            data-coordinator_id="{{ $member->coordinator_id }}"
                                            class="open-modal-btn p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit Profile">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <button type="button"
                                            class="delete-btn p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            data-url="{{ route('members.destroy', $member->id) }}"
                                            data-name="{{ $member->name }}" data-token="{{ csrf_token() }}"
                                            title="Delete">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div
                                        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-4">
                                        <i class="fa-solid fa-users-slash text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900">No members found</h3>
                                    <p class="text-sm text-slate-500 mt-1">Adjust your filters or register a new member.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $members->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>


    <!-- ================= Member Modal ================= -->
    <div id="memberModal"
        class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/75 backdrop-blur-sm transition-opacity"
        aria-hidden="true">
        <div
            class="relative w-full max-w-2xl bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

            <div
                class="flex items-center justify-between px-6 py-4 border-b border-slate-700 bg-slate-800/50 flex-shrink-0">
                <h5 class="modal-title text-lg font-semibold text-white">Constituent Profile</h5>
                <button type="button" class="close-modal text-slate-400 hover:text-white transition-colors"
                    aria-label="Close">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf

                <div class="p-6 space-y-6 overflow-y-auto flex-1 custom-scrollbar">

                    <!-- Personal Info Section -->
                    <div>
                        <h6 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Basic Information
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1">Full Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Mobile Number</label>
                                <input type="text" name="mobile_number" id="mobile_number" placeholder="09xxxxxxxxx"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Email Address</label>
                                <input type="email" name="email" id="email"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Political & Team Assignment -->
                    <div class="pt-4 border-t border-slate-700">
                        <h6 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">System Assignment
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Assigned Leader /
                                    Coordinator</label>
                                <select name="coordinator_id" id="coordinator_id"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <option value="">-- Direct Voter (No Leader) --</option>
                                    @foreach ($coordinators as $leader)
                                        <option value="{{ $leader->id }}">{{ $leader->name }} -
                                            {{ $leader->barangay }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Primary Team</label>
                                <select name="team_id" id="team_id"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <option value="">-- No Team Assigned --</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">System Role <span
                                        class="text-red-500">*</span></label>
                                <select name="user_role_id" id="user_role_id" required
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <option value="">Select role...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Voter Status <span
                                        class="text-red-500">*</span></label>
                                <select name="voter_status" id="voter_status" required
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <option value="registered">Registered</option>
                                    <option value="unregistered">Unregistered</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Account Status <span
                                        class="text-red-500">*</span></label>
                                <select name="status" id="status" required
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <option value="1">Active</option>
                                    <option value="2">Suspended</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Location Data -->
                    <div class="pt-4 border-t border-slate-700">
                        <h6 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Location & Precinct
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Barangay</label>
                                <input type="text" name="barangay" id="barangay"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">City / Municipality</label>
                                <input type="text" name="city" id="city"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Province</label>
                                <input type="text" name="province" id="province"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Precinct No.</label>
                                <input type="text" name="precinct_no" id="precinct_no"
                                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>

                </div>

                <div
                    class="flex items-center justify-end px-6 py-4 border-t border-slate-700 bg-slate-800/50 space-x-3 flex-shrink-0">
                    <button type="button"
                        class="close-modal px-4 py-2 text-sm font-medium text-slate-300 bg-transparent border border-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="submit-btn px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                        Save Constituent
                    </button>
                </div>
            </form>
        </div>
    </div>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #eidModal,
            #eidModal * {
                visibility: visible;
            }

            #eidModal {
                position: absolute;
                left: 0;
                top: 0;
                background: white !important;
                width: 100%;
                height: 100%;
            }

            .print-card {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                margin: 0 auto !important;
                transform: scale(1) !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>

   <div id="eidModal" class="modal fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true">
        <div class="relative w-full max-w-sm mx-4 flex flex-col items-center">
            
            <!-- Toolbar -->
            <div class="no-print w-full flex justify-end items-center mb-4 space-x-2">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
                    <i class="fa-solid fa-print mr-2"></i> Print
                </button>
                <button type="button" class="close-eid-modal bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
                    <i class="fa-solid fa-xmark mr-2"></i> Close
                </button>
            </div>

            <!-- The ID Card -->
            <div class="print-card bg-white w-full max-w-[340px] rounded-2xl shadow-xl overflow-hidden relative border border-slate-200">
                
                <!-- Header / Banner Area -->
                <div class="bg-blue-600 px-6 pt-6 pb-12 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10"></div>
                    <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-white opacity-10"></div>
                    
                    <!-- Dynamic App Name and Voter Status -->
                    <h1 class="text-white font-bold text-lg tracking-wide uppercase relative z-10">{{ $settings->app_name ?? 'System Portal' }}</h1>
                    <p id="eid-voter-status" class="text-blue-100 text-[10px] mt-1 relative z-10 font-medium tracking-widest uppercase"></p>
                </div>

                <!-- Profile Photo Placeholder -->
                <div class="flex justify-center -mt-12 relative z-20">
                    <div class="w-24 h-24 bg-white rounded-full p-1 shadow-md">
                        <div id="eid-avatar" class="w-full h-full rounded-full bg-slate-100 text-blue-600 flex items-center justify-center text-3xl font-bold uppercase">
                            <!-- Initial injected via JS -->
                        </div>
                    </div>
                </div>

                <!-- Member Details -->
                <div class="px-6 pt-4 pb-6 text-center">
                    <h2 id="eid-name" class="text-xl font-bold text-slate-900 leading-tight"></h2>
                    <p id="eid-role" class="text-sm text-blue-600 font-semibold mt-1"></p>
                    
                    <div class="mt-4 bg-slate-50 rounded-lg p-3 border border-slate-100 text-left space-y-2.5">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">ID Number</span>
                            <span id="eid-number" class="text-xs font-mono font-bold text-slate-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">Team</span>
                            <span id="eid-team" class="text-xs font-medium text-slate-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">Location</span>
                            <span id="eid-location" class="text-xs font-medium text-slate-800 truncate max-w-[140px] text-right"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">Precinct</span>
                            <span id="eid-precinct" class="text-xs font-medium text-slate-800"></span>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="px-6 pb-8 text-center flex flex-col items-center">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-3">Scan for Attendance</p>
                    <div id="eid-qrcode" class="p-2 bg-white border-2 border-slate-100 rounded-xl inline-block shadow-sm"></div>
                </div>
                
                <!-- Status Strip -->
                <div id="eid-status-strip" class="h-2 w-full bg-slate-500"></div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/crud-helper.js?v=') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const eidModal = document.getElementById('eidModal');

            // Open e-ID Modal
            document.querySelectorAll('.open-eid-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    // Populate Text Fields
                  // Populate Text Fields
                    const name = this.dataset.name;
                    document.getElementById('eid-name').innerText = name;
                    document.getElementById('eid-avatar').innerText = name.charAt(0);
                    document.getElementById('eid-role').innerText = this.dataset.role;
                    document.getElementById('eid-number').innerText = this.dataset.membership_number;
                    document.getElementById('eid-team').innerText = this.dataset.team;
                    document.getElementById('eid-voter-status').innerText = this.dataset.voter_status;
                    document.getElementById('eid-precinct').innerText = this.dataset.precinct;
                    
                    // Format Location
                    const brgy = this.dataset.barangay !== 'N/A' ? this.dataset.barangay + ', ' : '';
                    document.getElementById('eid-location').innerText = brgy + this.dataset.city;

                    // Handle Status Strip Color
                    const statusStrip = document.getElementById('eid-status-strip');
                    if (this.dataset.status === "1") {
                        statusStrip.className = "h-2 w-full bg-emerald-500";
                    } else {
                        statusStrip.className = "h-2 w-full bg-red-500";
                    }

                    // Generate QR Code
                    const qrContainer = document.getElementById('eid-qrcode');
                    qrContainer.innerHTML = ''; // Clear previous QR

                    if (this.dataset.token) {
                        new QRCode(qrContainer, {
                            text: this.dataset.token,
                            width: 140,
                            height: 140,
                            colorDark: "#0f172a",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } else {
                        qrContainer.innerHTML =
                            '<span class="text-xs text-red-500 font-medium">No Token</span>';
                    }

                    // Show Modal
                    eidModal.classList.remove('hidden');
                    eidModal.classList.add('flex');
                    document.body.style.overflow = 'hidden'; // Prevent background scrolling
                });
            });

            // Close e-ID Modal
            document.querySelectorAll('.close-eid-modal').forEach(btn => {
                btn.addEventListener('click', function() {
                    eidModal.classList.add('hidden');
                    eidModal.classList.remove('flex');
                    document.body.style.overflow = ''; // Restore scrolling
                });
            });

            // Close if clicking outside the card
            eidModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    eidModal.classList.add('hidden');
                    eidModal.classList.remove('flex');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
@endpush
