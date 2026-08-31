@extends('layouts.dashboard')

@section('content')
<div class="px-4 pt-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Product workflow summary and branch print report.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($productCounts['total']) }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    <i class="fa-solid fa-boxes-stacked text-xl"></i>
                </span>
            </div>
            <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                View all
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stand Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($productCounts['stand']) }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    <i class="fa-solid fa-qrcode text-xl"></i>
                </span>
            </div>
            <a href="{{ route('products.workflow.index', 'stand') }}" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                View stand list
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Online Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($productCounts['online']) }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                </span>
            </div>
            <a href="{{ route('products.workflow.index', 'online') }}" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                View online list
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stand And Online</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($productCounts['stand_and_online']) }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    <span class="inline-flex items-center gap-1">
                        <i class="fa-solid fa-qrcode text-lg"></i>
                        <i class="fa-solid fa-cart-shopping text-lg"></i>
                    </span>
                </span>
            </div>
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Products used for both stand and online workflows.</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Branch Print Matrix</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Quickly check which branches have printed each stand product's current version.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                        <i class="fa-solid fa-circle-check mr-1.5"></i> Printed
                    </span>
                    <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-1 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">
                        <i class="fa-solid fa-rotate mr-1.5"></i> Reprint
                    </span>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <i class="fa-solid fa-minus mr-1.5"></i> Not printed
                    </span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="sticky left-0 z-10 min-w-72 bg-gray-50 p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-300">Product</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Stage</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Version</th>
                        @foreach ($branches as $branch)
                            <th class="whitespace-nowrap p-4 text-center text-xs font-semibold uppercase text-gray-500 dark:text-gray-300" title="{{ $branch->branch_name }}">
                                {{ $branch->branch_short_name ?: $branch->branch_code }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse ($printReportProducts as $product)
                        @php
                            $latestPrintRecords = $product->printRecords
                                ->sortByDesc('printed_at')
                                ->unique('branch_id')
                                ->keyBy('branch_id');
                            $productStage = ucfirst(str_replace(['_', '-'], ' ', $product->stage ?: 'ongoing'));
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="sticky left-0 z-10 bg-white p-4 dark:bg-gray-800">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900 dark:text-white">{{ $product->product_name ?: $product->name }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $product->product_code }} · {{ $product->brand }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap p-4">
                                <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $productStage }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap p-4 text-gray-600 dark:text-gray-300">v{{ $product->print_version }}</td>
                            @foreach ($branches as $branch)
                                @php($printRecord = $latestPrintRecords->get($branch->id))
                                <td class="whitespace-nowrap p-4 text-center">
                                    @if (! $printRecord)
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-300" title="Not printed">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </span>
                                    @elseif ((int) $printRecord->product_version < (int) $product->print_version)
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300" title="Printed old version {{ $printRecord->product_version }}">
                                            <i class="fa-solid fa-rotate text-xs"></i>
                                        </span>
                                    @else
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300" title="Printed at {{ $printRecord->printed_at?->format('Y-m-d h:i A') }}">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + $branches->count() }}" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No checked stand products are ready for branch print tracking yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-gray-200 p-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-gray-500 dark:text-gray-400">Showing latest {{ $printReportProducts->count() }} stand products that are checked or finished.</p>
            <a href="{{ route('products.workflow.index', 'stand') }}" class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-900/30">
                Open Stand Products
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
</div>
@endsection
