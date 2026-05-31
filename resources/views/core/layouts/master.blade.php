<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - System Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col">
            <div class="h-16 flex items-center px-6 border-b border-slate-100">
                <i class="fa-solid fa-layer-group text-blue-600 text-xl mr-3"></i>
                <span class="text-lg font-semibold text-slate-900">System Portal</span>
            </div>
            @yield('content')

        </aside>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

    @stack('script')

  
</body>

</html>
