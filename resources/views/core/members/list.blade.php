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

        <!-- Filter Bar -->
        <div
            class="bg-white p-4 rounded-t-xl border border-slate-100 border-b-0 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ url()->current() }}" class="w-full flex flex-col md:flex-row gap-4">

                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, email, or PL-ID..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>

                <div class="w-full md:w-48">
                    <select name="voter_status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-slate-700">
                        <option value="">All Voters</option>
                        <option value="registered" {{ request('voter_status') === 'registered' ? 'selected' : '' }}>
                            Registered</option>
                        <option value="unregistered" {{ request('voter_status') === 'unregistered' ? 'selected' : '' }}>
                            Unregistered</option>
                    </select>
                </div>

                @if (request()->hasAny(['search', 'voter_status']) && (request('search') != '' || request('voter_status') != ''))
                    <a href="{{ url()->current() }}"
                        class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark mr-2"></i> Clear
                    </a>
                @endif
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
                                    <div
                                        class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                       <a href="{{ route('members.eid', $member->id) }}" target="_blank"
                                            class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                            title="View Digital e-ID">
                                            <i class="fa-solid fa-id-badge text-lg"></i>
                                        </a>
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

<!-- ================= e-ID Viewer Modal ================= -->
    <div id="eidModal" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true">
        <div class="relative w-full max-w-sm bg-transparent flex flex-col items-center justify-center p-4">

            <!-- Close Button -->
            <button type="button" class="close-modal absolute top-0 right-0 p-3 text-slate-300 hover:text-white transition-colors z-10" aria-label="Close">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>

            <!-- Digital ID Card Container -->
            <div class="bg-white w-full rounded-2xl shadow-2xl overflow-hidden relative border border-slate-200">
                
                <!-- Card Header (Partylist Branding) -->
                <div class="bg-gradient-to-r from-blue-700 to-blue-500 h-28 relative">
                    <!-- Subtle background pattern -->
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 10px 10px;"></div>
                    <div class="text-center pt-5 text-white font-bold tracking-widest text-xs uppercase opacity-90">
                        Official Partylist e-ID
                    </div>
                </div>

                <!-- Profile Photo (Overlapping) -->
                <div class="flex justify-center -mt-14 relative z-10">
                    <div class="w-28 h-28 rounded-full border-4 border-white bg-slate-100 flex items-center justify-center shadow-md overflow-hidden relative">
                        <!-- Placeholder for actual photo -->
                        <i class="fa-solid fa-user text-4xl text-slate-300 absolute"></i>
                        <span id="eid-initials" class="font-bold text-slate-600 text-3xl uppercase z-10 relative bg-slate-100 w-full h-full flex items-center justify-center">XX</span>
                    </div>
                </div>

                <!-- Member Data -->
                <div class="text-center px-6 pt-3 pb-6">
                    <h2 id="eid-name" class="text-xl font-bold text-slate-900 uppercase tracking-wide">Member Name</h2>
                    <p id="eid-team" class="text-sm font-semibold text-blue-600 mt-1">Team Designation</p>
                    <p id="eid-location" class="text-xs text-slate-500 mt-1 flex items-center justify-center">
                        <i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> <span id="eid-loc-text">City, Province</span>
                    </p>

                    <div class="mt-5 pt-4 border-t border-slate-100 border-dashed">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Membership Number</p>
                        <p id="eid-number" class="text-lg font-mono font-bold text-slate-800 bg-slate-50 py-1.5 rounded-lg border border-slate-100">
                            PL-2026-XXXXXX
                        </p>
                    </div>
                </div>

                <!-- QR Code Verification Area -->
                <div class="bg-slate-50 p-6 flex flex-col items-center justify-center border-t border-slate-100">
                    <div class="w-36 h-36 bg-white border-2 border-slate-200 rounded-xl shadow-sm flex items-center justify-center mb-3 p-2">
                        <!-- Dynamic QR Code Image will load here -->
                        <img id="eid-qr-image" src="" alt="QR Code" class="w-full h-full object-contain hidden" />
                        <i id="eid-qr-placeholder" class="fa-solid fa-qrcode text-6xl text-slate-300"></i>
                    </div>
                    <p class="text-[10px] text-slate-500 text-center max-w-[220px] leading-relaxed">
                        Scan this QR code during official events and rallies for verified attendance.
                    </p>
                </div>
            </div>

            <!-- External Actions -->
            <div class="mt-6 flex gap-3 w-full max-w-sm">
                <button type="button" onclick="window.print()" class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2.5 rounded-lg text-sm font-medium transition-colors backdrop-blur-sm border border-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-print mr-2"></i> Print / PDF
                </button>
            </div>
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
@endsection

@push('script')
    <script src="{{ asset('assets/js/crud-helper.js?v=') }}"></script>
@endpush
