<?php

namespace App\Support;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Support\Str;

class ProductValidationRules
{
    public const ONLINE_REQUIRED_SPECIFICATIONS = ['Weight', 'Length', 'Width', 'Height', 'Size'];

    public const STAND_REQUIRED_SPECIFICATIONS = ['Weight'];

    public static function create(array $data = [], array $options = []): array
    {
        $workflow = filled($data['workflow_id'] ?? null)
            ? Workflow::find($data['workflow_id'])
            : null;
        $workflowSlug = Str::lower((string) $workflow?->slug);
        $requiresStand = Str::contains($workflowSlug, 'stand');
        $requiresOnline = Str::contains($workflowSlug, 'online');
        $requiresImageValidation = (bool) ($options['require_images'] ?? true);
        $minimumOnlineDate = now()->startOfMonth()->toDateString();

        return [
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code'],
            'status_id' => ['required', 'exists:statuses,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'country_of_origin' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'main_image' => [$requiresImageValidation && $requiresStand ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'brand_icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'online_date' => [$requiresOnline ? 'required' : 'nullable', 'date_format:Y-m-d', 'after_or_equal:'.$minimumOnlineDate],
            'specifications' => [
                'required',
                'array',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($requiresStand, $requiresOnline, $workflow) {
                    if ($workflow?->slug === 'stand-only' && is_array($value) && count($value) > 10) {
                        $fail('Stand Only workflow allows a maximum of 10 specifications.');
                    }

                    if (($requiresStand || $requiresOnline) && is_array($value)) {
                        $submittedNames = collect($value)
                            ->pluck('name')
                            ->map(fn ($name) => Str::lower(Str::squish((string) $name)));

                        $requiredSpecifications = collect($requiresOnline ? self::ONLINE_REQUIRED_SPECIFICATIONS : [])
                            ->merge($requiresStand ? self::STAND_REQUIRED_SPECIFICATIONS : [])
                            ->unique();

                        $missing = $requiredSpecifications
                            ->reject(fn ($name) => $submittedNames->contains(Str::lower($name)));

                        if ($missing->isNotEmpty()) {
                            $fail('This workflow requires these specifications: '.$missing->implode(', ').'.');
                        }
                    }
                },
            ],
            'specifications.*.name' => ['required', 'string', 'max:255'],
            'specifications.*.value' => ['required', 'string', 'max:255'],
            'workflow_id' => [
                'required',
                'integer',
                'exists:workflows,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! WorkflowStep::where('workflow_id', $value)->exists()) {
                        $fail('The selected workflow does not have any steps.');
                    }
                },
            ],
        ];
    }
}
