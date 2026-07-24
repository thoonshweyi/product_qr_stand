@extends('layouts.dashboard')

@section('content')
<div class="border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 lg:mt-1.5">
    <nav class="mb-5 flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li>
                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-primary-600 dark:text-gray-300">Products</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z" clip-rule="evenodd"/></svg>
                <span class="ml-1 text-gray-400 md:ml-2">Print history</span>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-primary-700">{{ $product->product_code }}</p>
            <h1 class="mt-1 text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $product->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Branch printing status and the complete user activity history.</p>
        </div>
        <a href="{{ route('products.index') }}"
            class="inline-flex w-fit items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to products
        </a>
    </div>
</div>

<div class="space-y-6 p-4">
    <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 p-4 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Branch status</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A branch is marked as printed after any user from that branch prints this product.</p>
        </div>

        <div class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($branches as $branch)
                @php($summary = $summaryByBranch->get($branch->id))
                <article @class([
                    'rounded-xl border p-4',
                    'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' => $summary,
                    'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/30' => ! $summary,
                ])>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $branch->branch_name }}</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $branch->branch_code ?: $branch->branch_short_name }}</p>
                        </div>
                        <span @class([
                            'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $summary,
                            'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => ! $summary,
                        ])>
                            {{ $summary ? 'Printed' : 'Not printed' }}
                        </span>
                    </div>

                    @if ($summary)
                        <p class="mt-4 text-xs text-gray-600 dark:text-gray-300">
                            {{ $summary->print_count }} {{ Str::plural('print', $summary->print_count) }}
                            · Last {{ \Illuminate\Support\Carbon::parse($summary->last_printed_at)->diffForHumans() }}
                        </p>
                    @else
                        <p class="mt-4 text-xs text-gray-400">No print record yet</p>
                    @endif
                </article>
            @empty
                <p class="col-span-full py-6 text-center text-sm text-gray-500">No branches found.</p>
            @endforelse
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 p-4 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Print activity</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Who printed this product, from which branch, and when.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-xs font-semibold uppercase text-gray-500">No.</th>
                        <th class="p-4 text-left text-xs font-semibold uppercase text-gray-500">Branch</th>
                        <th class="p-4 text-left text-xs font-semibold uppercase text-gray-500">Printed by</th>
                        <th class="p-4 text-left text-xs font-semibold uppercase text-gray-500">Printed at</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($printRecords as $index => $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap p-4 text-sm text-gray-500">{{ $printRecords->firstItem() + $index }}</td>
                            <td class="whitespace-nowrap p-4">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record->branch?->branch_name ?? 'Unassigned branch' }}</p>
                                <p class="text-xs text-gray-500">{{ $record->branch?->branch_code }}</p>
                            </td>
                            <td class="whitespace-nowrap p-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $record->user?->name ?? 'Unknown user' }}</p>
                                <p class="text-xs text-gray-500">{{ $record->user?->employee_id }}</p>
                            </td>
                            <td class="whitespace-nowrap p-4 text-sm text-gray-600 dark:text-gray-300">
                                <p>{{ $record->printed_at?->format('d M Y, h:i A') ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $record->printed_at?->diffForHumans() }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-sm text-gray-500">This product has not been printed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($printRecords->hasPages())
            <div class="border-t border-gray-200 p-4 dark:border-gray-700">{{ $printRecords->links() }}</div>
        @endif
    </section>
</div>
@endsection
