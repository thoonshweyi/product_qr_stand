@extends('layouts.dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endsection

@section('content')
@php
    $currentUser = auth()->user();
    $canUseStandBulkActions = $workflowChannel === 'stand' && $currentUser?->hasRoles(['Editor', 'Checker', 'Viewer','Administrator']);
    $canUseOnlineBulkActions = $workflowChannel === 'online' && $currentUser?->hasRoles(['Ecommerce Admin','Administrator']);
    $canSeeBulkActionColumn = $canUseStandBulkActions || $canUseOnlineBulkActions;
    $canSeeCurrentBranchPrintStatus = $workflowChannel === 'stand' && $currentUser?->hasRoles(['Viewer']);
    $canSeeAllBranchPrintStatus = $workflowChannel === 'stand' && $currentUser?->hasRoles(['Administrator', 'Editor', 'Checker']);
    $canSeeProductStatusColumn = $currentUser?->hasRoles(['Administrator', 'Checker']);
    $productTableColumnCount = 7
        + ($canSeeBulkActionColumn ? 1 : 0)
        + ($canSeeCurrentBranchPrintStatus ? 1 : 0)
        + ($canSeeAllBranchPrintStatus ? 1 : 0)
        + ($workflowChannel === 'online' ? 1 : 0)
        + ($canSeeProductStatusColumn ? 1 : 0);
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
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500">Products</span>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    {{ $productListTitle }}
                </h1>
                @if ($workflowChannel)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-md shadow-blue-200/70 dark:shadow-none">
                        @if ($workflowChannel === 'stand')
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h8v8H3V3Zm2 2v4h4V5H5Zm8-2h8v8h-8V3Zm2 2v4h4V5h-4ZM3 13h8v8H3v-8Zm2 2v4h4v-4H5Zm8-2h3v3h-3v-3Zm5 0h3v5h-2v3h-3v-3h2v-5Zm-5 5h3v3h-3v-3Z"/></svg>
                        @else
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h2l2.4 10.1a2 2 0 0 0 2 1.5h7.8a2 2 0 0 0 1.9-1.4L21 8H7m3 11.5h.01m7 0h.01"/></svg>
                        @endif
                        {{ ucfirst($workflowChannel) }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                        <span class="inline-flex items-center gap-1" aria-label="QR and online shopping">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h8v8H3V3Zm2 2v4h4V5H5Zm8-2h8v8h-8V3Zm2 2v4h4V5h-4ZM3 13h8v8H3v-8Zm2 2v4h4v-4H5Zm8-2h3v3h-3v-3Zm5 0h3v5h-2v3h-3v-3h2v-5Zm-5 5h3v3h-3v-3Z"/></svg>
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h2l2.4 10.1a2 2 0 0 0 2 1.5h7.8a2 2 0 0 0 1.9-1.4L21 8H7m3 11.5h.01m7 0h.01"/></svg>
                        </span>
                        Stand And Online
                    </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if ($workflowChannel === 'stand')
                    Products assigned to workflows that include Stand.
                @elseif ($workflowChannel === 'online')
                    Products assigned to workflows that include Online.
                @else
                    Search products and manage their information and availability.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($canUseOnlineBulkActions)
                <button type="button" href="{{ route('products.online.export', request()->only(['keyword', 'status_id', 'brand', 'stage', 'online_month','category_id'])) }}" id="export-btn" class="inline-flex w-fit items-center justify-center rounded-lg border border-blue-200 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-900/30 dark:focus:ring-blue-900">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>
                    Export products
                </button>

                <button type="submit" form="product-batch-action-form" id="batch-finish-button" disabled
                    class="inline-flex w-fit items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500 dark:disabled:bg-gray-700 dark:disabled:text-gray-400">
                    <i class="fas fa-circle-check mr-2"></i>
                    Finish selected
                    <span id="selected-finish-count" class="ml-1">(0)</span>
                </button>
            @endif
            @if($canUseStandBulkActions)
            <button type="submit" form="product-batch-action-form" id="batch-print-button" disabled
                class="inline-flex w-fit items-center justify-center rounded-lg border border-primary-700 bg-white px-4 py-2.5 text-sm font-medium text-primary-700 transition hover:bg-primary-50 disabled:cursor-not-allowed disabled:border-gray-300 disabled:text-gray-400 disabled:hover:bg-white">
                <i class="fas fa-print mr-2"></i>
                Print selected
                <span id="selected-product-count" class="ml-1">(0)</span>
            </button>
            @endif

        @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}" class="inline-flex w-fit items-center justify-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-800 dark:bg-primary-600 dark:hover:bg-primary-700">
                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 5a1 1 0 0 1 1 1v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H6a1 1 0 1 1 0-2h3V6a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
                Add product
            </a>
        @endcan
        </div>
    </div>
</div>

@if (session('success'))
    <div class="border-b border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 dark:border-green-900 dark:bg-green-900/20 dark:text-green-300">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white dark:bg-gray-800">
    <div class="border-b border-gray-200 p-4 dark:border-gray-700">
        <form id="search_form" action="{{ $workflowChannel ? route('products.workflow.index', $workflowChannel) : route('products.index') }}" method="GET" class="flex w-full flex-wrap items-end gap-3 2xl:flex-nowrap">
            <div class="w-full min-w-64 flex-1">
                <label for="product-keyword" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Product code or name</label>
                <input type="search" name="keyword" id="product-keyword" value="{{ request('keyword') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter code or name">
            </div>

            <!-- <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                <label for="product-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="status_id" id="product-status"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) request('status_id') === (string) $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div> -->

            <div class="min-w-40 flex-1">
                <label for="product-brand" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Brand</label>
                <select name="brand" id="product-brand"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-40 flex-1">
                <label for="product-stage-filter" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Stage</label>
                <select name="stage" id="product-stage-filter"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All stages</option>
                    <option value="ongoing" @selected(request('stage') === 'ongoing')>Ongoing</option>
                    <option value="checked" @selected(request('stage') === 'checked')>Checked</option>
                    <option value="exported" @selected(request('stage') === 'exported')>Exported</option>
                    <option value="finished" @selected(request('stage') === 'finished')>Finished</option>
                </select>
            </div>

            @if ($workflowChannel === 'online')
                <div class="min-w-44 flex-1">
                    <label for="online_month" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Online month</label>
                    <input type="text" name="online_month" id="online_month" value="{{ request('online_month') }}"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="Choose month" autocomplete="off">
                </div>
            @endif

            <div class="min-w-44 flex-1">
                <label for="product-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Main Category</label>
                <select name="category_id" id="product-status"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-40 flex-1">
                <label for="created_date" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Date</label>
                <input type="date" name="created_date" id="created_date" value="{{ request('created_date') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex shrink-0 gap-2">
                <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800">Search</button>
                @if (request()->filled('keyword') || request()->filled('status_id') || request()->filled('brand') || request()->filled('stage') || request()->filled('online_month') || request()->filled('category_id') || request()->filled('created_date'))
                    <a href="{{ $workflowChannel ? route('products.workflow.index', $workflowChannel) : route('products.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <form id="product-batch-action-form" action="{{ $workflowChannel === 'online' ? route('products.workflow.online.finish') : route('products.batch-print') }}" method="POST" @if($workflowChannel !== 'online') target="_blank" @endif>
        @csrf
    <div class="freeze-table-header" style="--freeze-table-max-height: calc(100vh - 18rem);">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    @if($canSeeBulkActionColumn)
                    <th class="w-12 p-4 text-left">
                        <input type="checkbox" id="select-all-products"
                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500"
                            aria-label="Select all products on this page">
                    </th>
                    @endif
                    <th class="w-0 whitespace-nowrap p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                        Actions
                    </th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">No.</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Product</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Name</th>
                    @if($canSeeCurrentBranchPrintStatus)
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                        Print status
                        @if ($currentBranchId)
                            <span class="mt-0.5 block normal-case text-[10px] font-normal text-gray-400">{{ $currentBranch->branch_short_name ?: $currentBranch->branch_name }}</span>
                        @endif
                    </th>
                    @endif
                    @if($canSeeAllBranchPrintStatus)
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                        All Branch Print Status
                    </th>
                    @endif
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Category</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Brand</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Stage</th>
                    @if($workflowChannel === 'online')
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Exported At</th>
                    @endif
                    @if($canSeeProductStatusColumn)
                        <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                            Status
                        </th>
                    @endif
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Created by</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                @forelse ($products as $index => $product)
                    <tr id="product-row-{{ $product->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/60">
                        @php
                            $isProductReadyForBulkAction = match ($workflowChannel) {
                                'stand' => in_array($product?->stage, ['checked', 'finished'], true),
                                'online' => $product?->stage === 'exported',
                                default => false,
                            };
                        @endphp

                        @if($canSeeBulkActionColumn)
                        @if($isProductReadyForBulkAction)
                        <td class="w-12 p-4">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                class="product-action-checkbox h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500"
                                aria-label="Select {{ $product->name }}">
                        </td>
                        @else
                        <td class="w-12 p-4 text-center">
                            <span
                                class="inline-flex h-4 w-4 cursor-not-allowed items-center justify-center text-gray-400"
                                title="This product is not ready for bulk action"
                            >
                                <i class="fa-solid fa-ban"></i>
                            </span>
                        </td>
                        @endif
                        @endif
                             
                        <td class="whitespace-nowrap p-4 text-left">
                            <div class="flex items-center justify-start gap-2">
                                <a href="{{ route('products.print-history', $product) }}"
                                    class="inline-flex items-center rounded-lg bg-amber-500 px-3 py-2 text-sm font-medium text-white hover:bg-amber-600"
                                    aria-label="Print history for {{ $product->name }}"
                                    title="Branch print history">
                                    <i class="fas fa-clock-rotate-left h-4 w-4"></i>
                                </a>

                                <a href="{{ route('products.show', $product) }}" target="_blank"
                                    class="inline-flex items-center rounded-lg bg-green-700 px-3 py-2 text-sm font-medium text-white hover:bg-green-800"
                                    aria-label="View {{ $product->name }}">
                                    <i class="fas fa-eye h-4 w-4"></i>
                                </a>

                                @can('edit', $product)
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="inline-flex items-center rounded-lg bg-primary-700 px-3 py-2 text-sm font-medium text-white hover:bg-primary-800"
                                        aria-label="Edit {{ $product->name }}">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828Z"/><path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z" clip-rule="evenodd"/></svg>
                                    </a>
                                @endcan

                                @can('delete', $product)
                                    <button type="button"
                                        class="delete-product-button inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-delete-url="{{ route('products.destroy', $product) }}"
                                        aria-label="Delete {{ $product->name }}">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9ZM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8Zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Z" clip-rule="evenodd"/></svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $products->firstItem() + $index }}</td>
                        <td class="whitespace-nowrap p-4">
                            <div class="flex items-center gap-3">
                                @if (filled($product->thumbnail) || filled($product->image))
                                    <img src="{{ asset($product->thumbnail ?: $product->image) }}" alt="" class="h-11 w-11 rounded-lg object-cover">
                                @else
                                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
                                        {{ Str::upper(Str::substr($product->name, 0, 2)) }}
                                    </span>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{Str::limit($product->product_name,50)}}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->product_code }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{Str::limit($product->name,30)}}</td>

                        @if($canSeeCurrentBranchPrintStatus)
                        <td class="whitespace-nowrap p-4">
                            @if (! $currentBranchId)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <i class="fas fa-circle-exclamation mr-1.5"></i>
                                    No branch
                                </span>
                            @elseif (! $product->current_branch_last_printed_version)
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    <i class="fas fa-clock mr-1.5"></i>
                                    Not printed
                                </span>
                            @elseif ((int) $product->current_branch_last_printed_version < (int) $product->print_version)
                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-800 dark:bg-orange-900/40 dark:text-orange-300"
                                    title="Printed version {{ $product->current_branch_last_printed_version }}; current version {{ $product->print_version }}">
                                    <i class="fas fa-rotate mr-1.5"></i>
                                    Reprint required
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-300"
                                    title="Printed at {{ $product->current_branch_last_printed_at }}">
                                    <i class="fas fa-circle-check mr-1.5"></i>
                                    Printed · v{{ $product->print_version }}
                                </span>
                            @endif
                        </td>
                        @endif
                        @if($canSeeAllBranchPrintStatus)
                        <td class="whitespace-nowrap p-4">
                            @php
                                $allBranchPrintedCount = (int) ($product->all_branch_printed_count ?? 0);
                            @endphp

                            @if ($activeBranchCount < 1)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <i class="fas fa-circle-exclamation mr-1.5"></i>
                                    No branch
                                </span>
                            @elseif ($allBranchPrintedCount === 0)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300"
                                    title="0 of {{ $activeBranchCount }} active branches printed current version">
                                    <i class="fas fa-circle-minus mr-1.5"></i>
                                    Not Printed
                                </span>
                            @elseif ($allBranchPrintedCount < $activeBranchCount)
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300"
                                    title="{{ $allBranchPrintedCount }} of {{ $activeBranchCount }} active branches printed current version">
                                    <i class="fas fa-circle-half-stroke mr-1.5"></i>
                                    Partial Printed
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-300"
                                    title="All {{ $activeBranchCount }} active branches printed current version">
                                    <i class="fas fa-circle-check mr-1.5"></i>
                                    Fully Printed
                                </span>
                            @endif
                        </td>
                        @endif
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->category->name }}</td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->brand }}</td>
                        <td class="whitespace-nowrap p-4">
                            @php
                                $productStage = strtolower($product->stage ?: 'ongoing');
                                $stageLabel = ucfirst(str_replace(['_', '-'], ' ', $productStage));
                                $stageClasses = match ($productStage) {
                                    'finished', 'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                    'exported' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
                                    'checked' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                    default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                };
                                $stageDotClasses = match ($productStage) {
                                    'finished', 'completed' => 'bg-green-500',
                                    'exported' => 'bg-sky-500',
                                    'checked' => 'bg-yellow-500',
                                    default => 'bg-blue-500',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $stageClasses }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $stageDotClasses }}"></span>
                                {{ $stageLabel }}
                            </span>
                        </td>
                        @if($workflowChannel === 'online')
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">
                            @if ($product->exported_at)
                                <!-- <span class="inline-flex flex-col">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($product->exported_at)->format('Y-m-d') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($product->exported_at)->format('h:i A') }}</span>
                                </span> -->
                                {{ \Carbon\Carbon::parse($product->exported_at)->format('Y-m-d H:i:s') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        @endif

                        @if($canSeeProductStatusColumn)
                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            @can('edit', $product)
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input
                                    type="checkbox"
                                    class="peer sr-only change-btn"
                                    {{ $product->status_id === 1 ? "checked" : "" }}
                                    data-id="{{ $product->id }}"
                                />
                                <div
                                    class="h-5 w-9 rounded-full bg-gray-300 transition-colors
                                        after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4
                                        after:rounded-full after:bg-white after:transition-transform
                                        peer-checked:bg-blue-600 peer-checked:after:translate-x-4">
                                </div>
                            </label>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endcan
                        </td>
                        @endif

                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->user?->name ?? 'Unknown' }}</td>
                   
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $productTableColumnCount }}" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </form>

    @if ($products->hasPages())
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">{{ $products->links() }}</div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
    $(document).ready(function () {
        const onlineMonthInput = document.getElementById('online_month');

        if (onlineMonthInput) {
            flatpickr(onlineMonthInput, {
                allowInput: false,
                altInput: true,
                dateFormat: 'Y-m',
                plugins: [new monthSelectPlugin({
                    shorthand: false,
                    dateFormat: 'Y-m',
                    altFormat: 'F Y',
                })],
            });
        }

        const isOnlineWorkflowList = @js($workflowChannel === 'online');
        const $productCheckboxes = $('.product-action-checkbox');
        const $selectAll = $('#select-all-products');
        const $batchPrintButton = $('#batch-print-button');
        const $batchFinishButton = $('#batch-finish-button');
        const $selectedCount = $('#selected-product-count');
        const $selectedFinishCount = $('#selected-finish-count');

        function updateBatchPrintSelection() {
            const selected = $productCheckboxes.filter(':checked').length;
            $selectedCount.text(`(${selected})`);
            $selectedFinishCount.text(`(${selected})`);
            $batchPrintButton.prop('disabled', selected === 0);
            $batchFinishButton.prop('disabled', selected === 0);
            $selectAll.prop('checked', selected > 0 && selected === $productCheckboxes.length);
            $selectAll.prop('indeterminate', selected > 0 && selected < $productCheckboxes.length);
        }

        $('#product-batch-action-form').on('submit', function (event) {
            if (!isOnlineWorkflowList) return;
            event.preventDefault();

            const selected = $productCheckboxes.filter(':checked').length;
            if (selected === 0) {
                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Finish selected products?',
                text: `Finish ${selected} selected product(s)?`,
                showCancelButton: true,
                confirmButtonText: 'Yes, finish',
                confirmButtonColor: '#15803d',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (!result.isConfirmed) return;

                $batchFinishButton.prop('disabled', true);

                $.ajax({
                    url: this.action,
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    headers: { Accept: 'application/json' }
                }).done(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Finished',
                        text: response.message || 'Selected products were finished successfully.'
                    }).then(() => {
                        window.location.reload();
                    });
                }).fail(xhr => {
                    $batchFinishButton.prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Unable to finish',
                        text: xhr.responseJSON?.message || 'Something went wrong. Please try again.'
                    });
                });
            });
        });

        $selectAll.on('change', function () {
            $productCheckboxes.prop('checked', this.checked);
            updateBatchPrintSelection();
        });

        $productCheckboxes.on('change', updateBatchPrintSelection);

        $('.delete-product-button').on('click', function () {
            const $button = $(this);
            const productId = $button.data('product-id');
            const productName = ($button.data('product-name') || '').substring(0, 50);

            Swal.fire({
                icon: 'warning',
                title: 'Delete product?',
                text: `Are you sure you want to delete '${productName}'? This action cannot be undone.`,
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                confirmButtonColor: '#dc2626',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (!result.isConfirmed) return;

                $button.prop('disabled', true);

                $.ajax({
                    url: $button.data('delete-url'),
                    method: 'POST',
                    data: {
                        _token: @js(csrf_token()),
                        _method: 'DELETE'
                    },
                    dataType: 'json',
                    headers: { Accept: 'application/json' }
                }).done(response => {
                    $(`#product-row-${productId}`).fadeOut(200, function () {
                        $(this).remove();
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Product deleted',
                        text: response.message || 'The product was deleted successfully.'
                    });
                }).fail(xhr => {
                    $button.prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Unable to delete product',
                        text: xhr.responseJSON?.message || 'Something went wrong. Please try again.'
                    });
                });
            });
        });

        //Start change-btn
        $(document).on("change",".change-btn",function(){

            var getid = $(this).data("id");
            // console.log(getid); // 1 2

            var setstatus = $(this).prop("checked") === true ? 1 : 2;
            // console.log(setstatus); // 3 4

            $.ajax({
                    url:"productsstatus",
                    type:"GET",
                    dataType:"json",
                    data:{"id":getid,"status_id":setstatus},
                    success:function(response){
                        console.log(response); // {success: 'Status Change Successfully'}
                        console.log(response.success); // Status Change Successfully
                    
                        Swal.fire({
                            title: "Updated!",
                            text: "Updated Successfully",
                            icon: "success"
                        });
                    }
            });
        });
        // End change btn


        // Start export btn
        $('#export-btn').click(function(){
            console.log($('#search_form').serialize())
            Swal.fire({
                title: "Processing...",
                text: "Please wait while we generate the Online Product Excel file.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('products.workflow.index','online') }}",
                type: "GET",
                // dataType:"json",
                data: $('#search_form').serialize() + '&document_search=Export',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (blob, status, xhr) {
                   
                    let filename = "online_products.xlsx";
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.includes('filename=')) {
                        filename = disposition.split('filename=')[1].replace(/"/g, '');
                    }

                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    Swal.fire({
                        icon: "success",
                        title: "Exported!",
                        text: "Workflow actions were recorded. Please click 'Finish' once you have uploaded it online.",
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error:function(response){
                    console.log("Error:",response);
                    Swal.close(); // Close the modal

                    console.log(response.responseJSON.message);
                    if(response.responseJSON.message == "Maximum execution time of 60 seconds exceeded"){
                        Swal.fire({
                            icon: "error",
                            title: "Oops.... The Excel export took too long and was stopped.",
                            text: "Please Try Again",
                            {{-- footer: '<a href="#">Why do I have this issue?</a>' --}}
                        });
                    }else{
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Something went wrong!",
                            footer: '<a href="#">Why do I have this issue?</a>'
                          });
                    }
                },
                complete: function(){
                }
            });
        });
        // End export btn
    });
</script>
@endsection
