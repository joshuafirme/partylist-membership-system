@extends('core.layouts.app')

@section('title', 'User Roles - System Portal')

@section('content')
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">User Roles & Permissions</h2>
                <p class="text-sm text-slate-500 mt-1">Define roles and manage what users can access.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button type="button" data-target="#roleModal" data-role="fill-modal" data-mode="create"
                    data-action="{{ route('user-roles.store') }}" data-module="Role"
                    class="open-modal-btn inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Add New Role
                </button>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-medium">
                            <th class="px-6 py-4 w-1/4">Role Name</th>
                            <th class="px-6 py-4 w-1/2">Permissions</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">

                        @forelse($roles as $role)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $role->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @php $perms = $role->permissions ?? []; @endphp
                                        
                                        @if(in_array('all', $perms))
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs font-medium border border-purple-200">
                                                <i class="fa-solid fa-star mr-1"></i> Full Access
                                            </span>
                                        @else
                                            @foreach(array_slice($perms, 0, 4) as $perm)
                                                <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs border border-slate-200">
                                                    {{ ucwords(str_replace('_', ' ', $perm)) }}
                                                </span>
                                            @endforeach
                                            
                                            @if(count($perms) > 4)
                                                <span class="inline-flex items-center px-2 py-1 rounded bg-slate-50 text-slate-500 text-xs border border-slate-200">
                                                    +{{ count($perms) - 4 }} more
                                                </span>
                                            @endif
                                            
                                            @if(empty($perms))
                                                <span class="text-xs text-slate-400 italic">No permissions assigned</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        
                                        <button type="button" data-target="#roleModal"
                                            data-role="fill-modal" data-mode="edit"
                                            data-action="{{ route('user-roles.update', $role->id) }}" data-method="PUT"
                                            data-module="Role" data-name="{{ $role->name }}" 
                                            data-perms="{{ json_encode($role->permissions ?? []) }}"
                                            class="open-modal-btn p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        
                                        <form action="{{ route('user-roles.destroy', $role->id) }}" method="POST" class="inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this role? This might affect assigned users.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-4">
                                        <i class="fa-solid fa-shield-halved text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900">No roles found</h3>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="roleModal" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true">
        <div class="relative w-full max-w-2xl bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 bg-slate-800/50 flex-shrink-0">
                <h5 class="modal-title text-lg font-semibold text-white">System Role</h5>
                <button type="button" class="close-modal text-slate-400 hover:text-white transition-colors" aria-label="Close">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                
                <div class="p-6 space-y-6 overflow-y-auto flex-1 custom-scrollbar">

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Role Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required placeholder="e.g., Regional Manager"
                            class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3 border-b border-slate-700 pb-2">
                            <label class="block text-sm font-medium text-slate-300">Assign Permissions</label>
                            
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" id="selectAllPerms" class="rounded border-slate-600 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800 w-4 h-4 cursor-pointer">
                                <span class="ml-2 text-sm font-medium text-slate-400 group-hover:text-white transition-colors">Select All</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($availablePermissions as $group => $permissions)
                                <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                    <h6 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ $group }}</h6>
                                    <div class="space-y-2.5">
                                        @foreach($permissions as $key => $label)
                                            <label class="flex items-start cursor-pointer group">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                                        class="perm-cb rounded border-slate-600 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800 w-4 h-4 cursor-pointer transition-colors">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <span class="text-slate-300 group-hover:text-white transition-colors">{{ $label }}</span>
                                                    @if($key === 'all')
                                                        <p class="text-xs text-amber-500/80 mt-0.5">Grants override access to all modules.</p>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-end px-6 py-4 border-t border-slate-700 bg-slate-800/50 space-x-3 flex-shrink-0">
                    <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-slate-300 bg-transparent border border-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="submit-btn px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                        Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/crud-helper.js?v=') }}"></script>
    
    <script>
        $(document).ready(function() {
            
            // 1. Intercept the modal fill event specifically for this page to handle checkboxes
            $(document).on('click', '[data-role="fill-modal"]', function () {
                const mode = $(this).data('mode');
                
                // Uncheck everything first
                $('.perm-cb').prop('checked', false);
                $('#selectAllPerms').prop('checked', false);

                // If editing, read the data-perms JSON array and check the matching boxes
                if (mode === 'edit') {
                    const perms = $(this).data('perms'); // jQuery automatically parses valid JSON
                    
                    if (Array.isArray(perms)) {
                        perms.forEach(function(permValue) {
                            $(`.perm-cb[value="${permValue}"]`).prop('checked', true);
                        });
                    }
                }
                
                updateSelectAllState();
            });

            // 2. Select All Checkbox Logic
            $('#selectAllPerms').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.perm-cb').prop('checked', isChecked);
            });

            // 3. Individual Checkbox Logic (If user unchecks one, uncheck "Select All")
            $('.perm-cb').on('change', function() {
                updateSelectAllState();
            });

            // Helper function to check/uncheck the "Select All" box dynamically
            function updateSelectAllState() {
                const totalCheckboxes = $('.perm-cb').length;
                const checkedCheckboxes = $('.perm-cb:checked').length;
                
                if (totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0) {
                    $('#selectAllPerms').prop('checked', true);
                } else {
                    $('#selectAllPerms').prop('checked', false);
                }
            }
        });
    </script>
@endpush