@extends('layouts.dashboard')

@section('content')
<div class="border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 sm:flex sm:items-center sm:justify-between">
    <div>
        <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <a href="{{ route('dashboards.index') }}" class="font-medium hover:text-primary-600">Home</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
            <span>Workflows</span>
        </nav>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">All workflows</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage the destinations available during product creation.</p>
    </div>
    <button id="add-workflow-button" type="button" class="mt-4 inline-flex items-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-800 sm:mt-0">
        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add workflow
    </button>
</div>

<div class="p-4 sm:p-6">
    <form action="{{ route('workflows.index') }}" method="GET" class="mb-5 max-w-md">
        <label for="workflow-search" class="sr-only">Search workflows</label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
            <input id="workflow-search" name="keyword" value="{{ $keyword }}" class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 pl-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Search by name or slug">
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">No.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Workflow</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Slug</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Steps</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Created by</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($workflows as $index => $workflow)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-5 py-4 text-sm text-gray-500">{{ $workflows->firstItem() + $index }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $workflow->slug === 'online' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    </span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $workflow->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $workflow->slug }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">{{ $workflow->status?->name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $workflow->steps_count }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $workflow->user?->name ?? 'System' }}</td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <button type="button" data-id="{{ $workflow->id }}" class="edit-workflow rounded-lg p-2 text-primary-600 hover:bg-primary-50 dark:hover:bg-gray-700" title="Edit">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </button>
                                <button type="button" data-id="{{ $workflow->id }}" data-name="{{ $workflow->name }}" class="delete-workflow rounded-lg p-2 text-red-600 hover:bg-red-50 dark:hover:bg-gray-700" title="Delete">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M18.16 5.79 17.59 19.5A2.25 2.25 0 0 1 15.343 21H8.657a2.25 2.25 0 0 1-2.247-2.25L5.84 5.79m12.32 0a48.108 48.108 0 0 0-3.478-.397m-9.842.397a48.11 48.11 0 0 1 3.478-.397m6.364 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-1.184 0c-1.18.037-2.09 1.022-2.09 2.201v.916m5.364 0a48.667 48.667 0 0 0-5.364 0"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500">No workflows found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($workflows->hasPages())
            <div class="border-t border-gray-200 p-4 dark:border-gray-700">{{ $workflows->links() }}</div>
        @endif
    </div>
</div>

