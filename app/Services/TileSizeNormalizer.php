<?php

namespace App\Services;

class TileSizeNormalizer
{
    public static function normalize(?string $value): ?array
    {
        if (! $value) {
            return null;
        }

        $value = trim($value);

        // Step 1: Check the expected format first
        $pattern = '/^\(\s*\d+(?:\.\d+)?\s*mm\s*x\s*\d+(?:\.\d+)?\s*mm\s*\)\s*\/\s*\(\s*\d+(?:\.\d+)?\s*ft\s*x\s*\d+(?:\.\d+)?\s*ft\s*\)$/i';

        if (! preg_match($pattern, $value)) {
            return null;
        }

        // Step 2: Extract width and height only after format validation
        preg_match(
            '/^\(\s*(\d+(?:\.\d+)?)\s*mm\s*x\s*(\d+(?:\.\d+)?)\s*mm/i',
            $value,
            $matches
        );

        $width = (float) $matches[1];
        $height = (float) $matches[2];

        // Step 3: Validate dimensions
        if ($width <= 0 || $height <= 0) {
            return null;
        }

        // Step 4: Return normalized data
        return [
            'width_mm' => $width,
            'height_mm' => $height,
            'aspect_ratio' => round($width / $height, 4),
            'default_rotation_deg' => 0,
        ];
    }
}
