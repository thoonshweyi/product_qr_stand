@extends('layouts.main')

@section('content')
@php
    // Static content for the design demo. These values can be replaced with $product later.
    $demo = [
        'brand' => 'IM Dayuan',
        'name' => 'Automatic Pressure Pump',
        'model' => 'ID-750A',
        'code' => '2000000602110',
        'country' => 'China',
        'type' => 'Water Pump',
        'grade' => 'A Grade',
        'color' => 'Blue / Black',
        'size' => '447mm × 447mm',
        'unit' => '1 Unit',
        'package' => '1 Pc',
        'weight' => '12.5 Kg',
        'usage' => 'Indoor / Outdoor',
    ];

    $specifications = [
        'Brand' => $demo['brand'],
        'Name' => $demo['name'],
        'Model' => $demo['model'],
        'Code' => $demo['code'],
        'Country of origin' => $demo['country'],
        'Type' => $demo['type'],
        'Grade' => $demo['grade'],
        'Color' => $demo['color'],
        'Size' => $demo['size'],
        'Sales unit' => $demo['unit'],
        '1 Package' => $demo['package'],
        'Weight' => $demo['weight'],
        'Usage Location' => $demo['usage'],
    ];
@endphp

<div class="min-h-screen bg-slate-200 py-0 text-slate-950 sm:px-4 sm:py-6">
    <article class="relative mx-auto w-full max-w-[1180px] overflow-hidden border-[10px] border-[#073b78] bg-white shadow-xl sm:border-[14px]">
        <header class="flex h-20 items-center bg-white sm:h-28">
            <div class="flex h-full w-1/2 items-center bg-white px-4 sm:px-8">
                <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}"
                     alt="PRO 1 Global Home Center"
                     class="h-auto w-full max-w-[270px] object-contain sm:max-w-[340px]">
            </div>
            <h1 class="flex h-full flex-1 items-center justify-center rounded-bl-[7rem] bg-[#073b78] px-2 text-center text-lg font-extrabold uppercase tracking-wide text-white sm:px-8 sm:text-4xl lg:text-5xl">
                Product Description
            </h1>
        </header>

        <div class="px-4 py-6 sm:px-8 sm:py-8 lg:px-12">
            <section class="sm:w-[70%] mx-auto grid grid-cols-1 gap-6s sm:grid-cols-4">
                <div class="p-6 bg-blue-200">
                    
                </div>
                <div class="self-end overflow-hidden sm:col-span-3">
                    <div class="aspect-4/2 w-full">
                        <img src="{{ asset('assets/img/products/16a5b0382ebada5autopressurepump.jpeg') }}"
                             alt="{{ $demo['name'] }}"
                             class="h-full w-full object-cover">
                    </div>
                </div>
            </section>

            <div class="my-7 h-1 bg-[#0a4b91] sm:my-9"></div>

            <section class="relative overflow-hidden pb-4">
                <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}"
                     alt=""
                     aria-hidden="true"
                     class="pointer-events-none absolute left-1/2 top-1/2 w-3/4 -translate-x-1/2 -translate-y-1/2 opacity-[0.055] grayscale">

                <div class="relative">
                    <h2 class="mb-4 text-xl font-extrabold uppercase sm:text-3xl">
                        Product Description <span class="normal-case">(ကုန်ပစ္စည်းအကြောင်း)</span>
                    </h2>

                    <dl class="grid grid-cols-[minmax(120px,1fr)_12px_minmax(0,2fr)] gap-y-1 text-sm font-semibold sm:grid-cols-[270px_20px_minmax(0,1fr)] sm:text-xl">
                        @foreach ($specifications as $label => $value)
                            <dt class="before:mr-2 before:content-['•']">{{ $label }}</dt>
                            <dd class="text-center">:</dd>
                            <dd>{{ $value }}</dd>
                        @endforeach
                    </dl>

                    <p class="mt-6 text-justify text-sm leading-7 sm:text-lg sm:leading-9">
                        အရည်အသွေးကောင်းမွန်သော <strong>Grade A</strong> အဆင့်ရှိ ကုန်ပစ္စည်းများကို အသုံးပြု၍
                        လူနေအိမ်နှင့် လုပ်ငန်းသုံးနေရာများတွင် ယုံကြည်စိတ်ချစွာ အသုံးပြုနိုင်ပါသည်။
                        တာရှည်ခံပြီး ထိန်းသိမ်းရလွယ်ကူသောကြောင့် နေ့စဉ်အသုံးပြုမှုအတွက် သင့်တော်ပါသည်။
                    </p>
                    <p class="mt-1 text-justify text-sm leading-7 sm:text-lg sm:leading-9">
                        <strong>PRO 1 Global Home Center</strong> မှ အရည်အသွေးကောင်းမွန်သော ပစ္စည်းများကို
                        စိတ်ချယုံကြည်စွာ ရွေးချယ်ဝယ်ယူနိုင်ပါသည်။
                    </p>
                </div>
            </section>
        </div>

        <footer class="flex flex-col items-center justify-center gap-2 bg-[#073b78] px-4 py-4 text-center text-xs font-bold text-white sm:flex-row sm:gap-5 sm:text-base">
            <span>WWW.PRO1GLOBALHOMECENTER.COM</span>
            <div class="flex items-center gap-2" aria-label="PRO 1 social media channels">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500">f</span>
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-purple-500">v</span>
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-pink-500">◎</span>
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-black">♪</span>
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-600">▶</span>
            </div>
        </footer>
    </article>
</div>
@endsection
