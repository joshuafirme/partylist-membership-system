<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'System Portal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        @include('core.layouts.partials.sidenav')

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-btn" type="button"
                        class="md:hidden p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors flex items-center justify-center h-10 w-10 mr-2">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>

                <div class="flex-1 md:flex-none"></div>

                <div class="flex items-center space-x-4">
                    <button id="theme-toggle" type="button"
                        class="text-slate-500 hover:text-slate-900 hover:bg-slate-100 p-2 rounded-lg transition-colors flex items-center justify-center h-10 w-10 mr-3">
                        <i id="theme-toggle-dark-icon" class="fa-solid fa-moon hidden text-lg"></i>
                        <i id="theme-toggle-light-icon" class="fa-solid fa-sun hidden text-lg text-amber-500"></i>
                    </button>
                    <span class="text-sm font-medium text-slate-700">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-slate-500 hover:text-red-600 transition-colors flex items-center">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-1.5"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                @include('core.layouts.partials.alerts')

                @yield('content')
            </main>
        </div>
    </div>

    @stack('script')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const themeToggleBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');

            // 1. On page load, check local storage or system preference
            if (localStorage.getItem('bayanicore-theme') === 'dark' ||
                (!('bayanicore-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
            ) {

                document.body.classList.add('dark-theme');
                lightIcon.classList.remove('hidden'); // Show sun icon
            } else {
                document.body.classList.remove('dark-theme');
                darkIcon.classList.remove('hidden'); // Show moon icon
            }

            // 2. Listen for clicks on the toggle button
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    // Toggle icons
                    darkIcon.classList.toggle('hidden');
                    lightIcon.classList.toggle('hidden');

                    // Toggle theme on body
                    if (document.body.classList.contains('dark-theme')) {
                        document.body.classList.remove('dark-theme');
                        localStorage.setItem('bayanicore-theme', 'light');
                    } else {
                        document.body.classList.add('dark-theme');
                        localStorage.setItem('bayanicore-theme', 'dark');
                    }
                });
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            const currentUrl = window.location.href;
            const links = document.querySelectorAll('.sidebar-nav a');

            links.forEach(link => {
                if (link.href === currentUrl) {
                    const parentLi = link.parentElement;
                    parentLi.classList.add('active');

                    // If it's inside a submenu, open the submenu automatically
                    const collapseParent = link.closest('.collapse');
                    if (collapseParent) {
                        collapseParent.classList.add('show');
                        // Highlight the main parent dropdown as well
                        collapseParent.parentElement.classList.add('active');
                    }
                }
            });

            // Mobile Sidebar Elements
            const sidebar = document.getElementById('sidebar');
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('close-sidebar-btn');
            const overlay = document.getElementById('sidebar-overlay');

            // Function to open sidebar
            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            };

            // Function to close sidebar
            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scrolling
            };

            // Event Listeners
            if (mobileBtn) {
                mobileBtn.addEventListener('click', openSidebar);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        $(document).on('click', '.nav-toggle-btn', function() {
            const $target = $($(this).data('target'));
            const $arrow = $(this).find('.toggle-arrow');

            // Toggle the submenu visibility
            $target.slideToggle(200, function() {
                if ($target.is(':visible')) {
                    $target.removeClass('hidden');
                }
            });

            // Rotate the chevron arrow
            $arrow.toggleClass('rotate-180');
        });
    </script>
</body>

</html>
