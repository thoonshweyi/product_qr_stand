@extends('layouts.main')

@section('content')
@php
    $specifications = collect([
        'Brand' => $product->brand,
        'Name' => $product->name,
        //'Product Name' => $product->product_name,
        'Model' => $product->model,
        'Code' => $product->product_code,
        'Country of origin' => $product->country?->name,
        //'Category' => $product->category?->name,
        'Sales unit' => $product->unit,
    ])->filter(fn ($value) => filled($value));

    foreach ($product->specificationValues as $specificationValue) {
        if ($specificationValue->specification && filled($specificationValue->value)) {
            $specifications->put($specificationValue->specification->name, $specificationValue->value);
        }
    }

    $fallbackImage = 'assets/img/icon/pro1globalicon.png';
    $mainImage = $product->image ?: $fallbackImage;
    $thumbnailImage = $product->thumbnail ?: $mainImage;
    $brandImage = $product->brand_icon ?: $fallbackImage;
@endphp

<div class="product-page min-h-screen bg-slate-200 py-0 text-slate-950 sm:px-4 sm:py-6">
    <article class="product-sheet relative mx-auto w-full max-w-[1180px] overflow-hidden border-[10px] border-[#073b78] bg-white shadow-xl sm:border-[14px] mt-20">
        @if (filled($product->brand_icon))
            <header class="product-print-header bg-white px-4 pb-3 pt-3 sm:px-7 sm:pb-4">
                <div class="flex items-center justify-between gap-6">
                    <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}"
                        alt="PRO 1 Global Home Center"
                        class="h-10 w-auto max-w-[48%] object-contain sm:h-14">
                    <img src="{{ asset($product->brand_icon) }}"
                        alt="{{ $product->brand }} logo"
                        class="h-10 w-auto max-w-[42%] object-contain sm:h-14">
                </div>

                <h1 class="mt-3 rounded-xl bg-[#073b78] px-4 py-1.5 text-center text-sm font-extrabold uppercase tracking-wide text-white sm:px-7 sm:py-2 sm:text-xl">
                    Product Description <span class="normal-case">(ထုတ်ကုန်အကြောင်း)</span>
                </h1>
            </header>
        @else
            <header class="product-print-header flex min-h-16 items-start justify-between gap-4 bg-white sm:min-h-20">
                <div class="flex self-center items-center py-2.5 pl-4 sm:py-3 sm:pl-7">
                    <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}"
                        alt="PRO 1 Global Home Center"
                        class="h-10 w-auto max-w-full object-contain sm:h-14">
                </div>

                <h1 class="w-auto flex-none self-start rounded-bl-[2.5rem] bg-[#073b78] px-5 py-2 text-center text-sm font-extrabold uppercase tracking-wide text-white sm:px-8 sm:py-2.5 sm:text-xl lg:text-2xl">
                    Product Description
                </h1>
            </header>
        @endif

        <div class="product-content px-4 py-4 sm:px-8 sm:py-6 lg:px-12">
            <section class="product-print-media mx-auto grid w-full grid-cols-1 gap-6tests sm:aspect-[2/1] sm:w-[75%] sm:grid-cols-4 sm:grid-rows-1">
                <!-- QR and thumbnail -->
                <aside class="product-print-side grid min-h-0 w-full grid-cols-2 gap-4tests overflow-hiddens pb-4tests sm:h-full sm:grid-cols-1 sm:grid-rows-2 sm:gap-16tests sm:pb-0 gap-4 sm:gap-0 mb-8 sm:mb-0">
                    <div class="product-print-qr relative min-h-0 bg-white p-2 sm:p-6">
                        @if (filled($product->qr))
                            <img
                                src="{{ asset($product->qr) }}"
                                alt="{{ $product->name }} QR code"
                                class="h-full w-full object-contain [image-rendering:pixelated]"
                            >

                            <div class="product-print-qr-label absolute left-2 right-2 rounded bg-[#073b78] py-1 text-center text-sm font-bold text-white sm:-bottom-2 sm:left-6 sm:right-6 mt-2">
                                Scan Here
                            </div>
                        @else
                            <div class="flex h-full min-h-28 items-center justify-center border-2 border-dashed border-slate-300 text-center text-xs font-semibold text-slate-400">
                                QR code unavailable
                            </div>
                        @endif
                    </div>

                    <div class="product-print-thumbnail min-h-0 overflow-hidden bg-slate-100tests sm:p-6">
                        <img src="{{ asset($thumbnailImage) }}"
                            alt="{{ $product->name }} thumbnail"
                            class="h-full w-full object-cover">
                    </div>
                </aside>

                <!-- Main image -->
                <div class="product-print-main min-h-0 w-full overflow-hidden sm:col-span-3 sm:h-full sm:ps-12tests sm:p-6">
                    <img src="{{ asset($mainImage) }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover">
                </div>
            </section>
            <div class="product-print-divider my-5 h-0.5 bg-[#0a4b91] sm:my-4"></div>

            <section class="product-details relative overflow-hidden pb-3">
                <img src="{{ asset($brandImage) }}"
                     alt=""
                     aria-hidden="true"
                     class="pointer-events-none absolute left-1/2 top-1/2 w-3/4 -translate-x-1/2 -translate-y-1/2 opacity-[0.055] grayscale">

                <div class="relative">
                    @unless (filled($product->brand_icon))
                    <div class="mb-3">
                        <h2 class="text-base font-extrabold uppercase sm:text-xl">
                            Product Description <span class="normal-case">(ထုတ်ကုန်အကြောင်း)</span>
                        </h2>
                    </div>
                    @endunless

                    <dl class="product-print-specifications grid grid-cols-[minmax(110px,1fr)_10px_minmax(0,2fr)] gap-y-0.5 text-xs font-semibold leading-5 sm:grid-cols-[220px_16px_minmax(0,1fr)] sm:text-base sm:leading-6">
                        @foreach ($specifications as $label => $value)
                            <dt class="before:mr-2 before:content-['•']">{{ $label }}</dt>
                            <dd class="text-center">:</dd>
                            <dd>{{ $value }}</dd>
                        @endforeach
                    </dl>

                    <div class="product-print-description mt-4 whitespace-pre-line text-justify text-xs leading-6 sm:text-base sm:leading-7">{{ filled($product->description) ? $product->description : 'No product description is available.' }}</div>
                </div>
            </section>
        </div>

        <footer class="product-footer flex flex-col items-center justify-center gap-2 bg-[#073b78] px-4 py-3 text-center text-[11px] font-bold text-white sm:flex-row sm:gap-4 sm:text-sm">
            <span>WWW.PRO1GLOBALHOMECENTER.COM</span>
            <div class="flex items-center gap-2" aria-label="PRO 1 social media channels">
                <i class="fa-brands fa-facebook-f flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-sm text-white" title="Facebook" aria-label="Facebook"></i>
                <i class="fa-brands fa-viber flex h-7 w-7 items-center justify-center rounded-full bg-purple-600 text-sm text-white" title="Viber" aria-label="Viber"></i>
                <i class="fa-brands fa-instagram flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 text-sm text-white" title="Instagram" aria-label="Instagram"></i>
                <i class="fa-brands fa-tiktok flex h-7 w-7 items-center justify-center rounded-full bg-black text-sm text-white" title="TikTok" aria-label="TikTok"></i>
                <i class="fa-brands fa-youtube flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-sm text-white" title="YouTube" aria-label="YouTube"></i>
                <i class="fa-solid fa-paper-plane flex h-7 w-7 items-center justify-center rounded-full bg-sky-500 text-sm text-white" title="Telegram" aria-label="Telegram"></i>
            </div>
        </footer>
    </article>

    <nav class="no-print mx-4 mt-4 rounded-2xl border border-slate-200 bg-whites p-3 shadow-lgs sm:mx-auto sm:mt-5 sm:max-w-[1180px] sm:p-4" aria-label="Product actions">
        <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ auth()->check() ? route('products.index') : route('products.catalog') }}"
                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to list
            </a>

            @if (filled($product->website_url))
            <a href="{{ $product->website_url }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#073b78] bg-white px-5 py-2.5 text-sm font-semibold text-[#073b78] transition hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100">
                Go to website
                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 7v12h12v-5"/></svg>
            </a>
            @endif

            @auth
            <button type="button" id="print-product-button"
                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#073b78] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#052e5e] focus:outline-none focus:ring-4 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-4h12v8H6v-8z"/></svg>
                <span id="print-product-button-label">Print product</span>
            </button>
            @endauth
        </div>
    </nav>
