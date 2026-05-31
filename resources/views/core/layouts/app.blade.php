<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'System Portal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        @include('core.layouts.partials.sidenav')

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
                <div class="flex items-center md:hidden">
                    <button class="text-slate-500 hover:text-slate-700">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>

                <div class="flex-1 md:flex-none"></div>

                <div class="flex items-center space-x-4">
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

    @stack('script')

    <script>
        // ==========================================
        // SIDEBAR NAVIGATION TOGGLE
        // ==========================================
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
        document.addEventListener("DOMContentLoaded", function() {
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
        });
    </script>
</body>

</html>
