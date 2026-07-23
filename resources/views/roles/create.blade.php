@extends("layouts.dashboard")

@section("content")
<div class="border-b border-gray-200 bg-white px-4 py-5 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
    <nav class="mb-4 flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ url('/') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                    <svg class="mr-2.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('roles.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white md:ml-2">Roles</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">Create</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create a new role</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a role and choose whether it is available in the system.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="inline-flex w-fit items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to roles
        </a>
    </div>
</div>

<div class="mx-auto w-full max-w-5xl p-4 sm:p-6 lg:p-8">
    @if ($errors->any())
        <div class="mb-6 flex rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-300" role="alert">
            <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM9 5a1 1 0 012 0v5a1 1 0 11-2 0V5zm1 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            <div>
                <span class="font-semibold">Please check the form and try again.</span>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('roles.store') }}" method="POST" enctype="multipart/form-data" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf

        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Role details</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fields marked with an asterisk are required.</p>
        </div>

        <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-5">
            <div class="space-y-6 lg:col-span-3">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Role name <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        maxlength="50"
                        required
                        autofocus
                        aria-describedby="name-help @error('name') name-error @enderror"
                        class="block w-full rounded-lg border bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 {{ $errors->has('name') ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                        placeholder="e.g. Content Manager"
                    >
                    @error('name')
                        <p id="name-error" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @else
                        <p id="name-help" class="mt-2 text-sm text-gray-500 dark:text-gray-400">Use a clear, unique name of no more than 50 characters.</p>
                    @enderror
                </div>

                <div>
                    <label for="status_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Status <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <select
                        name="status_id"
                        id="status_id"
                        required
                        aria-describedby="@error('status_id') status-error @enderror"
                        class="block w-full rounded-lg border bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white {{ $errors->has('status_id') ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                    >
                        <option value="" disabled {{ old('status_id') === null ? 'selected' : '' }}>Choose a status</option>
                        @foreach ($statuses as $id => $name)
                            <option value="{{ $id }}" @selected((string) old('status_id') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('status_id')
                        <p id="status-error" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="lg:col-span-2">
                <label for="image" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Role image <span class="font-normal text-gray-500">(optional)</span></label>
                <label for="image" id="image-dropzone" class="flex min-h-56 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-5 text-center transition hover:border-primary-500 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:border-primary-500 dark:hover:bg-gray-700">
                    <div id="upload-placeholder">
                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Click to upload an image</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, JPEG or PNG · maximum 1 MB</p>
                    </div>
                    <img id="image-preview" class="hidden max-h-48 w-full rounded-lg object-contain" alt="Selected role image preview">
                    <span id="image-name" class="mt-3 hidden max-w-full truncate text-xs font-medium text-gray-600 dark:text-gray-300"></span>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden">
                </label>
                @error('image')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            @include('roles.partials.permission-checkboxes', [
                'columnClass' => 'lg:col-span-5',
                'selectedPermissionIds' => old('permission_ids', []),
            ])
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route('roles.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-700">Cancel</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create role
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('image');
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        const fileName = document.getElementById('image-name');

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (!file) {
                preview.src = '';
                preview.classList.add('hidden');
                fileName.classList.add('hidden');
                placeholder.classList.remove('hidden');
                return;
            }

            const reader = new FileReader();
            reader.addEventListener('load', function (event) {
                preview.src = event.target.result;
                preview.classList.remove('hidden');
                fileName.textContent = file.name;
                fileName.classList.remove('hidden');
                placeholder.classList.add('hidden');
            });
            reader.readAsDataURL(file);
        });
    });
</script>
@endsection

@section("scripts")
    <script type="text/javascript">
        

    </script>
@endsection