</div>
@endsection

@section('css')
<style>
    @media print {
        @page {
            size: auto;
            margin: 4mm;
        }

        .no-print {
            display: none !important;
        }

        html, body, main {
            width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .product-page {
            min-height: 0 !important;
            padding: 0 !important;
            background: white !important;
        }

        .product-sheet {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            border-width: 1.5mm !important;
            box-shadow: none !important;
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
        }

        .product-print-header {
            min-height: 0 !important;
        }

        .product-print-header img {
            max-height: 12mm !important;
            width: auto !important;
        }

        .product-print-header h1 {
            font-size: 1.05rem !important;
            line-height: 1.15 !important;
        }

        .product-content {
            padding: 2.5mm 4mm !important;
        }

        .product-print-media {
            display: grid !important;
            grid-template-columns: 1fr 3fr !important;
            grid-template-rows: 1fr !important;
            gap: 0 !important;
            width: 74% !important;
            aspect-ratio: 2 / 1 !important;
            margin: 0 auto !important;
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
        }

        .product-print-side {
            display: grid !important;
            grid-template-columns: 1fr !important;
            grid-template-rows: repeat(2, minmax(0, 1fr)) !important;
            gap: 0 !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        .product-print-qr,
        .product-print-thumbnail {
            min-height: 0 !important;
            padding: 2mm !important;
            overflow: hidden !important;
        }

        .product-print-qr img {
            width: 100% !important;
            height: 100% !important;
            box-sizing: border-box !important;
            padding-bottom: 4.5mm !important;
            object-fit: contain !important;
        }

        .product-print-thumbnail img,
        .product-print-main img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .product-print-qr-label {
            right: 2mm !important;
            bottom: 2mm !important;
            left: 2mm !important;
            padding: 0.6mm 0 !important;
            font-size: 0.625rem !important;
            line-height: 1.1 !important;
        }

        .product-print-main {
            grid-column: auto !important;
            min-height: 0 !important;
            height: 100% !important;
            padding: 2mm 2mm 2mm 5mm !important;
        }

        .product-print-divider {
            height: 0.4mm !important;
            margin: 2.5mm 0 !important;
        }

        .product-details {
            padding-bottom: 1.5mm !important;
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
        }

        .product-details h2 {
            margin: 0 0 2mm !important;
            font-size: 1.35rem !important;
            line-height: 1.4 !important;
        }

        .product-print-specifications {
            grid-template-columns: 42mm 4mm minmax(0, 1fr) !important;
            gap: 0 !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
        }

        .product-print-specifications dt {
            white-space: nowrap !important;
        }

        .product-print-description {
            margin-top: 3mm !important;
            font-size: 0.95rem !important;
            line-height: 1.7 !important;
            orphans: 2;
            widows: 2;
        }

        .product-footer {
            gap: 2mm !important;
            padding: 1.5mm 3mm !important;
            font-size: 0.8rem !important;
            line-height: 1 !important;
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
        }

        .product-footer i {
            width: 5mm !important;
            height: 5mm !important;
            font-size: 0.625rem !important;
        }
    }

    @media print and (max-width: 160mm) {
        @page {
            margin: 2.5mm;
        }

        .product-sheet {
            border-width: 1mm !important;
        }

        .product-print-header img {
            max-height: 9mm !important;
        }

        .product-print-header h1 {
            font-size: 0.95rem !important;
        }

        .product-content {
            padding: 2mm 3mm !important;
        }

        .product-print-media {
            width: 70% !important;
        }

        .product-print-specifications {
            grid-template-columns: 38mm 3mm minmax(0, 1fr) !important;
            font-size: 0.8rem !important;
            line-height: 1.45 !important;
        }

        .product-print-description {
            margin-top: 2.5mm !important;
            font-size: 0.825rem !important;
            line-height: 1.65 !important;
        }

        .product-details h2 {
            margin-bottom: 1.5mm !important;
            font-size: 1.2rem !important;
            line-height: 1.4 !important;
        }

        .product-footer {
            font-size: 0.725rem !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        let activePrintRecordId = null;
        const $button = $('#print-product-button');

        $button.on('click', function () {
            $button.prop('disabled', true);
            $('#print-product-button-label').text('Preparing preview...');

            $.ajax({
                url: @js(route('products.print-records.store', $product)),
                method: 'POST',
                dataType: 'json',
                data: { _token: @js(csrf_token()) },
                headers: { Accept: 'application/json' }
            }).done(response => {
                activePrintRecordId = response.data.id;
                window.print();
            }).fail(xhr => {
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to open print preview',
                    text: xhr.responseJSON?.message || 'The print request could not be recorded.'
                });
            }).always(() => {
                $button.prop('disabled', false);
                $('#print-product-button-label').text('Print product');
            });
        });

        window.addEventListener('afterprint', function () {
            if (!activePrintRecordId) return;

            const recordId = activePrintRecordId;
            activePrintRecordId = null;

            $.ajax({
                url: @js(url('/product-print-records')) + `/${recordId}/complete`,
                method: 'POST',
                data: {
                    _token: @js(csrf_token()),
                    _method: 'PATCH'
                },
                headers: { Accept: 'application/json' }
            }).done(() => {
                const count = Number($('#printed-count').text()) || 0;
                $('#printed-count').text(count + 1);
                $('#latest-printed-text').text(' · Last printed just now');
            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to save print record',
                    text: 'Please try again.'
                });
            });
        });
    });
</script>
@endsection
