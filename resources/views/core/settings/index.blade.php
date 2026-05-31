@extends('core.layouts.app')

@section('title', 'System Settings - System Portal')

@section('content')
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">System Settings</h2>
                <p class="text-sm text-slate-500 mt-1">Configure global application variables and brand assets.</p>
            </div>
            <button type="submit" form="settingsForm" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Save & Apply Settings
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                            <i class="fa-solid fa-cube mr-2 text-blue-600"></i> General Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Application Name</label>
                                <input type="text" name="app_name" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="{{ old('app_name', $settings->app_name ?? 'System Portal') }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Version</label>
                                <input type="text" name="version" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="{{ old('version', $settings->version ?? '1.0.0') }}" required>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-slate-700 mb-1">System Description (Meta)</label>
                                <textarea name="description" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors" rows="3">{{ old('description', $settings->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                            <i class="fa-solid fa-address-book mr-2 text-blue-600"></i> Contact & Social
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Contact Email</label>
                                <input type="email" name="contact_email" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="{{ old('contact_email', $settings->contact_email ?? '') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Contact Phone</label>
                                <input type="text" name="contact_phone" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="{{ old('contact_phone', $settings->contact_phone ?? '') }}">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Facebook URL</label>
                                <input type="url" name="facebook_url" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="{{ old('facebook_url', $settings->facebook_url ?? '') }}">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Twitter URL</label>
                                <input type="url" name="twitter_url" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="{{ old('twitter_url', $settings->twitter_url ?? '') }}">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1">LinkedIn URL</label>
                                <input type="url" name="linkedin_url" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="{{ old('linkedin_url', $settings->linkedin_url ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm h-full">
                        <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                            <i class="fa-solid fa-images mr-2 text-blue-600"></i> Brand Assets
                        </h3>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Main Logo</label>
                            <div class="w-full h-32 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center mb-2 overflow-hidden">
                                @if (!empty($settings->logo_path))
                                    <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" class="max-h-24">
                                @else
                                    <div class="text-slate-400 text-sm flex flex-col items-center">
                                        <i class="fa-solid fa-image text-2xl mb-1"></i> No Logo
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="logo" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Favicon</label>
                            <div class="w-16 h-16 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center mb-2">
                                @if (!empty($settings->favicon_path))
                                    <img src="{{ asset('storage/' . $settings->favicon_path) }}" alt="Favicon" class="max-h-10">
                                @else
                                    <i class="fa-solid fa-globe text-slate-400"></i>
                                @endif
                            </div>
                            <input type="file" name="favicon" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection