@extends('layouts.dashboard')

@section('content')
<div class="px-4 pt-6">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="mb-2 flex text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                <a href="{{ route('dashboards.index') }}" class="hover:text-blue-700 dark:hover:text-blue-300">Dashboard</a>
                <span class="mx-2">/</span>
                <span>Branch Print Report</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Branch Print Report</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Review all stand products and every branch that printed the current version.
            </p>
        </div>
        <a href="{{ route('dashboards.index') }}" class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-900/30">
            <i class="fa-solid fa-arrow-left mr-2 text-xs"></i>
            Back to Dashboard
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="min-w-80 p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Product</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Stage</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Version</th>
                        <th class="whitespace-nowrap p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Printed Count</th>
                        <th class="min-w-[40rem] p-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">Printed Branches</th>
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
                                ->filter(fn ($record) => (int) $record->product_version >= (int) $product->print_version);
                            $productStage = ucfirst(str_replace(['_', '-'], ' ', $product->stage ?: 'ongoing'));
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="p-4">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $product->product_name ?: $product->name }}</p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $product->product_code }} · {{ $product->brand }}</p>
                            </td>
                            <td class="whitespace-nowrap p-4">
                                <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $productStage }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap p-4 text-gray-600 dark:text-gray-300">v{{ $product->print_version }}</td>
                            <td class="whitespace-nowrap p-4">
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $currentVersionPrintedRecords->count() }} Branch{{ $currentVersionPrintedRecords->count() === 1 ? '' : 'es' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex max-w-5xl flex-wrap gap-2">
                                    @forelse ($currentVersionPrintedRecords as $printRecord)
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800" title="Printed at {{ $printRecord->printed_at?->format('Y-m-d h:i A') }}">
                                            <i class="fa-solid fa-circle-check mr-1.5"></i>
                                            {{ $printRecord->branch?->branch_name ?: $printRecord->branch?->branch_code ?: 'Branch' }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">No branch has printed current version yet.</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="whitespace-nowrap p-4 text-right">
                                <a href="{{ route('products.print-history', $product) }}" class="inline-flex items-center text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                                    Print History
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

        @if ($printReportProducts->hasPages())
            <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                {{ $printReportProducts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
