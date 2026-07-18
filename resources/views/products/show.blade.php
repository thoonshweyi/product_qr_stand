@extends('layouts.main')

@section('css')
<style>
    .product-poster {
        --poster-blue: #073d78;
        font-family: Arial, "Noto Sans Myanmar", sans-serif;
    }

    .product-watermark {
        opacity: .055;
        filter: saturate(.7);
    }

    @media print {
        body { background: white !important; }
        .product-poster { padding: 0 !important; }
        .product-poster > article { box-shadow: none !important; }
    }
</style>
@endsection

@section('content')
@php
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

<div class="product-poster min-h-screen bg-[#073d78] p-2 sm:p-3 lg:p-4">
    <article class="mx-auto max-w-[1540px] overflow-hidden bg-white shadow-2xl">
        {{-- Header --}}
        <header class="grid min-h-[130px] grid-cols-[46%_54%] sm:min-h-[155px]">
            <div class="flex items-center px-4 py-5 sm:px-8 lg:px-10">
                <img
                    src="{{ asset('assets/img/icon/pro1globalicon.png') }}"
                    alt="PRO1 Global Home Center"
                    class="w-full max-w-[410px] object-contain"
                >
            </div>
            <div class="flex items-center justify-center rounded-bl-[58px] bg-[#073d78] px-3 text-center text-white sm:rounded-bl-[90px] lg:rounded-bl-[110px]">
                <h1 class="text-xl font-black tracking-tight sm:text-4xl lg:text-6xl xl:text-7xl">Product Description</h1>
            </div>
        </header>

        <main class="px-5 pb-7 pt-5 sm:px-8 sm:pb-10 lg:px-10 lg:pt-7">
            {{-- QR, thumbnail and main image --}}
            <section class="mx-auto grid max-w-[1050px] gap-5 sm:grid-cols-[230px_minmax(0,1fr)] lg:grid-cols-[270px_minmax(0,1fr)] lg:gap-10">
                <div>
                    <div class="mx-auto w-full max-w-[230px] lg:max-w-[270px]">
                        <img
                            src="{{ asset('assets/img/products/qrs/2000000602110.png') }}"
                            alt="Product QR Code"
                            class="mx-auto aspect-square w-[78%] object-contain [image-rendering:pixelated]"
                        >
                        <div class="mx-auto mt-2 w-[78%] rounded-md bg-[#073d78] py-1.5 text-center text-lg font-black text-white sm:text-2xl">Scan Here</div>
                    </div>

                    <div class="mt-5">
                        <div class="flex items-center text-sm text-slate-800">
                            <span class="h-[2px] flex-1 bg-slate-800"></span>
                            <span class="px-3">300mm</span>
                            <span class="h-[2px] flex-1 bg-slate-800"></span>
                        </div>
                        <div class="relative mt-1 flex">
                            <div class="flex w-5 flex-col items-center justify-center">
                                <span class="h-full w-[2px] bg-slate-800"></span>
                                <span class="absolute -rotate-90 whitespace-nowrap text-sm">300mm</span>
                            </div>
                            <img
                                src="{{ asset('assets/img/products/16a59d57f0b9244thumbnail.jpeg') }}"
                                alt="Product tile thumbnail"
                                class="aspect-square min-w-0 flex-1 object-cover"
                            >
                        </div>
                    </div>
                </div>

                <img
                    src="{{ asset('assets/img/products/16a59d57f082b64main.jpeg') }}"
                    alt="Product usage preview"
                    class="h-full min-h-[330px] w-full object-cover sm:min-h-[520px]"
                >
            </section>

            <div class="my-8 h-[3px] w-full bg-[#073d78] sm:my-10"></div>

            {{-- Description --}}
            <section class="relative overflow-hidden px-1 pb-1 sm:px-2">
                <img
                    src="{{ asset('assets/img/icon/pro1globalicon.png') }}"
                    alt=""
                    aria-hidden="true"
                    class="product-watermark pointer-events-none absolute left-1/2 top-[48%] w-[75%] max-w-[850px] -translate-x-1/2 -translate-y-1/2"
                >

                <div class="relative z-10">
                    <h2 class="mb-4 text-2xl font-black uppercase leading-tight text-black sm:text-4xl lg:text-5xl">
                        Product Description <span class="normal-case">(ကုန်ပစ္စည်းအကြောင်း)</span>
                    </h2>

                    <dl class="max-w-[940px] text-base font-bold leading-tight text-black sm:text-xl lg:text-2xl">
                        @foreach ($details as $label => $value)
                            <div class="grid grid-cols-[minmax(135px,300px)_14px_minmax(0,1fr)] items-baseline py-1 sm:grid-cols-[minmax(210px,390px)_18px_minmax(0,1fr)]">
                                <dt class="flex items-baseline gap-3">
                                    <span class="text-base sm:text-xl">•</span>
                                    <span>{{ $label }}</span>
                                </dt>
                                <span>:</span>
                                <dd class="pl-2">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-5 space-y-2 text-justify text-base font-medium leading-8 text-black sm:text-xl sm:leading-10 lg:text-2xl lg:leading-[1.9]">
                        <p>
                            အရည်အသွေးကောင်းမွန်သော <strong>Grade A</strong> အဆင့်ရှိ ကြွေပြားများကို အသုံးပြု၍ ပြုလုပ်ထားသောကြောင့် ရေရှည်ခံပြီး အကြမ်းခံခြင်း၊ အရောင်ငြိမ်ခြင်း၊ သန့်ရှင်းရလွယ်ကူခြင်းနှင့် လှပခန့်ညားမှုများကို ပေးစွမ်းနိုင်ပါသည်။
                        </p>
                        <p>
                            <strong>PRO 1 Global Home Center</strong> မှ အရည်အသွေးကောင်းမွန်သော ပစ္စည်းများကိုသာ ရွေးချယ်ပေးထားပြီး သင့်အိမ်အတွက် ယုံကြည်စိတ်ချစွာ ဝယ်ယူနိုင်ပါသည်။
                        </p>
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="flex flex-col items-center justify-center gap-3 bg-[#073d78] px-5 py-5 text-white sm:flex-row">
            <p class="text-center text-sm font-black uppercase tracking-wide sm:text-lg">www.pro1globalhomecenter.com</p>
            <div class="flex items-center gap-2">
                @foreach ([
                    ['fab fa-facebook-f', '#1877f2'],
                    ['fab fa-viber', '#7356a8'],
                    ['fab fa-instagram', '#e1306c'],
                    ['fab fa-tiktok', '#050505'],
                    ['fab fa-youtube', '#ff0000'],
                    ['fab fa-telegram-plane', '#229ed9'],
                ] as [$icon, $color])
                    <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs text-white sm:h-8 sm:w-8 sm:text-sm" style="background-color: {{ $color }}">
                        <i class="{{ $icon }}"></i>
                    </span>
                @endforeach
            </div>
        </footer>
    </article>
</div>
@endsection
