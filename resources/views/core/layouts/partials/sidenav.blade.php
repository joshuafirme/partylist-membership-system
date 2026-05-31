<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col h-screen">
    
    <div class="h-16 flex items-center px-6 border-b border-slate-100 flex-shrink-0 justify-between">
        <div class="flex items-center overflow-hidden">
            @if (!empty($settings->logo_path))
                <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" class="h-8 w-auto mr-3 object-contain flex-shrink-0">
            @else
                <i class="fa-solid fa-layer-group text-blue-600 text-xl mr-3 flex-shrink-0"></i>
            @endif
            <span class="text-lg font-semibold text-slate-900 truncate">
                {{ $settings->app_name ?? 'System Portal' }}
            </span>
        </div>
        
        <button id="close-sidebar-btn" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">

        <a href="{{ route('dashboard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="fa-solid fa-chart-pie w-6 text-center mr-2"></i> Dashboard
        </a>

        @php
            // Active route check
            $isAdminActive = request()->routeIs('users.*', 'user-roles.*', 'settings.*');
        @endphp

        @can('view_teams')
            <a href="{{ route('teams.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('teams.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-people-group w-6 text-center mr-2"></i> Teams
            </a>
        @endcan

        @can('view_events')
            <a href="{{ route('events.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('events.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-calendar-day w-6 text-center mr-2"></i> Events
            </a>
        @endcan

        @can('scan_qr')
            <a href="{{ route('scanner.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('scanner.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-qrcode w-6 text-center mr-2"></i> QR Scanner
            </a>
        @endcan

        @can('view_attendances')
            <a href="{{ route('attendances.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('attendances.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-clipboard-user w-6 text-center mr-2"></i> Attendance History
            </a>
        @endcan

        @can('view_members')
            <a href="{{ route('members.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('members.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-address-card w-6 text-center mr-2"></i> Members
            </a>
        @endcan

        @canany(['manage_users', 'manage_roles', 'manage_settings'])
            <div>
                <button type="button"
                    class="nav-toggle-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors hover:bg-slate-50 hover:text-slate-900 {{ $isAdminActive ? 'text-blue-700' : 'text-slate-600' }}"
                    data-target="#adminSubmenu">
                    <div class="flex items-center">
                        <i class="fa-solid fa-shield-halved w-6 text-center mr-2"></i> Administration
                    </div>
                    <i
                        class="fa-solid fa-chevron-down text-sm transition-transform duration-200 toggle-arrow {{ $isAdminActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="adminSubmenu" class="mt-1 space-y-1 pl-8 {{ $isAdminActive ? 'block' : 'hidden' }}">

                    @can('manage_users')
                        <a href="{{ route('users.index') }}"
                            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i
                                class="fa-regular fa-circle text-[8px] mr-2 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                            Users
                        </a>
                    @endcan

                    @can('manage_roles')
                        <a href="{{ route('user-roles.index') }}"
                            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('user-roles.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i
                                class="fa-regular fa-circle text-[8px] mr-2 {{ request()->routeIs('user-roles.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                            User Roles
                        </a>
                    @endcan

                    @can('manage_settings')
                        <a href="{{ route('settings.index') }}"
                            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i
                                class="fa-regular fa-circle text-[8px] mr-2 {{ request()->routeIs('settings.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                            System Settings
                        </a>
                    @endcan

                </div>
            </div>
        @endcanany

    </nav>

    <div class="px-6 py-4 border-t border-slate-100 bg-slate flex-shrink-0">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">System Info</span>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                v{{ $settings->version ?? '1.0.0' }}
            </span>
        </div>
    </div>
</aside>
