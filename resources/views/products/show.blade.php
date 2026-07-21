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

<div class="no-print sticky top-0 z-40 border-b border-slate-200 bg-white/95 px-4 py-3 shadow-sm backdrop-blur">
    <div class="mx-auto flex max-w-[1180px] flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900">Print tracking</p>
            <p class="mt-0.5 text-xs text-slate-500">
                Confirmed prints: <span id="printed-count">{{ $printedCount }}</span>
                <span id="latest-printed-text">
                @if ($latestPrintedRecord)
                    · Last printed {{ $latestPrintedRecord->printed_at->diffForHumans() }}
                @endif
                </span>
            </p>
        </div>
        <button type="button" id="print-product-button" class="inline-flex items-center justify-center rounded-lg bg-[#073b78] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#052e5e] disabled:cursor-not-allowed disabled:opacity-60">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-4h12v8H6v-8z"/></svg>
            <span id="print-product-button-label">Print product</span>
        </button>
    </div>
</div>

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
            <section class="mx-auto grid w-full grid-cols-1 gap-6 sm:aspect-[2/1] sm:w-[70%] sm:grid-cols-4 sm:grid-rows-1">
                <!-- QR and thumbnail -->
                <aside class="grid min-h-0 w-full grid-cols-2 gap-4 overflow-hiddens pb-4 sm:h-full sm:grid-cols-1 sm:grid-rows-2 sm:gap-16 sm:pb-0">
                    <div class="relative min-h-0 bg-white p-2">
                        <img src="{{ asset('assets/img/products/qrs/2000000602110.png') }}"
                            alt="Product QR code"
                            class="h-full w-full object-contain [image-rendering:pixelated]">
                        <button type="button" class="absolute left-2 right-2 top-full mt-1 rounded bg-[#073b78] py-1 text-sm font-bold text-white">
                            Scan Here
                        </button>
                    </div>

                    <div class="min-h-0 overflow-hidden bg-slate-100">
                        <img src="{{ asset('assets/img/products/16a59d57f0b9244thumbnail.jpeg') }}"
                            alt="{{ $demo['name'] }} thumbnail"
                            class="h-full w-full object-cover">
                    </div>
                </aside>

                <!-- Main image -->
                <div class="min-h-0 w-full overflow-hidden sm:col-span-3 sm:h-full sm:ps-12">
                    <img src="{{ asset('assets/img/products/16a59d57f082b64main.jpeg') }}"
                        alt="{{ $demo['name'] }}"
                        class="h-full w-full object-cover">
                </div>
            </section>
            <div class="my-7 h-1 bg-[#0a4b91] sm:my-9"></div>

            <section class="relative overflow-hidden pb-5">
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

@section('css')
<style>
    @media print {
        /* @page {
            size: A4 portrait;
            margin: 0;
        } */

        .no-print {
            display: none !important;
        }

        html, body, main {
            background: white !important;
        }

        article {
            box-shadow: none !important;
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
