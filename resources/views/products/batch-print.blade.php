@extends('layouts.dashboard')

@section('css')
    <style>
        @font-face {
            font-family: "BatchPrintLatin";
            src: url("{{ asset('assets/fonts/batch-print/LiberationSans-Regular.ttf') }}") format("truetype");
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: "BatchPrintLatin";
            src: url("{{ asset('assets/fonts/batch-print/LiberationSans-Bold.ttf') }}") format("truetype");
            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: "BatchPrintMyanmar";
            src: url("{{ asset('assets/fonts/batch-print/Myanmar3.ttf') }}") format("truetype");
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: "BatchPrintPyidaungsu";
            src: url("{{ asset('assets/fonts/batch-print/Pyidaungsu-Regular.ttf') }}") format("truetype");
            font-weight: 400;
            font-style: normal;
        }

        * {
            box-sizing: border-box;
        }

        .batch-print-workspace {
            background: #d9dee5;
            color: #0f172a;
            font-family: Arial, sans-serif;
        }

        .batch-print-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 24px;
            padding: 24px;
        }

        .batch-print-preview {
            min-width: 0;
            overflow: auto;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #d9dee5;
        }

        @media (min-width: 1280px) {
            .batch-print-layout {
                grid-template-columns: 340px minmax(0, 1fr);
                align-items: start;
            }

            .batch-print-information {
                position: sticky;
                top: 88px;
            }
        }

        .print-pages {
            padding: 24px 0;
        }

        .print-page {
            position: relative;
            display: grid;
            grid-template-columns: 489.6px 489.6px;
            align-items: center;
            justify-content: center;
            gap: 28px;
            width: 1122.52px;
            height: 793.7px;
            margin: 0 auto 24px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.18);
        }

        .print-page.single {
            grid-template-columns: 489.6px;
            justify-content: start;
            padding-left: 57.66px;
        }

        .print-page::after {
            position: absolute;
            top: 118.45px;
            bottom: 118.45px;
            left: 50%;
            border-left: 2px dashed #000000;
            content: "";
            transform: translateX(-1px);
        }

        .product-sheet {
            display: flex;
            flex-direction: column;
            width: 489.6px;
            min-width: 489.6px;
            max-width: 489.6px;
            /* height: 556.8px;
            min-height: 556.8px;
            max-height: 556.8px; */
            height: 550px;
            min-height: 550px;
            max-height: 550px;
            overflow: hidden;
            border: 5px solid #073b78;
            background: #ffffff;
        }

        .sheet-header {
            flex: 0 0 auto;
            background: #ffffff;
        }

        .sheet-header img {
            display: block;
            width: auto;
            max-height: 34px;
            object-fit: contain;
        }

        .sheet-header h1 {
            margin: 0;
            background: #073b78;
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            line-height: 18px;
            text-align: center;
            text-transform: uppercase;
        }

        .sheet-header h1 span,
        .sheet-details h2 span {
            text-transform: none;
        }

        .sheet-header-standard {
            display: flex;
            min-height: 42px;
            align-items: flex-start;
            justify-content: space-between;
        }

        .sheet-header-standard > img {
            margin: 7px 0 0 12px;
        }

        .sheet-header-standard h1 {
            border-bottom-left-radius: 28px;
            padding: 6px 16px;
        }

        .sheet-header-brand {
            padding: 6px 12px 5px;
        }

        .brand-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-row img {
            max-width: 45%;
        }

        .sheet-header-brand h1 {
            margin-top: 5px;
            border-radius: 10px;
            padding: 3px 8px;
        }

        .sheet-content {
            display: grid;
            flex: 1 1 auto;
            grid-template-rows: auto auto minmax(0, 1fr);
            min-height: 0;
            overflow: hidden;
            padding: 4px 12px;
        }

        .sheet-media {
            display: grid;
            grid-template-columns: 1fr 3fr;
            grid-template-rows: 1fr;
            gap: 0;
            width: 80%;
            aspect-ratio: 2 / 1;
            margin: 0 auto;
        }

        .sheet-media.portrait-main {
            grid-template-columns: 1fr 1fr;
        }

        .sheet-media.portrait-main .sheet-main {
            padding: 6px;
        }

        .sheet-media.portrait-main .sheet-qr-label {
            right: auto;
            left: 50%;
            width: 72px;
            transform: translateX(-50%);
        }

        .sheet-side {
            display: grid;
            grid-template-rows: repeat(2, minmax(0, 1fr));
            min-height: 0;
            height: 100%;
        }

        .sheet-side.without-thumbnail {
            display: flex;
            align-items: flex-end;
        }

        .sheet-side.without-thumbnail .sheet-qr {
            width: 100%;
            height: 50%;
        }

        .sheet-qr,
        .sheet-thumbnail {
            position: relative;
            min-height: 0;
            overflow: hidden;
            padding: 6px;
        }

        .sheet-qr img {
            display: block;
            width: 100%;
            height: 100%;
            padding-bottom: 15px;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .sheet-qr-label {
            position: absolute;
            right: 6px;
            bottom: 6px;
            left: 6px;
            border-radius: 3px;
            padding: 2px 0;
            background: #073b78;
            color: #ffffff;
            font-size: 8px;
            font-weight: 700;
            line-height: 10px;
            text-align: center;
        }

        .sheet-thumbnail img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* object-position: left center; */
        }

        .sheet-main img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* object-position: center; */
            object-position: left center;
        }

        .sheet-main {
            min-height: 0;
            height: 100%;
            overflow: hidden;
            padding: 6px 6px 6px 14px;
        }

        .sheet-missing {
            display: flex;
            height: 100%;
            align-items: center;
            justify-content: center;
            border: 1px dashed #cbd5e1;
            color: #94a3b8;
            font-size: 8px;
            text-align: center;
        }

        .sheet-divider {
            height: 2px;
            /* margin: 6px 0; */
            margin-bottom: 6px;
            background: #0a4b91;
        }

        .sheet-details {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
            padding-bottom: 0;
            font-family: "BatchPrintLatin", "BatchPrintPyidaungsu", "BatchPrintMyanmar", sans-serif;
            text-rendering: optimizeLegibility;
        }

        .sheet-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 90%;
            opacity: 0.055;
            filter: grayscale(1);
            transform: translate(-50%, -50%);
        }

        .sheet-details-body {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 100%;
            width: 100%;
        }

        .sheet-details h2 {
            margin: 0 0 2px;
            font-size: 11px;
            font-weight: 900;
            line-height: 1.5; 
            text-transform: uppercase;
        }

        .sheet-text-body {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            justify-content: space-evenly;
            min-height: 0;
        }

        .sheet-specifications {
            display: grid;
            grid-template-columns: 132px 10px minmax(0, 1fr);
            gap: 0;
            margin: 0;
            font-size: 10px;
            font-weight: 900;
            line-height: 13px;
            /* -webkit-text-stroke: 0.15px currentColor; */
        }

        .sheet-specifications dt,
        .sheet-specifications dd {
            min-width: 0;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sheet-specifications dt + dd {
            text-align: center;
        }

        .sheet-description {
            display: -webkit-box;
            overflow: hidden;
            margin-top: 0;
            white-space: pre-line;
            font-size: 9px;
            line-height: 13px;
            font-weight: 400;
            text-align: justify;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: var(--description-lines);
        }

        .sheet-footer {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 4px 4px;
            background: #073b78;
            color: #ffffff;
            font-size: 8px;
            font-weight: 700;
            line-height: 10px;
            text-align: center;
        }

        .sheet-socials {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .sheet-socials i {
            display: flex;
            width: 12px;
            height: 12px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #2563eb;
            color: #ffffff;
            font-size: 8px;
        }

        .sheet-socials i:nth-child(2) { background: #9333ea; }
        .sheet-socials i:nth-child(3) { background: #ec4899; }
        .sheet-socials i:nth-child(4) { background: #000000; }
        .sheet-socials i:nth-child(5) { background: #dc2626; }
        .sheet-socials i:nth-child(6) { background: #0ea5e9; }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 1122.52px;
                margin: 0;
                padding: 0;
                background: #ffffff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            body > nav,
            #sidebar,
            #sidebarBackdrop,
            #main-content > footer,
            #main-content > p {
                display: none !important;
            }

            body > div.flex {
                display: block !important;
                padding-top: 0 !important;
                overflow: visible !important;
            }

            #main-content,
            #main-content main,
            .batch-print-layout,
            .batch-print-preview {
                display: block !important;
                width: 1122.52px !important;
                max-width: none !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .print-pages {
                padding: 0;
            }

            .print-page {
                width: 1122.52px;
                height: 793.7px;
                margin: 0;
                break-after: page;
                page-break-after: always;
                box-shadow: none;
            }

            .print-page:last-child {
                break-after: auto;
                page-break-after: auto;
            }
        }
    </style>
@endsection

@section('content')
    <div class="no-print border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 lg:mt-1.5">
        <nav class="mb-5 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2 text-sm font-medium">
                <li><a href="{{ route('dashboards.index') }}" class="text-gray-600 hover:text-primary-700 dark:text-gray-300"><i class="fas fa-home mr-2"></i>Home</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
                <li><a href="{{ route('products.index') }}" class="text-gray-600 hover:text-primary-700 dark:text-gray-300">Products</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
                <li class="text-gray-400">Batch print</li>
            </ol>
        </nav>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Batch product print</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review the print information before opening the browser print preview.</p>
            </div>
            <a href="{{ route('products.index') }}"
                class="inline-flex w-fit items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to list
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
                <p class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Selected products</p>
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

<!-- 
thumbnail portrait = object-fit: contain;
main portrait = object-position: left center;

.sheet-thumbnail img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    /* object-position: left center; */
}

.sheet-main img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: left center;
}
-->
