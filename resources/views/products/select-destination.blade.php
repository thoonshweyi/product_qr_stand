@extends('layouts.dashboard')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-gray-50 px-4 py-8 dark:bg-gray-900 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl">
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <a href="{{ route('products.index') }}" class="font-medium hover:text-primary-600 dark:hover:text-primary-400">Products</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
            <span class="text-gray-700 dark:text-gray-200">Choose destination</span>
        </nav>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-6 py-7 text-center dark:border-gray-700 sm:px-10">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Where will this product be used?</h1>
                <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-gray-500 dark:text-gray-400">Select at least one destination. You can choose both Stand and Online for the same product.</p>
            </div>

            <form id="destination-form" action="{{ route('products.create.destination') }}" method="POST" class="p-6 sm:p-10">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="destination-card group relative cursor-pointer rounded-2xl border-2 border-gray-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md dark:border-gray-600 dark:bg-gray-800 dark:hover:border-amber-600">
                        <input type="checkbox" name="destinations[]" value="stand" class="destination-checkbox peer sr-only" @checked(in_array('stand', old('destinations', [])))>
                        <span class="absolute right-5 top-5 flex h-6 w-6 items-center justify-center rounded-md border-2 border-gray-300 bg-white text-transparent transition peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-gray-500 dark:bg-gray-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <span class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 21h16M5 21V10h14v11M3 10l2-6h14l2 6M8 14h3v7m4-7h1"/></svg>
                        </span>
                        <span class="block text-lg font-bold text-gray-900 dark:text-white">Stand</span>
                        <span class="mt-2 block text-sm leading-6 text-gray-500 dark:text-gray-400">Create this product for an in-store product stand.</span>
                    </label>

                    <label class="destination-card group relative cursor-pointer rounded-2xl border-2 border-gray-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md dark:border-gray-600 dark:bg-gray-800 dark:hover:border-sky-600">
                        <input type="checkbox" name="destinations[]" value="online" class="destination-checkbox peer sr-only" @checked(in_array('online', old('destinations', [])))>
                        <span class="absolute right-5 top-5 flex h-6 w-6 items-center justify-center rounded-md border-2 border-gray-300 bg-white text-transparent transition peer-checked:border-sky-500 peer-checked:bg-sky-500 peer-checked:text-white dark:border-gray-500 dark:bg-gray-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <span class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9 9 0 1 0 0-18m0 18c2 0 3.5-4 3.5-9S14 3 12 3m0 18c-2 0-3.5-4-3.5-9S10 3 12 3M3 12h18"/></svg>
                        </span>
                        <span class="block text-lg font-bold text-gray-900 dark:text-white">Online</span>
                        <span class="mt-2 block text-sm leading-6 text-gray-500 dark:text-gray-400">Create this product for online publishing and marketing.</span>
                    </label>
                </div>

                @error('destinations')
                    <p class="mt-4 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('products.index') }}" class="inline-flex justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</a>
                    <button id="continue-button" type="submit" disabled class="inline-flex items-center justify-center rounded-lg bg-primary-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500 disabled:shadow-none dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 dark:disabled:bg-gray-700 dark:disabled:text-gray-400">
                        Continue to product details
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = [...document.querySelectorAll('.destination-checkbox')];
        const continueButton = document.getElementById('continue-button');

        const updateSelection = () => {
            checkboxes.forEach((checkbox) => {
                const card = checkbox.closest('.destination-card');
                const selected = checkbox.checked;
                card.classList.toggle('border-amber-500', selected && checkbox.value === 'stand');
                card.classList.toggle('bg-amber-50', selected && checkbox.value === 'stand');
                card.classList.toggle('dark:bg-amber-900/10', selected && checkbox.value === 'stand');
                card.classList.toggle('border-sky-500', selected && checkbox.value === 'online');
                card.classList.toggle('bg-sky-50', selected && checkbox.value === 'online');
                card.classList.toggle('dark:bg-sky-900/10', selected && checkbox.value === 'online');
                card.classList.toggle('border-gray-200', !selected);
                card.classList.toggle('dark:border-gray-600', !selected);
            });

            continueButton.disabled = !checkboxes.some((checkbox) => checkbox.checked);
        };

        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));
        updateSelection();
    });
</script>
@endsection