<div id="workflow-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4">
    <div class="flex max-h-[92vh] w-full max-w-4xl flex-col rounded-2xl bg-white shadow-xl dark:bg-gray-800">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h2 id="workflow-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Add workflow</h2>
            <button type="button" class="close-workflow-modal rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">✕</button>
        </div>
        <form id="workflow-form" class="overflow-y-auto p-6">
            @csrf
            <input id="workflow-id" type="hidden">
            <div id="workflow-form-errors" class="mb-5 hidden rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-900/20 dark:text-red-300"></div>
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="workflow-name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Name <span class="text-red-600">*</span></label>
                    <input id="workflow-name" name="name" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. Marketplace">
                    <p data-error="name" class="mt-1 hidden text-xs text-red-600"></p>
                </div>
                <div>
                    <label for="workflow-slug" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Slug</label>
                    <input id="workflow-slug" name="slug" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Auto-generated from name">
                    <p data-error="slug" class="mt-1 hidden text-xs text-red-600"></p>
                </div>
                <div>
                    <label for="workflow-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-600">*</span></label>
                    <select id="workflow-status" name="status_id" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <p data-error="status_id" class="mt-1 hidden text-xs text-red-600"></p>
                </div>
                <div class="sm:col-span-3">
                    <label for="workflow-description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Description</label>
                    <textarea id="workflow-description" name="description" rows="2" maxlength="1000" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Short description of this workflow"></textarea>
                    <p data-error="description" class="mt-1 hidden text-xs text-red-600"></p>
                </div>
            </div>

            <div class="mt-7 border-t border-gray-200 pt-6 dark:border-gray-700">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Workflow steps</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Steps run from top to bottom.</p>
                    </div>
                    <button id="add-step-button" type="button" class="inline-flex shrink-0 items-center rounded-lg border border-primary-300 bg-primary-50 px-3 py-2 text-xs font-semibold text-primary-700 hover:bg-primary-100 dark:border-primary-700 dark:bg-primary-900/20 dark:text-primary-300">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add step
                    </button>
                </div>
                <div id="step-rows" class="space-y-3"></div>
                <p data-error="steps" class="mt-2 hidden text-xs text-red-600"></p>
            </div>
            <div class="mt-7 flex justify-end gap-3">
                <button type="button" class="close-workflow-modal rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-300">Cancel</button>
                <button id="save-workflow" type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-800">Save workflow</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    const roles = @js($roles);
    const statuses = @js($statuses);
    const modal = $('#workflow-modal');
    const form = $('#workflow-form');
    let stepRows = [];
    let nextStepKey = 1;
    const escapeHtml = value => $('<div>').text(value ?? '').html()
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    const options = (items, selected, placeholder) =>
        `<option value="">${placeholder}</option>` + items.map(item =>
            `<option value="${item.id}" ${String(item.id) === String(selected || '') ? 'selected' : ''}>${escapeHtml(item.name)}</option>`
        ).join('');
    const clearErrors = () => {
        $('[data-error]').addClass('hidden').text('');
        $('#workflow-form-errors').addClass('hidden').empty();
    };
    const openModal = () => modal.removeClass('hidden').addClass('flex');
    const closeModal = () => modal.addClass('hidden').removeClass('flex');

    function newStep(data = {}) {
        return {
            key: nextStepKey++,
            id: data.id || '',
            name: data.name || '',
            role_id: data.role_id || '',
            status_id: data.status_id || '',
        };
    }

    function renderSteps() {
        $('#step-rows').html(stepRows.map((step, index) => `
            <div class="step-row rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-700/30" data-key="${step.key}">
                <input type="hidden" name="steps[${index}][id]" value="${step.id}">
                <div class="grid items-end gap-3 md:grid-cols-[2rem_minmax(0,1.25fr)_minmax(0,1fr)_minmax(0,.9fr)_2rem]">
                    <span class="mb-1.5 flex h-7 w-7 items-center justify-center rounded-full bg-primary-100 text-xs font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">${index + 1}</span>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Step name *</label>
                        <select name="steps[${index}][name]" class="step-field block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" data-field="name">
                            <option value="">Choose step</option>
                            <option value="Check" ${step.name === 'Check' ? 'selected' : ''}>Check</option>
                            <option value="Finish" ${step.name === 'Finish' ? 'selected' : ''}>Finish</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Role</label>
                        <select name="steps[${index}][role_id]" class="step-field block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" data-field="role_id">${options(roles, step.role_id, 'Any role')}</select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Status</label>
                        <select name="steps[${index}][status_id]" class="step-field block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" data-field="status_id">${options(statuses, step.status_id, 'No status')}</select>
                    </div>
                    <button type="button" class="remove-step mb-1.5 rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" aria-label="Remove step">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>`).join(''));
    }

    $('#add-workflow-button').on('click', function () {
        form[0].reset();
        $('#workflow-id').val('');
        $('#workflow-modal-title').text('Add workflow');
        stepRows = [newStep()];
        renderSteps();
        clearErrors();
        openModal();
    });

    $('.close-workflow-modal').on('click', closeModal);
    modal.on('click', function (event) { if (event.target === this) closeModal(); });

    $('.edit-workflow').on('click', function () {
        clearErrors();
        $.get(`{{ url('/workflows') }}/${$(this).data('id')}`).done(function (response) {
            $('#workflow-id').val(response.data.id);
            $('#workflow-name').val(response.data.name);
            $('#workflow-slug').val(response.data.slug);
            $('#workflow-status').val(response.data.status_id);
            $('#workflow-description').val(response.data.description || '');
            stepRows = response.data.steps.map(newStep);
            if (!stepRows.length) stepRows = [newStep()];
            renderSteps();
            $('#workflow-modal-title').text('Edit workflow');
            openModal();
        });
    });

    $('#add-step-button').on('click', function () {
        stepRows.push(newStep());
        renderSteps();
    });

    $('#step-rows').on('input change', '.step-field', function () {
        const row = stepRows.find(item => item.key === Number($(this).closest('.step-row').data('key')));
        const field = $(this).data('field');
        row[field] = $(this).is(':checkbox') ? $(this).is(':checked') : $(this).val();
    }).on('click', '.remove-step', function () {
        if (stepRows.length === 1) {
            $('[data-error="steps"]').removeClass('hidden').text('A workflow must have at least one step.');
            return;
        }
        const key = Number($(this).closest('.step-row').data('key'));
        stepRows = stepRows.filter(item => item.key !== key);
        renderSteps();
    });

    form.on('submit', function (event) {
        event.preventDefault();
        clearErrors();
        const id = $('#workflow-id').val();
        const data = form.serializeArray();
        if (id) data.push({ name: '_method', value: 'PUT' });

        $('#save-workflow').prop('disabled', true).text('Saving...');
        $.ajax({
            url: id ? `{{ url('/workflows') }}/${id}` : `{{ route('workflows.store') }}`,
            method: 'POST',
            data: $.param(data),
        }).done(function (response) {
            Swal.fire({ icon: 'success', title: response.message, timer: 1200, showConfirmButton: false })
                .then(() => window.location.reload());
        }).fail(function (xhr) {
            const errors = xhr.responseJSON?.errors || {};
            Object.entries(errors).forEach(([field, messages]) => {
                $(`[data-error="${field}"]`).removeClass('hidden').text(messages[0]);
            });
            const messages = Object.values(errors).flat();
            $('#workflow-form-errors').toggleClass('hidden', !messages.length)
                .html(messages.map(message => `<div>• ${escapeHtml(message)}</div>`).join(''));
        }).always(function () {
            $('#save-workflow').prop('disabled', false).text('Save workflow');
        });
    });

    $('.delete-workflow').on('click', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            icon: 'warning',
            title: `Delete ${name}?`,
            text: 'This action cannot be undone.',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, delete it',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `{{ url('/workflows') }}/${id}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
            }).done(() => window.location.reload()).fail(function (xhr) {
                const message = Object.values(xhr.responseJSON?.errors || {}).flat()[0]
                    || xhr.responseJSON?.message
                    || 'Unable to delete this workflow.';
                Swal.fire({ icon: 'error', title: 'Delete failed', text: message });
            });
        });
    });
});
</script>
@endsection
