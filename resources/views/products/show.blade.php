@extends('layouts.main')

@section('css')
<style>
    :root { color-scheme: light; }

    body, main { margin: 0; background: white !important; }

    .product-show-page {
        --blue: #073d78;
        width: 100%;
        padding: .8vw;
        font-family: Arial, "Noto Sans Myanmar", sans-serif;
        background: var(--blue);
    }

    /* All measurements below are tied to the poster width (cqw). This keeps the
       composition identical on a phone, desktop and print instead of reflowing. */
    .product-poster {
        container-type: inline-size;
        position: relative;
        width: min(100%, 1490px);
        margin: 0 auto;
        overflow: hidden;
        background: #fff;
        color: #050505;
        box-shadow: 0 1.2cqw 3cqw rgb(0 0 0 / .2);
    }

    .poster-header { display: grid; grid-template-columns: 55% 45%; height: 9.5cqw; }
    .poster-logo { display: flex; align-items: center; padding: 1.35cqw 3cqw; }
    .poster-logo img { display: block; width: 25cqw; height: 6.8cqw; object-fit: contain; }
    .poster-title {
        display: flex; align-items: center; justify-content: center;
        border-bottom-left-radius: 5.2cqw; background: var(--blue); color: #fff;
        font-size: 3.65cqw; font-weight: 900; letter-spacing: -.12cqw; white-space: nowrap;
    }

    .poster-body { padding: 1.2cqw 3cqw 2.2cqw; }
    .visuals {
        display: grid; grid-template-columns: 18.2cqw 45cqw;
        justify-content: center; gap: 2.6cqw; height: 36.9cqw;
    }
    .visual-left { display: grid; grid-template-rows: 19.2cqw 16cqw; gap: 1.7cqw; min-width: 0; }
    .qr-panel { display: flex; flex-direction: column; align-items: center; min-height: 0; }
    .qr-panel img { display: block; width: 14.8cqw; height: 14.8cqw; object-fit: contain; image-rendering: pixelated; }
    .scan-label {
        width: 14.2cqw; margin-top: .45cqw; padding: .22cqw 0 .32cqw;
        border-radius: .42cqw; background: var(--blue); color: #fff;
        text-align: center; font-size: 1.72cqw; font-weight: 900; line-height: 1;
    }
    .size-horizontal { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: .8cqw; font-size: .95cqw; }
    .size-horizontal i { height: .13cqw; background: #222; }
    .tile-row { display: grid; grid-template-columns: 1.5cqw 1fr; height: 14.65cqw; margin-top: .25cqw; }
    .size-vertical { position: relative; border-left: .13cqw solid #222; }
    .size-vertical span {
        position: absolute; top: 50%; left: -.3cqw; transform: translate(-50%, -50%) rotate(-90deg);
        font-size: .92cqw; white-space: nowrap; background: #fff; padding: .25cqw;
    }
    .tile-frame, .main-frame { overflow: hidden; background: #f3f3f3; }
    .tile-frame img, .main-frame img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .main-frame { width: 45cqw; height: 36.9cqw; }

    .divider { height: .22cqw; margin: 4.5cqw 0 2.2cqw; background: var(--blue); }
    .description { position: relative; min-height: 47cqw; overflow: hidden; }
    .watermark {
        position: absolute; z-index: 0; left: 50%; top: 51%; width: 63cqw; height: 23cqw;
        transform: translate(-50%, -50%); object-fit: contain; opacity: .055; pointer-events: none;
    }
    .description-content { position: relative; z-index: 1; }
    .description h2 { margin: 0 0 .7cqw; font-size: 2.65cqw; font-weight: 900; line-height: 1.15; text-transform: uppercase; }
    .description h2 span { text-transform: none; }
    .details { width: 67cqw; margin: 0; font-size: 1.82cqw; font-weight: 700; line-height: 1.23; }
    .detail-row { display: grid; grid-template-columns: 27cqw 1.5cqw 1fr; align-items: baseline; }
    .detail-row dt, .detail-row dd { margin: 0; }
    .detail-row dt::before { content: "•"; display: inline-block; width: 2cqw; }
    .copy { margin-top: 1.15cqw; font-size: 1.58cqw; font-weight: 500; line-height: 1.7; text-align: justify; }
    .copy p { margin: 0 0 .2cqw; }

    .poster-footer {
        display: flex; align-items: center; justify-content: center; gap: 1cqw;
        height: 5.4cqw; background: var(--blue); color: #fff;
    }
    .website { font-size: 1.3cqw; font-weight: 900; text-transform: uppercase; }
    .socials { display: flex; gap: .5cqw; }
    .socials span {
        display: flex; align-items: center; justify-content: center; width: 2cqw; height: 2cqw;
        border-radius: 50%; color: #fff; font-size: 1.05cqw;
    }

    @media print {
        @page { margin: 0; }
        .product-show-page { padding: 0; }
        .product-poster { width: 100%; box-shadow: none; }
    }
</style>
@endsection

@section('content')
@php
    // Static values intentionally used while this screen is a design prototype.
    $details = [
        'Brand' => 'Cotto',
        'Name' => 'Ceramic Tile',
        'Model' => 'Ft Willy Flax',
        'Code' => '2000000082547',
        'Country of origin' => 'Thailand',
        'Type' => 'Floor Tile',
        'Grade' => 'A Grade',
        'Color' => 'Light Beige',
        'Size' => '(300mm × 300mm) / (1ft × 1ft)',
        '1 SUD (Kyin)' => '100 Pcs',
        '1 Package' => '11 Pcs',
        'Weight' => '16.6 Kg',
        'Usage Location' => 'Indoor',
    ];
@endphp

<div class="product-show-page">
    <article class="product-poster bg-white">
        <header class="poster-header">
            <div class="poster-logo">
                <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}" alt="PRO1 Global Home Center">
            </div>
            <div class="poster-title">Product Description</div>
        </header>

        <main class="poster-body">
            <section class="visuals" aria-label="Product images and QR code">
                <div class="visual-left">
                    <div class="qr-panel">
                        <img src="{{ asset('assets/img/products/qrs/2000000602110.png') }}" alt="Product QR Code">
                        <div class="scan-label">Scan Here</div>
                    </div>

                    <div class="tile-size">
                        <div class="size-horizontal"><i></i><span>300mm</span><i></i></div>
                        <div class="tile-row">
                            <div class="size-vertical"><span>300mm</span></div>
                            <div class="tile-frame">
                                <img src="{{ asset('assets/img/products/16a59d57f0b9244thumbnail.jpeg') }}" alt="Ceramic tile sample">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-frame">
                    <img src="{{ asset('assets/img/products/16a59d57f082b64main.jpeg') }}" alt="Ceramic tile used in a kitchen">
                </div>
            </section>

            <div class="divider"></div>

            <section class="description">
                <img class="watermark" src="{{ asset('assets/img/icon/pro1globalicon.png') }}" alt="" aria-hidden="true">
                <div class="description-content">
                    <h2>Product Description <span>(ကုန်ပစ္စည်းအကြောင်း)</span></h2>
                    <dl class="details">
                        @foreach ($details as $label => $value)
                            <div class="detail-row">
                                <dt>{{ $label }}</dt><span>:</span><dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="copy">
                        <p>အရည်အသွေးကောင်းမွန်သော <strong>Grade A</strong> အဆင့်ရှိ ကြွေပြားများကို အသုံးပြု၍ ပြုလုပ်ထားသောကြောင့် ရေရှည်ခံပြီး အကြမ်းခံခြင်း၊ အရောင်ငြိမ်ခြင်း၊ သန့်ရှင်းရလွယ်ကူခြင်းနှင့် လှပခန့်ညားမှုများကို ပေးစွမ်းနိုင်ပါသည်။</p>
                        <p><strong>PRO 1 Global Home Center</strong> မှ အရည်အသွေးကောင်းမွန်သော ပစ္စည်းများကိုသာ ရွေးချယ်ပေးထားပြီး သင့်အိမ်အတွက် ယုံကြည်စိတ်ချစွာ ဝယ်ယူနိုင်ပါသည်။</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="poster-footer">
            <span class="website">www.pro1globalhomecenter.com</span>
            <div class="socials" aria-label="Social media">
                @foreach ([
                    ['fab fa-facebook-f', '#1877f2'], ['fab fa-viber', '#7356a8'],
                    ['fab fa-instagram', '#e1306c'], ['fab fa-tiktok', '#050505'],
                    ['fab fa-youtube', '#ff0000'], ['fab fa-telegram-plane', '#229ed9'],
                ] as [$icon, $color])
                    <span style="background: {{ $color }}"><i class="{{ $icon }}"></i></span>
                @endforeach
            </div>
        </footer>
    </article>
</div>
@endsection
