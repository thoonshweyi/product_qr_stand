<div class="{{ $columnClass ?? '' }}">
    <div class="mb-3">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Permissions</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose what users with this role are allowed to do.</p>
    </div>

    @if ($permissions->isEmpty())
        <p class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400">
            No active permissions are available.
        </p>
    @else
        <div class="grid gap-3 rounded-lg border border-gray-200 p-4 sm:grid-cols-2 lg:grid-cols-3 dark:border-gray-600">
            @foreach ($permissions as $permission)
                <label class="flex cursor-pointer items-center gap-3 rounded-lg p-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <input type="checkbox"
                        name="permission_ids[]"
                        value="{{ $permission->id }}"
                        @checked(in_array((string) $permission->id, array_map('strval', $selectedPermissionIds), true))
                        class="h-4 w-4 rounded border-gray-300 bg-gray-50 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $permission->name }}</span>
                </label>
            @endforeach
        </div>
    @endif

    @error('permission_ids')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('permission_ids.*')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
