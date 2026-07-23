@extends('layouts.dashboard')

@section('content')
<div class="border-b border-gray-200 bg-white px-4 py-5 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
    <nav class="mb-4 flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li>
                <a href="{{ route('dashboards.index') }}" class="text-gray-700 hover:text-primary-600 dark:text-gray-300">Home</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z" clip-rule="evenodd"/></svg>
                <a href="{{ route('roles.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 dark:text-gray-300 md:ml-2">Roles</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z" clip-rule="evenodd"/></svg>
                <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">Edit</span>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit role</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update {{ $role->name }} role information.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="inline-flex w-fit items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
            Back to roles
        </a>
    </div>
</div>

<div class="mx-auto w-full max-w-4xl p-4 sm:p-6">
    <form action="{{ route('roles.update', $role) }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf
        @method('PUT')

        <div class="grid gap-6 p-5 md:grid-cols-2 sm:p-6">
            <div class="space-y-5">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Role name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required maxlength="50"
                        class="block w-full rounded-lg border bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select name="status_id" id="status_id" required
                        class="block w-full rounded-lg border bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white {{ $errors->has('status_id') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" @selected((string) old('status_id', $role->status_id) === (string) $status->id)>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="image" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                    Role image <span class="font-normal text-gray-500">(optional)</span>
                </label>

                <label for="image" class="flex min-h-52 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 text-center hover:border-primary-500 dark:border-gray-600 dark:bg-gray-700/50">
                    <img id="image-preview"
                        src="{{ filled($role->image) ? asset($role->image) : '' }}"
                        class="{{ filled($role->image) ? '' : 'hidden' }} max-h-36 max-w-full rounded-lg object-contain"
                        alt="Role image preview">

                    <div id="image-placeholder" class="{{ filled($role->image) ? 'hidden' : '' }}">
                        <svg class="mx-auto mb-2 h-9 w-9 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Choose a replacement image</p>
                    </div>

                    <p id="image-name" class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        {{ filled($role->image) ? 'Current image — click to replace' : 'JPG, JPEG or PNG · maximum 1 MB' }}
                    </p>
                    <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden">
                </label>

                @error('image')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @include('roles.partials.permission-checkboxes', [
                'columnClass' => 'md:col-span-2',
                'selectedPermissionIds' => old('permission_ids', $role->permissions->pluck('id')->all()),
            ])
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
            <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">Cancel</a>
            <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 dark:bg-primary-600">
                Update role
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('image')?.addEventListener('change', function () {
        const file = this.files?.[0];

        if (!file) return;

        const preview = document.getElementById('image-preview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        document.getElementById('image-placeholder').classList.add('hidden');
        document.getElementById('image-name').textContent = file.name;
    });
</script>
@endsection
