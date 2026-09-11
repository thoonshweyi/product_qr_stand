@extends('layouts.dashboard')

@section('content')
@php
    $summary = session('import_summary', [
        'total' => 0,
        'success' => 0,
        'fail' => 0,
    ]);
    $failedRecords = collect(session('validation_errors', []));
    $previewFailedRecords = $failedRecords->isNotEmpty()
        ? $failedRecords
        : collect($sampleFailedRecords ?? []);
@endphp

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
                <a href="{{ route('products.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">Products</a>
            </li>
            <li class="flex items-center">
                <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z" clip-rule="evenodd"/></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500">Import</span>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Product Import</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload product Excel data and review failed records with row-level messages.</p>
        </div>

        <a href="{{ route('products.index') }}" class="inline-flex w-fit items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Back to products
        </a>
    </div>
</div>

@if (session('success'))
    <div class="border-b border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 dark:border-green-900 dark:bg-green-900/20 dark:text-green-300">
        {{ session('success') }}
    </div>
@endif

@if (session('error') || $errors->any())
    <div class="border-b border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-900 dark:bg-red-900/20 dark:text-red-300">
        {{ session('error') ?? $errors->first() }}
    </div>
@endif

<div class="space-y-6 p-4">
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    <i class="fa-solid fa-file-import"></i>
                </span>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Import Excel File</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Only .xls and .xlsx files are allowed. Maximum file size is 5MB.</p>
                </div>
            </div>
        </div>

        <form id="product-import-form" action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="p-5">
            @csrf

            <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <label for="product-import-file" class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">
                        Product Excel File <span class="text-red-600">*</span>
                    </label>
                    <label for="product-import-file" class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/40 px-6 py-8 text-center transition hover:border-blue-300 hover:bg-blue-50 dark:border-blue-800 dark:bg-blue-900/10 dark:hover:bg-blue-900/20">
                        <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-white text-blue-700 shadow-sm dark:bg-gray-800 dark:text-blue-300">
                            <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                        </span>
                        <span id="selected-file-name" class="text-sm font-semibold text-gray-900 dark:text-white">Choose Excel file to import</span>
                        <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">Click here or drop your prepared product file</span>
                    </label>
                    <input type="file" name="file" id="product-import-file" accept=".xls,.xlsx" class="hidden">
                    @error('file')
                        <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" id="import-product-button" class="inline-flex h-11 items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-200 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-900">
                        <i class="fa-solid fa-upload mr-2"></i>
                        <span id="import-product-button-label">Import</span>
                    </button>

                    <a href="{{ asset('download/product_format.xlsx') }}" download
                        class="inline-flex h-11 items-center justify-center rounded-lg border border-blue-200 bg-white px-5 py-2.5 text-sm font-semibold text-blue-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-900/30 dark:focus:ring-blue-900">
                        <i class="fa-solid fa-file-excel mr-2"></i>
                        Sample
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Records</p>
                    <p id="total-count" class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                </div>
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    <i class="fa-solid fa-list-check text-lg"></i>
                </span>
            </div>
        </div>

        <div class="rounded-xl border border-green-200 bg-white p-5 shadow-sm dark:border-green-900 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Success Records</p>
                    <p id="success-count" class="mt-2 text-3xl font-bold text-green-700 dark:text-green-300">0</p>
                </div>
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </span>
            </div>
        </div>

        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm dark:border-red-900 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed Records</p>
                    <p id="fail-count" class="mt-2 text-3xl font-bold text-red-700 dark:text-red-300">0</p>
                </div>
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Failed Record List</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    The following records contained errors and could not be imported. Please review and fix them.
                </p>
            </div>

            @if ($failedRecords->isEmpty())
                <!-- <span class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    Sample Preview
                </span> -->
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Row</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Product</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Workflow</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Error Message</th>
                    </tr>
                </thead>
                <tbody id="failed-records-lists-tbody" class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    {{-- 
                    @forelse ($previewFailedRecords as $record)
                        <tr class="{{ $failedRecords->isEmpty() ? 'opacity-70' : '' }}">
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                #{{ $record['row'] ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record['product_name'] ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record['product_code'] ?? '-' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $record['workflow'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <ul class="space-y-1">
                                    @foreach (collect($record['errors'] ?? [])->flatten() as $message)
                                        <li class="flex items-start gap-2 text-sm text-red-700 dark:text-red-300">
                                            <i class="fa-solid fa-circle-xmark mt-0.5 text-xs"></i>
                                            <span>{{ $message }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No failed records yet.
                            </td>
                        </tr>
                    @endforelse
                    --}}

                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No failed records yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#product-import-file').on('change', function () {
        const fileName = this.files.length ? this.files[0].name : 'Choose Excel file to import';
        $('#selected-file-name').text(fileName);
    });

    $('#import-product-button').on('click', function () {
        const fileInput = $('#product-import-file');
        if (!fileInput.val()) {
            Swal.fire({
                icon: 'error',
                title: 'No file selected',
                text: 'Please select an Excel file to import.',
            });
            return;
        }
        
        Swal.fire({
            icon: "question",
            title: "Are you sure to import product?",
            // text: ``,
            showCancelButton: true,
        }).then((result) => {
            if(result.isConfirmed)
            {
                isSubmitting = true;                            
                $(".fullloader").removeClass("hidden");

                $('#import-product-button').prop('disabled', true);
                $('#import-product-button-label').text('Saving...');

                // console.log('submit');
                const form = document.getElementById('product-import-form');
                const formData = new FormData(form);

                Swal.fire({
                    title: "Processing...",
                    text: "Please wait while we import the Product Excel file.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: form.action,
                    type:"POST",
                    dataType: "json",
                    data:formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: 'application/json'
                    },
                    success:async function(response){
                        console.log(response);

                        const data = response;

                        if(data.success){
                            Swal.fire({
                                icon: "success",
                                title: "Products imported successfully!",
                                text: data.message,
                            });

                            $('#total-count').text(data.import_dashboard.total);
                            $('#success-count').text(data.import_dashboard.success);
                            $('#fail-count').text(data.import_dashboard.fail);
                            
                            
                            let html = '';
                            let failedRecords = data.failed_records;
                            $.each(failedRecords,function(idx,failedRecord){
                                html += `
                                <tr>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                            #${failedRecord.row ?? '-'}
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">${failedRecord.product_name ?? '-'}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">${failedRecord.product_code ?? '-'}</p>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                ${failedRecord.workflow ?? '-'}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <ul class="space-y-1">
                                                ${failedRecord.errors.map(message => `
                                                    <li class="flex items-start gap-2 text-sm text-red-700 dark:text-red-300">
                                                        <i class="fa-solid fa-circle-xmark mt-0.5 text-xs"></i>
                                                        <span>${message}</span>
                                                    </li>
                                                `).join('')}
                                            </ul>
                                        </td>
                                    </tr>
                                `;

                            })
                            $('#failed-records-lists-tbody').html(html);
                            
                        }else{
                            Swal.fire({
                                icon: "error",
                                title: "Product Import Error!!",
                                text: `${data.message}`,
                            });
                        }
                    },
                    error:function(response){
                        console.log("Error: ", response);
                    },
                    complete: function() {
                        isSubmitting = false;
                        $(".fullloader").addClass("hidden");
                        $('#import-product-button').prop('disabled', false);
                        $('#import-product-button-label').text('Import');
                    }
                
                });

            }
        })

        
    });
</script>
@endsection
