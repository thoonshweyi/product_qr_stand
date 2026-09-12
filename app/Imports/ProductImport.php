<?php

namespace App\Imports;

use App\Exceptions\ExcelImportValidationException;
use App\Models\Category;
use App\Models\Country;
use App\Models\ProductWorkflow;
use App\Models\Status;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProductImport implements ToCollection, WithHeadingRow
{
    private const ONLINE_REQUIRED_SPECIFICATIONS = ['Weight', 'Length', 'Width', 'Height', 'Size'];

    private const STAND_REQUIRED_SPECIFICATIONS = ['Weight'];

    private const DEFAULT_DESCRIPTION_MM = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';

    private const DEFAULT_DESCRIPTION_EN = 'Shop for variety of high quality products with reasonable price at PRO 1 Global, leading provider for construction and home improvement products.';

    private ProductService $productService;

    private ?User $user;

    private int $totalCount = 0;

    private int $importedCount = 0;

    private array $rowErrors = [];

    public function __construct(?ProductService $productService = null, ?User $user = null)
    {
        $this->productService = $productService ?? app(ProductService::class);
        $this->user = $user;
    }

    public function collection(Collection $rows)
    {
        $numberedRows = $rows
            ->map(fn ($row, $index) => [
                'row_number' => $index + 2,
                'data' => $row->toArray(),
            ])
            ->reject(fn ($row) => collect($row['data'])->filter(fn ($value) => filled($value))->isEmpty())
            ->values();

        $this->totalCount = $numberedRows->count();

        $productCodes = $rows->pluck('product_code')->filter()->toArray();
        $productresults = collect($this->productService->search_products($productCodes))->keyBy('barcode_code');

        // dd($productresults);

        foreach ($numberedRows as $numberedRow) {
            $data = $numberedRow['data'];

            // Start Same Data Structure for Request and Row
                    $workflow_id = Workflow::where('name', $data['workflow'] ?? null)->value('id');
                    $data['workflow_id'] = $workflow_id;
                    if (! empty($data['online_date'])) {
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
                    // $data['country_of_origin'] = $attributes['country_of_origin']['value'] ?? '';
                    $data['description'] = $description['description'] ?? '';
                    $data['description_en'] = $description_en['description'] ?? '';
                    
                    $countryOfOrigin = $attributes['country_of_origin']['value'] ?? '';
                    $data['country_of_origin'] = (string) Country::where('name', $countryOfOrigin)->value('id') ?? $countryOfOrigin;

                    $data['specifications'] = $this->productService->getSpecifications(Arr::except($attributes, [
                        'brand',
                        'name',
                        'model',
                        'code',
                        'country_of_origin',
                    ]));
                    // End GET From Description

                    // Start Get From ERP
                    $productresult = $productresults->get($data['product_code']);
                    $maincategory = $productresult->maincategory ?? '';
                    $data['category_id'] = Category::where('name', $maincategory)->value('id');
                    // End Get From ERP

                    $data['website_url'] = $data['website_url_ss'] ?? '';
                    // dd($data);
            // End Same Data Structure for Request and Row

            $selectedWorkflowSlug = Workflow::whereKey($data['workflow_id'])->value('slug');
            $requiresMainImage = Str::contains(strtolower((string) $selectedWorkflowSlug), 'stand');
            $requiresOnlineDate = Str::contains(strtolower((string) $selectedWorkflowSlug), 'online');
            $minimumOnlineDate = now()->startOfMonth()->toDateString();

            $validator = Validator::make($data, [
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
                // 'main_image' => [$requiresMainImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
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

                $this->rowErrors[] = [
                    'row' => $numberedRow['row_number'],
                    'product_code' => $data['product_code'] ?? '',
                    'product_name' => $data['product_name'] ?? '',
                    'workflow' => $data['workflow'] ?? '',
                    'errors' => $validator->errors()->all(),
                ];

                continue;
            }

            $product = $this->productService->create($data, $this->user);


            $this->importedCount++;

            if (Str::contains(Str::lower((string) ($data['workflow'] ?? '')), 'stand')) {
                $product->update([
                    'stage' => 'default',
                ]);

                $product->latestWorkflow?->update([
                    'current_step_id' => null,
                    'status' => 'default',
                ]);
                // ProductWorkflow::where('product_id', $product->id)
                // ->latest('id')
                // ->first()
                // ?->update([
                //     'status' => 'default',
                // ]);
            }
        }
    }

    public function totalCount(): int
    {
        return $this->totalCount;
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function rowErrors(): array
    {
        return $this->rowErrors;
    }
}
