<nav class="no-print fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-gradient-to-b from-black/40 to-[#073b78]/90 text-white shadow-lg backdrop-blur-sm" aria-label="Main navigation">
    <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between gap-6">
            <a href="{{ route('products.catalog') }}" class="flex flex-none items-center" aria-label="PRO 1 Global Product Catalog">
                <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}" class="h-12 w-auto object-contain" alt="PRO 1 Global Home Center">
            </a>

            <button type="button" id="catalog-menu-button" class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-white/30 text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white lg:hidden" aria-controls="catalog-menu" aria-expanded="false">
                <span class="sr-only">Open navigation menu</span>
                <svg id="catalog-menu-open-icon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="catalog-menu-close-icon" class="hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="hidden min-w-0 items-center justify-end lg:flex" id="catalog-desktop-menu">
                <ul class="flex items-center gap-1 text-xs font-bold uppercase tracking-wide xl:gap-2 xl:text-sm">
                    <li><a href="https://pro1globalhomecenter.com/home.html?divisionId=1&townshipId=18" class="block border-b-2 border-white px-2.5 py-7 text-white xl:px-3">Home</a></li>
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

    <div id="catalog-menu" class="mx-auto hidden max-w-screen-2xl border-t border-white/10 bg-[#073b78]/95 px-4 pb-4 text-white backdrop-blur-md sm:px-6 lg:hidden lg:px-8">
        <ul class="space-y-1 pt-3 text-sm font-bold uppercase tracking-wide">
            <li><a href="https://pro1globalhomecenter.com/home.html?divisionId=1&townshipId=18" class="block rounded-lg bg-white/15 px-3 py-2.5 text-white">Home</a></li>
            <li><a href="https://pro1globalhomecenter.com/promotion-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Promotions</a></li>
            <li><a href="https://pro1globalhomecenter.com/our-services-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Our Services</a></li>
            <li><a href="https://pro1globalhomecenter.com/our-center-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Our Center</a></li>
            <li><a href="https://pro1globalhomecenter.com/home-tips-list.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Tips and Knowledge</a></li>
            <li><a href="https://pro1globalhomecenter.com/contact-us-detail.html?divisionId=1&townshipId=18" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">Contact Us</a></li>
            <li><a href="https://pro1globalhomecenter.com/product-list.html?divisionId=1&townshipId=18&sortBy=1&searchText=#" class="block rounded-lg px-3 py-2.5 hover:bg-slate-100 hover:text-[#0a4b91]">About Us</a></li>
        </ul>
    </div>
</nav>