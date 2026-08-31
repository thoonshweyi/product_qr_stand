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
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stand Only Products</p>
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
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Online Only Products</p>
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Branch Print Progress Report</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Latest 5 stand products. Open detail to review all products.
                    </p>
                </div>
                <a href="{{ route('dashboards.branch-print-report') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                    View Detail
                    <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="min-w-80 p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Product</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Version</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Printed Count</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">All Branch Print Status</th>
                        <th class="min-w-[32rem] p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Printed Branches</th>
                        <th class="whitespace-nowrap p-4 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse ($printReportProducts as $product)
                        @php
                            $latestPrintRecords = $product->printRecords
                                ->sortByDesc('printed_at')
                                ->unique('branch_id')
                                ->keyBy('branch_id');
                            $currentVersionPrintedRecords = $latestPrintRecords
                                ->filter(fn ($record) => (int) $record->product_version === (int) $product->print_version);
                            $currentPrintedCount = $currentVersionPrintedRecords->count();
                            $previewPrintedRecords = $currentVersionPrintedRecords->take(5);
                            $morePrintedCount = max($currentPrintedCount - $previewPrintedRecords->count(), 0);
                            $allBranchPrintedCount = (int) ($product->all_branch_printed_count ?? 0);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="p-4">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900 dark:text-white">{{ $product->product_name ?: $product->name }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $product->product_code }} · {{ $product->brand }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap p-4 text-gray-600 dark:text-gray-300">v{{ $product->print_version }}</td>
                            <td class="whitespace-nowrap p-4">
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $currentPrintedCount }} Branch{{ $currentPrintedCount === 1 ? '' : 'es' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap p-4">
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
                            <td class="p-4">
                                <div class="flex max-w-4xl flex-wrap gap-2">
                                    @if ($activeBranchCount > 0 && $allBranchPrintedCount >= $activeBranchCount)
                                        <span class="inline-flex items-center rounded-md border border-green-300 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300" title="All {{ $activeBranchCount }} active branches printed current version">
                                            <i class="fa-solid fa-circle-check mr-1.5"></i>
                                            All branches printed
                                        </span>
                                    @else
                                        @forelse ($previewPrintedRecords as $printRecord)
                                            <span class="inline-flex items-center rounded-md border border-green-300 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300" title="Printed at {{ $printRecord->printed_at?->format('Y-m-d h:i A') }}">
                                                <i class="fa-solid fa-circle-check mr-1.5"></i>
                                                {{ $printRecord->branch?->branch_name ?: $printRecord->branch?->branch_code ?: 'Branch' }}
                                            </span>
                                        @empty
                                            <span class="px-2 py-1.5 text-xs text-gray-400">No branch has printed current version yet.</span>
                                        @endforelse

                                        @if ($morePrintedCount > 0)
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                +{{ $morePrintedCount }} more
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap p-4 text-right">
                                <a href="{{ route('products.print-history', $product) }}" class="inline-flex items-center text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No checked stand products are ready for branch print tracking yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-gray-200 p-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-gray-500 dark:text-gray-400">Showing latest {{ $printReportProducts->count() }} stand products only.</p>
            <a href="{{ route('dashboards.branch-print-report') }}" class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-900/30">
                View Full Report
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
</div>
@endsection
