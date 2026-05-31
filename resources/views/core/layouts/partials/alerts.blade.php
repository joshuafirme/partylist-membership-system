@if(session('success'))
    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-lg mb-6 border border-emerald-100">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100">
        {{ session('error') }}
    </div>
@endif