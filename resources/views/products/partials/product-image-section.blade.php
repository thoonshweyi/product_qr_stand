@php
    $showThumbnailMeasurement = false;
    $thumbnailWidthLabel = null;
    $thumbnailHeightLabel = null;
    $categoryName = mb_strtolower((string) $product->category?->name);

    if ($thumbnailImage && str_contains($categoryName, 'surface covering')) {
        $sizeSpecification = $product->specificationValues->first(
            fn ($item) => mb_strtolower(trim((string) $item->specification?->name)) === 'size'
        );
        $metricSize = trim(explode('/', (string) $sizeSpecification?->value, 2)[0]);

        if ($metricSize !== '' && preg_match('/mm/i', $metricSize)) {
            preg_match_all('/\d+(?:[.,]\d+)?/u', $metricSize, $dimensionMatches);
            $dimensions = $dimensionMatches[0] ?? [];

            if (count($dimensions) >= 2) {
                $thumbnailWidthLabel = str_replace(',', '.', $dimensions[0]).'mm';
                $thumbnailHeightLabel = str_replace(',', '.', $dimensions[1]).'mm';
                $showThumbnailMeasurement = true;
            }
        }
    }
@endphp

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
            <div @class(['sheet-thumbnail', 'has-measurement' => $showThumbnailMeasurement])>
                <img src="{{ asset($thumbnailImage) }}" alt="{{ $product->name }} thumbnail">
                @if ($showThumbnailMeasurement)
                    <div class="sheet-thumbnail-measurement" aria-hidden="true">
                        <div class="sheet-measurement-top"><span>{{ $thumbnailWidthLabel }}</span></div>
                        <div class="sheet-measurement-left"><span>{{ $thumbnailHeightLabel }}</span></div>
                    </div>
                @endif
            </div>
        @endif
    </aside>

    <div class="sheet-main">
        <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}">
    </div>
</section>
