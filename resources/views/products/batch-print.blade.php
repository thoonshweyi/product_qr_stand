@extends('layouts.dashboard')

@php
    $isProductPreview = isset($previewProduct);
@endphp

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/dist/css/product-print.css') }}">
@endsection

@section('content')
    <div class="no-print border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 lg:mt-1.5">
        <nav class="mb-5 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2 text-sm font-medium">
                <li><a href="{{ route('dashboards.index') }}" class="text-gray-600 hover:text-primary-700 dark:text-gray-300"><i class="fas fa-home mr-2"></i>Home</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
                <li><a href="{{ route('products.index') }}" class="text-gray-600 hover:text-primary-700 dark:text-gray-300">Products</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
                <li class="text-gray-400">{{ $isProductPreview ? 'Print preview' : 'Batch print' }}</li>
            </ol>
        </nav>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $isProductPreview ? 'Product print preview' : 'Batch product print' }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $isProductPreview
                    ? 'Preview the saved details for this product.'
                    : 'Review the print information before opening the browser print preview.' }}</p>
            </div>
            <a href="{{ $isProductPreview ? route('products.edit', $previewProduct) : route('products.index') }}"
                class="inline-flex w-fit items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ $isProductPreview ? 'Back to edit' : 'Back to list' }}
            </a>
        </div>
    </div>

    <section class="batch-print-layout">
        <aside class="batch-print-information no-print overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
                        <i class="fas fa-file-lines"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Print information</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Review your print setup</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-px bg-gray-200 dark:bg-gray-700">
                <div class="bg-white p-4 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Products</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $products->count() }}</p>
                </div>
                <div class="bg-white p-4 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">A4 pages</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ (int) ceil($products->count() / 2) }}</p>
                </div>
                <div class="bg-white p-4 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Paper</p>
                    <p class="mt-1 text-sm font-bold text-gray-900 dark:text-white">A4 Landscape</p>
                </div>
                <div class="bg-white p-4 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Sheets/page</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">2</p>
                </div>
            </div>

            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                <p class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ $isProductPreview ? 'Product' : 'Selected products' }}</p>
                <div class="max-h-64 space-y-2 overflow-y-auto pr-1">
                    @foreach ($products as $product)
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-2.5 dark:bg-gray-700/70">
                            <span class="flex h-8 w-8 flex-none items-center justify-center rounded-md bg-white text-xs font-bold text-primary-700 shadow-sm dark:bg-gray-800 dark:text-primary-300">{{ $loop->iteration }}</span>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">{{ $product->product_code }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                <button type="button" onclick="window.print()"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-800">
                    <i class="fas fa-print mr-2"></i>
                    Open print preview
                </button>
            </div>
        </aside>

        <div class="batch-print-preview">
            <main class="batch-print-workspace print-pages">
                @foreach ($products->chunk(2) as $pageProducts)
                    <section class="print-page {{ $pageProducts->count() === 1 ? 'single' : '' }}">
                        @foreach ($pageProducts as $product)
                            @include('products.partials.print-sheet', ['product' => $product])
                        @endforeach
                    </section>
                @endforeach
            </main>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        let batchPrintRecorded = false;

        const originalDescriptions = new WeakMap();
        const descriptionSegmenter = new Intl.Segmenter('my', { granularity: 'grapheme' });

        window.addEventListener('beforeprint', function () {
            if (batchPrintRecorded) return;

            batchPrintRecorded = true;

            const formData = new FormData();
            formData.append('_token', @js(csrf_token()));

            @foreach ($products as $product)
                formData.append('product_ids[]', @js($product->id));
            @endforeach

            const queued = navigator.sendBeacon(
                @js(route('products.batch-print-records.store')),
                formData
            );

            if (!queued) {
                batchPrintRecorded = false;
            }
        });
    </script>
@endsection
