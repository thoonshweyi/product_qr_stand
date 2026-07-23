@extends('layouts.dashboard')

@section('content')
<div class="border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 lg:mt-1.5">
    <nav class="mb-5 flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li>
                <a href="{{ route('dashboards.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                    <svg class="mr-2.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Home
                </a>
            </li>
            <li class="flex items-center">
                <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500">Permissions</span>
            </li>
        </ol>
    </nav>

    <div class="mb-5">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Permission management</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create permissions and manage existing access rules.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('permissions.store') }}" method="POST" class="border-t border-gray-200 pt-4 dark:border-gray-700">
        @csrf

        <div class="grid grid-cols-12 items-end gap-4">
            <div class="col-span-12 md:col-span-4">
                <label for="permission-name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                    Name <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name" id="permission-name" value="{{ old('name') }}" required maxlength="100"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter permission name">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-12 md:col-span-3">
                <label for="permission-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                    Status <span class="text-red-600">*</span>
                </label>
                <select name="status_id" id="permission-status" required
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Choose status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) old('status_id') === (string) $status->id)>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @error('status_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-12 flex gap-2 md:col-span-5">
                <button type="reset" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    Clear
                </button>
                <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 dark:bg-primary-600">
                    Submit
                </button>
            </div>
        </div>
    </form>
</div>

<div class="bg-white dark:bg-gray-800">
    <div class="flex flex-col gap-3 border-b border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
        <form action="{{ route('permissions.index') }}" method="GET" class="flex w-full max-w-lg gap-2">
            <label for="permissions-search" class="sr-only">Search permissions</label>
            <input type="search" name="filtername" id="permissions-search" value="{{ request('filtername') }}"
                class="block min-w-0 flex-1 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                placeholder="Search permissions by name">
            <button type="submit" class="inline-flex items-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-800">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                Search
            </button>
            @if (request()->filled('filtername'))
                <a href="{{ route('permissions.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300">Clear</a>
            @endif
        </form>

        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $permissions->total() }} {{ Str::plural('permission', $permissions->total()) }}
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">No.</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Name</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Slug</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Updated by</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Updated at</th>
                    <th class="p-4 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                @forelse ($permissions as $index => $permission)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/60">
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $permissions->firstItem() + $index }}
                        </td>
                        <td class="whitespace-nowrap p-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $permission->name }}</td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-500 dark:text-gray-400">{{ $permission->slug }}</td>
                        <td class="whitespace-nowrap p-4">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' => $permission->status_id === 3,
                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $permission->status_id !== 3,
                            ])>
                                {{ $permission->status?->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $permission->user?->name ?? 'Unknown' }}</td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $permission->updated_at?->format('Y-m-d h:i A') }}</td>
                        <td class="whitespace-nowrap p-4 text-right">
                            <button type="button"
                                class="edit-permission-button inline-flex items-center rounded-lg bg-primary-700 px-3 py-2 text-sm font-medium text-white hover:bg-primary-800"
                                data-modal-target="edit-permission-modal" data-modal-toggle="edit-permission-modal"
                                data-id="{{ $permission->id }}" data-name="{{ $permission->name }}" data-status-id="{{ $permission->status_id }}">
                                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828Z"/><path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z" clip-rule="evenodd"/></svg>
                                Edit
                            </button>
                            <button type="button"
                                class="delete-permission-button ml-2 inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
                                data-modal-target="delete-permission-modal" data-modal-toggle="delete-permission-modal"
                                data-id="{{ $permission->id }}" data-name="{{ $permission->name }}">
                                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9ZM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8Zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Z" clip-rule="evenodd"/></svg>
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">No permissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($permissions->hasPages())
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">{{ $permissions->links() }}</div>
    @endif
</div>

<div id="edit-permission-modal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 p-4">
    <div class="relative max-h-full w-full max-w-lg">
        <div class="relative rounded-xl bg-white shadow dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit permission</h3>
                <button type="button" data-modal-hide="edit-permission-modal" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white" aria-label="Close">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/></svg>
                </button>
            </div>

            <form id="edit-permission-form" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="space-y-5 p-6">
                    <div>
                        <label for="edit-permission-name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input type="text" name="name" id="edit-permission-name" required maxlength="100"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @error('name', 'updatePermission')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit-permission-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select name="status_id" id="edit-permission-status" required
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        @error('status_id', 'updatePermission')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-200 p-5 dark:border-gray-700">
                    <button type="button" data-modal-hide="edit-permission-modal" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">Cancel</button>
                    <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800">Update permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="delete-permission-modal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 p-4">
    <div class="relative max-h-full w-full max-w-md">
        <div class="relative rounded-xl bg-white p-6 text-center shadow dark:bg-gray-800">
            <svg class="mx-auto h-14 w-14 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/></svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Delete permission?</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You are about to delete <strong id="delete-permission-name"></strong>. This action cannot be undone.</p>

            <form id="delete-permission-form" method="POST" action="" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" data-modal-hide="delete-permission-modal" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">Cancel</button>
                <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('click', function (event) {
        const editButton = event.target.closest('.edit-permission-button');

        if (editButton) {
            document.getElementById('edit-permission-name').value = editButton.dataset.name;
            document.getElementById('edit-permission-status').value = editButton.dataset.statusId;
            document.getElementById('edit-permission-form').action =
                @js(url('/permissions')) + '/' + editButton.dataset.id;
        }

        const deleteButton = event.target.closest('.delete-permission-button');

        if (deleteButton) {
            document.getElementById('delete-permission-name').textContent = deleteButton.dataset.name;
            document.getElementById('delete-permission-form').action =
                @js(url('/permissions')) + '/' + deleteButton.dataset.id;
        }
    }, true);

    @if ($errors->updatePermission->any() && old('edit_permission_id'))
        window.addEventListener('load', function () {
            document.querySelector(
                `.edit-permission-button[data-id="{{ old('edit_permission_id') }}"]`
            )?.click();
        });
    @endif
</script>
@endsection
