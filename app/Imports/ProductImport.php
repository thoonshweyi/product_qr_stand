<?php

namespace App\Imports;

use App\Models\Status;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Services\ProductService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProductImport implements ToModel, WithHeadingRow
{
    private const ONLINE_REQUIRED_SPECIFICATIONS = ['Weight', 'Length', 'Width', 'Height', 'Size'];

    private const STAND_REQUIRED_SPECIFICATIONS = ['Weight'];

    private const DEFAULT_DESCRIPTION_MM = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';

    private const DEFAULT_DESCRIPTION_EN = 'Shop for variety of high quality products with reasonable price at PRO 1 Global, leading provider for construction and home improvement products.';

    public function __construct(ProductService $productService = null, User $user = null)
    {
        $this->productService = $productService;
        $this->user = $user;
    }

    public function model(array $row){
        $data = (array) $row;

        // Start Same Data Structure for Request and Row
            $workflow_id = Workflow::where('name', $data['workflow'] ?? null)->value('id');
            $data['workflow_id'] = $workflow_id;
            if (!empty($data['online_date'])) {
            $data['online_date'] = is_numeric($data['online_date'])
                    ? Date::excelToDateTimeObject($data['online_date'])->format('Y-m-d')
                    : Carbon::parse($data['online_date'])->format('Y-m-d');
            } else {
                $data['online_date'] = null;
            }
            
            $data['status_id'] = Status::where('name', 'Active')->value('id');

            // Start GET From Description
            $description = $this->productService->parseProductDescription($data['description'] ?? '');
            $description_en = $this->productService->parseProductDescription($data['description_en'] ?? '');

            $attributes = $description['attributes'] ?? [];
            
            $data['name'] = $attributes['name']['value'] ?? '';
            $data['product_name'] = $data['product_name'] ?? '';
            $data['brand'] = $attributes['brand']['value'] ?? '';
            $data['model'] = $attributes['model']['value'] ?? '';
            $data['country_of_origin'] = $attributes['country_of_origin']['value'] ?? '';
            $data['description'] = $description['description'] ?? '';
            $data['description_en'] = $description_en['description'] ?? '';

            $data['specifications'] = $this->productService->getSpecifications(Arr::except($attributes, [
                'brand', 
                'name', 
                'model', 
                'code', 
                'country_of_origin'
            ]));
            // dd($data);
        // End Same Data Structure for Request and Row

        $selectedWorkflowSlug = Workflow::whereKey($data['workflow_id'])->value('slug');
        $requiresMainImage = Str::contains(strtolower((string) $selectedWorkflowSlug), 'stand');
        $requiresOnlineDate = Str::contains(strtolower((string) $selectedWorkflowSlug), 'online');
        $minimumOnlineDate = now()->startOfMonth()->toDateString();

        $validator = Validator::make($data,[
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code'],
            'status_id' => ['required', 'exists:statuses,id'],
            // 'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'country_of_origin' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'main_image' => [$requiresMainImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'brand_icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'online_date' => [$requiresOnlineDate ? 'required' : 'nullable', 'date_format:Y-m-d', 'after_or_equal:'.$minimumOnlineDate],
            'specifications' => [
                'required',
                'array',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($data, $requiresMainImage, $requiresOnlineDate) {
                    $isStandOnly = Workflow::whereKey($data['workflow_id'])
                        ->where('slug', 'stand-only')
                        ->exists();

                    if ($isStandOnly && is_array($value) && count($value) > 10) {
                        $fail('Stand Only workflow allows a maximum of 10 specifications.');
                    }

                    if (($requiresMainImage || $requiresOnlineDate) && is_array($value)) {
                        $submittedNames = collect($value)
                            ->pluck('name')
                            ->map(fn ($name) => Str::lower(Str::squish((string) $name)));
                        $requiredSpecifications = collect($requiresOnlineDate ? self::ONLINE_REQUIRED_SPECIFICATIONS : [])
                            ->merge($requiresMainImage ? self::STAND_REQUIRED_SPECIFICATIONS : [])
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
        ]);

        if ($validator->fails()) {
            Log::info('Product import validation failed', [
                'errors' => $validator->errors()->all(),
                'row' => $data,
            ]);
            throw new Exception('Validation failed: '.implode(', ', $validator->errors()->all()));
        }

        // $product = app(ProductCreateService::class)
        // ->create($row, $this->user);
        $product = $this->productService->create($data, $this->user);

    }
}
