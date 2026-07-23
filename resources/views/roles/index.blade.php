@extends('layouts.dashboard')

@section('content')
<div class="border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 lg:mt-1.5">
    <nav class="mb-5 flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li>
                <a href="{{ route('dashboards.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                    <svg class="mr-2.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 0 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7Z"/></svg>
                    Home
                </a>
            </li>
            <li class="flex items-center">
                <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z" clip-rule="evenodd"/></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500">Roles</span>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Role management</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create roles and manage their details and availability.</p>
        </div>
        <a href="{{ route('roles.create') }}" class="inline-flex w-fit items-center justify-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-800 dark:bg-primary-600 dark:hover:bg-primary-700">
            <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 5a1 1 0 0 1 1 1v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H6a1 1 0 1 1 0-2h3V6a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
            Add role
        </a>
    </div>

    @if (session('success'))
        <div class="mt-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300" role="alert">
            {{ session('success') }}
        </div>
    @endif
</div>

<div class="bg-white dark:bg-gray-800">
    <div class="border-b border-gray-200 p-4 dark:border-gray-700">
        <form action="{{ route('roles.index') }}" method="GET" class="grid grid-cols-12 items-end gap-3">
            <div class="col-span-12 md:col-span-4">
                <label for="roles-search" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Role name</label>
                <input type="search" name="filtername" id="roles-search" value="{{ request('filtername') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Search role name">
            </div>

            <div class="col-span-12 md:col-span-3">
                <label for="role-status-filter" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="filterstatus_id" id="role-status-filter"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach ($filterstatuses as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('filterstatus_id') === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-12 flex gap-2 md:col-span-5">
                <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800">Search</button>
                @if (request()->filled('filtername') || request()->filled('filterstatus_id'))
                    <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">No.</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Role</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Updated by</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Created at</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Updated at</th>
                    <th class="p-4 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                @forelse ($roles as $index => $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/60">
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $roles->firstItem() + $index }}</td>
                        <td class="whitespace-nowrap p-4">
                            <div class="flex items-center gap-3">
                                @if (filled($role->image))
                                    <img src="{{ asset($role->image) }}" alt="" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
                                        {{ Str::upper(Str::substr($role->name, 0, 2)) }}
                                    </span>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $role->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $role->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap p-4">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' => $role->status_id === 3,
                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $role->status_id !== 3,
                            ])>
                                {{ $role->status?->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $role->user?->name ?? 'Unknown' }}</td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $role->created_at?->format('Y-m-d h:i A') }}</td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $role->updated_at?->format('Y-m-d h:i A') }}</td>
                        <td class="whitespace-nowrap p-4 text-right">
                            <a href="{{ route('roles.edit', $role) }}"
                                class="inline-flex items-center rounded-lg bg-primary-700 px-3 py-2 text-sm font-medium text-white hover:bg-primary-800">
                                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828Z"/><path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z" clip-rule="evenodd"/></svg>
                                Edit
                            </a>
                            <button type="button"
                                class="delete-role-button ml-2 inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
                                data-modal-target="delete-role-modal" data-modal-toggle="delete-role-modal"
                                data-id="{{ $role->id }}" data-name="{{ $role->name }}">
                                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9ZM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8Zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Z" clip-rule="evenodd"/></svg>
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($roles->hasPages())
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">{{ $roles->links() }}</div>
    @endif
</div>

<div id="delete-role-modal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 p-4">
    <div class="relative max-h-full w-full max-w-md">
        <div class="relative rounded-xl bg-white p-6 text-center shadow dark:bg-gray-800">
            <svg class="mx-auto h-14 w-14 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/></svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Delete role?</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You are about to delete <strong id="delete-role-name"></strong>. This action cannot be undone.</p>
            <form id="delete-role-form" method="POST" action="" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" data-modal-hide="delete-role-modal" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">Cancel</button>
                <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('click', function (event) {
        const deleteButton = event.target.closest('.delete-role-button');

        if (deleteButton) {
            document.getElementById('delete-role-name').textContent = deleteButton.dataset.name;
            document.getElementById('delete-role-form').action = @js(url('/roles')) + '/' + deleteButton.dataset.id;
        }
    }, true);
</script>
@endsection
