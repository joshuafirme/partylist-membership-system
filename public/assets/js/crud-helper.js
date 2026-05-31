$(document).ready(function () {

    // ==========================================
    // 1. TAILWIND MODAL VISIBILITY CONTROLS
    // ==========================================
    
    // Open Modal
    $(document).on('click', '.open-modal-btn', function () {
        const target = $(this).data('target');
        $(target).removeClass('hidden').addClass('flex');
    });

    // Close Modal (Clicking X or Cancel buttons)
    $(document).on('click', '.close-modal', function () {
        $(this).closest('.modal').removeClass('flex').addClass('hidden');
    });

    // Optional: Close Modal when clicking outside the modal content area
    $(document).on('click', '.modal', function (e) {
        if ($(e.target).hasClass('modal')) {
            $(this).removeClass('flex').addClass('hidden');
        }
    });

    // ==========================================
    // 2. FORM HELPERS & DATA FILLING
    // ==========================================

    $(document).on('input', '.slug-source', function () {
        const $modal = $(this).closest('.modal');
        const slug = $(this).val()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');
        $modal.find('.slug-target').val(slug);
    });

    $(document).on('click', '[data-role="fill-modal"]', function () {
        const $trigger = $(this);
        
        // Changed from 'bs-target' to 'target' for Tailwind
        const targetModal = $trigger.data('target'); 
        const $modal = $(targetModal);
        const $form = $modal.find('form');
        const mode = $trigger.data('mode'); // 'create' or 'edit'
        const data = $trigger.data();

        // 1. Reset Form & Clear Errors
        $form.trigger('reset');
        // Replaced Bootstrap's .is-invalid with a Tailwind equivalent border reset
        $modal.find('.border-red-500').removeClass('border-red-500'); 
        $modal.find('.dynamic-text').text('');

        // 2. Dynamic Title & Button UI (Updated for Tailwind)
        if (mode === 'edit') {
            $modal.find('.modal-title').html(
                `<i class="fa-solid fa-pen-to-square mr-2"></i> Edit ${data.module || 'Item'}`);
                
            // Swap Emerald for Blue
            $modal.find('button[type="submit"]')
                .text('Update Changes')
                .removeClass('bg-emerald-600 hover:bg-emerald-700')
                .addClass('bg-blue-600 hover:bg-blue-700');

            // Logic for Laravel/Rails/etc. that require _method spoofing
            if ($form.find('input[name="_method"]').length === 0 && data.method) {
                $form.prepend(`<input type="hidden" name="_method" value="${data.method}">`);
            }
        } else {
            $modal.find('.modal-title').html(
                `<i class="fa-solid fa-plus mr-2"></i> Add New ${data.module || 'Item'}`);
                
            // Swap Blue for Emerald
            $modal.find('button[type="submit"]')
                .text('Save Item')
                .removeClass('bg-blue-600 hover:bg-blue-700')
                .addClass('bg-emerald-600 hover:bg-emerald-700');
                
            $form.find('input[name="_method"]').remove();
        }

        // 3. Set Action URL
        $form.attr('action', $trigger.data('action'));

        // 4. Populate Inputs (Only if in edit mode)
        if (mode === 'edit') {
            Object.keys(data).forEach(key => {
                let $field = $modal.find(`[name="${key}"], #${key}`);
                if ($field.length) {
                    if ($field.is(':checkbox')) {
                        $field.prop('checked', !!data[key]);
                    } else {
                        $field.val(data[key]);
                    }
                }
            });
        }
    });

    // ==========================================
    // 3. DELETE CONFIRMATION (SWEETALERT)
    // ==========================================

    $(document).on('click', '.delete-btn', function () {
        const $btn = $(this);
        const url = $btn.data('url');
        const itemName = $btn.data('name') || 'this item';
        const token = $btn.data('token');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete "${itemName}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a dynamic form and submit it
                const $tempForm = $('<form>', {
                    'action': url,
                    'method': 'POST'
                }).append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': token
                })).append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                // Show loading state on the clicked button
                $btn.prop('disabled', true).html(
                    '<i class="fa-solid fa-spinner fa-spin"></i>');

                // Add to body and submit
                $('body').append($tempForm);
                $tempForm.submit();
            }
        });
    });
});