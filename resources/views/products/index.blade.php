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
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500">Products</span>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Product management</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search products and manage their information and availability.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="submit" form="product-batch-print-form" id="batch-print-button" disabled
                class="inline-flex w-fit items-center justify-center rounded-lg border border-primary-700 bg-white px-4 py-2.5 text-sm font-medium text-primary-700 transition hover:bg-primary-50 disabled:cursor-not-allowed disabled:border-gray-300 disabled:text-gray-400 disabled:hover:bg-white">
                <i class="fas fa-print mr-2"></i>
                Print selected
                <span id="selected-product-count" class="ml-1">(0)</span>
            </button>

        @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}" class="inline-flex w-fit items-center justify-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-800 dark:bg-primary-600 dark:hover:bg-primary-700">
                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 5a1 1 0 0 1 1 1v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H6a1 1 0 1 1 0-2h3V6a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
                Add product
            </a>
        @endcan
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800">
    <div class="border-b border-gray-200 p-4 dark:border-gray-700">
        <form action="{{ route('products.index') }}" method="GET" class="grid w-full max-w-4xl grid-cols-12 items-end gap-3">
            <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                <label for="product-keyword" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Product code or name</label>
                <input type="search" name="keyword" id="product-keyword" value="{{ request('keyword') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter code or name">
            </div>

            <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                <label for="product-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="status_id" id="product-status"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) request('status_id') === (string) $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                <label for="product-brand" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Brand</label>
                <select name="brand" id="product-brand"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-12 flex gap-2 lg:col-span-4">
                <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800">Search</button>
                @if (request()->filled('keyword') || request()->filled('status_id') || request()->filled('brand'))
                    <a href="{{ route('products.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <form id="product-batch-print-form" action="{{ route('products.batch-print') }}" method="POST" target="_blank">
        @csrf
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="w-12 p-4 text-left">
                        <input type="checkbox" id="select-all-products"
                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500"
                            aria-label="Select all products on this page">
                    </th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">No.</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Product</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Brand</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Model</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Country</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                    <th class="p-4 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Updated by</th>
                    <th class="p-4 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                @forelse ($products as $index => $product)
                    <tr id="product-row-{{ $product->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/60">
                        <td class="w-12 p-4">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                class="product-print-checkbox h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500"
                                aria-label="Select {{ $product->name }} for printing">
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
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->product_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->brand }}</td>
                        <td class="max-w-xs truncate p-4 text-sm text-gray-600 dark:text-gray-300" title="{{ $product->model }}">{{ $product->model }}</td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->country?->name ?? '—' }}</td>
                        <td class="whitespace-nowrap p-4">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' => $product->status_id === 1,
                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $product->status_id !== 1,
                            ])>
                                {{ $product->status?->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->user?->name ?? 'Unknown' }}</td>
                        <td class="whitespace-nowrap p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">No products found.</td>
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
<script>
    $(document).ready(function () {
        const $productCheckboxes = $('.product-print-checkbox');
        const $selectAll = $('#select-all-products');
        const $batchPrintButton = $('#batch-print-button');
        const $selectedCount = $('#selected-product-count');

        function updateBatchPrintSelection() {
            const selected = $productCheckboxes.filter(':checked').length;
            $selectedCount.text(`(${selected})`);
            $batchPrintButton.prop('disabled', selected === 0);
            $selectAll.prop('checked', selected > 0 && selected === $productCheckboxes.length);
            $selectAll.prop('indeterminate', selected > 0 && selected < $productCheckboxes.length);
        }

        $selectAll.on('change', function () {
            $productCheckboxes.prop('checked', this.checked);
            updateBatchPrintSelection();
        });

        $productCheckboxes.on('change', updateBatchPrintSelection);

        $('.delete-product-button').on('click', function () {
            const $button = $(this);
            const productId = $button.data('product-id');
            const productName = $button.data('product-name');

            Swal.fire({
                icon: 'warning',
                title: 'Delete product?',
                text: `Are you sure you want to delete ${productName}? This action cannot be undone.`,
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
    });
</script>
@endsection
