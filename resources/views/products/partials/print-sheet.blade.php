@php
    $specifications = collect([
        'Brand' => $product->brand,
        'Name' => $product->name,
        'Model' => $product->model,
        'Code' => $product->product_code,
        'Country of origin' => $product->country?->name,
        'Sales unit' => $product->unit,
    ])->filter(fn ($value) => filled($value));

    foreach ($product->specificationValues as $specificationValue) {
        if ($specificationValue->specification && filled($specificationValue->value)) {
            $specifications->put($specificationValue->specification->name, $specificationValue->value);
        }
    }

    $fallbackImage = 'assets/img/icon/pro1globalicon.png';
    $mainImage = $product->image ?: $fallbackImage;
    $thumbnailImage = $product->thumbnail ?: null;
    $brandImage = $product->brand_icon ?: $fallbackImage;
@endphp

<article class="product-sheet">
    @if (filled($product->brand_icon))
        <header class="sheet-header sheet-header-brand">
            <div class="brand-row">
                <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}" alt="PRO 1 Global Home Center">
                <img src="{{ asset($product->brand_icon) }}" alt="{{ $product->brand }} logo">
            </div>
            <h1>Product Description <span>(ထုတ်ကုန်အကြောင်း)</span></h1>
        </header>
    @else
        <header class="sheet-header sheet-header-standard">
            <img src="{{ asset('assets/img/icon/pro1globalicon.png') }}" alt="PRO 1 Global Home Center">
            <h1>Product Description</h1>
        </header>
    @endif

    <div class="sheet-content">
        <section class="sheet-media">
            <aside class="sheet-side {{ $thumbnailImage ? '' : 'without-thumbnail' }}">
                <div class="sheet-qr">
                    @if (filled($product->qr))
                        <img src="{{ asset($product->qr) }}" alt="{{ $product->name }} QR code">
                        <div class="sheet-qr-label">Scan Here</div>
                    @else
                        <div class="sheet-missing">QR unavailable</div>
                    @endif
                </div>

                @if ($thumbnailImage)
                    <div class="sheet-thumbnail">
                        <img src="{{ asset($thumbnailImage) }}" alt="{{ $product->name }} thumbnail">
                    </div>
                @endif
            </aside>

            <div class="sheet-main">
                <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}">
            </div>
        </section>

        <div class="sheet-divider"></div>

        <section class="sheet-details">
            <img src="{{ asset($brandImage) }}" alt="" class="sheet-watermark">
            <div class="sheet-details-body">
                @unless (filled($product->brand_icon))
                    <h2>Product Description <span>(ထုတ်ကုန်အကြောင်း)</span></h2>
                @endunless

                <dl class="sheet-specifications">
                    @foreach ($specifications as $label => $value)
                        <dt>• {{ $label }}</dt>
                        <dd>:</dd>
                        <dd>{{ $value }}</dd>
                    @endforeach
                </dl>

                <div class="sheet-description">{{ filled($product->description) ? $product->description : 'No product description is available.' }}</div>
            </div>
        </section>
    </div>

    <footer class="sheet-footer">
        <span>WWW.PRO1GLOBALHOMECENTER.COM</span>
        <div class="sheet-socials">
            <i class="fa-brands fa-facebook-f"></i>
            <i class="fa-brands fa-viber"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-tiktok"></i>
            <i class="fa-brands fa-youtube"></i>
            <i class="fa-solid fa-paper-plane"></i>
        </div>
    </footer>
</article>
