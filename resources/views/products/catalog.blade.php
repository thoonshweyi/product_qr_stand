@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-white">
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-slate-200 bg-white/95 text-slate-800 shadow-sm backdrop-blur" aria-label="Main navigation">
        <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between gap-6">
                <a href="{{ route('products.catalog') }}" class="flex flex-none items-center" aria-label="PRO 1 Global Product Catalog">
                    <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}" class="h-12 w-auto object-contain" alt="PRO 1 Global Home Center">
                </a>

                <button type="button" id="catalog-menu-button" class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-slate-200 text-[#073b78] hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[#073b78] lg:hidden" aria-controls="catalog-menu" aria-expanded="false">
                    <span class="sr-only">Open navigation menu</span>
                    <svg id="catalog-menu-open-icon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="catalog-menu-close-icon" class="hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="hidden min-w-0 items-center justify-end lg:flex" id="catalog-desktop-menu">
                    <ul class="flex items-center gap-1 text-xs font-bold uppercase tracking-wide xl:gap-2 xl:text-sm">
                        <li><a href="https://pro1globalhomecenter.com/home.html?divisionId=1&townshipId=18" class="block border-b-2 border-[#0a4b91] px-2.5 py-7 text-[#0a4b91] xl:px-3">Home</a></li>
                        <li><a href="https://pro1globalhomecenter.com/promotion-list.html?divisionId=1&townshipId=18" class="block border-b-2 border-transparent px-2.5 py-7 transition hover:border-[#0a4b91] hover:text-[#0a4b91] xl:px-3">Promotions</a></li>
                        <li><a href="https://pro1globalhomecenter.com/our-services-list.html?divisionId=1&townshipId=18" class="block border-b-2 border-transparent px-2.5 py-7 transition hover:border-[#0a4b91] hover:text-[#0a4b91] xl:px-3">Our Services</a></li>
                        <li><a href="https://pro1globalhomecenter.com/our-center-list.html?divisionId=1&townshipId=18" class="block border-b-2 border-transparent px-2.5 py-7 transition hover:border-[#0a4b91] hover:text-[#0a4b91] xl:px-3">Our Center</a></li>
                        <li><a href="https://pro1globalhomecenter.com/home-tips-list.html?divisionId=1&townshipId=18" class="block border-b-2 border-transparent px-2.5 py-7 transition hover:border-[#0a4b91] hover:text-[#0a4b91] xl:px-3">Tips and Knowledge</a></li>
                        <li><a href="https://pro1globalhomecenter.com/contact-us-detail.html?divisionId=1&townshipId=18" class="block border-b-2 border-transparent px-2.5 py-7 transition hover:border-[#0a4b91] hover:text-[#0a4b91] xl:px-3">Contact Us</a></li>
                        <li><a href="https://pro1globalhomecenter.com/product-list.html?divisionId=1&townshipId=18&sortBy=1&searchText=#" class="block border-b-2 border-transparent px-2.5 py-7 transition hover:border-[#0a4b91] hover:text-[#0a4b91] xl:px-3">About Us</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="catalog-menu" class="mx-auto hidden max-w-screen-2xl border-t border-slate-200 bg-white px-4 pb-4 text-slate-800 sm:px-6 lg:hidden lg:px-8">
            <ul class="space-y-1 pt-3 text-sm font-bold uppercase tracking-wide">
                <li><a href="https://pro1globalhomecenter.com/home.html?divisionId=1&townshipId=18" class="block rounded-lg bg-blue-50 px-3 py-2.5 text-[#0a4b91]">Home</a></li>
                <li><a href="https://pro1globalhomecenter.com/promotion-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Promotions</a></li>
                <li><a href="https://pro1globalhomecenter.com/our-services-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Our Services</a></li>
                <li><a href="https://pro1globalhomecenter.com/our-center-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Our Center</a></li>
                <li><a href="https://pro1globalhomecenter.com/home-tips-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Tips and Knowledge</a></li>
                <li><a href="https://pro1globalhomecenter.com/contact-us-detail.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Contact Us</a></li>
                <li><a href="https://pro1globalhomecenter.com/product-list.html?divisionId=1&townshipId=18&sortBy=1&searchText=#" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">About Us</a></li>
            </ul>
        </div>
    </nav>

    <section class="relative mt-20 flex min-h-[310px] items-center bg-cover bg-center px-4 py-14 text-white" style="background-image: url('{{ asset('assets/img/banners/livingroom1.jpg') }}');">
        <div class="absolute inset-0 bg-slate-950/65"></div>
        <div class="relative mx-auto w-full max-w-5xl text-center">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-blue-200">PRO 1 Global</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Explore our products</h1>
            <p class="mt-2 text-slate-200">Browse product information, specifications and usage details.</p>

        <form action="{{ route('products.catalog') }}" method="GET" class="mx-auto mt-7 max-w-3xl text-left">
            <label for="catalog-product-search" class="sr-only">Search products</label>
            <div class="flex overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10 focus-within:ring-4 focus-within:ring-blue-400/40">
                <span class="flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                </span>
                <input id="catalog-product-search" name="q" value="{{ $search }}" type="search" class="min-w-0 flex-1 border-0 bg-transparent px-3 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0" placeholder="Search by product name, code, brand or model...">
                @if ($search !== '')
                    <a href="{{ route('products.catalog') }}" class="flex items-center px-3 text-sm font-medium text-slate-500 hover:text-slate-900">Clear</a>
                @endif
                <button type="submit" class="bg-[#073b78] px-5 text-sm font-semibold text-white hover:bg-[#052e5e] sm:px-7">Search</button>
            </div>
        </form>
        </div>
    </section>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($products as $product)
                <a href="{{ route('products.show', $product) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-700 dark:bg-slate-800">
                    <div class="aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">
                        <img src="{{ $product->thumbnail || $product->image ? asset($product->thumbnail ?: $product->image) : asset('assets/img/icon/pro1globalicon.png') }}"
                            alt="{{ $product->name }}" class="h-full w-full object-contain p-4 transition duration-300 group-hover:scale-105">
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#0a4b91] dark:text-blue-400">{{ $product->category?->name ?? 'Product' }}</p>
                        <h2 class="mt-2 line-clamp-2 text-lg font-bold">{{ $product->name }}</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $product->brand }} · {{ $product->model }}</p>
                        <div class="mt-4 flex items-center justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">{{ $product->product_code }}</span>
                            <span class="font-semibold text-[#0a4b91] dark:text-blue-400">View details →</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-500 sm:col-span-2 lg:col-span-3 xl:col-span-4 dark:border-slate-700 dark:text-slate-400">
                    {{ $search !== '' ? 'No products match your search.' : 'No products are available yet.' }}
                </div>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="mt-10">{{ $products->links() }}</div>
        @endif
    </main>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#catalog-menu-button').on('click', function () {
            const isOpen = !$('#catalog-menu').hasClass('hidden');
            $('#catalog-menu').toggleClass('hidden', isOpen);
            $('#catalog-menu-open-icon').toggleClass('hidden', !isOpen);
            $('#catalog-menu-close-icon').toggleClass('hidden', isOpen);
            $(this).attr('aria-expanded', String(!isOpen));
        });
    });
</script>
@endsection
