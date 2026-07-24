<section @class(['sheet-media', 'portrait-main' => $mainImageIsPortrait])>
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
