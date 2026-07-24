<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Batch product print</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #d9dee5;
            color: #0f172a;
            font-family: Arial, sans-serif;
        }

        .print-toolbar {
            position: sticky;
            z-index: 20;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 20px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.14);
        }

        .print-toolbar p {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
        }

        .print-toolbar span {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 12px;
            font-weight: 400;
        }

        .print-toolbar button {
            border: 0;
            border-radius: 8px;
            padding: 10px 18px;
            background: #073b78;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
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
            height: 556.8px;
            min-height: 556.8px;
            max-height: 556.8px;
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
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px;
        }

        .sheet-media {
            display: grid;
            grid-template-columns: 1fr 3fr;
            grid-template-rows: 1fr;
            gap: 0;
            width: 74%;
            aspect-ratio: 2 / 1;
            margin: 0 auto;
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

        .sheet-thumbnail img,
        .sheet-main img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            margin: 7px 0;
            background: #0a4b91;
        }

        .sheet-details {
            position: relative;
            overflow: hidden;
            padding-bottom: 4px;
        }

        .sheet-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 72%;
            opacity: 0.055;
            filter: grayscale(1);
            transform: translate(-50%, -50%);
        }

        .sheet-details-body {
            position: relative;
        }

        .sheet-details h2 {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 800;
            line-height: 18px;
            text-transform: uppercase;
        }

        .sheet-specifications {
            display: grid;
            grid-template-columns: 112px 10px minmax(0, 1fr);
            gap: 0;
            margin: 0;
            font-size: 10px;
            font-weight: 600;
            line-height: 13px;
        }

        .sheet-specifications dt,
        .sheet-specifications dd {
            min-width: 0;
            margin: 0;
        }

        .sheet-specifications dt {
            white-space: nowrap;
        }

        .sheet-specifications dt + dd {
            text-align: center;
        }

        .sheet-description {
            margin-top: 7px;
            white-space: pre-line;
            font-size: 9px;
            line-height: 12px;
            text-align: justify;
        }

        .sheet-footer {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 4px 8px;
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
            width: 16px;
            height: 16px;
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
</head>
<body>
    <div class="print-toolbar no-print">
        <p>
            A4 batch print · {{ $products->count() }} {{ Str::plural('product', $products->count()) }}
            <span>Landscape · Two product sheets per A4 page · {{ (int) ceil($products->count() / 2) }} {{ Str::plural('page', (int) ceil($products->count() / 2)) }}</span>
        </p>
        <button type="button" onclick="window.print()">
            <i class="fas fa-print"></i>
            Print A4 sheets
        </button>
    </div>

    <main class="print-pages">
        @foreach ($products->chunk(2) as $pageProducts)
            <section class="print-page {{ $pageProducts->count() === 1 ? 'single' : '' }}">
                @foreach ($pageProducts as $product)
                    @include('products.partials.print-sheet', ['product' => $product])
                @endforeach
            </section>
        @endforeach
    </main>
</body>
</html>
